jQuery(document).ready(function($){
    $('#isw-ml-form').on('submit', function(e){
        e.preventDefault();
        var $form = $(this);
        var formData = $form.serialize() + '&action=isw_ml_submit';
        $.post(isw_ml_ajax.ajax_url, formData, function(response){
            var msg = '';
            if(response.success){
                msg = '<div class="isw-ml-form-message">' + isw_ml_ajax.success_msg + '</div>';
                $form[0].reset();
            } else {
                msg = '<div class="isw-ml-form-message isw-ml-error">' + isw_ml_ajax.error_msg + '</div>';
            }
            $('#isw-ml-form-message').html(msg);
        });
    });
});
