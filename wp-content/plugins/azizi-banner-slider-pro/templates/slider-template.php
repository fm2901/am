<?php
$q = new WP_Query([
    'post_type'      => 'azizi_banner',
    'posts_per_page' => -1
]);

$autoplay = get_option('azizi_autoplay', 6000);
$effect   = get_option('azizi_effect', 'fade-scale');
$parallax = get_option('azizi_parallax', 'mouse');
$count    = $q->found_posts;
?>

<div class="azizi-slider mts-loader" data-count="<?=$count?>">

    <!-- TOP PROGRESS BARS -->
    <div class="mts-progress">
        <?php for($i=0; $i<$count; $i++): ?>
            <div class="mts-pagination-button">
                <div class="mts-pagination-progress"></div>
            </div>
        <?php endfor; ?>
    </div>

    <div class="swiper"
         data-autoplay="<?=$autoplay?>"
         data-effect="<?=$effect?>"
         data-parallax="<?=$parallax?>">

        <div class="swiper-wrapper">
            <?php while($q->have_posts()): $q->the_post();

                $pre = get_post_meta(get_the_ID(),'pretitle',true);
                $sub = get_post_meta(get_the_ID(),'subtitle',true);
                $btn = get_post_meta(get_the_ID(),'btn_text',true);
                $url = get_post_meta(get_the_ID(),'btn_url',true);

                /* DESKTOP IMAGE */
                $desk_raw = get_post_meta(get_the_ID(),'desktop_image',true);
                $desk = is_numeric($desk_raw) ? wp_get_attachment_url($desk_raw) : $desk_raw;

                /* MOBILE IMAGE */
                $mob_raw = get_post_meta(get_the_ID(),'mobile_image',true);
                $mob = is_numeric($mob_raw) ? wp_get_attachment_url($mob_raw) : $mob_raw;

                if(!$mob && $desk) $mob = $desk;
                if(!$desk && $mob) $desk = $mob;

                if(!$desk) $desk = "https://via.placeholder.com/1600x600/000/fff?text=No+Image";
                if(!$mob)  $mob  = $desk;

                $vid = get_post_meta(get_the_ID(),'video_url',true);
                ?>

                <div class="swiper-slide"
                     data-desktop="<?=esc_url($desk)?>"
                     data-mobile="<?=esc_url($mob)?>"
                     data-video="<?=esc_url($vid)?>">

                    <div class="slide-bg"></div>
                    <div class="video-wrap"></div>

                    <!-- TEXT CONTENT -->
                    <div class="content">
                        <?php if($pre): ?><div class="pre"><?=esc_html(pll__($pre))?></div><?php endif; ?>
                        <h2 class="title"><?=esc_html(pll__(get_the_title()))?></h2>
                        <?php if($sub): ?><div class="sub"><?=esc_html(pll__($sub))?></div><?php endif; ?>
                    </div>

                    <!-- BOTTOM PANEL (только кнопка) -->
                    <div class="bottom-panel">
                        <div class="left">
                            <?php if($btn): ?>
                                <a href="<?=esc_url($url)?>" target="_blank" class="btn cta"><?=esc_html($btn)?></a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

            <?php endwhile; wp_reset_postdata(); ?>
        </div>

        <!-- GLOBAL ARROWS (одни на весь слайдер!) -->
        <div class="mts-arrows">
            <button class="mts-arrow mts-prev"></button>
            <button class="mts-arrow mts-next"></button>
        </div>

    </div>
</div>
