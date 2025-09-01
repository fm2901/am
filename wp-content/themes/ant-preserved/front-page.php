<?php get_header(); ?>
    <?php echo do_shortcode('[azizi_slider]'); ?>
    <?php echo do_shortcode('[azizi_quick_actions]'); ?>

    <?php echo do_shortcode('[azizi_rates_widget]'); ?>

    <?php echo do_shortcode('[azizi_recommended title="Рекомендуемые продукты" limit="8"]'); ?>

    <section class="_section_g5z7t_1 __fullWidth_g5z7t_9">
        <iframe
                src="https://maps.google.com/maps?q=Хоҷи%20Абдулазиз%2C%20улица%20Джура%20Зокирова%2C%20Худжанд%2C%20Таджикистан&amp;t=m&amp;z=14&amp;output=embed&amp;iwloc=near"
                style="border:0; width:100vw; height:50vh;"
                allowfullscreen=""
                loading="lazy">
        </iframe>
    </section>

    <?php echo do_shortcode('[azizi_news]'); ?>
    <div class="_divider_1e93u_1 __big_1e93u_5"></div>
    <section class="_section_crtk2_1">
        <div class="_content_crtk2_8">
            <div class="_info_crtk2_16">
                <div class="_title_crtk2_21">Замимаи мобилиро зеркашӣ кунед</div>
                <p class="_desc_crtk2_30">Барномаи мо идоракунии ҳамаҷонибаи хидматрасонии 24/7-ро пешниҳод
                    мекунад, ки ба шумо имкон медиҳад, ки дар як вақт кортҳои сершуморро назорат кунед ва ҳисоби
                    худро ба тарзе, ки ба шумо мувофиқ бошад, пур кунед.</p>
                <div class="_stores_huq58_1"><a href="https://apps.apple.com/us/app/com-itconsultingtj-myant/id1395436061" target="_blank"><img alt="app store logo" src="<?php echo get_template_directory_uri(); ?>/assets/app-store.png"/></a><a href="https://play.google.com/store/apps/details?id=com.itconsultingtj.myant" target="_blank"><img alt="google play logo" src="<?php echo get_template_directory_uri(); ?>/assets/google-play.png"/></a></div>
            </div>
            <div class="_img_crtk2_39"><img src="<?php echo get_template_directory_uri(); ?>/assets/phone.png"/></div>
        </div>
    </section>
<?php get_footer(); ?>
