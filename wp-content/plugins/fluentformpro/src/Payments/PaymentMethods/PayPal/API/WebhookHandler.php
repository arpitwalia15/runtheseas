<?php

namespace FluentFormPro\Payments\PaymentMethods\PayPal\API;

if (!defined('ABSPATH')) {
    exit;
}

use FluentForm\Framework\Helpers\ArrayHelper;
use FluentFormPro\Payments\PaymentHelper;
use FluentFormPro\Payments\PaymentMethods\PayPal\PayPalProcessor;
use FluentFormPro\Payments\PaymentMethods\PayPal\PayPalSettings;

class WebhookHandler
{
    public function handle()
    {
        $body = file_get_contents('php://input');
        if (!$body) {
            status_header(200);
            return;
        }

        $event = json_decode($body, true);
        if (!$event || empty($event['event_type'])) {
            status_header(200);
            return;
        }

        // Verify webhook signature — refuse processing without webhook_id.
        // Return 5xx so PayPal retries the event, otherwise subscription
        // activation events (BILLING.SUBSCRIPTION.ACTIVATED) would be
        // silently acknowledged-and-dropped and subscriptions would stay
        // pending forever the moment an admin forgets to configure the
        // webhook ID.
        $webhookId = PayPalSettings::getWebhookId();
        if (!$webhookId) {
            PaymentHelper::log([
                'status' => 'error',
                'title'  => __('PayPal Webhook ID not configured. Webhook event NOT processed; PayPal will retry.', 'fluentformpro'),
            ]);
            status_header(503);
            return;
        }

        $verified = $this->verifySignature($webhookId, $body);
        if (!$verified) {
            PaymentHelper::log([
                'status' => 'error',
                'title'  => __('PayPal Webhook signature verification failed.', 'fluentformpro'),
            ]);
            status_header(400);
            return;
        }

        status_header(200);

        $eventType = sanitize_text_field($event['event_type']);
        $resource = ArrayHelper::get($event, 'resource', []);

        switch ($eventType) {
            case 'BILLING.SUBSCRIPTION.ACTIVATED':
                $this->handleSubscriptionActivated($resource);
                break;
            case 'PAYMENT.SALE.COMPLETED':
                $this->handlePaymentCompleted($resource);
                break;
            case 'BILLING.SUBSCRIPTION.CANCELLED':
                $this->handleSubscriptionCancelled($resource);
                break;
            case 'BILLING.SUBSCRIPTION.EXPIRED':
                $this->handleSubscriptionExpired($resource);
                break;
            case 'BILLING.SUBSCRIPTION.PAYMENT.FAILED':
            case 'BILLING.SUBSCRIPTION.SUSPENDED':
                $this->handleSubscriptionFailed($resource);
                break;
        }
    }

    private function handleSubscriptionActivated($resource)
    {
        $vendorSubscriptionId = sanitize_text_field(ArrayHelper::get($resource, 'id', ''));
        if (!$vendorSubscriptionId) {
            return;
        }

        $subscription = $this->findSubscriptionByVendorId($vendorSubscriptionId);
        if (!$subscription) {
            PaymentHelper::log([
                'status'      => 'error',
                'title'       => __('PayPal Webhook: Subscription not found', 'fluentformpro'),
                'description' => sprintf(
                    __('Received ACTIVATED event for subscription %s but no matching local record exists.', 'fluentformpro'),
                    $vendorSubscriptionId
                ),
            ]);
            return;
        }

        $submission = $this->findSubmission($subscription->submission_id);
        if (!$submission) {
            return;
        }

        // Don't reactivate a cancelled/completed subscription
        if (in_array($subscription->status, ['cancelled', 'completed'])) {
            return;
        }

        $this->updateSubscription($subscription->id, [
            'vendor_response' => maybe_serialize($resource),
        ]);

        $subscriptionStatus = 'active';
        if ($subscription->trial_days && $subscription->status == 'pending') {
            $subscriptionStatus = 'trialling';
        }

        $processor = new PayPalProcessor();
        $processor->setSubmissionId($submission->id);

        $subscription = fluentFormApi('submissions')->getSubscription($subscription->id);
        $processor->updateSubscriptionStatus($subscription, $subscriptionStatus);

        $this->updateSubmission($submission->id, [
            'payment_status' => 'paid',
        ]);

        do_action('fluentform/log_data', [
            'parent_source_id' => $submission->form_id,
            'source_type'      => 'submission_item',
            'source_id'        => $submission->id,
            'component'        => 'Subscription',
            'status'           => 'success',
            'title'            => __('PayPal Subscription Activated', 'fluentformpro'),
            'description'      => __('PayPal subscription has been activated via webhook.', 'fluentformpro'),
        ]);
    }

