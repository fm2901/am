<?php
/**
 * Plugin Name: Azizi Slider (Vanilla)
 * Description: Слайдер баннеров с кастомным JS (vanilla) + мультиязычность. Фон задаётся через Featured Image.
 * Version:     1.4.0
 * Author:      Azizi Team
 * Text Domain: azizi-slider
 */

if (!defined('ABSPATH')) exit;

class Azizi_Slider {
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('azizi_slider', [$this, 'render']);
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // ✅ Включаем поддержку миниатюр для слайдов
        add_action('after_setup_theme', function () {
            add_theme_support('post-thumbnails', ['azizi_slide']);
        });
    }

    /** Загрузка переводов */
    public function load_textdomain() {
        load_plugin_textdomain('azizi-slider', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    /** Регистрируем CPT */
    public function register_cpt() {
        register_post_type('azizi_slide', [
            'labels' => [
                'name'          => __('Слайды', 'azizi-slider'),
                'singular_name' => __('Слайд', 'azizi-slider'),
                'add_new'       => __('Добавить слайд', 'azizi-slider'),
                'add_new_item'  => __('Добавить новый слайд', 'azizi-slider'),
                'edit_item'     => __('Редактировать слайд', 'azizi-slider'),
                'view_item'     => __('Просмотреть слайд', 'azizi-slider'),
            ],
            'public'       => true,
            'show_ui'      => true,
            'show_in_menu' => true,
            'supports'     => ['title','editor','thumbnail','page-attributes'],
            'menu_icon'    => 'dashicons-images-alt2',
            'show_in_rest' => true,
        ]);
    }

    /** Метабоксы */
    public function add_meta_boxes() {
        add_meta_box(
            'azizi_slide_meta',
            __('Параметры слайда', 'azizi-slider'),
            [$this, 'render_meta'],
            'azizi_slide',
            'normal',
            'high'
        );
    }

    /** Поля метабокса */
    public function render_meta($post) {
        $pretitle = get_post_meta($post->ID, '_azizi_slide_pretitle', true);
        $subtitle = get_post_meta($post->ID, '_azizi_slide_subtitle', true);
        $link     = get_post_meta($post->ID, '_azizi_slide_link', true);

        wp_nonce_field('azizi_slide_meta', 'azizi_slide_meta_nonce');

        echo '<p><label>'.__('Предзаголовок', 'azizi-slider').'</label><br>';
        echo '<input type="text" name="azizi_slide_pretitle" value="'.esc_attr($pretitle).'" class="widefat"></p>';

        echo '<p><label>'.__('Подзаголовок', 'azizi-slider').'</label><br>';
        echo '<input type="text" name="azizi_slide_subtitle" value="'.esc_attr($subtitle).'" class="widefat"></p>';

        echo '<p><label>'.__('Ссылка', 'azizi-slider').'</label><br>';
        echo '<input type="url" name="azizi_slide_link" value="'.esc_attr($link).'" class="widefat"></p>';

        echo '<p><em>'.__('Фон слайда задаётся через «Изображение записи» (Featured Image).', 'azizi-slider').'</em></p>';
    }

    /** Сохранение мета */
    public function save_meta($post_id) {
        if (!isset($_POST['azizi_slide_meta_nonce']) || !wp_verify_nonce($_POST['azizi_slide_meta_nonce'], 'azizi_slide_meta')) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

        if (isset($_POST['azizi_slide_pretitle'])) {
            update_post_meta($post_id, '_azizi_slide_pretitle', sanitize_text_field($_POST['azizi_slide_pretitle']));
        }
        if (isset($_POST['azizi_slide_subtitle'])) {
            update_post_meta($post_id, '_azizi_slide_subtitle', sanitize_text_field($_POST['azizi_slide_subtitle']));
        }
        if (isset($_POST['azizi_slide_link'])) {
            update_post_meta($post_id, '_azizi_slide_link', esc_url_raw($_POST['azizi_slide_link']));
        }
    }

    /** Подключаем CSS/JS */
    public function enqueue_assets() {
        $base = plugin_dir_url(__FILE__) . 'assets/';
        wp_enqueue_style('azizi-slider', $base . 'css/slider.css', [], '1.4.0');
        wp_enqueue_script('azizi-slider', $base . 'js/slider.js', [], '1.4.0', true);
    }

    /** Шорткод [azizi_slider] */
    public function render() {
        $args = [
            'post_type'      => 'azizi_slide',
            'posts_per_page' => -1,
            'post_status'    => 'publish'
        ];
        $query = new WP_Query($args);

        if (!$query->have_posts()) return '';

        ob_start(); ?>
        <section class="sl-section">
            <div class="swiper" data-autoplay="3000" data-loop="true" data-effect="fade">
                <div class="swiper-wrapper">
                    <?php while ($query->have_posts()): $query->the_post();
                        $pretitle = get_post_meta(get_the_ID(), '_azizi_slide_pretitle', true);
                        $subtitle = get_post_meta(get_the_ID(), '_azizi_slide_subtitle', true);
                        $link     = get_post_meta(get_the_ID(), '_azizi_slide_link', true);
                        $bg       = get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>
                        <div class="swiper-slide">
                            <article class="sl-banner" style="background-image:url('<?php echo esc_url($bg); ?>');">
                                <div class="sl-content">
                                    <?php if ($pretitle): ?>
                                        <p class="sl-pretitle"><?php echo esc_html($pretitle); ?></p>
                                    <?php endif; ?>
                                    <h3 class="sl-title"><?php the_title(); ?></h3>
                                    <?php if ($subtitle): ?>
                                        <p class="sl-subtitle"><?php echo esc_html($subtitle); ?></p>
                                    <?php endif; ?>
                                </div>
                                <?php if ($link): ?>
                                    <a class="sl-button banner-link" href="<?php echo esc_url($link); ?>">
                                        <?php _e('Муфассал', 'azizi-slider'); ?>
                                    </a>
                                <?php endif; ?>
                            </article>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

new Azizi_Slider();
