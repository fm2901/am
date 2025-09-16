<?php
/*
Plugin Name: Depn Lead Form
Description: Модальное окно для получения лидов по депозитам.
Version: 1.1
Author: Ваше Имя
Text Domain: depn-lead-form
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

class DepnLeadForm {
    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_shortcode('depn_lead_form', [$this, 'render_button']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_submit_depn_lead', [$this, 'handle_form']);
        add_action('wp_ajax_nopriv_submit_depn_lead', [$this, 'handle_form']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('depn-lead-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function enqueue_assets() {
        wp_enqueue_style('depn-lead-form', plugin_dir_url(__FILE__) . '/assets/style.css');
        wp_enqueue_script('depn-lead-form', plugin_dir_url(__FILE__) . '/assets/script.js', ['jquery'], null, true);
        wp_localize_script('depn-lead-form', 'depnLeadFormAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'successMessage' => __('Заявка успешно отправлена!', 'depn-lead-form'),
            'errorMessage'   => __('Ошибка при сохранении заявки!', 'depn-lead-form'),
        ]);
    }

    public function render_button() {
        ob_start(); ?>
        <button class="depn-lead-open"><?php _e('Открыть вклад', 'depn-lead-form'); ?></button>
        <div id="depn-lead-modal" class="depn-lead-modal">
            <div class="depn-lead-content">
                <span class="depn-lead-close">&times;</span>
                <div class="azizi-title"><?php _e('Открытие вклада', 'depn-lead-form'); ?></div>
                <form id="depn-lead-form">
                    <div class="azizi-field">
                        <label class="azizi-labels" for="name"><?php _e('ФИО', 'depn-lead-form'); ?></label>
                        <input type="text" id="name" name="name" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="phone"><?php _e('Номер телефона', 'depn-lead-form'); ?></label>
                        <input type="tel" id="phone" name="phone" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="city"><?php _e('Город', 'depn-lead-form'); ?></label>
                        <input type="text" id="city" name="city" class="azizi-input" required>
                    </div>
                    <div class="cc-form-group">
                        <label><?php _e("Сумма вклада", "depn-lead-form"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="number" name="desired_amount" id="cc-lead-amount" class="cc-input" required>
                            <span class="cc-suffix">TJS</span>
                        </div>
                    </div>
                    <div class="cc-form-group">
                        <label><?php _e("Срок вклада", "depn-lead-form"); ?></label>
                        <div class="cc-input-wrap">
                            <input type="number" name="desired_term" id="cc-lead-term" class="cc-input" required>
                            <span class="cc-suffix"><?php _e("мес.", "depn-lead-form"); ?></span>
                        </div>
                    </div>
                    <input type="hidden" name="lead_type" value="depn">
                    <button type="submit" class="azizi-btn"><?php _e('Отправить', 'depn-lead-form'); ?></button>
                </form>
                <div id="depn-lead-message"></div>
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
            'lead_type' => 'depn',
        ];

        // вызов функции сохранения из includes/save-lead.php
        $insert_id = save_depn_lead($data);

        if ($insert_id) {
            wp_send_json_success(['message' => __('Заявка успешно отправлена!', 'depn-lead-form')]);
        } else {
            wp_send_json_error(['message' => __('Ошибка при сохранении заявки!', 'depn-lead-form')]);
        }
    }
}

new DepnLeadForm();