    private function handlePaymentCompleted($resource)
    {
        $billingAgreementId = sanitize_text_field(ArrayHelper::get($resource, 'billing_agreement_id', ''));
        if (!$billingAgreementId) {
            return;
        }

        $subscription = $this->findSubscriptionByVendorId($billingAgreementId);
        if (!$subscription) {
            PaymentHelper::log([
                'status'      => 'error',
                'title'       => __('PayPal Webhook: Subscription not found', 'fluentformpro'),
                'description' => sprintf(
                    __('Received PAYMENT event for subscription %s but no matching local record exists.', 'fluentformpro'),
                    $billingAgreementId
                ),
            ]);
            return;
        }

        $submission = $this->findSubmission($subscription->submission_id);
        if (!$submission) {
            return;
        }

        $txnId = sanitize_text_field(ArrayHelper::get($resource, 'id', ''));

        // Check for duplicate transaction
        $existingTransaction = wpFluent()->table('fluentform_transactions')
            ->where('charge_id', $txnId)
            ->first();
        if ($existingTransaction) {
            return;
        }

        $amount = ArrayHelper::get($resource, 'amount.total', '0');
        $currency = ArrayHelper::get($resource, 'amount.currency', $submission->currency);

        if (!is_numeric($amount) || floatval($amount) <= 0) {
            PaymentHelper::log([
                'status'      => 'error',
                'title'       => __('PayPal Webhook: Invalid payment amount', 'fluentformpro'),
                'description' => sprintf(
                    __('Received payment event with invalid amount: %s. Event ignored.', 'fluentformpro'),
                    $amount
                ),
            ], $submission);
            return;
        }

        $paymentData = [
            'form_id'          => $submission->form_id,
            'submission_id'    => $submission->id,
            'subscription_id'  => $subscription->id,
            'payer_email'      => sanitize_email(ArrayHelper::get($resource, 'payer.payer_info.email_address', '')),
            'payer_name'       => sanitize_text_field(ArrayHelper::get($resource, 'payer.payer_info.payer_name', '')),
            'transaction_type' => 'subscription',
            'payment_method'   => 'paypal',
            'charge_id'        => $txnId,
            'payment_total'    => intval(round(floatval($amount) * 100)),
            'status'           => 'paid',
            'currency'         => $currency,
            'payment_mode'     => PayPalSettings::isLive($submission->form_id) ? 'live' : 'test',
            'payment_note'     => maybe_serialize($resource),
        ];

        if ($submission->user_id) {
            $paymentData['user_id'] = $submission->user_id;
        }

        $processor = new PayPalProcessor();
        $processor->setSubmissionId($submission->id);

        // Check for pending transaction to update — constrain to the exact subscription
        $pendingTransaction = wpFluent()->table('fluentform_transactions')
            ->whereNull('charge_id')
            ->where('submission_id', $submission->id)
            ->where('subscription_id', $subscription->id)
            ->where('payment_method', 'paypal')
            ->where('status', 'pending')
            ->orderBy('id', 'DESC')
            ->first();

        // Fallback: redirect-back may have already paid this transaction without a charge_id.
        if (!$pendingTransaction) {
            $pendingTransaction = wpFluent()->table('fluentform_transactions')
                ->whereNull('charge_id')
                ->where('submission_id', $submission->id)
                ->where('subscription_id', $subscription->id)
                ->where('payment_method', 'paypal')
                ->where('status', 'paid')
                ->orderBy('id', 'DESC')
                ->first();
        }

        if ($pendingTransaction) {
            $processor->updateTransaction($pendingTransaction->id, $paymentData);
        } else {
            $processor->maybeInsertSubscriptionCharge($paymentData);
        }

        $processor->recalculatePaidTotal();

        if ($pendingTransaction) {
            $processor->completePaymentSubmission(false);
        }

        $updatedSubscription = fluentFormApi('submissions')->getSubscription($subscription->id);

        do_action('fluentform/log_data', [
            'parent_source_id' => $submission->form_id,
            'source_type'      => 'submission_item',
            'source_id'        => $submission->id,
            'component'        => 'Subscription',
            'status'           => 'success',
            'title'            => __('PayPal Subscription Payment Received', 'fluentformpro'),
            'description'      => __('A subscription payment has been received via webhook.', 'fluentformpro'),
        ]);

        do_action('fluentform/subscription_payment_received', $submission, $updatedSubscription, $submission->form_id, $subscription);
        do_action('fluentform/subscription_payment_received_paypal', $submission, $updatedSubscription, $submission->form_id, $subscription);

        if ($updatedSubscription->bill_count === 1) {
            PaymentHelper::maybeFireSubmissionActionHok($submission);
        }
    }

