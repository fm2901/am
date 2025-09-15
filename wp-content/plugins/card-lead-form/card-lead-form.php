<?php
/*
Plugin Name: Card Lead Form
Description: Модальное окно для получения лидов по картам.
Version: 1.1
Author: Ваше Имя
Text Domain: card-lead-form
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

require_once plugin_dir_path(__FILE__) . 'includes/save-lead.php';

class CardLeadForm {
    public function __construct() {
        add_action('plugins_loaded', [$this, 'load_textdomain']);
        add_shortcode('card_lead_form', [$this, 'render_button']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_submit_card_lead', [$this, 'handle_form']);
        add_action('wp_ajax_nopriv_submit_card_lead', [$this, 'handle_form']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('card-lead-form', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function enqueue_assets() {
        wp_enqueue_style('card-lead-form', plugin_dir_url(__FILE__) . '/assets/style.css');
        wp_enqueue_script('card-lead-form', plugin_dir_url(__FILE__) . '/assets/script.js', ['jquery'], null, true);
        wp_localize_script('card-lead-form', 'CardLeadFormAjax', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'successMessage' => __('Заявка успешно отправлена!', 'card-lead-form'),
            'errorMessage'   => __('Ошибка при сохранении заявки!', 'card-lead-form'),
        ]);
    }

    public function render_button() {
        ob_start(); ?>
        <button class="card-lead-open"><?php _e('Заказать карту', 'card-lead-form'); ?></button>
        <div id="card-lead-modal" class="card-lead-modal">
            <div class="card-lead-content">
                <span class="card-lead-close">&times;</span>
                <div class="azizi-title"><?php _e('Заявка на карту', 'card-lead-form'); ?></div>
                <form id="card-lead-form">
                    <div class="azizi-field">
                        <label class="azizi-labels" for="name"><?php _e('ФИО', 'card-lead-form'); ?></label>
                        <input type="text" id="name" name="name" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="phone"><?php _e('Номер телефона', 'card-lead-form'); ?></label>
                        <input type="tel" id="phone" name="phone" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="city"><?php _e('Город', 'card-lead-form'); ?></label>
                        <input type="text" id="city" name="city" class="azizi-input" required>
                    </div>
                    <div class="azizi-field">
                        <label class="azizi-labels" for="card_name"><?php _e('Имя на карте (латиницей)', 'card-lead-form'); ?></label>
                        <input type="text" id="card_name" name="card_name" class="azizi-input" required>
                    </div>
                    <input type="hidden" name="lead_type" value="CARD">
                    <button type="submit" class="azizi-btn"><?php _e('Отправить', 'card-lead-form'); ?></button>
                </form>
                <div id="card-lead-message"></div>
            </div>
        </div>
        <?php return ob_get_clean();
    }

    public function handle_form() {
        $data = [
            'full_name' => sanitize_text_field($_POST['full_name']),
            'phone'     => sanitize_text_field($_POST['phone']),
            'city'      => sanitize_text_field($_POST['city']),
            'card_name' => sanitize_text_field($_POST['card_name']),
            'lead_type' => 'CARD',
        ];

        // вызов функции сохранения из includes/save-lead.php
        $insert_id = save_card_lead($data);

        if ($insert_id) {
            wp_send_json_success(['message' => __('Заявка успешно отправлена!', 'card-lead-form')]);
        } else {
            wp_send_json_error(['message' => __('Ошибка при сохранении заявки!', 'card-lead-form')]);
        }
    }
}

new CardLeadForm();
