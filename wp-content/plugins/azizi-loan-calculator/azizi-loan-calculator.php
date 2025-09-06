<?php
/**
 * Plugin Name: Azizi Loan Calculator
 * Description: Loan calculator with adjustable amount, term, interest rate and payment schedule. Shortcode: [azizi_loan_calculator].
 * Version: 1.3.1
 * Author: ChatGPT
 * License: GPL2
 * Text Domain: azizi-loan-calculator
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('plugins_loaded', function() {
    load_plugin_textdomain('azizi-loan-calculator', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

function azizi_loan_enqueue_assets(){
    $ver = '1.3.1';
    wp_register_style('azizi-loan-calculator', plugins_url('assets/style.css', __FILE__), array(), $ver);
    wp_register_script('azizi-loan-calculator', plugins_url('assets/calculator.js', __FILE__), array(), $ver, true);
}
add_action('wp_enqueue_scripts', 'azizi_loan_enqueue_assets');

function azizi_loan_render_shortcode($atts = array()){
    $atts = shortcode_atts(array(
        'amount_min'     => 1000,
        'amount_max'     => 100000,
        'amount_step'    => 500,
        'term_min'       => 2,
        'term_max'       => 36,
        'term_step'      => 1,
        'rate_min'       => 1,
        'rate_max'       => 50,
        'rate_step'      => 0.1,
        'rate'           => 20,
        'currency'       => __('somoni','azizi-loan-calculator'),
        'default_amount' => 1000,
        'default_term'   => 2,
        'title'          => __('Loan Calculator', 'azizi-loan-calculator'),
        'show_totals'    => 'yes'
    ), $atts, 'azizi_loan_calculator');

    wp_enqueue_style('azizi-loan-calculator');
    wp_enqueue_script('azizi-loan-calculator');

    ob_start(); ?>
    <div class="azizi-loan-wrapper">
        <div class="azizi-loan-card">
            <div class="azizi-loan-left">
                <h3 class="azizi-title"><?php echo esc_html($atts['title']); ?></h3>
                <!-- Amount -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Credit amount', 'azizi-loan-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-amount-value"><?php echo number_format((float) $atts['default_amount'], 0, '.', ' '); ?></span> <?php echo esc_html($atts['currency']); ?></div>
                    </div>
                    <input type="range" min="<?php echo esc_attr($atts['amount_min']); ?>" max="<?php echo esc_attr($atts['amount_max']); ?>" step="<?php echo esc_attr($atts['amount_step']); ?>" value="<?php echo esc_attr($atts['default_amount']); ?>" class="azizi-range js-amount-range">
                </div>
                <!-- Term -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Loan term', 'azizi-loan-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-term-value"><?php echo intval($atts['default_term']); ?></span> <?php echo esc_html(__('mo.', 'azizi-loan-calculator')); ?></div>
                    </div>
                    <input type="range" min="<?php echo esc_attr($atts['term_min']); ?>" max="<?php echo esc_attr($atts['term_max']); ?>" step="<?php echo esc_attr($atts['term_step']); ?>" value="<?php echo esc_attr($atts['default_term']); ?>" class="azizi-range js-term-range">
                </div>
                <!-- Rate -->
                <div class="azizi-field">
                    <div class="azizi-labels">
                        <label><?php echo esc_html(__('Interest rate', 'azizi-loan-calculator')); ?></label>
                        <div class="azizi-value"><span class="js-rate-value"><?php echo floatval($atts['rate']); ?></span> %</div>
                    </div>
                    <input type="range" min="<?php echo esc_attr($atts['rate_min']); ?>" max="<?php echo esc_attr($atts['rate_max']); ?>" step="<?php echo esc_attr($atts['rate_step']); ?>" value="<?php echo esc_attr($atts['rate']); ?>" class="azizi-range js-rate-range">
                </div>
            </div>
            <div class="azizi-loan-right">
                <div class="azizi-result">
                    <div class="azizi-result-title"><?php echo esc_html(__('Monthly payment', 'azizi-loan-calculator')); ?></div>
                    <div class="azizi-result-number"><span class="js-monthly">—</span> <?php echo esc_html($atts['currency']); ?></div>
                    <div class="azizi-extra">
                        <div class="azizi-extra-row"><span><?php echo esc_html(__('Overpayment', 'azizi-loan-calculator')); ?></span><strong><span class="js-overpay">—</span> <?php echo esc_html($atts['currency']); ?></strong></div>
                        <div class="azizi-extra-row"><span><?php echo esc_html(__('Total to pay', 'azizi-loan-calculator')); ?></span><strong><span class="js-total">—</span> <?php echo esc_html($atts['currency']); ?></strong></div>
                    </div>
                    <div class="azizi-schedule">
                        <h4><?php echo esc_html(__('Payment schedule', 'azizi-loan-calculator')); ?></h4>
                        <div class="azizi-schedule-table-wrapper">
                            <table class="js-schedule-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo esc_html(__('Payment', 'azizi-loan-calculator')); ?></th>
                                    <th><?php echo esc_html(__('Balance', 'azizi-loan-calculator')); ?></th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('azizi_loan_calculator', 'azizi_loan_render_shortcode');
