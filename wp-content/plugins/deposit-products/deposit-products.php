<?php
/**
 * Plugin Name: Deposit Products
 * Plugin URI: https://example.com
 * Description: Мультиязычный плагин для управления депозитными продуктами
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: deposit-products
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

class Deposit_Products {

    public function __construct() {
        add_action('init', [$this, 'register_post_type']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_deposit', [$this, 'save_meta_boxes']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_shortcode('deposit_products', [$this, 'shortcode_display']);
        add_filter('single_template', [$this, 'load_single_template']);

        // Загрузка текстов
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Интеграция с Polylang
        add_filter('pll_get_post_types', [$this, 'add_polylang_support']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('deposit-products', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function register_post_type() {
        $labels = [
            'name'               => __('Депозиты', 'deposit-products'),
            'singular_name'      => __('Депозит', 'deposit-products'),
            'menu_name'          => __('Депозиты', 'deposit-products'),
            'add_new'            => __('Добавить новый', 'deposit-products'),
            'add_new_item'       => __('Добавить новый депозит', 'deposit-products'),
            'edit_item'          => __('Редактировать депозит', 'deposit-products'),
            'new_item'           => __('Новый депозит', 'deposit-products'),
            'view_item'          => __('Просмотр депозита', 'deposit-products'),
            'search_items'       => __('Искать депозит', 'deposit-products'),
            'not_found'          => __('Депозитов не найдено', 'deposit-products'),
            'not_found_in_trash' => __('В корзине депозитов не найдено', 'deposit-products'),
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => ['slug' => 'deposits'],
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'menu_position'       => 5,
            'menu_icon'           => 'dashicons-money-alt',
            'supports'            => ['title', 'thumbnail'],
        ];

        register_post_type('deposit', $args);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'deposit_details',
            __('Параметры депозита', 'deposit-products'),
            [$this, 'render_meta_box'],
            'deposit',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('deposit_meta_box', 'deposit_meta_box_nonce');

        $rate_somoni = get_post_meta($post->ID, '_deposit_rate_somoni', true);
        $rate_dollar = get_post_meta($post->ID, '_deposit_rate_dollar', true);
        $period = get_post_meta($post->ID, '_deposit_period', true);
        $min_amount = get_post_meta($post->ID, '_deposit_min_amount', true);
        $currency = get_post_meta($post->ID, '_deposit_currency', true) ?: 'somoni';
        $form_url = get_post_meta($post->ID, '_deposit_form_url', true);
        ?>
        <style>
            .deposit-meta-field {
                margin-bottom: 15px;
            }
            .deposit-meta-field label {
                display: inline-block;
                width: 200px;
                font-weight: bold;
            }
            .deposit-meta-field input[type="text"],
            .deposit-meta-field input[type="number"] {
                width: 300px;
            }
        </style>

        <div class="deposit-meta-field">
            <label><?php _e('Процент в сомони (до X%)', 'deposit-products'); ?></label>
            <input type="number" step="0.01" name="deposit_rate_somoni" value="<?php echo esc_attr($rate_somoni); ?>" />
        </div>

        <div class="deposit-meta-field">
            <label><?php _e('Процент в долларах (до X%)', 'deposit-products'); ?></label>
            <input type="number" step="0.01" name="deposit_rate_dollar" value="<?php echo esc_attr($rate_dollar); ?>" />
        </div>

        <div class="deposit-meta-field">
            <label><?php _e('Срок (например: 1 год, 6 месяцев)', 'deposit-products'); ?></label>
            <input type="text" name="deposit_period" value="<?php echo esc_attr($period); ?>" />
        </div>

        <div class="deposit-meta-field">
            <label><?php _e('Минимальная сумма (число)', 'deposit-products'); ?></label>
            <input type="number" step="0.01" name="deposit_min_amount" value="<?php echo esc_attr($min_amount); ?>" />
        </div>

        <div class="deposit-meta-field">
            <label><?php _e('Валюта минимальной суммы', 'deposit-products'); ?></label>
            <select name="deposit_currency">
                <option value="somoni" <?php selected($currency, 'somoni'); ?>><?php _e('Сомони', 'deposit-products'); ?></option>
                <option value="dollar" <?php selected($currency, 'dollar'); ?>><?php _e('Долларов', 'deposit-products'); ?></option>
            </select>
        </div>

        <div class="deposit-meta-field">
            <label><?php _e('URL формы заявки (необязательно)', 'deposit-products'); ?></label>
            <input type="text" name="deposit_form_url" value="<?php echo esc_attr($form_url); ?>" placeholder="#deposit-form" style="width: 400px;" />
            <p class="description"><?php _e('Оставьте пустым для использования #deposit-form по умолчанию', 'deposit-products'); ?></p>
        </div>
        <?php
    }

    public function save_meta_boxes($post_id) {
        if (!isset($_POST['deposit_meta_box_nonce']) || !wp_verify_nonce($_POST['deposit_meta_box_nonce'], 'deposit_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = ['deposit_rate_somoni', 'deposit_rate_dollar', 'deposit_period', 'deposit_min_amount', 'deposit_currency', 'deposit_form_url'];

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }

    public function enqueue_scripts() {
        wp_enqueue_style('deposit-products', plugin_dir_url(__FILE__) . 'assets/css/deposit-products.css', [], '1.0.0');
    }

    public function shortcode_display($atts) {
        $atts = shortcode_atts([
            'limit' => -1,
        ], $atts);

        $args = [
            'post_type' => 'deposit',
            'posts_per_page' => $atts['limit'],
            'post_status' => 'publish',
        ];

        $query = new WP_Query($args);

        if (!$query->have_posts()) {
            return '';
        }

        ob_start();
        ?>
        <div class="dp-deposit-products">
            <?php while ($query->have_posts()) : $query->the_post();
                $rate_somoni = get_post_meta(get_the_ID(), '_deposit_rate_somoni', true);
                $rate_dollar = get_post_meta(get_the_ID(), '_deposit_rate_dollar', true);
                $period = get_post_meta(get_the_ID(), '_deposit_period', true);
                $min_amount = get_post_meta(get_the_ID(), '_deposit_min_amount', true);
                $currency = get_post_meta(get_the_ID(), '_deposit_currency', true) ?: 'somoni';

                $currency_label = $currency === 'somoni' ? __('сом.', 'deposit-products') : __('$', 'deposit-products');
            ?>
            <div class="deposit-card-horizontal">

                <!-- IMAGE -->
                <div class="deposit-card-horizontal__image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php endif; ?>
                </div>

                <!-- CONTENT -->
                <div class="deposit-card-horizontal__content">

                    <h3 class="deposit-card-horizontal__title">
                        «<?php the_title(); ?>»
                    </h3>

                    <div class="deposit-card-horizontal__params">

                        <?php if ($rate_somoni) : ?>
                        <div class="param">
                            <span><?php _e('Доход в сомони', 'deposit-products'); ?></span>
                            <strong><?php _e('до', 'deposit-products'); ?> <?php echo esc_html($rate_somoni); ?>%</strong>
                        </div>
                        <?php endif; ?>

                        <?php if ($rate_dollar) : ?>
                        <div class="param">
                            <span><?php _e('Доход в долларах', 'deposit-products'); ?></span>
                            <strong><?php _e('до', 'deposit-products'); ?> <?php echo esc_html($rate_dollar); ?>%</strong>
                        </div>
                        <?php endif; ?>

                        <?php if ($period) : ?>
                        <div class="param">
                            <span><?php _e('Срок', 'deposit-products'); ?></span>
                            <strong><?php echo esc_html($period); ?></strong>
                        </div>
                        <?php endif; ?>

                        <?php if ($min_amount) : ?>
                        <div class="param">
                            <span><?php _e('Сумма', 'deposit-products'); ?></span>
                            <strong><?php _e('от', 'deposit-products'); ?> <?php echo esc_html($min_amount); ?> <?php echo esc_html($currency_label); ?></strong>
                        </div>
                        <?php endif; ?>

                    </div>

                    <div class="deposit-card-horizontal__actions">
                        <button class="btn-primary open-deposit-modal">
                            <?php _e('Открыть вклад', 'deposit-products'); ?>
                        </button>

                        <a href="<?php the_permalink(); ?>" class="deposit-card-horizontal__more">
                            <?php _e('Подробнее', 'deposit-products'); ?>
                        </a>
                    </div>

                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    public function load_single_template($template) {
        if (is_singular('deposit')) {
            $plugin_template = plugin_dir_path(__FILE__) . 'templates/single-deposit.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }
        return $template;
    }

    /**
     * Добавляем поддержку Polylang для типа записи deposit
     */
    public function add_polylang_support($post_types) {
        $post_types['deposit'] = 'deposit';
        return $post_types;
    }
}

new Deposit_Products();
