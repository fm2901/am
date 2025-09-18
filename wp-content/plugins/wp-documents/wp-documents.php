<?php
/*
Plugin Name: WP Documents
Description: Управление документами по категориям с архивом и сортировкой
Version: 1.1
Author: ChatGPT
Text Domain: wp-documents
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

// === Загрузка переводов для фронта ===
add_action('plugins_loaded', function() {
    load_plugin_textdomain('wp-documents', false, dirname(plugin_basename(__FILE__)) . '/languages');
});

// === Регистрация типа записи ===
add_action('init', function () {
    register_post_type('documents', [
        'labels' => [
            'name'          => 'Документы',
            'singular_name' => 'Документ',
        ],
        'public'      => true,
        'menu_icon'   => 'dashicons-media-document',
        'supports'    => ['title'],
    ]);

    register_taxonomy('doc_category', 'documents', [
        'labels' => [
            'name'          => 'Категории документов',
            'singular_name' => 'Категория документов',
        ],
        'hierarchical'     => true,
        'show_admin_column'=> true,
    ]);
});

// === Подключаем стили и скрипты ===
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'wp-documents-style',
        plugin_dir_url(__FILE__) . 'assets/style.css',
        [],
        '1.0'
    );
    wp_enqueue_script(
        'wp-documents-script',
        plugin_dir_url(__FILE__) . 'assets/script.js',
        ['jquery'],
        '1.0',
        true
    );
});

// === Метаполя для документов ===
add_action('add_meta_boxes', function () {
    add_meta_box('doc_meta', 'Параметры документа', 'doc_meta_box', 'documents');
});

function doc_meta_box($post) {
    $file   = get_post_meta($post->ID, '_doc_file', true);
    $active = get_post_meta($post->ID, '_doc_active', true);
    $order  = get_post_meta($post->ID, '_doc_order', true);
    ?>
    <p><label>Файл:</label><br>
        <input type="text" name="doc_file" value="<?php echo esc_attr($file); ?>" style="width:100%">
        <button class="button upload-doc">Загрузить</button></p>

    <p><label><input type="checkbox" name="doc_active" value="1" <?php checked($active, '1'); ?>> Активный документ</label></p>

    <p><label>Порядок:</label><br>
        <input type="number" name="doc_order" value="<?php echo esc_attr($order); ?>" style="width:100px"></p>

    <script>
        jQuery(document).ready(function($){
            var frame;
            $('.upload-doc').click(function(e){
                e.preventDefault();
                if(frame){ frame.open(); return; }
                frame = wp.media({
                    title: 'Выберите документ',
                    button: { text: 'Использовать' },
                    multiple: false
                });
                frame.on('select', function(){
                    var attachment = frame.state().get('selection').first().toJSON();
                    $('input[name="doc_file"]').val(attachment.url);
                });
                frame.open();
            });
        });
    </script>
    <?php
}

add_action('save_post', function ($post_id) {
    if (get_post_type($post_id) != 'documents') return;
    update_post_meta($post_id, '_doc_file', $_POST['doc_file'] ?? '');
    update_post_meta($post_id, '_doc_active', isset($_POST['doc_active']) ? '1' : '0');
    update_post_meta($post_id, '_doc_order', $_POST['doc_order'] ?? 0);
});

// === Поле для сортировки категорий ===
add_action('doc_category_add_form_fields', function () {
    ?>
    <div class="form-field">
        <label for="term_order">Порядок</label>
        <input type="number" name="term_order" value="0">
    </div>
    <?php
});
add_action('created_doc_category', function ($term_id) {
    update_term_meta($term_id, 'term_order', $_POST['term_order'] ?? 0);
});
add_action('doc_category_edit_form_fields', function ($term) {
    $order = get_term_meta($term->term_id, 'term_order', true);
    ?>
    <tr class="form-field">
        <th scope="row"><label for="term_order">Порядок</label></th>
        <td><input type="number" name="term_order" value="<?php echo esc_attr($order); ?>"></td>
    </tr>
    <?php
});
add_action('edited_doc_category', function ($term_id) {
    update_term_meta($term_id, 'term_order', $_POST['term_order'] ?? 0);
});

// === Подключение медиабиблиотеки в админке ===
add_action('admin_enqueue_scripts', function($hook) {
    global $post;
    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if (isset($post) && get_post_type($post) === 'documents') {
            wp_enqueue_media();
            wp_enqueue_script('jquery');
        }
    }
});

// === Шорткод вывода документов ===
add_shortcode('documents_list', function () {
    ob_start();
    include plugin_dir_path(__FILE__) . 'templates/documents-list.php';
    return ob_get_clean();
});
