jQuery(document).ready(function($){
    const modal = $('#cred-lead-modal');

    if (!modal.parent().is('body')) {
        modal.appendTo('body');
    }

    $('.cred-lead-open').on('click', function(){
        modal.show();
    });

    $('.cred-lead-close').on('click', function(){
        modal.hide();
    });

    $('#cred-lead-form').on('submit', function(e){
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save_lead';

        $.post(credLeadFormAjax.ajaxurl, formData, function(response){
            const msgBox = $('#cred-lead-message');
            msgBox.empty();

            if(response.success) {
                msgBox.html('<div class="cred-lead-success">'+response.data.message+'</div>');
                $('#cred-lead-form')[0].reset();
            } else {
                msgBox.html('<div class="cred-lead-error">'+response.data.message+'</div>');
            }
        });
    });

});
