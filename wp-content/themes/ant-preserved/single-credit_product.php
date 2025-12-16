<?php get_header(); ?>

<div class="cc-credit-products">

    <!-- Хлебные крошки -->
    <nav class="credit-breadcrumbs">
        <a href="<?php echo esc_url( get_post_type_archive_link('credit_product') ); ?>">
            ← <?php _e('Ҳамаи қарзҳо', 'credit-calculator'); ?>
        </a>
    </nav>

    <?php while (have_posts()) : the_post();

        $rate     = get_post_meta(get_the_ID(), 'cc_rate', true);
        $min      = get_post_meta(get_the_ID(), 'cc_min', true);
        $max      = get_post_meta(get_the_ID(), 'cc_max', true);
        $term_min = get_post_meta(get_the_ID(), 'cc_term_min', true);
        $term_max = get_post_meta(get_the_ID(), 'cc_term_max', true);
        ?>

        <div class="credit-card-horizontal">

            <!-- Левая часть -->
            <div class="credit-card-horizontal__image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail('large'); ?>
                <?php endif; ?>
            </div>

            <!-- Правая часть -->
            <div class="credit-card-horizontal__content">

                <h1 class="credit-card-horizontal__title">
                    <?php the_title(); ?>
                </h1>

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

                    <a href="<?php echo esc_url( get_post_type_archive_link('credit_product') ); ?>"
                       class="credit-card-horizontal__more">
                        <?php _e('Бозгашт ба рӯйхат', 'credit-calculator'); ?>
                    </a>

                </div>

            </div>
        </div>

        <!-- Описание продукта -->
        <div class="credit-single-description">
            <?php the_content(); ?>
        </div>

    <?php endwhile; ?>

    <?php echo do_shortcode('[credit_calculator]');?>
</div>

<?php get_footer(); ?>

<style>
    .credit-single-description {
        max-width: 900px;
        margin: 40px auto 0;
        font-size: 16px;
        line-height: 1.6;
        color: #1c3553;
    }
</style>