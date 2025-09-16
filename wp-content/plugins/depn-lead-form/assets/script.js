jQuery(document).ready(function($){
    const modal = $('#depn-lead-modal');

    if (!modal.parent().is('body')) {
        modal.appendTo('body');
    }

    $('.depn-lead-open').on('click', function(){
        modal.show();
    });

    $('.depn-lead-close').on('click', function(){
        modal.hide();
    });

    $('#depn-lead-form').on('submit', function(e){
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save_lead';

        $.post(depnLeadFormAjax.ajaxurl, formData, function(response){
            const msgBox = $('#depn-lead-message');
            msgBox.empty();

            if(response.success) {
                msgBox.html('<div class="depn-lead-success">'+response.data.message+'</div>');
                $('#depn-lead-form')[0].reset();
            } else {
                msgBox.html('<div class="depn-lead-error">'+response.data.message+'</div>');
            }
        });
    });

});