    private function handleSubscriptionCancelled($resource)
    {
        $vendorSubscriptionId = sanitize_text_field(ArrayHelper::get($resource, 'id', ''));
        $subscription = $this->findSubscriptionByVendorId($vendorSubscriptionId);
        if (!$subscription) {
            return;
        }

        $processor = new PayPalProcessor();
        $processor->setSubmissionId($subscription->submission_id);
        $subscription = fluentFormApi('submissions')->getSubscription($subscription->id);
        $processor->updateSubscriptionStatus($subscription, 'cancelled');
    }

    private function handleSubscriptionExpired($resource)
    {
        $vendorSubscriptionId = sanitize_text_field(ArrayHelper::get($resource, 'id', ''));
        $subscription = $this->findSubscriptionByVendorId($vendorSubscriptionId);
        if (!$subscription) {
            return;
        }

        $processor = new PayPalProcessor();
        $processor->setSubmissionId($subscription->submission_id);
        $subscription = fluentFormApi('submissions')->getSubscription($subscription->id);
        $processor->updateSubscriptionStatus($subscription, 'completed');
    }

    private function handleSubscriptionFailed($resource)
    {
        $vendorSubscriptionId = sanitize_text_field(ArrayHelper::get($resource, 'id', ''));
        $subscription = $this->findSubscriptionByVendorId($vendorSubscriptionId);
        if (!$subscription) {
            return;
        }

        $processor = new PayPalProcessor();
        $processor->setSubmissionId($subscription->submission_id);
        $subscription = fluentFormApi('submissions')->getSubscription($subscription->id);
        $processor->updateSubscriptionStatus($subscription, 'cancelled');
    }

    private function verifySignature($webhookId, $rawBody)
    {
        $headers = [
            'auth_algo'         => isset($_SERVER['HTTP_PAYPAL_AUTH_ALGO']) ? sanitize_text_field($_SERVER['HTTP_PAYPAL_AUTH_ALGO']) : '',
            'cert_url'          => isset($_SERVER['HTTP_PAYPAL_CERT_URL']) ? sanitize_url($_SERVER['HTTP_PAYPAL_CERT_URL']) : '',
            'transmission_id'   => isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID']) ? sanitize_text_field($_SERVER['HTTP_PAYPAL_TRANSMISSION_ID']) : '',
            'transmission_sig'  => isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']) ? sanitize_text_field($_SERVER['HTTP_PAYPAL_TRANSMISSION_SIG']) : '',
            'transmission_time' => isset($_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME']) ? sanitize_text_field($_SERVER['HTTP_PAYPAL_TRANSMISSION_TIME']) : '',
        ];

        $verifyData = [
            'auth_algo'         => $headers['auth_algo'],
            'cert_url'          => $headers['cert_url'],
            'transmission_id'   => $headers['transmission_id'],
            'transmission_sig'  => $headers['transmission_sig'],
            'transmission_time' => $headers['transmission_time'],
            'webhook_id'        => $webhookId,
            'webhook_event'     => json_decode($rawBody, true),
        ];

        $api = new SubscriptionsAPI();
        $response = $api->verifyWebhookSignature($verifyData);

        if (is_wp_error($response)) {
            return false;
        }

        return ArrayHelper::get($response, 'verification_status') === 'SUCCESS';
    }

    private function findSubscriptionByVendorId($vendorSubscriptionId)
    {
        if (!$vendorSubscriptionId) {
            return null;
        }
        return wpFluent()->table('fluentform_subscriptions')
            ->where('vendor_subscription_id', $vendorSubscriptionId)
            ->first();
    }

    private function findSubmission($id)
    {
        return wpFluent()->table('fluentform_submissions')
            ->where('id', $id)
            ->first();
    }

    private function updateSubmission($id, $data)
    {
        $data['updated_at'] = current_time('mysql');
        wpFluent()->table('fluentform_submissions')
            ->where('id', $id)
            ->update($data);
    }

    private function updateSubscription($id, $data)
    {
        $data['updated_at'] = current_time('mysql');
        wpFluent()->table('fluentform_subscriptions')
            ->where('id', $id)
            ->update($data);
    }
}
