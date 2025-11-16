<?php
add_action('init', function() {
    register_post_type('azizi_banner', [
        'labels' => ['name'=>'Баннеры','singular_name'=>'Баннер'],
        'public'=>true,
        'menu_icon'=>'dashicons-images-alt2',
        'supports'=>['title','thumbnail']
    ]);
});
