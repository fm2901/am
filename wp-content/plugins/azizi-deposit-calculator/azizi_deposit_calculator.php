<?php
/**
 * Plugin Name: Azizi Deposit Calculator
 * Description: Deposit calculator with adjustable amount, term, interest rate and capitalization. Shortcode: [azizi_deposit_calculator].
 * Version: 1.0.1
 * Author: ChatGPT
 * License: GPL2
 * Text Domain: azizi-deposit-calculator
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

/** ---------- i18n ---------- */
add_action('plugins_loaded', function() {
    load_plugin_textdomain('azizi-deposit-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/** ---------- Assets ---------- */
function azizi_deposit_enqueue_assets(){
    $ver = '1.0.1';
    wp_register_style('azizi-deposit-calculator', plugins_url('assets/deposit.css', __FILE__), array(), $ver);
    wp_register_script('azizi-deposit-calculator', plugins_url('assets/deposit.js', __FILE__), array(), $ver, true);
}
add_action('wp_enqueue_scripts', 'azizi_deposit_enqueue_assets');

function azizi_deposit_enqueue_scripts() {
    // Пробрасываем ajaxurl в JS
    wp_localize_script('azizi-deposit-js', 'aziziDeposit', [
        'ajaxurl' => admin_url('admin-ajax.php'),
    ]);
}
add_action('wp_enqueue_scripts', 'azizi_deposit_enqueue_scripts');


/** ---------- Shortcode ---------- */
function azizi_deposit_render_shortcode($atts = array()){
    $atts = shortcode_atts(array(
        'amount_min' => 1000,
        'amount_max' => 1000000,
        'amount_step' => 1000,
        'term_min' => 1,
        'term_max' => 60,
        'term_step' => 1,
        'rate_min' => 1,
        'rate_max' => 30,
        'rate_step' => 0.1,
        'rate' => 14,
        'currency' => __('somoni','azizi-deposit-calculator'),
        'default_amount' => 10000,
        'default_term' => 12,
        'title' => __('Deposit Calculator', 'azizi-deposit-calculator'),
        'capitalization' => 'monthly' // 🔥 дефолт: ежемесячная
    ), $atts, 'azizi_deposit_calculator');

    wp_enqueue_style('azizi-deposit-calculator');
    wp_enqueue_script('azizi-deposit-calculator');

    ob_start(); ?>
    <div class="azizi-deposit-wrapper"
         data-capitalization="<?php echo esc_attr($atts['capitalization']); ?>">
        <div class="azizi-loan-card">
            <div class="azizi-loan-left">
                <h3 class="azizi-title"><?php echo esc_html($atts['title']); ?></h3>

                <!-- Deposit amount -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Deposit amount', 'azizi-deposit-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-dep-amount-value"><?php echo $atts['default_amount']; ?></span> <?php echo esc_html($atts['currency']); ?></div>
                    </div>
                    <input type="range" min="<?php echo $atts['amount_min']; ?>" max="<?php echo $atts['amount_max']; ?>" step="<?php echo $atts['amount_step']; ?>" value="<?php echo $atts['default_amount']; ?>" class="azizi-range js-dep-amount">
                </div>

                <!-- Deposit term -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Term', 'azizi-deposit-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-dep-term-value"><?php echo $atts['default_term']; ?></span> <?php echo esc_html(__('mo.', 'azizi-deposit-calculator')); ?></div>
                    </div>
                    <input type="range" min="<?php echo $atts['term_min']; ?>" max="<?php echo $atts['term_max']; ?>" step="<?php echo $atts['term_step']; ?>" value="<?php echo $atts['default_term']; ?>" class="azizi-range js-dep-term">
                </div>

                <!-- Rate -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Rate', 'azizi-deposit-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-dep-rate-value"><?php echo $atts['rate']; ?></span> %</div>
                    </div>
                    <input type="range" min="<?php echo $atts['rate_min']; ?>" max="<?php echo $atts['rate_max']; ?>" step="<?php echo $atts['rate_step']; ?>" value="<?php echo $atts['rate']; ?>" class="azizi-range js-dep-rate">
                </div>
            </div>

            <div class="azizi-loan-right">
                <div class="azizi-result">
                    <div class="azizi-extra-row">
                        <span><?php echo esc_html(__('Profit', 'azizi-deposit-calculator')); ?></span>
                        <strong><span class="js-dep-profit">—</span> <?php echo esc_html($atts['currency']); ?></strong>
                    </div>
                    <div class="azizi-extra-row">
                        <span><?php echo esc_html(__('Total amount', 'azizi-deposit-calculator')); ?></span>
                        <strong><span class="js-dep-total">—</span> <?php echo esc_html($atts['currency']); ?></strong>
                    </div>
                    <button class="cc-btn" id="open-deposit-modal">
                        <?php _e('Открыть вклад', 'azizi-deposit-calculator'); ?>
                    </button>
                </div>
                <div class="azizi-schedule">
                    <h4><?php echo esc_html(__('Accrual schedule', 'azizi-deposit-calculator')); ?></h4>
                    <div class="azizi-schedule-table-wrapper">
                        <table class="js-dep-schedule">
                            <thead>
                            <tr><th>#</th><th><?php echo esc_html(__('Balance', 'azizi-deposit-calculator')); ?></th></tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="cc-modal" id="deposit-modal">
        <div class="cc-modal-content">
            <span class="cc-modal-close">&times;</span>
            <h3><?php _e("Открыть вклад", "azizi-deposit-calculator"); ?></h3>
            <form id="deposit-lead-form">
                <input type="hidden" name="action" value="save_deposit_lead">
                <input type="hidden" name="lead_type" value="deposit">

                <div class="cc-form-group">
                    <label><?php _e("Ваше имя", "azizi-deposit-calculator"); ?></label>
                    <input type="text" name="name" class="cc-input" required>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Телефон", "azizi-deposit-calculator"); ?></label>
                    <input type="text" name="phone" class="cc-input" required>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Город", "azizi-deposit-calculator"); ?></label>
                    <input type="text" name="city" class="cc-input">
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Сумма вклада", "azizi-deposit-calculator"); ?></label>
                    <input type="number" id="lead-deposit-amount" name="desired_amount" readonly>
                </div>
                <div class="cc-form-group">
                    <label><?php _e("Срок вклада", "azizi-deposit-calculator"); ?></label>
                    <input type="number" id="lead-deposit-term" name="desired_term" readonly>
                </div>

                <button type="submit" class="cc-btn"><?php _e("Открыть вклад", "azizi-deposit-calculator"); ?></button>
            </form>

        </div>
    </div>

    <?php
    return ob_get_clean();
}
add_shortcode('azizi_deposit_calculator', 'azizi_deposit_render_shortcode');


