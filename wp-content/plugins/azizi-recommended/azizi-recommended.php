<?php
/**
 * Plugin Name: Azizi — Recommended Products
 * Description: Блок «Рекомендуемые продукты» + слайдер (vanilla). Показывает по два продукта.
 * Version:     1.1.0
 * Author:      Azizi Team
 * Text Domain: azizi-recommended
 */

if (!defined('ABSPATH')) exit;

class AziziRecommended {
    const CPT = 'azp_product';
    const NONCE = 'azp_meta_nonce';

    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('azizi_recommended', [$this, 'shortcode']);
    }

    public function register_cpt() {
        register_post_type(self::CPT, [
            'labels' => [
                'name'          => __('Реком. продукты', 'azizi-recommended'),
                'singular_name' => __('Реком. продукт', 'azizi-recommended'),
                'add_new'       => __('Добавить', 'azizi-recommended'),
                'add_new_item'  => __('Добавить продукт', 'azizi-recommended'),
                'edit_item'     => __('Редактировать продукт', 'azizi-recommended'),
            ],
            'public'      => true,
            'show_ui'     => true,
            'menu_icon'   => 'dashicons-thumbs-up',
            'supports'    => ['title','editor','thumbnail','page-attributes'],
        ]);
    }

    public function add_meta_boxes() {
        add_meta_box('azp_details', __('Настройки карточки', 'azizi-recommended'), [$this, 'render_meta_box'], self::CPT, 'normal', 'high');
    }

    public function render_meta_box($post) {
        wp_nonce_field(self::NONCE, self::NONCE);
        $price     = get_post_meta($post->ID, 'azp_price', true);
        $price_suf = get_post_meta($post->ID, 'azp_price_suffix', true);
        $features  = get_post_meta($post->ID, 'azp_features', true);
        $btn1_lbl  = get_post_meta($post->ID, 'azp_btn1_label', true);
        $btn1_url  = get_post_meta($post->ID, 'azp_btn1_url', true);
        $btn2_lbl  = get_post_meta($post->ID, 'azp_btn2_label', true);
        $btn2_url  = get_post_meta($post->ID, 'azp_btn2_url', true);
        ?>
        <p><label><?php _e('Цена', 'azizi-recommended'); ?><br>
                <input type="text" name="azp_price" value="<?php echo esc_attr($price); ?>"></label></p>
        <p><label><?php _e('Подпись к цене', 'azizi-recommended'); ?><br>
                <input type="text" name="azp_price_suffix" value="<?php echo esc_attr($price_suf); ?>"></label></p>
        <p><label><?php _e('Преимущества (каждое с новой строки)', 'azizi-recommended'); ?><br>
                <textarea name="azp_features" rows="4"><?php echo esc_textarea($features); ?></textarea></label></p>
        <p><label><?php _e('Кнопка 1 — текст', 'azizi-recommended'); ?><br>
                <input type="text" name="azp_btn1_label" value="<?php echo esc_attr($btn1_lbl); ?>"></label></p>
        <p><label><?php _e('Кнопка 1 — ссылка', 'azizi-recommended'); ?><br>
                <input type="url" name="azp_btn1_url" value="<?php echo esc_url($btn1_url); ?>"></label></p>
        <p><label><?php _e('Кнопка 2 — текст', 'azizi-recommended'); ?><br>
                <input type="text" name="azp_btn2_label" value="<?php echo esc_attr($btn2_lbl); ?>"></label></p>
        <p><label><?php _e('Кнопка 2 — ссылка', 'azizi-recommended'); ?><br>
                <input type="url" name="azp_btn2_url" value="<?php echo esc_url($btn2_url); ?>"></label></p>
        <?php
    }

    public function save_meta($post_id) {
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce($_POST[self::NONCE], self::NONCE)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        $fields = ['azp_price','azp_price_suffix','azp_features','azp_btn1_label','azp_btn1_url','azp_btn2_label','azp_btn2_url'];
        foreach ($fields as $f) {
            if (isset($_POST[$f])) update_post_meta($post_id, $f, sanitize_text_field($_POST[$f]));
        }
    }

    public function enqueue_assets() {
        $base = plugin_dir_url(__FILE__) . 'assets/';
        wp_enqueue_style('azp-reco', $base.'reco.css', [], '1.1.0');
        wp_enqueue_script('azp-reco', $base.'reco.js', [], '1.1.0', true);
    }

    public function shortcode($atts) {
        $atts = shortcode_atts(['title'=>__('Рекомендуемые продукты','azizi-recommended'),'limit'=>12], $atts);
        $q = new WP_Query([
            'post_type'      => self::CPT,
            'posts_per_page' => (int)$atts['limit'],
        ]);
        if (!$q->have_posts()) return '';

        ob_start(); ?>
        <section class="azp-section">
            <div class="azp-head">
                <h2 class="azp-title"><?php _e('Рекомендуемые продукты', 'azizi-recommended'); ?></h2>
                <div class="azp-nav">
                    <button class="azp-prev" type="button"></button>
                    <button class="azp-next" type="button"></button>
                </div>
            </div>

            <div class="azp-swiper" data-autoplay="4000000" data-loop="true">
                <div class="azp-wrapper">
                    <?php while ($q->have_posts()): $q->the_post();
                        $id = get_the_ID();
                        $img = get_the_post_thumbnail_url($id,'large');
                        $price = get_post_meta($id,'azp_price',true);
                        $price_suf = get_post_meta($id,'azp_price_suffix',true);
                        $features = array_filter(array_map('trim', explode("\n", get_post_meta($id,'azp_features',true))));
                        $btn1_lbl = get_post_meta($id,'azp_btn1_label',true);
                        $btn1_url = get_post_meta($id,'azp_btn1_url',true);
                        $btn2_lbl = get_post_meta($id,'azp_btn2_label',true);
                        $btn2_url = get_post_meta($id,'azp_btn2_url',true);
                        ?>
                        <div class="azp-slide">
                            <article class="azp-card">
                                <?php if ($img): ?>
                                    <img src="<?php echo esc_url($img); ?>" alt="" class="azp-card__image">
                                <?php endif; ?>
                                <div class="azp-card__header">
                                    <h4 class="azp-card__title"><?php the_title(); ?></h4>
                                </div>
                                <?php if($features): ?>
                                    <ul class="azp-card__features">
                                        <?php foreach($features as $f): ?>
                                            <li><?php echo esc_html($f); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                                <div class="azp-card__footer">
                                    <?php if($price): ?>
                                        <div class="azp-card__price">
                                            <span class="azp-card__price-value"><?php echo esc_html($price); ?></span>
                                            <span class="azp-card__price-suf"><?php echo esc_html($price_suf); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="azp-card__actions">
                                        <?php if($btn1_lbl): ?>
                                            <?php echo do_shortcode($btn1_lbl); ?>
<!--                                            <a href="--><?php //echo esc_url($btn1_url); ?><!--" class="azp-btn azp-btn--primary">--><?php //echo esc_html($btn1_lbl); ?><!--</a>-->
                                        <?php endif; ?>
                                        <?php if($btn2_lbl && $btn2_url): ?>
                                            <a href="<?php echo esc_url($btn2_url); ?>" class="azp-btn azp-btn--ghost"><?php echo esc_html($btn2_lbl); ?></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }
}

add_action('plugins_loaded', function() {
    load_plugin_textdomain('azizi-recommended', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});

new AziziRecommended();
