<?php
add_action('wp_ajax_save_lead', 'cc_save_card_lead');
add_action('wp_ajax_nopriv_save_lead', 'cc_save_card_lead');

function cc_save_card_lead() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cc_leads';

    $lead_type = sanitize_text_field($_POST['lead_type'] ?? 'credit'); // <-- добавили
    $name      = sanitize_text_field($_POST['name'] ?? '');
    $phone     = sanitize_text_field($_POST['phone'] ?? '');
    $city      = sanitize_text_field($_POST['city'] ?? '');
    $card_name = sanitize_text_field($_POST['card_name'] ?? '');
    $product   = intval($_POST['product'] ?? 0);
    $amount    = intval($_POST['desired_amount'] ?? 0);
    $term      = intval($_POST['desired_term'] ?? 0);

    if (empty($name) || empty($phone)) {
        wp_send_json_error(['message' => __('Заполните обязательные поля', 'credit-calculator')]);
    }

    $wpdb->insert($table_name, [
        'lead_type' => strtolower($lead_type), // <-- сохраняем тип
        'name'      => $name,
        'phone'     => $phone,
        'city'      => $city,
        'card_name' => $card_name,
        'product'   => $product,
        'amount'    => $amount,
        'term'      => $term,
        'created_at'=> current_time('mysql'),
    ], ['%s','%s','%s','%s','%s','%d','%d','%d','%s']);

    $product_title = get_the_title($product);
    wp_mail(
        get_option('admin_email'),
        __('Новая заявка с калькулятора', 'credit-calculator'),
        "Тип: $lead_type\nИмя: $name\nТелефон: $phone\nИмя на карте: $card_name\nГород: $city\nПродукт: $product_title\nСумма: $amount TJS\nСрок: $term мес."
    );

    wp_send_json_success(['message' => __('Заявка успешно отправлена!', 'credit-calculator')]);
}
