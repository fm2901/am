<?php
/**
 * Plugin Name: Azizi Credit Application
 * Description: Мультиязычная форма заявки на кредит. Сохраняет лиды и отправляет их на email.
 * Version: 1.1.0
 * Author: ChatGPT
 * Text Domain: azizi-credit-application
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/** ---------- i18n ---------- */
add_action('plugins_loaded', function() {
    load_plugin_textdomain('azizi-credit-application', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

/** ---------- Создание таблицы для лидов ---------- */
register_activation_hook(__FILE__, function() {
    global $wpdb;
    $table = $wpdb->prefix . 'credit_applications';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255),
        amount DECIMAL(12,2) NOT NULL,
        currency VARCHAR(20) NOT NULL,
        city VARCHAR(100),
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) $charset;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

/** ---------- Подключаем стили ---------- */
add_action('wp_enqueue_scripts', function() {
    wp_register_style(
        'azizi-credit-form',
        plugins_url('assets/credit-form.css', __FILE__),
        [],
        '1.1.0'
    );
});

/** ---------- Шорткод формы ---------- */
add_shortcode('azizi_credit_form', function() {
    wp_enqueue_style('azizi-credit-form');

    ob_start();

    // Сообщение об успешной отправке
    if (isset($_GET['application']) && $_GET['application'] === 'success') {
        echo '<div class="azizi-form-success">' .
            esc_html(__('Your application was sent successfully!', 'azizi-credit-application')) .
            '</div>';
    }
    ?>

    <form method="post" class="azizi-credit-form">
        <h2><?php _e('Apply for a Loan', 'azizi-credit-application'); ?></h2>

        <p>
            <label><?php _e('Full Name', 'azizi-credit-application'); ?></label>
            <input type="text" name="name" required>
        </p>

        <p>
            <label><?php _e('Phone Number', 'azizi-credit-application'); ?></label>
            <input type="tel" name="phone" required>
        </p>

        <p>
            <label><?php _e('Email', 'azizi-credit-application'); ?></label>
            <input type="email" name="email">
        </p>

        <p>
            <label><?php _e('Loan Amount', 'azizi-credit-application'); ?></label>
            <input type="number" name="amount" required>
        </p>

        <p>
            <label><?php _e('Currency', 'azizi-credit-application'); ?></label>
            <select name="currency" required>
                <option value="TJS"><?php _e('Somoni', 'azizi-credit-application'); ?></option>
                <option value="USD"><?php _e('USD', 'azizi-credit-application'); ?></option>
                <option value="EUR"><?php _e('Euro', 'azizi-credit-application'); ?></option>
            </select>
        </p>

        <p>
            <label><?php _e('City', 'azizi-credit-application'); ?></label>
            <input type="text" name="city">
        </p>

        <?php wp_nonce_field('azizi_credit_form', 'azizi_credit_nonce'); ?>

        <p>
            <button type="submit" name="azizi_credit_submit">
                <?php _e('Send Application', 'azizi-credit-application'); ?>
            </button>
        </p>
    </form>
    <?php
    return ob_get_clean();
});

/** ---------- Обработка формы ---------- */
add_action('init', function() {
    if (isset($_POST['azizi_credit_submit'])) {
        if (!isset($_POST['azizi_credit_nonce']) ||
            !wp_verify_nonce($_POST['azizi_credit_nonce'], 'azizi_credit_form')) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'credit_applications';

        $data = [
            'name'     => sanitize_text_field($_POST['name']),
            'phone'    => sanitize_text_field($_POST['phone']),
            'email'    => sanitize_email($_POST['email']),
            'amount'   => floatval($_POST['amount']),
            'currency' => sanitize_text_field($_POST['currency']),
            'city'     => sanitize_text_field($_POST['city']),
        ];

        $wpdb->insert($table, $data);

        // Отправка письма админу
        $admin_email = get_option('admin_email');
        $subject = __('New Loan Application', 'azizi-credit-application');
        $message = sprintf(
            __("New loan request:\n\nName: %s\nPhone: %s\nEmail: %s\nAmount: %s %s\nCity: %s\n", 'azizi-credit-application'),
            $data['name'], $data['phone'], $data['email'], $data['amount'], $data['currency'], $data['city']
        );

        wp_mail($admin_email, $subject, $message);

        wp_safe_redirect(add_query_arg('application', 'success', wp_get_referer()));
        exit;
    }
});

add_action('phpmailer_init', function($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'smtp.yandex.ru'; // SMTP сервер
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465;
    $phpmailer->Username   = 'info@yourdomain.com'; // учётка
    $phpmailer->Password   = 'app_password';        // пароль (лучше пароль приложения)
    $phpmailer->SMTPSecure = 'ssl';                 // или 'tls'
    $phpmailer->From       = 'info@yourdomain.com';
    $phpmailer->FromName   = 'Azizi Credit';
});
