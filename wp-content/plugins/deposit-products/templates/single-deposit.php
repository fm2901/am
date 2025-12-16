<?php
/**
 * Template for single deposit page
 */

get_header();

$rate_somoni = get_post_meta(get_the_ID(), '_deposit_rate_somoni', true);
$rate_dollar = get_post_meta(get_the_ID(), '_deposit_rate_dollar', true);
$period = get_post_meta(get_the_ID(), '_deposit_period', true);
$min_amount = get_post_meta(get_the_ID(), '_deposit_min_amount', true);
$currency = get_post_meta(get_the_ID(), '_deposit_currency', true) ?: 'somoni';
$form_url = get_post_meta(get_the_ID(), '_deposit_form_url', true) ?: '#deposit-form';

$currency_label = $currency === 'somoni' ? __('с.', 'deposit-products') : __('$', 'deposit-products');
?>

<div class="deposit-single-page">
    <!-- Баннер -->
    <div class="deposit-banner">
        <?php if (has_post_thumbnail()) : ?>
            <div class="deposit-banner-bg">
                <?php the_post_thumbnail('full'); ?>
            </div>
        <?php endif; ?>

        <div class="deposit-banner-content">
            <h1 class="deposit-banner-title"><?php the_title(); ?></h1>

            <div class="deposit-banner-info">
                <?php if ($rate_somoni) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Даромад бо сом.', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('то', 'deposit-products'); ?> <?php echo esc_html($rate_somoni); ?>%</span>
                    </div>
                <?php endif; ?>

                <?php if ($rate_dollar) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Даромад бо долл.', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('то', 'deposit-products'); ?> <?php echo esc_html($rate_dollar); ?>%</span>
                    </div>
                <?php endif; ?>

                <?php if ($period) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Муҳлат', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php echo esc_html($period); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($min_amount) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Маблағ', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('аз', 'deposit-products'); ?> <?php echo esc_html($min_amount); ?> <?php echo esc_html($currency_label); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <a href="<?php echo esc_url($form_url); ?>" class="deposit-banner-btn">
                <?php _e('Амонат гузоштан', 'deposit-products'); ?>
            </a>
        </div>
    </div>

    <!-- Подробная информация -->
    <div class="deposit-content">
        <div class="deposit-content-inner">
            <?php the_content(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
