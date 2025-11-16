<?php

add_action('add_meta_boxes', function () {
    add_meta_box(
        'azizi_meta',
        'Параметры баннера',
        'azizi_meta_box',
        'azizi_banner',
        'normal',
        'high'
    );
});

function azizi_image_field($label, $name, $value) {
    $img = $value ? wp_get_attachment_image_src($value, 'medium')[0] : '';
    ?>

    <div class="azizi-image-field" style="margin-bottom:20px;">
        <label><b><?php echo $label; ?>:</b></label><br>

        <img src="<?php echo $img; ?>"
             class="azizi-preview-<?php echo $name; ?>"
             style="width:200px;height:auto;display:<?php echo $img ? 'block' : 'none'; ?>;
                     margin:10px 0;border:1px solid #ccc;border-radius:6px;" />

        <input type="hidden" name="<?php echo $name; ?>" value="<?php echo esc_attr($value); ?>" class="azizi-input-<?php echo $name; ?>">

        <button type="button" class="button azizi-upload" data-target="<?php echo $name; ?>">
            Выбрать изображение
        </button>

        <button type="button" class="button azizi-remove" data-target="<?php echo $name; ?>" style="color:#a00;margin-left:10px;">
            Удалить
        </button>
    </div>

    <?php
}

function azizi_meta_box($post) {

    /* ─────────────────────────────
       ✔ FIX: подключаем media uploader
       ───────────────────────────── */
    wp_enqueue_media();

    $pre = get_post_meta($post->ID, 'pretitle', true);
    $sub = get_post_meta($post->ID, 'subtitle', true);
    $btn = get_post_meta($post->ID, 'btn_text', true);
    $url = get_post_meta($post->ID, 'btn_url', true);

    $desk = get_post_meta($post->ID, 'desktop_image', true);
    $mob  = get_post_meta($post->ID, 'mobile_image', true);
    $vid  = get_post_meta($post->ID, 'video_url', true);
    ?>

    <p><label><b>Pre-title:</b></label>
        <input class="widefat" name="pretitle" value="<?php echo esc_attr($pre); ?>"></p>

    <p><label><b>Subtitle:</b></label>
        <input class="widefat" name="subtitle" value="<?php echo esc_attr($sub); ?>"></p>

    <p><label><b>Button text:</b></label>
        <input class="widefat" name="btn_text" value="<?php echo esc_attr($btn); ?>"></p>

    <p><label><b>Button URL:</b></label>
        <input class="widefat" name="btn_url" value="<?php echo esc_attr($url); ?>"></p>

    <?php
    azizi_image_field("Desktop Image", "desktop_image", $desk);
    azizi_image_field("Mobile Image", "mobile_image", $mob);
    ?>

    <p><label><b>Фоновое видео (mp4/webm URL):</b></label>
        <input class="widefat" name="video_url" value="<?php echo esc_attr($vid); ?>"></p>

    <script>
        jQuery(function ($) {

            let frame;

            $('.azizi-upload').on('click', function (e) {
                e.preventDefault();

                let target = $(this).data('target');

                frame = wp.media({
                    title: 'Выберите изображение',
                    button: { text: 'Использовать' },
                    multiple: false
                });

                frame.on('select', function () {
                    let attachment = frame.state().get('selection').first().toJSON();

                    $('.azizi-input-' + target).val(attachment.id);
                    $('.azizi-preview-' + target).attr('src', attachment.url).show();
                });

                frame.open();
            });

            $('.azizi-remove').on('click', function () {
                let target = $(this).data('target');

                $('.azizi-input-' + target).val('');
                $('.azizi-preview-' + target).hide();
            });

        });
    </script>

    <?php
}

add_action('save_post', function ($id) {

    $fields = [
        'pretitle',
        'subtitle',
        'btn_text',
        'btn_url',
        'desktop_image',
        'mobile_image',
        'video_url'
    ];

    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($id, $f, sanitize_text_field($_POST[$f]));
        }
    }
});
