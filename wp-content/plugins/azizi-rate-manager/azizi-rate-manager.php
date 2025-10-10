<?php
/*
Plugin Name: Azizimoliya Rate Manager
Description: Управление курсами валют
Version: 4.0
Author: Fayziev Muminjon
*/

if (!defined('ABSPATH')) exit;
date_default_timezone_set("Asia/Dushanbe");

/**
 * 🔹 1. Создание роли при активации
 */
register_activation_hook(__FILE__, function () {
    add_role('rate_manager', 'Менеджер курсов', [
        'read' => true,
        'manage_rates' => true
    ]);
});

/**
 * 🔹 2. Ограничиваем доступ к /setrate
 */
add_action('template_redirect', function() {
    if (is_page('setrate') && !is_user_logged_in()) {
        auth_redirect();
    }
});

/**
 * 🔹 3. Шорткод [azizi_rate_form]
 */
add_shortcode('azizi_rate_form', function () {
    if (!is_user_logged_in()) {
        return '<div style="color:red;">⚠️ Для доступа к форме необходимо <a href="' . wp_login_url(get_permalink()) . '">войти</a>.</div>';
    }

    if (!current_user_can('manage_rates') && !current_user_can('administrator')) {
        return '<div style="color:red;">🚫 У вас нет доступа к этой странице.</div>';
    }

    global $wpdb;
    $table = 'currency_rate_new'; // wp_currency_rate_new

    $currencyCodes = [
        2 => 'USD',
        3 => 'EUR',
        4 => 'RUB'
    ];

    $rateTypes = [
        "nbt"    => "НБТ",
        "card"   => "Кошелек",
        "mt"     => "Денежные переводы",
        "beznal" => "Безналичные",
        "kassa"  => "Касса",
        "tin"    => "Тинькофф"
    ];

    // 🔹 Обработка отправки формы
    if (isset($_POST['save_rates']) && check_admin_referer('azizi_rate_nonce')) {
        if ($_POST["password"] != "koftaGadAM123") {
            echo '<div style="color:red;">❌ Неверный пароль!</div>';
        } else {
            foreach ($currencyCodes as $curId => $curName) {
                $wpdb->insert($table, [
                    'CURRENCY'     => $curId,
                    'BUY'          => floatval($_POST['buy'][$curId]),
                    'SELL'         => floatval($_POST['sell'][$curId]),
                    'BUY_FIZ'      => floatval($_POST['buy_fiz'][$curId]),
                    'SELL_FIZ'     => floatval($_POST['sell_fiz'][$curId]),
                    'nbt'          => floatval($_POST['nbt'][$curId]),
                    'card_buy'     => floatval($_POST['card_buy'][$curId]),
                    'card_sell'    => floatval($_POST['card_sell'][$curId]),
                    'beznal_buy'   => floatval($_POST['beznal_buy'][$curId]),
                    'beznal_sell'  => floatval($_POST['beznal_sell'][$curId]),
                    'kassa_buy'    => floatval($_POST['kassa_buy'][$curId]),
                    'kassa_sell'   => floatval($_POST['kassa_sell'][$curId]),
                    'mt_buy'       => floatval($_POST['mt_buy'][$curId]),
                    'mt_sell'      => floatval($_POST['mt_sell'][$curId]),
                    'tin_buy'      => floatval($_POST['tin_buy'][$curId]),
                    'tin_sell'     => floatval($_POST['tin_sell'][$curId]),
                    'DATETIME'     => current_time('mysql')
                ]);
            }
            echo '<div style="color:green;">✅ Курсы успешно сохранены!</div>';
        }
    }

    ob_start();
    ?>
    <div class="azizi-rate-form" style="max-width:1200px; margin:auto; border:1px solid #ccc; padding:25px; border-radius:10px; background:#f9f9f9;">
        <h2 style="text-align:center;">💰 Установка курса валют</h2>

        <form method="POST">
            <?php wp_nonce_field('azizi_rate_nonce'); ?>
            <table class="widefat striped text-center" style="text-align:center; min-width:900px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th>Валюта</th>
                        <th>Покупка (физ)</th>
                        <th>Продажа (физ)</th>
                        <th>Покупка (юр)</th>
                        <th>Продажа (юр)</th>
                        <th>НБТ</th>
                        <th>Кошелёк</th>
                        <th>Безнал</th>
                        <th>Касса</th>
                        <th>Переводы</th>
                        <th>Тинькофф</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currencyCodes as $curId => $curName): ?>
                        <tr>
                            <td><strong><?= $curName; ?></strong></td>
                            <td><input type="number" step="0.0001" name="buy[<?= $curId; ?>]" placeholder="BUY" required></td>
                            <td><input type="number" step="0.0001" name="sell[<?= $curId; ?>]" placeholder="SELL" required></td>
                            <td><input type="number" step="0.0001" name="buy_fiz[<?= $curId; ?>]" placeholder="BUY_FIZ" required></td>
                            <td><input type="number" step="0.0001" name="sell_fiz[<?= $curId; ?>]" placeholder="SELL_FIZ" required></td>
                            <td><input type="number" step="0.0001" name="nbt[<?= $curId; ?>]" placeholder="НБТ" required></td>

                            <td>
                                <input type="number" step="0.0001" name="card_buy[<?= $curId; ?>]" placeholder="card_buy" required>
                                <input type="number" step="0.0001" name="card_sell[<?= $curId; ?>]" placeholder="card_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="beznal_buy[<?= $curId; ?>]" placeholder="beznal_buy" required>
                                <input type="number" step="0.0001" name="beznal_sell[<?= $curId; ?>]" placeholder="beznal_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="kassa_buy[<?= $curId; ?>]" placeholder="kassa_buy" required>
                                <input type="number" step="0.0001" name="kassa_sell[<?= $curId; ?>]" placeholder="kassa_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="mt_buy[<?= $curId; ?>]" placeholder="mt_buy" required>
                                <input type="number" step="0.0001" name="mt_sell[<?= $curId; ?>]" placeholder="mt_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="tin_buy[<?= $curId; ?>]" placeholder="tin_buy" required>
                                <input type="number" step="0.0001" name="tin_sell[<?= $curId; ?>]" placeholder="tin_sell" required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="text-align:center; margin-top:20px;">
                <input type="password" name="password" placeholder="Пароль" class="form-control" style="max-width:300px; margin:10px auto;"><br>
                <input type="submit" name="save_rates" value="Сохранить" class="button button-primary button-large">
            </p>
        </form>
    </div>
    <?php
    return ob_get_clean();
});

