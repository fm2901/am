<?php
/*
Template Name: Депозиты
Template Post Type: page
*/
?>
<?php get_header(); ?>
    <?php echo do_shortcode('[azizi_deposit_calculator]');?>
    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article <?php post_class('entry'); ?>>
      <h1><?php the_title(); ?></h1>
      <div class="content"><?php the_content(); ?></div>
    </article>
    <?php endwhile; endif; ?>
<?php get_footer(); ?>
