<?php
add_action('init', function() {
    register_post_type('azizi_banner', [
        'labels' => ['name'=>'Баннеры','singular_name'=>'Баннер'],
        'public'=>true,
        'menu_icon'=>'dashicons-images-alt2',
        'supports'=>['title','thumbnail']
    ]);
});

add_filter('pll_get_post_types', function($types){
    $types['azizi_banner'] = 'azizi_banner';
    return $types;
});
