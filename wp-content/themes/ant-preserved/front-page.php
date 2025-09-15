<?php get_header(); ?>
    <?php echo do_shortcode('[azizi_slider]'); ?>
    <?php echo do_shortcode('[azizi_quick_actions]'); ?>
    <?php echo do_shortcode('[azizi_recommended title="Рекомендуемые продукты" limit="8"]'); ?>
    <?php echo do_shortcode('[azizi_news]'); ?>
    <?php echo do_shortcode('[azizi_rates_widget]'); ?>
    <?php echo do_shortcode('[azizi_deposit_calculator]'); ?>
    <?php echo do_shortcode('[credit_calculator]'); ?>
    <section class="_section_crtk2_1">
    <div class="_content_crtk2_8">
        <div class="_info_crtk2_16">
            <div class="_title_crtk2_21"><?php _e('Загрузите мобильное приложение', 'azizi-main'); ?></div>
            <p class="_desc_crtk2_30"><?php _e('Управляй своими картами и счетами прямо с телефона. Пополняй баланс, делай переводы и контролируй расходы 24/7 — всё в одном приложении.', 'azizi-main'); ?></p>
            <div class="_stores_huq58_1">
                <a href="https://onelink.to/fakf6p" target="_blank"><img alt="app store logo" src="<?php echo get_template_directory_uri(); ?>/assets/app-store.png"/></a>
                <a href="https://onelink.to/fakf6p" target="_blank"><img alt="google play logo" src="<?php echo get_template_directory_uri(); ?>/assets/google-play.png"/></a>
            </div>
        </div>
        <div class="_img_crtk2_39"><img src="<?php echo get_template_directory_uri(); ?>/assets/phone.png"/></div>
    </div>
</section>
<?php get_footer(); ?>