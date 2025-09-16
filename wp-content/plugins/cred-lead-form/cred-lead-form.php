<?php
/*
Plugin Name: Cred Lead Form
Description: Модальное окно для получения лидов по кредитам.
Version: 1.1
Author: Ваше Имя
Text Domain: cred-lead-form
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

class CredLeadForm {
    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_shortcode('cred_lead_form', [$this, 'render_button']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_submit_cred_lead', [$this, 'handle_form']);
        add_action('wp_ajax_nopriv_submit_cred_lead', [$this, 'handle_form']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('cred-lead-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function enqueue_assets() {
        wp_enqueue_style('cred-lead-form', plugin_dir_url(__FILE__) . '/assets/style.css');
        wp_enqueue_script('cred-lead-form', plugin_dir_url(__FILE__) . '/assets/script.js', ['jquery'], null, true);
        wp_localize_script('cred-lead-form', 'credLeadFormAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'successMessage' => __('Заявка успешно отправлена!', 'cred-lead-form'),
            'errorMessage'   => __('Ошибка при сохранении заявки!', 'cred-lead-form'),
        ]);
    }

    public function render_button() {
        ob_start(); ?>
        <button class="cred-lead-open"><?php _e('Оформить кредит', 'cred-lead-form'); ?></button>
        <div id="cred-lead-modal" class="cred-lead-modal">
            <div class="cred-lead-content">
                <span class="cred-lead-close">&times;</span>
                <div class="azizi-title"><?php _e('Заявка на кредит', 'cred-lead-form'); ?></div>
                <form id="cred-lead-form">
                    <div class="azizi-field">
                        <label class="azizi-labels" for="name"><?php _e('ФИО', 'cred-lead-form'); ?></label>
                        <input type="text" id="name" name="name" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="phone"><?php _e('Номер телефона', 'cred-lead-form'); ?></label>
                        <input type="tel" id="phone" name="phone" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="city"><?php _e('Город', 'cred-lead-form'); ?></label>
                        <input type="text" id="city" name="city" class="azizi-input" required>
                    </div>
                    <div class="cc-form-group">
                        <label><?php _e("Желаемая сумма кредита", "cred-lead-form"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="number" name="desired_amount" id="cc-lead-amount" class="cc-input" required>
                            <span class="cc-suffix">TJS</span>
                        </div>
                    </div>
                    <div class="cc-form-group">
                        <label><?php _e("Желаемый срок кредита", "cred-lead-form"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="number" name="desired_term" id="cc-lead-term" class="cc-input" required>
                            <span class="cc-suffix"><?php _e("мес.", "cred-lead-form"); ?></span>
                        </div>
                    </div>
                    <input type="hidden" name="lead_type" value="cred">
                    <button type="submit" class="azizi-btn"><?php _e('Отправить', 'cred-lead-form'); ?></button>
                </form>
                <div id="cred-lead-message"></div>
            </div>
        </div>
        <?php return ob_get_clean();
    }

    public function handle_form() {
        $data = [
            'full_name' => sanitize_text_field($_POST['full_name']),
            'phone'     => sanitize_text_field($_POST['phone']),
            'city'      => sanitize_text_field($_POST['city']),
            'summ'      => intval($_POST['summ']),
            'term'      => intval($_POST['term']),
            'lead_type' => 'CRED',
        ];

        // вызов функции сохранения из includes/save-lead.php
        $insert_id = save_cred_lead($data);

        if ($insert_id) {
            wp_send_json_success(['message' => __('Заявка успешно отправлена!', 'cred-lead-form')]);
        } else {
            wp_send_json_error(['message' => __('Ошибка при сохранении заявки!', 'cred-lead-form')]);
        }
    }
}

new CredLeadForm();
