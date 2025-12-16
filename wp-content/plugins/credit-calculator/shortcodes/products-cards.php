<?php
add_shortcode('credit_products_list', 'render_credit_products');

function render_credit_products() {

    $query = new WP_Query([
        'post_type'      => 'credit_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    ]);

    if (!$query->have_posts()) {
        return '<p>' . __('Кредитҳо дастрас нестанд', 'credit-calculator') . '</p>';
    }

    ob_start();
    ?>

    <!-- SCOPED WRAPPER -->
    <div class="cc-credit-products">

        <?php while ($query->have_posts()) : $query->the_post();

            // ✅ ПРАВИЛЬНЫЕ META-ПОЛЯ
            $rate     = get_post_meta(get_the_ID(), 'cc_rate', true);
            $min      = get_post_meta(get_the_ID(), 'cc_min', true);
            $max      = get_post_meta(get_the_ID(), 'cc_max', true);
            $term_min = get_post_meta(get_the_ID(), 'cc_term_min', true);
            $term_max = get_post_meta(get_the_ID(), 'cc_term_max', true);
        ?>

            <div class="credit-card-horizontal">

                <!-- IMAGE -->
                <div class="credit-card-horizontal__image">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('large'); ?>
                    <?php endif; ?>
                </div>

                <!-- CONTENT -->
                <div class="credit-card-horizontal__content">

                    <h3 class="credit-card-horizontal__title">
                        «<?php the_title(); ?>»
                    </h3>

                    <div class="credit-card-horizontal__params">

                        <div class="param">
                            <span><?php _e('Фоиз', 'credit-calculator'); ?></span>
                            <strong><?php echo esc_html($rate); ?>%</strong>
                        </div>

                        <div class="param">
                            <span><?php _e('Муҳлат', 'credit-calculator'); ?></span>
                            <strong>
                                <?php echo esc_html($term_min); ?>–<?php echo esc_html($term_max); ?>
                                <?php _e('моҳ', 'credit-calculator'); ?>
                            </strong>
                        </div>

                        <div class="param">
                            <span><?php _e('Маблағ', 'credit-calculator'); ?></span>
                            <strong>
                                <?php echo esc_html($min); ?>–<?php echo esc_html($max); ?>
                                <?php _e('с.', 'credit-calculator'); ?>
                            </strong>
                        </div>

                    </div>

                    <div class="credit-card-horizontal__actions">
                        <button
                            class="btn-primary open-credit-modal"
                            data-product="<?php echo esc_attr(get_the_title()); ?>">
                            <?php _e('Дархости қарз', 'credit-calculator'); ?>
                        </button>

                        <a href="<?php the_permalink(); ?>" class="credit-card-horizontal__more">
                            <?php _e('Маълумоти бештар', 'credit-calculator'); ?>
                        </a>
                    </div>

                </div>
            </div>

        <?php endwhile; wp_reset_postdata(); ?>

    </div>

    <?php
    return ob_get_clean();
}
