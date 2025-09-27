<?php
/**
 * Шаблон: блок новостей (слайдер Swiper)
 * Используется в шорткоде [azizi_news]
 */
?>

<section class="_section_1fhxe_1">
    <div class="_header_1fhxe_21">
        <h2 class="_title_1fhxe_48"><?php _e('Новости компании', 'azizi-news'); ?></h2>
        <a class="sl-button" href="<?php echo get_post_type_archive_link('azp_news'); ?>">
            <span><?php _e('Все новости', 'azizi-news'); ?></span>
        </a>
    </div>

    <div class="swiper swiper-effect-fade-loop swiper-horizontal sl-swiper" data-autoplay="3000" data-loop="true">
        <div class="swiper-wrapper">
            <?php
            $args = [
                'post_type'      => 'azp_news',
                'posts_per_page' => 3,
            ];
            $query = new WP_Query($args);

            if ($query->have_posts()):
                while ($query->have_posts()): $query->the_post(); ?>

                    <div class="sl-slide" style="margin-right: 24px">
                        <a class="sl-news" href="<?php the_permalink(); ?>" data-discover="true">
                            <div class="_newsImage_o6yon_11">
                                <?php if (has_post_thumbnail()): ?>
                                    <?php the_post_thumbnail('medium_large', ['alt' => get_the_title()]); ?>
                                <?php else: ?>
                                    <img src="<?php echo esc_url(get_template_directory_uri().'/assets/placeholder.png'); ?>" alt="<?php the_title_attribute(); ?>">
                                <?php endif; ?>
                            </div>

                            <div class="_newsContent_o6yon_27">
                                <h4 class="_newsTitle_o6yon_35"><?php the_title(); ?></h4>
                                <div class="_newsFooter_o6yon_57">

                                    <div class="_newsDate_o6yon_65">
                                        <svg fill="none" height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M2.5 10c0 1 .2 2 .6 2.9.4.9 1 1.7 1.7 2.4.7.7 1.5 1.3 2.4 1.7.9.4 1.9.6 2.8.6s2-.2 2.9-.6c.9-.4 1.7-1 2.4-1.7.7-.7 1.3-1.5 1.7-2.4.4-.9.6-1.9.6-2.9 0-2-0.8-3.9-2.2-5.3C13.9 3.3 12 2.5 10 2.5s-3.9.8-5.3 2.2C3.3 6.1 2.5 8 2.5 10z" stroke="#0D0E11" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M10 5.8V10l2.5 2.5" stroke="#0D0E11" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                        <span><?php echo get_the_date('d F Y'); ?></span>
                                    </div>

                                    <div class="_newsButton_o6yon_8">
                                        <svg fill="none" height="20" width="20" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M14.1666 5.83398L5.83331 14.1673" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                            <path d="M6.66669 5.83398H14.1667V13.334" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/>
                                        </svg>
                                    </div>

                                </div>
                            </div>
                        </a>
                    </div>

                <?php endwhile;
                wp_reset_postdata();
            else: ?>
                <p><?php _e('Новостей пока нет', 'azizi-news'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</section>

