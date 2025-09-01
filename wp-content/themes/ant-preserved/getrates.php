<?php
/*
Template Name: API для получения курса
Template Post Type: page
*/
?>
<?php
    ob_end_clean();
    header('Content-type: application/json');
    echo json_encode(get_rate_new());
?>
