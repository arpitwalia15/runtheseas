jQuery(document).ready(function($) {
    // Handle form submission for all login forms (supports multiple widgets on same page)
    $('.wb_login_form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var widgetId = $form.data('widget-id');
        var $errorDiv = $('#wbcom-login-error-' + widgetId);
        var formData = $form.serialize() + '&security=' + wbcom_ajax_login_params.security;
        var redirectUrl = $form.find('input[name="redirect_to"]').val();

        $.ajax({
            type: 'POST',
            url: wbcom_ajax_login_params.ajax_url,
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                $errorDiv.hide();
            },
            success: function(response) {
                if (response.loggedin) {
                    window.location.href = response.redirect ? response.redirect : redirectUrl;
                } else {
                    $errorDiv.html(response.message).fadeIn();
                }
            }
        });
    });
});
