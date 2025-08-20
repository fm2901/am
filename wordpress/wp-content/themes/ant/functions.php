<?php
if ( ! defined('AM_EXACT_CLASSIC_VER') ) {
    define('AM_EXACT_CLASSIC_VER', '1.1.0');
}

/**
 * Ищем первый файл по шаблону в /assets темы.
 * Возвращает массив ['abs' => ..., 'url' => ..., 'name' => ...] или false.
 */
function am_exact_asset( $pattern ) {
    $dir = trailingslashit( get_template_directory() ) . 'assets/';
    $url = trailingslashit( get_template_directory_uri() ) . 'assets/';
    $files = glob( $dir . $pattern );
    if ( ! $files ) return false;
    // Берём самый «короткий» (избегаем имён с пробелами/кириллицей/«Без названия»)
    usort( $files, function( $a, $b ) { return strlen( $a ) <=> strlen( $b ); } );
    $file = basename( $files[0] );
    return [
        'abs'  => $dir . $file,
        'url'  => $url . $file,
        'name' => $file,
    ];
}

/** Версия по mtime файла (для кэш-бастинга) */
function am_file_ver( $abs, $fallback = AM_EXACT_CLASSIC_VER ) {
    return ( $abs && file_exists( $abs ) ) ? filemtime( $abs ) : $fallback;
}

add_action( 'wp_enqueue_scripts', function () {
    // Базовый стиль темы (из style.css)
    $style_abs = get_stylesheet_directory() . '/style.css';
    wp_enqueue_style( 'am-base', get_stylesheet_uri(), [], am_file_ver( $style_abs ) );

    // Основной собранный CSS: index-*.css (если есть)
    $bundle_css = am_exact_asset( 'index-*.css' );
    if ( $bundle_css ) {
        wp_enqueue_style(
            'am-bundle',
            $bundle_css['url'],
            [ 'am-base' ],
            am_file_ver( $bundle_css['abs'] )
        );
    }

    // Дополнительные правки: fix.css (если есть)
    $fix_abs = get_template_directory() . '/assets/fix.css';
    if ( file_exists( $fix_abs ) ) {
        $deps = [ 'am-base' ];
        if ( $bundle_css ) { $deps[] = 'am-bundle'; }
        wp_enqueue_style(
            'am-fix',
            get_template_directory_uri() . '/assets/fix.css',
            $deps,
            am_file_ver( $fix_abs )
        );
    }


    // --- JS ---
    // 1) В приоритете кастомная логика без модулей: assets/app.js
    $app_abs = get_template_directory() . '/assets/app.js';
    if ( file_exists( $app_abs ) ) {
        wp_enqueue_script(
            'am-app',
            get_template_directory_uri() . '/assets/app.js',
            [],                          // без зависимостей
            am_file_ver( $app_abs ),
            true                         // в футере
        );
        return; // если app.js есть — дальше не подключаем bundle
    }

    // 2) Иначе — Vite-бандл как ES Module: index-*.js*
    $bundle_js = am_exact_asset( 'index-*.js*' );
    if ( $bundle_js ) {
        wp_enqueue_script(
            'am-bundle',
            $bundle_js['url'],
            [],
            am_file_ver( $bundle_js['abs'] ),
            true
        );
    }
} );

// Проставляем type="module" для Vite-бандла
add_filter( 'script_loader_tag', function ( $tag, $handle, $src ) {
    if ( $handle === 'am-bundle' ) {
        return '<script type="module" src="' . esc_url( $src ) . '"></script>';
    }
    return $tag;
}, 10, 3 );
