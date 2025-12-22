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

$currency_label = $currency === 'somoni' ? __('сом.', 'deposit-products') : __('$', 'deposit-products');

// Получаем URL страницы депозитов для хлебных крошек
$deposits_page_url = function_exists('pll_get_post') ? get_permalink(pll_get_post(get_option('page_for_deposits'))) : '#';
if (!$deposits_page_url || $deposits_page_url === '#') {
    // Пробуем найти страницу с шаблоном Депозиты
    $pages = get_pages(array('meta_key' => '_wp_page_template', 'meta_value' => 'deposit.php'));
    if (!empty($pages)) {
        $deposits_page_url = get_permalink($pages[0]->ID);
    }
}
?>

<div class="deposit-single-page">
    <!-- Хлебные крошки -->
    <nav class="deposit-breadcrumbs" style="max-width: 1200px; margin: 20px auto; padding: 0 20px;">
        <a href="<?php echo esc_url($deposits_page_url); ?>" style="color: #1c3553; text-decoration: none;">
            ← <?php _e('Все депозиты', 'deposit-products'); ?>
        </a>
    </nav>

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
                        <span class="deposit-banner-label"><?php _e('Доход в сомони', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('до', 'deposit-products'); ?> <?php echo esc_html($rate_somoni); ?>%</span>
                    </div>
                <?php endif; ?>

                <?php if ($rate_dollar) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Доход в долларах', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('до', 'deposit-products'); ?> <?php echo esc_html($rate_dollar); ?>%</span>
                    </div>
                <?php endif; ?>

                <?php if ($period) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Срок', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php echo esc_html($period); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($min_amount) : ?>
                    <div class="deposit-banner-item">
                        <span class="deposit-banner-label"><?php _e('Сумма', 'deposit-products'); ?></span>
                        <span class="deposit-banner-value"><?php _e('от', 'deposit-products'); ?> <?php echo esc_html($min_amount); ?> <?php echo esc_html($currency_label); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <button class="deposit-banner-btn open-deposit-modal">
                <?php _e('Открыть вклад', 'deposit-products'); ?>
            </button>
        </div>
    </div>

    <!-- Подробная информация -->
    <div class="deposit-content">
        <div class="deposit-content-inner">
            <?php the_content(); ?>
        </div>
    </div>

    <!-- Калькулятор депозита -->
    <div class="deposit-calculator-section" style="margin-top: 40px;">
        <?php echo do_shortcode('[azizi_deposit_calculator]'); ?>
    </div>
</div>

<?php get_footer(); ?>
