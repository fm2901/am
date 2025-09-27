<?php
/*
Plugin Name: Credit Calculator
Description: Кредитный калькулятор с мультиязычностью, заявками и настройками.
Version: 1.3
Author: Ваше Имя
Text Domain: credit-calculator
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

add_action('wp_enqueue_scripts', function(){
    wp_enqueue_style('credit-calculator-style', plugins_url('/assets/style.css', __FILE__));
    wp_enqueue_script('credit-calculator-script', plugins_url('/assets/script.js', __FILE__), ['jquery'], false, true);
    wp_localize_script('credit-calculator-script', 'cc_ajax', [
        'url'      => admin_url('admin-ajax.php'),
        'i18n'     => [
            'from'     => __('от', 'credit-calculator'),
            'to'       => __('до', 'credit-calculator'),
            'months'   => __('мес.', 'credit-calculator'),
            'success'  => __('Заявка успешно отправлена!', 'credit-calculator'),
            'error'    => __('Ошибка при отправке заявки. Попробуйте позже.', 'credit-calculator'),
        ]
    ]);

});

add_action('plugins_loaded', function() {
    load_plugin_textdomain('credit-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});

// Подключаем части
require_once plugin_dir_path(__FILE__) . 'includes/products-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

// Шорткод
//add_shortcode('credit_calculator', 'credit_calculator_shortcode');
add_shortcode('credit_calculator', function(){

    ob_start(); ?>

    <section class="cc-calculator-box">
        <div class="cc-inner">

            <!-- Левая часть -->
            <div class="cc-left">
                <h2 class="cc-title white"><?php _e("Кредитный калькулятор", "credit-calculator"); ?></h2>

                <!-- Цель кредита -->
                <div class="cc-form-group">
                    <label class="white"><?php _e("Цель кредита", "credit-calculator"); ?></label>
                    <select id="cc-product" class="cc-input">
                        <?php
                        $products = get_posts([
                            'post_type'=>'credit_product',
                            'numberposts'=>-1,
                            'lang'=>function_exists('pll_current_language') ? pll_current_language() : ''
                        ]);
                        foreach($products as $prod):
                            $rate     = get_post_meta($prod->ID, 'cc_rate', true);
                            $min      = get_post_meta($prod->ID, 'cc_min', true);
                            $max      = get_post_meta($prod->ID, 'cc_max', true);
                            $term_min = get_post_meta($prod->ID, 'cc_term_min', true);
                            $term_max = get_post_meta($prod->ID, 'cc_term_max', true);
                            echo '<option 
                      value="'.$prod->ID.'"
                      data-rate="'.$rate.'"
                      data-min="'.$min.'"
                      data-max="'.$max.'"
                      data-term-min="'.$term_min.'"
                      data-term-max="'.$term_max.'">'
                                .esc_html($prod->post_title).' - '.$rate.'%</option>';
                        endforeach;
                        ?>
                    </select>
                </div>

                <!-- Сумма и срок -->
                <div class="cc-form-row">

                    <!-- Сумма кредита -->
                    <div class="cc-form-group">
                        <label class="white"><?php _e("Сумма кредита", "credit-calculator"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="number" id="cc-amount" class="cc-input">
                            <span class="cc-suffix">TJS</span>
                        </div>
                        <input type="range" id="cc-amount-range" class="cc-range" step="500">
                        <div class="cc-hints white">
                            <span id="cc-amount-min"><?php _e("от", "credit-calculator"); ?> 0</span>
                            <span id="cc-amount-max"><?php _e("до", "credit-calculator"); ?> 0</span>
                        </div>
                    </div>

                    <!-- Срок кредита -->
                    <div class="cc-form-group">
                        <label class="white"><?php _e("Срок кредита", "credit-calculator"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="text" id="cc-term-value" class="cc-input" readonly>
                            <span class="cc-suffix"><?php _e("мес.", "credit-calculator"); ?></span>
                        </div>
                        <input type="range" id="cc-term" class="cc-range">
                        <div class="cc-hints white">
                            <span id="cc-term-min">от 0 мес.</span>
                            <span id="cc-term-max">до 0 мес.</span>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Правая часть -->
            <div class="cc-right">
                <p class="cc-subtitle"><?php _e("Вероятность одобрения кредита", "credit-calculator"); ?></p>
                <p class="cc-approve">5% <span class="cc-note">+50% <?php _e("за заполнение заявки", "credit-calculator"); ?></span></p>

                <p class="cc-subtitle"><?php _e("Ежемесячный платёж", "credit-calculator"); ?></p>
                <p class="cc-monthly"><span id="monthly">0</span> TJS</p>

                <button type="button" id="open-modal" class="cc-btn">
                    <?php _e("Оформить кредит", "credit-calculator"); ?>
                </button>
            </div>

        </div>
    </section>

    <!-- Модалка -->
    <div id="calc-modal" class="cc-modal">
        <div class="cc-modal-content">
            <span class="cc-modal-close">&times;</span>
            <h3><?php _e("Оставьте заявку", "credit-calculator"); ?></h3>
            <form id="lead-form">
                <!-- Продукт -->
                <div class="cc-form-group">
                    <label><?php _e("Выберите продукт", "credit-calculator"); ?></label>
                    <select name="product" id="cc-lead-product" class="cc-input">
                        <?php
                        $products = get_posts([
                            'post_type'=>'credit_product',
                            'numberposts'=>-1,
                            'lang'=>function_exists('pll_current_language') ? pll_current_language() : ''
                        ]);
                        foreach($products as $prod):
                            $rate = get_post_meta($prod->ID, 'cc_rate', true);
                            echo '<option value="'.$prod->ID.'">'.esc_html($prod->post_title).' - '.$rate.'%</option>';
                        endforeach;
                        ?>
                    </select>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Желаемая сумма кредита", "credit-calculator"); ?></label>
                    <div class="cc-input-wrap">
                        <input type="number" name="desired_amount" id="cc-lead-amount" class="cc-input" required>
                        <span class="cc-suffix">TJS</span>
                    </div>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Желаемый срок кредита", "credit-calculator"); ?></label>
                    <div class="cc-input-wrap">
                        <input type="number" name="desired_term" id="cc-lead-term" class="cc-input" required>
                        <span class="cc-suffix"><?php _e("мес.", "credit-calculator"); ?></span>
                    </div>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Ваше имя", "credit-calculator"); ?></label>
                    <input type="text" name="name" required class="cc-input" placeholder="Иван Иванов">
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Телефон", "credit-calculator"); ?></label>
                    <input type="text" name="phone" required class="cc-input" placeholder="+992 (__) ___-__-__">
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Город", "credit-calculator"); ?></label>
                    <input type="text" name="city" class="cc-input" placeholder="Душанбе">
                </div>
                <input type="hidden" name="lead_type" value="credit">
                <button type="submit" class="cc-btn"><?php _e("Отправить", "credit-calculator"); ?></button>
            </form>
        </div>
    </div>


    <?php
    return ob_get_clean();
});

// Шорткод списка/одного продукта
add_shortcode('credit_products', function($atts){
    $atts = shortcode_atts([
        'id' => '' // если указан ID, показываем один продукт
    ], $atts);

    // Получаем список продуктов
    $args = [
        'post_type'      => 'credit_product',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'lang'           => function_exists('pll_current_language') ? pll_current_language() : ''
    ];

    if (!empty($atts['id'])) {
        $args['p'] = intval($atts['id']); // конкретный продукт
    } else {
        $args['numberposts'] = -1; // все продукты
    }

    $products = get_posts($args);

    if (!$products) {
        return '<p>'.__('Продукты не найдены', 'credit-calculator').'</p>';
    }

    ob_start();
    echo '<div class="cc-products-grid">';
    foreach ($products as $prod):
        $rate     = get_post_meta($prod->ID, 'cc_rate', true);
        $min      = get_post_meta($prod->ID, 'cc_min', true);
        $max      = get_post_meta($prod->ID, 'cc_max', true);
        $term_min = get_post_meta($prod->ID, 'cc_term_min', true);
        $term_max = get_post_meta($prod->ID, 'cc_term_max', true);

        $thumb = get_the_post_thumbnail($prod->ID, 'medium', ['class'=>'cc-product-thumb']);
        $desc  = wp_trim_words($prod->post_content, 25);

        echo '<div class="cc-product-card">';
        echo $thumb ? $thumb : '<div class="cc-product-thumb placeholder"></div>';
        echo '<h3 class="cc-product-title">'.esc_html($prod->post_title).'</h3>';
        echo '<div class="cc-product-desc">'.$desc.'</div>';
        echo '<ul class="cc-product-meta">';
        echo '<li>'.__('Ставка', 'credit-calculator').': <b>'.$rate.'%</b></li>';
        echo '<li>'.__('Сумма', 'credit-calculator').': '.$min.' - '.$max.' TJS</li>';
        echo '<li>'.__('Срок', 'credit-calculator').': '.$term_min.' - '.$term_max.' мес.</li>';
        echo '</ul>';
        echo '<a href="'.get_permalink($prod->ID).'" class="cc-btn">'.__('Подробнее', 'credit-calculator').'</a>';
        echo '</div>';
    endforeach;
    echo '</div>';

    return ob_get_clean();
});

add_action('init', function() {
    $labels = [
        'name'               => __('Кредитные продукты', 'credit-calculator'),
        'singular_name'      => __('Кредитный продукт', 'credit-calculator'),
        'add_new'            => __('Добавить продукт', 'credit-calculator'),
        'add_new_item'       => __('Добавить новый продукт', 'credit-calculator'),
        'edit_item'          => __('Редактировать продукт', 'credit-calculator'),
        'new_item'           => __('Новый продукт', 'credit-calculator'),
        'all_items'          => __('Все продукты', 'credit-calculator'),
        'view_item'          => __('Просмотр продукта', 'credit-calculator'),
        'search_items'       => __('Поиск продукта', 'credit-calculator'),
        'not_found'          => __('Продукты не найдены', 'credit-calculator'),
        'not_found_in_trash' => __('В корзине продуктов нет', 'credit-calculator'),
        'menu_name'          => __('Кредитные продукты', 'credit-calculator')
    ];

    register_post_type('credit_product', [
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-bank',
        'supports' => ['title', 'editor', 'thumbnail'], // ✅ картинка + описание
        'has_archive' => true,
        'rewrite' => ['slug' => 'credit-products'],
        'show_in_rest' => true,
    ]);
});


register_activation_hook(__FILE__, 'cc_install_table');
function cc_install_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cc_leads';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        city VARCHAR(100),
        card_name varchar(255) NOT NULL,
        product BIGINT(20) UNSIGNED,
        amount INT,
        term INT,
        lead_type VARCHAR(50) DEFAULT 'credit',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


