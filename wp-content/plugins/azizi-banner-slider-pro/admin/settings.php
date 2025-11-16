<?php
add_action('admin_menu', function(){
    add_options_page('Azizi Slider Settings','Azizi Slider','manage_options','azizi-slider','azizi_slider_settings');
});

function azizi_slider_settings(){
    if(isset($_POST['save'])){
        foreach(['autoplay','effect','parallax'] as $o){
            update_option("azizi_$o", $_POST[$o]);
        }
    }
    $autoplay=get_option('azizi_autoplay',6000);
    $effect=get_option('azizi_effect','fade-scale');
    $parallax=get_option('azizi_parallax','mouse');
    echo '<h2>Azizi Slider Settings</h2><form method="post">';
    echo "Autoplay (ms): <input name='autoplay' value='$autoplay'><br><br>";
    echo "Effect: <select name='effect'>
            <option value='fade'>Fade</option>
            <option value='fade-scale'>Fade + Scale</option>
            <option value='kenburns'>Ken Burns</option>
            <option value='slide'>Slide</option>
            <option value='parallax'>Parallax Fade</option>
        </select><br><br>";
    echo "Parallax: <select name='parallax'>
            <option value='mouse'>Mouse</option>
            <option value='scroll'>Scroll</option>
            <option value='both'>Both</option>
        </select><br><br>";
    echo "<button class='button-primary' name='save'>Save</button>";
    echo "</form>";
}
