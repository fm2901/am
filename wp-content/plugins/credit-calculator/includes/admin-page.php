<?php
// Добавляем меню "Калькулятор"
add_action('admin_menu', function(){
    add_menu_page(
        __('Кредитный калькулятор', 'credit-calculator'),
        __('Калькулятор', 'credit-calculator'),
        'manage_options',
        'credit-calculator',
        'cc_admin_page_html',
        'dashicons-chart-pie',
        26
    );

    // Подменю "Настройки"
    add_submenu_page(
        'credit-calculator',
        __('Настройки калькулятора', 'credit-calculator'),
        __('Настройки', 'credit-calculator'),
        'manage_options',
        'credit-calculator-settings',
        'cc_settings_page_html'
    );
});

add_action('admin_menu', function(){
    add_menu_page(
        __('Лиды', 'credit-calculator'),
        __('Лиды', 'credit-calculator'),
        'manage_options',
        'cc-leads',
        'cc_render_leads_page',
        'dashicons-list-view',
        26
    );
});

function cc_render_leads_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'cc_leads';
    $leads = $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");

    echo '<div class="wrap"><h1>' . __('Лиды', 'credit-calculator') . '</h1>';
    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>
            <th>ID</th>
            <th>' . __('Тип', 'credit-calculator') . '</th>
            <th>' . __('Имя', 'credit-calculator') . '</th>
            <th>' . __('Телефон', 'credit-calculator') . '</th>
            <th>' . __('Город', 'credit-calculator') . '</th>
            <th>' . __('Имя на карте', 'credit-calculator') . '</th>
            <th>' . __('Продукт', 'credit-calculator') . '</th>
            <th>' . __('Сумма', 'credit-calculator') . '</th>
            <th>' . __('Срок', 'credit-calculator') . '</th>
            <th>' . __('Дата', 'credit-calculator') . '</th>
          </tr></thead><tbody>';

    foreach ($leads as $lead) {
        echo '<tr>';
        echo '<td>' . esc_html($lead->id) . '</td>';
        echo '<td>' . esc_html($lead->lead_type) . '</td>';
        echo '<td>' . esc_html($lead->name) . '</td>';
        echo '<td>' . esc_html($lead->phone) . '</td>';
        echo '<td>' . esc_html($lead->city) . '</td>';
        echo '<td>' . esc_html($lead->card_name) . '</td>';
        echo '<td>' . esc_html(get_the_title($lead->product)) . '</td>';
        echo '<td>' . esc_html($lead->amount) . '</td>';
        echo '<td>' . esc_html($lead->term) . '</td>';
        echo '<td>' . esc_html($lead->created_at) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table></div>';
}


// Установка таблицы лидов при активации плагина
function cc_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . "credit_leads";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        city VARCHAR(255),
        created_at DATETIME NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(dirname(__FILE__,2).'/credit-calculator.php', 'cc_create_table');

// Страница со списком заявок
function cc_admin_page_html() {
    if (!current_user_can('manage_options')) return;

    global $wpdb;
    $table = $wpdb->prefix . "credit_leads";
    $leads = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
    ?>
    <div class="wrap">
        <h1><?php _e("Заявки на кредит", "credit-calculator"); ?></h1>
        <?php if($leads): ?>
            <table class="widefat striped">
                <thead>
                <tr>
                    <th><?php _e("ID", "credit-calculator"); ?></th>
                    <th><?php _e("ФИО", "credit-calculator"); ?></th>
                    <th><?php _e("Телефон", "credit-calculator"); ?></th>
                    <th><?php _e("Город", "credit-calculator"); ?></th>
                    <th><?php _e("Дата", "credit-calculator"); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($leads as $lead): ?>
                    <tr>
                        <td><?php echo $lead->id; ?></td>
                        <td><?php echo esc_html($lead->name); ?></td>
                        <td><?php echo esc_html($lead->phone); ?></td>
                        <td><?php echo esc_html($lead->city); ?></td>
                        <td><?php echo $lead->created_at; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?php _e("Заявок пока нет.", "credit-calculator"); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

// Регистрируем опцию "Продукт по умолчанию"
add_action('admin_init', function(){
    register_setting('cc_settings_group', 'cc_default_product');
});

// Страница "Настройки"
function cc_settings_page_html() {
    if (!current_user_can('manage_options')) return;

    $default = get_option('cc_default_product');
    $products = get_posts(['post_type'=>'credit_product','numberposts'=>-1]);
    ?>
    <div class="wrap">
        <h1><?php _e("Настройки калькулятора", "credit-calculator"); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields('cc_settings_group'); ?>
            <?php do_settings_sections('cc_settings_group'); ?>

            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e("Продукт по умолчанию", "credit-calculator"); ?></th>
                    <td>
                        <select name="cc_default_product">
                            <?php foreach($products as $prod): ?>
                                <option value="<?php echo $prod->ID; ?>" <?php selected($default, $prod->ID); ?>>
                                    <?php echo esc_html($prod->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
