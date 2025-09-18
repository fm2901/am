<?php
/*
Template Name: Документы
Template Post Type: page
*/
?>
<?php get_header(); ?>
    <h1><?php the_title(); ?></h1>
    <?php echo do_shortcode('[documents_list]');?>
<?php get_footer(); ?>
