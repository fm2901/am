<?php
/*
Template Name: Депозиты
Template Post Type: page
*/
?>
<?php get_header(); ?>
    <h1 class="page-title"><?php the_title(); ?></h1>
    <?php echo do_shortcode('[deposit_products]');?>
    <?php echo do_shortcode('[azizi_deposit_calculator]');?>
<?php get_footer(); ?>
