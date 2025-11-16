<?php
add_shortcode('azizi_slider', function(){
    ob_start();
    include plugin_dir_path(__FILE__)."../templates/slider-template.php";
    return ob_get_clean();
});
