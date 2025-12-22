<?php
/**
 * Утилита для поиска ID страницы депозитов
 * 
 * ИНСТРУКЦИЯ:
 * 1. Откройте этот файл в браузере: https://ваш-сайт.tj/wp-content/plugins/deposit-products/find-deposits-page.php
 * 2. Скопируйте найденный ID
 * 3. Добавьте код в functions.php (см. вывод скрипта)
 * 4. УДАЛИТЕ этот файл после использования (для безопасности)
 */

// Загружаем WordPress
require_once('../../../../wp-load.php');

// Проверяем права администратора
if (!current_user_can('manage_options')) {
    die('Доступ запрещен. Войдите как администратор.');
}

echo '<html><head><meta charset="UTF-8"><title>Поиск страницы депозитов</title>';
echo '<style>body{font-family:Arial,sans-serif;max-width:800px;margin:50px auto;padding:20px;}';
echo 'h1{color:#333;}table{border-collapse:collapse;width:100%;margin:20px 0;}';
echo 'th,td{border:1px solid #ddd;padding:12px;text-align:left;}th{background:#0073aa;color:white;}';
echo '.code{background:#f5f5f5;padding:15px;border-left:4px solid #0073aa;margin:20px 0;font-family:monospace;}';
echo '.success{color:green;font-weight:bold;}.warning{color:orange;}</style></head><body>';

echo '<h1>🔍 Поиск страниц депозитов</h1>';

// Ищем страницы с шорткодом [deposit_products]
$args = array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'posts_per_page' => -1,
);

$pages = get_posts($args);
$found_pages = array();

foreach ($pages as $page) {
    if (has_shortcode($page->post_content, 'deposit_products')) {
        $found_pages[] = $page;
    }
}

if (!empty($found_pages)) {
    echo '<p class="success">✅ Найдено ' . count($found_pages) . ' страниц с шорткодом [deposit_products]:</p>';
    echo '<table>';
    echo '<tr><th>ID</th><th>Название</th><th>URL</th><th>Язык</th></tr>';
    
    foreach ($found_pages as $page) {
        $lang = function_exists('pll_get_post_language') ? pll_get_post_language($page->ID) : 'n/a';
        echo '<tr>';
        echo '<td><strong>' . $page->ID . '</strong></td>';
        echo '<td>' . esc_html($page->post_title) . '</td>';
        echo '<td>' . esc_html($page->post_name) . '</td>';
        echo '<td>' . esc_html($lang) . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    
    // Находим русскую версию
    $ru_page = null;
    foreach ($found_pages as $page) {
        $lang = function_exists('pll_get_post_language') ? pll_get_post_language($page->ID) : '';
        if ($lang === 'ru') {
            $ru_page = $page;
            break;
        }
    }
    
    // Если русской нет, берем первую
    if (!$ru_page && !empty($found_pages)) {
        $ru_page = $found_pages[0];
    }
    
    if ($ru_page) {
        echo '<h2>📋 Код для functions.php:</h2>';
        echo '<div class="code">';
        echo "// Добавьте этот код в functions.php вашей темы (один раз):<br>";
        echo "add_action('init', function() {<br>";
        echo "&nbsp;&nbsp;&nbsp;&nbsp;update_option('page_for_deposits', " . $ru_page->ID . ");<br>";
        echo "}, 999);<br>";
        echo "// После добавления обновите любую страницу, затем удалите этот код";
        echo '</div>';
        
        echo '<h3>🚀 Быстрая установка:</h3>';
        echo '<p>Или выполните эту команду в консоли PHP вашего хостинга:</p>';
        echo '<div class="code">';
        echo "update_option('page_for_deposits', " . $ru_page->ID . ");";
        echo '</div>';
    }
    
} else {
    echo '<p class="warning">⚠️ Не найдено страниц с шорткодом [deposit_products]</p>';
    echo '<p>Убедитесь, что:</p>';
    echo '<ul>';
    echo '<li>Вы создали страницу "Депозиты"</li>';
    echo '<li>На ней размещен шорткод <code>[deposit_products]</code></li>';
    echo '<li>Страница опубликована</li>';
    echo '</ul>';
}

echo '<hr><p style="color:#999;font-size:12px;">⚠️ ВАЖНО: Удалите этот файл (find-deposits-page.php) после использования!</p>';
echo '</body></html>';
