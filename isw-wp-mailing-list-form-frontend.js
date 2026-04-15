jQuery(document).ready(function($){
    $(document).on('submit', '.isw-ml-form', function(e){
        e.preventDefault();
        var $form = $(this);
        var $messageTarget = $form.closest('.isw-ml-form-container').find('.isw-ml-form-message-target').first();
        var formData = $form.serialize() + '&action=isw_ml_submit';
        $.post(isw_ml_ajax.ajax_url, formData, function(response){
            var msg = '';
            if(response.success){
                msg = '<div class="isw-ml-form-message">' + isw_ml_ajax.success_msg + '</div>';
                $form[0].reset();
            } else {
                var errorMessage = isw_ml_ajax.error_msg;
                if (response.data && response.data.reason === 'duplicate') {
                    errorMessage = isw_ml_ajax.duplicate_msg;
                } else if (response.data && response.data.reason === 'invalid_email') {
                    errorMessage = isw_ml_ajax.invalid_email_msg;
                } else if (response.data && response.data.reason === 'invalid_nonce') {
                    errorMessage = isw_ml_ajax.invalid_nonce_msg;
                }
                msg = '<div class="isw-ml-form-message isw-ml-error">' + errorMessage + '</div>';
            }
            $messageTarget.html(msg);
        });
    });
});
