<?php
/*
Template Name: Кредиты
Template Post Type: page
*/
?>
<?php get_header(); ?>
    <h1 class="page-title"><?php the_title(); ?></h1>
    <?php echo do_shortcode('[credit_products_list]');?>
    <?php echo do_shortcode('[credit_calculator]');?>
<?php get_footer(); ?>
