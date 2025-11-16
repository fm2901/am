<?php
add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('azizi-css', plugins_url('../assets/slider.css', __FILE__));
    wp_enqueue_script('azizi-js', plugins_url('../assets/slider.js', __FILE__), [], false, true);
});
