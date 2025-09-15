jQuery(document).ready(function($){
    const modal = $('#card-lead-modal');

    $('.card-lead-open').on('click', function(){
        modal.show();
    });

    $('.card-lead-close').on('click', function(){
        modal.hide();
    });

    // автотранслитерация ФИО → card_name
    $('#card-lead-form [name="name"]').on('input', function(){
        const fullName = $(this).val();
        const translit = transliterate(fullName);
        $('#card-lead-form [name="card_name"]').val(translit);
    });

    $('#card-lead-form').on('submit', function(e){
        e.preventDefault();
        const formData = $(this).serialize() + '&action=save_lead';

        $.post(CardLeadFormAjax.ajaxurl, formData, function(response){
            const msgBox = $('#card-lead-message');
            msgBox.empty();

            if(response.success) {
                msgBox.html('<div class="card-lead-success">'+response.data.message+'</div>');
                $('#card-lead-form')[0].reset();
            } else {
                msgBox.html('<div class="card-lead-error">'+response.data.message+'</div>');
            }
        });
    });

    function transliterate(text) {
        const map = {
            'А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'E','Ж':'Zh',
            'З':'Z','И':'I','Й':'Y','К':'K','Л':'L','М':'M','Н':'N','О':'O',
            'П':'P','Р':'R','С':'S','Т':'T','У':'U','Ф':'F','Х':'Kh','Ц':'Ts',
            'Ч':'Ch','Ш':'Sh','Щ':'Sch','Ы':'Y','Э':'E','Ю':'Yu','Я':'Ya',
            'Ь':'','Ъ':''
        };

        // разбиваем строку на слова
        let parts = text.trim().split(/\s+/);

        // берём только первые два (имя и фамилия)
        parts = parts.slice(0, 2);

        // транслитерируем каждое слово
        const result = parts.map(word =>
            word.split('').map(c => map[c] || map[c.toUpperCase()]?.toLowerCase() || c).join('')
        );

        // собираем обратно через пробел
        return result.join(' ');
    }
});