/**
 * 🔹 4. Админка: история
 */
add_action('admin_menu', function () {
    add_menu_page(
        'История курсов валют',
        'Курсы валют',
        'manage_rates',
        'azizi-rate-history',
        'azizi_rate_history_page',
        'dashicons-chart-line',
        26
    );
});

/**
 * 🔹 5. История в админке
 */
function azizi_rate_history_page() {
    global $wpdb;
    $table = 'currency_rate_new';

    $currencyCodes = [
        2 => 'USD',
        3 => 'EUR',
        4 => 'RUB'
    ];

    // 🔹 Обработка отправки формы
    if (isset($_POST['save_rates']) && check_admin_referer('azizi_admin_rate_nonce')) {
		foreach ($currencyCodes as $curId => $curName) {
			$wpdb->insert($table, [
				'CURRENCY'     => $curId,
				'BUY'          => floatval($_POST['buy'][$curId]),
				'SELL'         => floatval($_POST['sell'][$curId]),
				'BUY_FIZ'      => floatval($_POST['buy_fiz'][$curId]),
				'SELL_FIZ'     => floatval($_POST['sell_fiz'][$curId]),
				'nbt'          => floatval($_POST['nbt'][$curId]),
				'card_buy'     => floatval($_POST['card_buy'][$curId]),
				'card_sell'    => floatval($_POST['card_sell'][$curId]),
				'beznal_buy'   => floatval($_POST['beznal_buy'][$curId]),
				'beznal_sell'  => floatval($_POST['beznal_sell'][$curId]),
				'kassa_buy'    => floatval($_POST['kassa_buy'][$curId]),
				'kassa_sell'   => floatval($_POST['kassa_sell'][$curId]),
				'mt_buy'       => floatval($_POST['mt_buy'][$curId]),
				'mt_sell'      => floatval($_POST['mt_sell'][$curId]),
				'tin_buy'      => floatval($_POST['tin_buy'][$curId]),
				'tin_sell'     => floatval($_POST['tin_sell'][$curId]),
				'DATETIME'     => current_time('mysql')
			]);
		}
		echo '<div class="notice notice-success"><p>✅ Курсы успешно добавлены!</p></div>';
    }

    // 🔹 История последних 30 записей
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY DATETIME DESC LIMIT 30");
    ?>
    <div class="wrap">
        <h1>💰 Курсы валют</h1>
        <h2>➕ Добавить новые курсы</h2>

        <form method="POST" style="margin-bottom:30px;">
            <?php wp_nonce_field('azizi_admin_rate_nonce'); ?>

            <table class="widefat striped text-center" style="text-align:center; min-width:900px;">
                <thead>
                    <tr style="background:#f3f3f3;">
                        <th>Валюта</th>
                        <th>Покупка (физ)</th>
                        <th>Продажа (физ)</th>
                        <th>Покупка (юр)</th>
                        <th>Продажа (юр)</th>
                        <th>НБТ</th>
                        <th>Кошелёк</th>
                        <th>Безнал</th>
                        <th>Касса</th>
                        <th>Переводы</th>
                        <th>Тинькофф</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($currencyCodes as $curId => $curName): ?>
                        <tr>
                            <td><strong><?= $curName; ?></strong></td>
                            <td><input type="number" step="0.0001" name="buy[<?= $curId; ?>]" placeholder="BUY" required></td>
                            <td><input type="number" step="0.0001" name="sell[<?= $curId; ?>]" placeholder="SELL" required></td>
                            <td><input type="number" step="0.0001" name="buy_fiz[<?= $curId; ?>]" placeholder="BUY_FIZ" required></td>
                            <td><input type="number" step="0.0001" name="sell_fiz[<?= $curId; ?>]" placeholder="SELL_FIZ" required></td>
                            <td><input type="number" step="0.0001" name="nbt[<?= $curId; ?>]" placeholder="НБТ" required></td>

                            <td>
                                <input type="number" step="0.0001" name="card_buy[<?= $curId; ?>]" placeholder="card_buy" required>
                                <input type="number" step="0.0001" name="card_sell[<?= $curId; ?>]" placeholder="card_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="beznal_buy[<?= $curId; ?>]" placeholder="beznal_buy" required>
                                <input type="number" step="0.0001" name="beznal_sell[<?= $curId; ?>]" placeholder="beznal_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="kassa_buy[<?= $curId; ?>]" placeholder="kassa_buy" required>
                                <input type="number" step="0.0001" name="kassa_sell[<?= $curId; ?>]" placeholder="kassa_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="mt_buy[<?= $curId; ?>]" placeholder="mt_buy" required>
                                <input type="number" step="0.0001" name="mt_sell[<?= $curId; ?>]" placeholder="mt_sell" required>
                            </td>
                            <td>
                                <input type="number" step="0.0001" name="tin_buy[<?= $curId; ?>]" placeholder="tin_buy" required>
                                <input type="number" step="0.0001" name="tin_sell[<?= $curId; ?>]" placeholder="tin_sell" required>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <p style="text-align:center; margin-top:20px;">
                <input type="submit" name="save_rates" value="Сохранить" class="button button-primary button-large">
            </p>
        </form>

        <h2>📜 История последних 30 записей</h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Валюта</th>
                    <th>BUY</th>
                    <th>SELL</th>
                    <th>BUY_FIZ</th>
                    <th>SELL_FIZ</th>
                    <th>nbt</th>
                    <th>card_buy</th>
                    <th>card_sell</th>
                    <th>beznal_buy</th>
                    <th>beznal_sell</th>
                    <th>kassa_buy</th>
                    <th>kassa_sell</th>
                    <th>mt_buy</th>
                    <th>mt_sell</th>
                    <th>tin_buy</th>
                    <th>tin_sell</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($results): ?>
                    <?php foreach ($results as $row): ?>
                        <tr>
                            <td><?= esc_html($row->ID); ?></td>
                            <td><?= esc_html($row->CURRENCY); ?></td>
                            <td><?= esc_html($row->BUY); ?></td>
                            <td><?= esc_html($row->SELL); ?></td>
                            <td><?= esc_html($row->BUY_FIZ); ?></td>
                            <td><?= esc_html($row->SELL_FIZ); ?></td>
                            <td><?= esc_html($row->nbt); ?></td>
                            <td><?= esc_html($row->card_buy); ?></td>
                            <td><?= esc_html($row->card_sell); ?></td>
                            <td><?= esc_html($row->beznal_buy); ?></td>
                            <td><?= esc_html($row->beznal_sell); ?></td>
                            <td><?= esc_html($row->kassa_buy); ?></td>
                            <td><?= esc_html($row->kassa_sell); ?></td>
                            <td><?= esc_html($row->mt_buy); ?></td>
                            <td><?= esc_html($row->mt_sell); ?></td>
                            <td><?= esc_html($row->tin_buy); ?></td>
                            <td><?= esc_html($row->tin_sell); ?></td>
                            <td><?= esc_html($row->DATETIME); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="18">Нет данных</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>