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
$deposits_page_url = '#';

// Метод 1: Проверяем опцию сайта для ID страницы депозитов
$deposits_page_id = get_option('page_for_deposits');
if ($deposits_page_id) {
    // Если используется Polylang, получаем перевод страницы
    if (function_exists('pll_get_post')) {
        $translated_page_id = pll_get_post($deposits_page_id);
        if ($translated_page_id) {
            $deposits_page_url = get_permalink($translated_page_id);
        }
    } else {
        $deposits_page_url = get_permalink($deposits_page_id);
    }
}

// Метод 2: Ищем страницу с шорткодом [deposit_products]
if (!$deposits_page_url || $deposits_page_url === '#') {
    $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        's' => '[deposit_products]', // Ищем по содержанию шорткода
    );

    $all_pages = get_posts($args);

    // Если ничего не найдено по поиску, попробуем проверить все страницы
    if (empty($all_pages)) {
        $args = array(
            'post_type' => 'page',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        );
        $all_pages = get_posts($args);
    }

    // Ищем страницу с шорткодом [deposit_products] в контенте
    foreach ($all_pages as $page) {
        if (has_shortcode($page->post_content, 'deposit_products')) {
            // Нашли страницу! Теперь получаем её перевод на текущий язык
            if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
                $current_lang = pll_current_language();
                $translated_page_id = pll_get_post($page->ID, $current_lang);
                if ($translated_page_id) {
                    $deposits_page_url = get_permalink($translated_page_id);
                    break;
                }
            } else {
                $deposits_page_url = get_permalink($page->ID);
                break;
            }
        }
    }
}

// Метод 3: Ищем по шаблону страницы
if (!$deposits_page_url || $deposits_page_url === '#') {
    $args = array(
        'post_type' => 'page',
        'post_status' => 'publish',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'deposit.php',
        'posts_per_page' => 1,
    );

    $pages = get_posts($args);
    if (!empty($pages)) {
        // Получаем перевод страницы на текущий язык
        if (function_exists('pll_get_post') && function_exists('pll_current_language')) {
            $current_lang = pll_current_language();
            $translated_page_id = pll_get_post($pages[0]->ID, $current_lang);
            if ($translated_page_id) {
                $deposits_page_url = get_permalink($translated_page_id);
            }
        } else {
            $deposits_page_url = get_permalink($pages[0]->ID);
        }
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
