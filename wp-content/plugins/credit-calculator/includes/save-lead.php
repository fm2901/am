<?php
add_action('wp_ajax_save_lead', 'save_lead');
add_action('wp_ajax_nopriv_save_lead', 'save_lead');

function save_lead() {
    global $wpdb;

    $name  = sanitize_text_field($_POST['name']);
    $phone = sanitize_text_field($_POST['phone']);
    $city  = sanitize_text_field($_POST['city']);

    $table = $wpdb->prefix . "credit_leads";
    $wpdb->insert($table, [
        'name' => $name,
        'phone' => $phone,
        'city' => $city,
        'created_at' => current_time('mysql')
    ]);

    // Отправка письма
    wp_mail(
        get_option('admin_email'),
        "Новая заявка на кредит",
        "ФИО: $name\nТелефон: $phone\nГород: $city"
    );

    wp_send_json_success(['message' => 'Заявка успешно отправлена!']);
}
