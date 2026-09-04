<?php

if (!defined('ABSPATH')) {
    exit;
}

/** Whether the current administrator may view and edit RTS registration data. */
function rts_can_manage_user_registration_profile($user_id)
{
    return current_user_can('edit_user', absint($user_id))
        && (current_user_can('manage_options') || current_user_can(RTS_MANAGE_CAPABILITY));
}

/** Return a user-meta value, falling back to the authoritative participant row. */
function rts_admin_user_registration_value($user_id, $meta_key, $participant, $participant_field)
{
    $value = get_user_meta($user_id, $meta_key, true);
    if ('' === (string) $value && $participant && isset($participant->{$participant_field})) {
        $value = $participant->{$participant_field};
    }

    return (string) $value;
}

/** Display registration and survey-location details on Users -> Edit User. */
function rts_render_admin_user_registration_profile($user)
{
    if (!$user instanceof WP_User || !rts_can_manage_user_registration_profile($user->ID)) {
        return;
    }

    $registration = RunTheSeasPlugin::get_instance()->registration;
    $participant = $registration->get_participant_for_user($user);
    $value = static function ($meta_key, $participant_field) use ($user, $participant) {
        return rts_admin_user_registration_value($user->ID, $meta_key, $participant, $participant_field);
    };
    $marketing_consent = '1' === $value('rts_marketing_consent', 'marketing_consent');
    $age_confirmed_at = $value('rts_age_consent_confirmed_at', 'age_consent_confirmed_at');
    ?>
    <h2><?php esc_html_e('Run The Seas Registration & Location', 'run-the-seas'); ?></h2>
    <p><?php esc_html_e('Registration details are synchronized with the participant record. First name, last name, and email are managed in the standard WordPress fields above.', 'run-the-seas'); ?></p>
    <?php wp_nonce_field('rts_save_admin_user_registration_' . $user->ID, 'rts_admin_user_registration_nonce'); ?>
    <table class="form-table" role="presentation">
        <tr>
            <th><label for="rts_phone"><?php esc_html_e('Mobile phone', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="tel" id="rts_phone" name="rts_phone" value="<?php echo esc_attr($value('rts_phone', 'phone')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_address"><?php esc_html_e('Address line 1', 'run-the-seas'); ?></label></th>
            <td><textarea class="large-text" rows="2" id="rts_address" name="rts_address"><?php echo esc_textarea($value('rts_address', 'address')); ?></textarea></td>
        </tr>
        <tr>
            <th><label for="rts_address_2"><?php esc_html_e('Address line 2', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_address_2" name="rts_address_2" value="<?php echo esc_attr($value('rts_address_2', 'address_2')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_city"><?php esc_html_e('City', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_city" name="rts_city" value="<?php echo esc_attr($value('rts_city', 'city')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_province"><?php esc_html_e('State / province', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_province" name="rts_province" value="<?php echo esc_attr($value('rts_province', 'registration_province')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_postal_code"><?php esc_html_e('ZIP / postal code', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_postal_code" name="rts_postal_code" maxlength="30" value="<?php echo esc_attr($value('rts_postal_code', 'postal_code')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_registration_country"><?php esc_html_e('Country entered at registration', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_registration_country" name="rts_registration_country" maxlength="100" value="<?php echo esc_attr($value('rts_registration_country', 'registration_country')); ?>"></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Country detected during survey', 'run-the-seas'); ?></th>
            <td><code><?php echo esc_html($value('rts_detected_country', 'detected_country') ?: '—'); ?></code><p class="description"><?php esc_html_e('Read-only survey location data.', 'run-the-seas'); ?></p></td>
        </tr>
        <tr>
            <th><label for="rts_gender"><?php esc_html_e('Gender', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_gender" name="rts_gender" value="<?php echo esc_attr($value('rts_gender', 'gender')); ?>"></td>
        </tr>
        <tr>
            <th><label for="rts_age_range"><?php esc_html_e('Age range', 'run-the-seas'); ?></label></th>
            <td><input class="regular-text" type="text" id="rts_age_range" name="rts_age_range" value="<?php echo esc_attr($value('rts_age_range', 'age_range')); ?>"></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Marketing consent', 'run-the-seas'); ?></th>
            <td><label><input type="checkbox" name="rts_marketing_consent" value="1" <?php checked($marketing_consent); ?>> <?php esc_html_e('Participant opted in', 'run-the-seas'); ?></label></td>
        </tr>
        <tr>
            <th><?php esc_html_e('Age and policy confirmation', 'run-the-seas'); ?></th>
            <td><label><input type="checkbox" disabled <?php checked('' !== $age_confirmed_at); ?>> <?php esc_html_e('Confirmed during registration', 'run-the-seas'); ?></label><?php if ($age_confirmed_at) : ?><p class="description"><?php echo esc_html($age_confirmed_at); ?></p><?php endif; ?></td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'rts_render_admin_user_registration_profile');
add_action('edit_user_profile', 'rts_render_admin_user_registration_profile');

/** Save edited registration fields to both user meta and the participant row. */
function rts_save_admin_user_registration_profile($user_id)
{
    $user_id = absint($user_id);
    if (
        !$user_id
        || !rts_can_manage_user_registration_profile($user_id)
        || empty($_POST['rts_admin_user_registration_nonce'])
        || !wp_verify_nonce(
            sanitize_text_field(wp_unslash($_POST['rts_admin_user_registration_nonce'])),
            'rts_save_admin_user_registration_' . $user_id
        )
    ) {
        return;
    }

    $posted_value = static function ($key, $textarea = false) {
        if (!isset($_POST[$key]) || !is_scalar($_POST[$key])) {
            return '';
        }
        $raw_value = wp_unslash((string) $_POST[$key]);

        return $textarea ? sanitize_textarea_field($raw_value) : sanitize_text_field($raw_value);
    };

    $values = array(
        'phone' => $posted_value('rts_phone'),
        'address' => $posted_value('rts_address', true),
        'address_2' => $posted_value('rts_address_2'),
        'city' => $posted_value('rts_city'),
        'registration_province' => $posted_value('rts_province'),
        'postal_code' => substr($posted_value('rts_postal_code'), 0, 30),
        'registration_country' => substr($posted_value('rts_registration_country'), 0, 100),
        'gender' => $posted_value('rts_gender'),
        'age_range' => $posted_value('rts_age_range'),
        'marketing_consent' => !empty($_POST['rts_marketing_consent']) ? 1 : 0,
    );

    $meta_map = array(
        'phone' => 'rts_phone',
        'address' => 'rts_address',
        'address_2' => 'rts_address_2',
        'city' => 'rts_city',
        'registration_province' => 'rts_province',
        'postal_code' => 'rts_postal_code',
        'registration_country' => 'rts_registration_country',
        'gender' => 'rts_gender',
        'age_range' => 'rts_age_range',
        'marketing_consent' => 'rts_marketing_consent',
    );
    foreach ($meta_map as $participant_field => $meta_key) {
        update_user_meta($user_id, $meta_key, (string) $values[$participant_field]);
    }

    $registration = RunTheSeasPlugin::get_instance()->registration;
    $participant = $registration->get_participant_for_user($user_id);
    if (!$participant) {
        return;
    }

    $participant_updates = $values;
    $participant_updates['province'] = $values['registration_province'];
    $participant_updates['updated_at'] = current_time('mysql');
    if (empty($participant->detected_country) && empty($participant->country_verified)) {
        $participant_updates['country'] = $values['registration_country'];
    }

    global $wpdb;
    $updated = $wpdb->update(
        $wpdb->prefix . 'rts_participants',
        $participant_updates,
        array('id' => absint($participant->id))
    );
    if (false !== $updated) {
        $registration->sync_participant_user_meta($participant->id);
        $administrator = wp_get_current_user();
        $registration->log_timeline(
            $participant->id,
            'admin_registration_update',
            'Registration details updated from the WordPress user profile by ' . $administrator->display_name
        );
    }
}
add_action('personal_options_update', 'rts_save_admin_user_registration_profile');
add_action('edit_user_profile_update', 'rts_save_admin_user_registration_profile');

