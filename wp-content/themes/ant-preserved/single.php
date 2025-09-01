<?php get_header(); ?>
<main class="container">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article <?php post_class('entry'); ?>>
      <h1><?php the_title(); ?></h1>
      <div class="content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; comments_template(); endif; ?>
</main>
<?php get_footer(); ?>
