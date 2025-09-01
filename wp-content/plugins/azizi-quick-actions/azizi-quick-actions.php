<?php
/**
 * Plugin Name: Azizi — Quick Actions
 * Description: Блок «Быстрые действия» с поддержкой мультиязычности (Polylang).
 * Version: 1.2.0
 * Author: your team
 */

if (!defined('ABSPATH')) exit;

class AziziQuickActions {
    const CPT = 'azp_action';
    const NONCE = 'azp_action_nonce';

    // Внутри класса AziziQuickActions

    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_meta']);
        add_shortcode('azizi_quick_actions', [$this, 'shortcode']);
        add_theme_support('post-thumbnails', [self::CPT]);

        // Polylang поддержка
        add_filter('pll_get_the_post_types', [$this, 'pll_register_cpt']);
        add_filter('pll_get_the_post_metas', [$this, 'pll_register_meta']);

        // 🔽 сортировка в админке
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('wp_ajax_azp_sort_actions', [$this, 'ajax_sort_actions']);
    }

    /**
     * Подключаем jQuery UI Sortable только на экране нашего CPT
     */
    public function enqueue_admin_scripts($hook) {
        global $post_type;
        if ($post_type === self::CPT && $hook === 'edit.php') {
            wp_enqueue_script('jquery-ui-sortable');
            wp_add_inline_script('jquery-ui-sortable', "
            jQuery(function($){
                var tbody = $('#the-list');
                tbody.sortable({
                    items: 'tr',
                    cursor: 'move',
                    axis: 'y',
                    handle: '.column-title',
                    update: function(){
                        var order = [];
                        tbody.find('tr').each(function(i, el){
                            order.push($(el).attr('id').replace('post-', ''));
                        });
                        $.post(ajaxurl, {
                            action: 'azp_sort_actions',
                            order: order,
                            nonce: '".wp_create_nonce('azp_sort_nonce')."'
                        });
                    }
                });
            });
        ");
        }
    }

    /**
     * AJAX обработчик для сохранения порядка
     */
    public function ajax_sort_actions() {
        if (!current_user_can('edit_posts') || !check_ajax_referer('azp_sort_nonce','nonce',false)) {
            wp_send_json_error('Not allowed');
        }
        if (!empty($_POST['order']) && is_array($_POST['order'])) {
            foreach ($_POST['order'] as $position => $post_id) {
                wp_update_post([
                    'ID'         => intval($post_id),
                    'menu_order' => intval($position)
                ]);
            }
        }
        wp_send_json_success();
    }


    public function register_cpt() {
        register_post_type(self::CPT, [
            'labels' => [
                'name'          => __('Быстрые действия', 'azizi'),
                'singular_name' => __('Действие', 'azizi'),
                'add_new'       => __('Добавить', 'azizi'),
                'add_new_item'  => __('Добавить действие', 'azizi'),
                'edit_item'     => __('Редактировать', 'azizi'),
                'new_item'      => __('Новое действие', 'azizi'),
                'view_item'     => __('Просмотреть', 'azizi'),
                'search_items'  => __('Искать', 'azizi'),
                'menu_name'     => __('Быстрые действия', 'azizi'),
            ],
            'public'       => true,
            'show_ui'      => true,
            'show_in_menu' => true,
            'menu_icon'    => 'dashicons-lightbulb',
            'supports'     => ['title','thumbnail','page-attributes'],
            'has_archive'  => false,
            'show_in_rest' => true,
        ]);
    }

    public function add_meta_boxes() {
        add_meta_box('azp_action_meta', __('Настройки действия', 'azizi'), [$this, 'render_meta_box'], self::CPT, 'normal', 'high');
    }

    public function render_meta_box($post) {
        wp_nonce_field(self::NONCE, self::NONCE);
        $type = get_post_meta($post->ID, 'azp_action_type', true) ?: 'button';
        $url  = get_post_meta($post->ID, 'azp_action_url', true);
        ?>
        <p>
            <label><strong><?php _e('Тип действия:', 'azizi'); ?></strong></label><br>
            <select name="azp_action_type">
                <option value="button" <?php selected($type, 'button'); ?>><?php _e('Кнопка', 'azizi'); ?></option>
                <option value="link" <?php selected($type, 'link'); ?>><?php _e('Ссылка', 'azizi'); ?></option>
            </select>
        </p>
        <p>
            <label><strong><?php _e('Ссылка (для "Ссылки"):', 'azizi'); ?></strong></label><br>
            <input type="url" name="azp_action_url" value="<?php echo esc_attr($url); ?>" style="width:100%">
        </p>
        <p><em><?php _e('Иконка задаётся как Featured Image. Порядок — через поле «Порядок» (Page Attributes).', 'azizi'); ?></em></p>
        <?php
    }

    public function save_meta($post_id) {
        if (!isset($_POST[self::NONCE]) || !wp_verify_nonce($_POST[self::NONCE], self::NONCE)) return;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;

        update_post_meta($post_id, 'azp_action_type', sanitize_text_field($_POST['azp_action_type'] ?? 'button'));
        update_post_meta($post_id, 'azp_action_url', esc_url_raw($_POST['azp_action_url'] ?? ''));
    }

    public function shortcode($atts) {
        $q = new WP_Query([
            'post_type'      => self::CPT,
            'posts_per_page' => -1,
            'orderby'        => ['menu_order' => 'ASC', 'date' => 'DESC'],
        ]);

        if (!$q->have_posts()) return '';

        ob_start(); ?>
        <section>
            <ul class="sl-list">
                <?php while ($q->have_posts()): $q->the_post();
                    $id    = get_the_ID();
                    $type  = get_post_meta($id, 'azp_action_type', true) ?: 'button';
                    $url   = get_post_meta($id, 'azp_action_url', true);
                    $title = get_the_title();
                    $icon  = get_the_post_thumbnail_url($id, 'full');
                    ?>
                    <li>
                        <?php if ($type === 'link' && $url): ?>
                            <a class="_action_1bg83_1" href="<?php echo esc_url($url); ?>">
                                <h4 class="_name_1bg83_21"><?php echo esc_html($title); ?></h4>
                                <?php if ($icon): ?><img class="_icon_1bg83_16" src="<?php echo esc_url($icon); ?>" alt=""><?php endif; ?>
                            </a>
                        <?php else: ?>
                            <button class="_action_1bg83_1" type="button">
                                <h4 class="_name_1bg83_21"><?php echo esc_html($title); ?></h4>
                                <?php if ($icon): ?><img class="_icon_1bg83_16" src="<?php echo esc_url($icon); ?>" alt=""><?php endif; ?>
                            </button>
                        <?php endif; ?>
                    </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        </section>
        <?php
        return ob_get_clean();
    }

    /** ---------- POLYLANG SUPPORT ---------- */
    public function pll_register_cpt($post_types) {
        $post_types[self::CPT] = true; // делаем CPT мультиязычным
        return $post_types;
    }

    public function pll_register_meta($metas) {
        $metas[] = 'azp_action_type';
        $metas[] = 'azp_action_url';
        return $metas;
    }
}

new AziziQuickActions();
