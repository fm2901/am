<?php
/**
 * Plugin Name: Azizi — News CPT
 * Description: Кастомный тип записи «Новости» с рубриками, тегами, миниатюрами и выводом блока через шорткод.
 * Version:     1.1.0
 * Author:      Your Team
 * Text Domain: azizi-news
 */

if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', function() {
    load_plugin_textdomain(
        'azizi-news',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
});


class Azizi_News_CPT {
    const CPT   = 'azp_news';
    const TAX_C = 'azp_news_cat';
    const TAX_T = 'azp_news_tag';

    public function __construct() {
        add_action('init', [$this, 'register_cpt_and_tax']);
        add_action('after_setup_theme', [$this, 'enable_thumbnails'], 11);

        // Регистрируем шорткод
        add_shortcode('azizi_news', [$this, 'render_news_shortcode']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    /**
     * Включаем миниатюры для новостей
     */
    public function enable_thumbnails() {
        add_theme_support('post-thumbnails', [self::CPT]);
        add_post_type_support(self::CPT, 'thumbnail');
    }

    public function enqueue_assets() {
        $base = plugin_dir_url(__FILE__) . 'assets/';
        wp_enqueue_style('azp-news', $base.'css/news.css', [], '1.1.0');
    }

    /**
     * Регистрируем CPT и таксономии
     */
    public function register_cpt_and_tax() {
        // === CPT: Новости ===
        register_post_type(self::CPT, [
            'labels' => [
                'name'               => __('Новости', 'azizi-news'),
                'singular_name'      => __('Новость', 'azizi-news'),
                'add_new'            => __('Добавить новость', 'azizi-news'),
                'add_new_item'       => __('Добавить новость', 'azizi-news'),
                'edit_item'          => __('Редактировать новость', 'azizi-news'),
                'new_item'           => __('Новая новость', 'azizi-news'),
                'view_item'          => __('Просмотреть новость', 'azizi-news'),
                'search_items'       => __('Искать новости', 'azizi-news'),
                'not_found'          => __('Новостей не найдено', 'azizi-news'),
                'not_found_in_trash' => __('В корзине новостей нет', 'azizi-news'),
                'all_items'          => __('Все новости', 'azizi-news'),
                'menu_name'          => __('Новости', 'azizi-news'),
            ],
            'public'          => true,
            'show_ui'         => true,
            'show_in_menu'    => true,
            'menu_position'   => 21,
            'menu_icon'       => 'dashicons-megaphone',
            'supports'        => ['title','editor','excerpt','thumbnail','author','revisions','page-attributes'],
            'has_archive'     => true,
            'rewrite'         => ['slug' => 'news', 'with_front' => false],
            'show_in_rest'    => true, // Gutenberg + REST API
            'capability_type' => 'post',
            'map_meta_cap'    => true,
        ]);

        // === Таксономия: Рубрики (иерархическая) ===
        register_taxonomy(self::TAX_C, [self::CPT], [
            'labels' => [
                'name'          => __('Рубрики новостей', 'azizi-news'),
                'singular_name' => __('Рубрика новости', 'azizi-news'),
                'menu_name'     => __('Рубрики', 'azizi-news'),
            ],
            'public'            => true,
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'news-category'],
        ]);

        // === Таксономия: Теги (плоская) ===
        register_taxonomy(self::TAX_T, [self::CPT], [
            'labels' => [
                'name'          => __('Теги новостей', 'azizi-news'),
                'singular_name' => __('Тег новости', 'azizi-news'),
                'menu_name'     => __('Теги', 'azizi-news'),
            ],
            'public'            => true,
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => ['slug' => 'news-tag'],
        ]);
    }

    /**
     * Рендер шорткода [azizi_news]
     */
    public function render_news_shortcode($atts) {
        ob_start();
        include plugin_dir_path(__FILE__) . 'templates/news-section.php';
        return ob_get_clean();
    }
}

new Azizi_News_CPT();
