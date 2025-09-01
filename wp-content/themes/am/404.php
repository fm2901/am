
<?php get_header(); ?>
<div style="max-width:820px;margin:60px auto;padding:0 20px;text-align:center">
  <h1>404</h1>
  <p><?php _e('Страница не найдена.','am-exact-classic'); ?></p>
  <p><a href="<?php echo esc_url( home_url('/') ); ?>" style="text-decoration:underline"><?php _e('На главную','am-exact-classic'); ?></a></p>
</div>
<?php get_footer(); ?>
