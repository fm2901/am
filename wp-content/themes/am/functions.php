<?php
if ( ! defined('AM_EXACT_CLASSIC_VER') ) define('AM_EXACT_CLASSIC_VER', '1.0.0');

/**
 * Находим первый файл по шаблону в /assets темы.
 * Возвращает [abs_path, url, basename] или false.
 */
function am_exact_asset($pattern){
  $dir = trailingslashit( get_template_directory() ) . 'assets/';
  $url = trailingslashit( get_template_directory_uri() ) . 'assets/';
  $files = glob($dir . $pattern);
  if ( ! $files ) return false;
  // Берём самый "короткий" (избегаем имён с пробелами/кириллицей)
  usort($files, function($a,$b){ return strlen($a) <=> strlen($b); });
  $file = basename($files[0]);
  return array($dir.$file, $url.$file, $file);
}

add_action('wp_enqueue_scripts', function(){
  // Базовый файл темы (ничего не ломает), ваш основной CSS и fix.css
  wp_enqueue_style('am-base', get_stylesheet_uri(), array(), AM_EXACT_CLASSIC_VER);

  $css = am_exact_asset('index-*.css');
  if ( $css ) {
    wp_enqueue_style('am-bundle', $css[1], array('am-base'), AM_EXACT_CLASSIC_VER);
  }

  if ( file_exists( get_template_directory() . '/assets/fix.css' ) ) {
    wp_enqueue_style('am-fix', get_template_directory_uri().'/assets/fix.css', array('am-bundle'), AM_EXACT_CLASSIC_VER);
  }

  // JS модулем — ищем и index-*.js, и "нестандартные" имена (например, .js.Без названия)
  $js = am_exact_asset('index-*.js*');
  if ( $js ) {
    wp_enqueue_script('am-bundle', $js[1], array(), AM_EXACT_CLASSIC_VER, true);
  }
});

// Добавляем type="module" нашему бандлу
add_filter('script_loader_tag', function($tag, $handle, $src){
  if ( $handle === 'am-bundle' ) {
    return '<script type="module" src="'.esc_url($src).'"></script>';
  }
  return $tag;
}, 10, 3);
