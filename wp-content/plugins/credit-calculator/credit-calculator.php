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
    wp_localize_script('credit-calculator-script', 'cc_ajax', ['url' => admin_url('admin-ajax.php')]);
});

add_action('plugins_loaded', function() {
    load_plugin_textdomain('credit-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages/');
});

// Подключаем части
require_once plugin_dir_path(__FILE__) . 'includes/products-cpt.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

// Шорткод
add_shortcode('credit_calculator', 'credit_calculator_shortcode');
function credit_calculator_shortcode() {
    $default = get_option('cc_default_product');
    ob_start(); ?>

    <section class="cc-calculator-wrapper">
        <div class="cc-card">
            <h3 class="cc-title"><?php _e("Кредитный калькулятор", "credit-calculator"); ?></h3>

            <form id="calc-form">
                <!-- Продукт -->
                <div class="cc-form-group">
                    <label><?php _e("Цель кредита", "credit-calculator"); ?></label>
                    <select name="product" id="cc-product" class="cc-input">
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
                            $selected = ($prod->ID == $default) ? 'selected' : '';
                            echo '<option '.$selected.'
                                value="'.$prod->ID.'"
                                data-rate="'.$rate.'"
                                data-min="'.$min.'"
                                data-max="'.$max.'"
                                data-term-min="'.$term_min.'"
                                data-term-max="'.$term_max.'">'.esc_html($prod->post_title).' - '.$rate.'%</option>';
                        endforeach;
                        ?>
                    </select>
                </div>

                <!-- Сумма -->
                <div class="cc-form-group">
                    <label><?php _e("Сумма кредита", "credit-calculator"); ?></label>
                    <input type="number" id="cc-amount" name="amount" class="cc-input">
                    <input type="range" id="cc-amount-range" class="cc-range">
                </div>

                <!-- Срок -->
                <div class="cc-form-group">
                    <label><?php _e("Срок кредита (мес.)", "credit-calculator"); ?></label>
                    <input type="range" id="cc-term" name="term" class="cc-range">
                    <div><span id="cc-term-value">0</span> <?php _e("мес.", "credit-calculator"); ?></div>
                </div>

                <!-- Итог -->
                <div class="cc-result">
                    <p><?php _e("Ежемесячный платёж:", "credit-calculator"); ?></p>
                    <p class="cc-monthly"><span id="monthly">0</span> TJS</p>
                </div>

                <!-- Кнопка -->
                <div class="cc-form-group">
                    <button type="button" id="open-modal" class="cc-btn">
                        <?php _e("Оформить заявку", "credit-calculator"); ?>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Модалка -->
    <div id="calc-modal" class="cc-modal">
        <div class="cc-modal-content">
            <span class="cc-modal-close">&times;</span>
            <h3><?php _e("Оформить заявку", "credit-calculator"); ?></h3>
            <form id="lead-form">
                <div class="cc-form-group">
                    <input type="text" name="name" class="cc-input" placeholder="<?php _e("ФИО", "credit-calculator"); ?>" required>
                </div>
                <div class="cc-form-group">
                    <input type="tel" name="phone" class="cc-input" placeholder="<?php _e("Телефон", "credit-calculator"); ?>" required>
                </div>
                <div class="cc-form-group">
                    <input type="text" name="city" class="cc-input" placeholder="<?php _e("Город", "credit-calculator"); ?>">
                </div>
                <button type="submit" class="cc-btn"><?php _e("Отправить", "credit-calculator"); ?></button>
            </form>
        </div>
    </div>

    <?php
    return ob_get_clean();
}
