<?php
// Регистрируем кастомный тип записи "Кредитные продукты"
add_action('init', function() {
    register_post_type('credit_product', [
        'labels' => [
            'name'          => __('Кредитные продукты', 'credit-calculator'),
            'singular_name' => __('Кредитный продукт', 'credit-calculator')
        ],
        'public' => false,
        'show_ui' => true,
        'menu_icon' => 'dashicons-calculator',
        'supports' => ['title'],
    ]);
});

// Делаем CPT мультиязычным для Polylang
add_filter('pll_get_post_types', function($types) {
    $types['credit_product'] = 'credit_product';
    return $types;
});

// Метаполя для продуктов
add_action('add_meta_boxes', function(){
    add_meta_box(
        'cc_product_meta',
        __('Параметры кредита', 'credit-calculator'),
        'cc_product_meta_cb',
        'credit_product',
        'normal',
        'high'
    );
});

function cc_product_meta_cb($post) {
    $rate     = get_post_meta($post->ID, 'cc_rate', true);
    $min      = get_post_meta($post->ID, 'cc_min', true);
    $max      = get_post_meta($post->ID, 'cc_max', true);
    $term_min = get_post_meta($post->ID, 'cc_term_min', true);
    $term_max = get_post_meta($post->ID, 'cc_term_max', true);
    ?>
    <p>
        <label><?php _e("Процентная ставка (%)", "credit-calculator"); ?></label><br>
        <input type="number" name="cc_rate" value="<?php echo esc_attr($rate); ?>" step="0.1">
    </p>
    <p>
        <label><?php _e("Мин. сумма", "credit-calculator"); ?></label><br>
        <input type="number" name="cc_min" value="<?php echo esc_attr($min); ?>">
    </p>
    <p>
        <label><?php _e("Макс. сумма", "credit-calculator"); ?></label><br>
        <input type="number" name="cc_max" value="<?php echo esc_attr($max); ?>">
    </p>
    <p>
        <label><?php _e("Мин. срок (мес.)", "credit-calculator"); ?></label><br>
        <input type="number" name="cc_term_min" value="<?php echo esc_attr($term_min); ?>">
    </p>
    <p>
        <label><?php _e("Макс. срок (мес.)", "credit-calculator"); ?></label><br>
        <input type="number" name="cc_term_max" value="<?php echo esc_attr($term_max); ?>">
    </p>
    <?php
}

add_action('save_post', function($post_id){
    if (get_post_type($post_id) !== 'credit_product') return;
    update_post_meta($post_id, 'cc_rate', sanitize_text_field($_POST['cc_rate']));
    update_post_meta($post_id, 'cc_min', sanitize_text_field($_POST['cc_min']));
    update_post_meta($post_id, 'cc_max', sanitize_text_field($_POST['cc_max']));
    update_post_meta($post_id, 'cc_term_min', sanitize_text_field($_POST['cc_term_min']));
    update_post_meta($post_id, 'cc_term_max', sanitize_text_field($_POST['cc_term_max']));
});
