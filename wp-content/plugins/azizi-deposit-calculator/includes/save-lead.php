<?php
add_action('wp_ajax_save_deposit_lead', 'save_deposit_lead');
add_action('wp_ajax_nopriv_save_deposit_lead', 'save_deposit_lead');

function save_deposit_lead() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cc_leads';

    $name   = sanitize_text_field($_POST['name'] ?? '');
    $phone  = sanitize_text_field($_POST['phone'] ?? '');
    $city   = sanitize_text_field($_POST['city'] ?? '');
    $amount = intval($_POST['desired_amount'] ?? 0);
    $term   = intval($_POST['desired_term'] ?? 0);

    if (empty($name) || empty($phone)) {
        wp_send_json_error(['message' => __('Заполните обязательные поля', 'credit-calculator')]);
    }

    $wpdb->insert($table_name, [
        'name'      => $name,
        'phone'     => $phone,
        'city'      => $city,
        'amount'    => $amount,
        'term'      => $term,
        'lead_type' => 'deposit',
    ], ['%s','%s','%s','%d','%d','%s']);

    wp_send_json_success(['message' => __('Заявка на вклад успешно отправлена!', 'credit-calculator')]);
}

