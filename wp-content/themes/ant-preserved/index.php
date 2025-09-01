<?php get_header(); ?>
<main class="wp-loop container">
  <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
    <article <?php post_class('entry'); ?>>
      <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
      <div class="content"><?php the_content(); ?></div>
    </article>
  <?php endwhile; the_posts_pagination(); else: ?>
    <p>Записей не найдено.</p>
  <?php endif; ?>
</main>
<?php get_footer(); ?>
