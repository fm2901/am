<?php
/**
 * Page Template — Variant D (Single Large Card)
 * Matches theme style: radius 16px, light border, soft shadow, Montserrat
 */
get_header(); ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        .page-wrapper {
            max-width: 980px;
            margin: 60px auto;
            padding: 0 20px;
            font-family: 'Montserrat', sans-serif;
        }

        .page-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,.06);
            border-radius: 16px;
            padding: 40px 38px;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }

        .page-title {
            font-size: 42px;
            font-weight: 700;
            margin-bottom: 28px;
            line-height: 1.25;
            color: #222;
        }

        .page-content {
            font-size: 18px;
            line-height: 1.7;
            color: #444;
        }

        .page-content img {
            max-width: 100%;
            border-radius: 12px;
            margin: 28px 0;
        }

        .page-content p { margin-bottom: 18px; }

        .page-content h2,
        .page-content h3,
        .page-content h4 {
            font-weight: 600;
            margin: 32px 0 16px;
            color: #222;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                margin: 30px auto;
            }
            .page-card {
                padding: 26px 22px;
            }
            .page-title {
                font-size: 30px;
                margin-bottom: 20px;
            }
            .page-content {
                font-size: 16px;
            }
        }
    </style>

    <div class="page-wrapper">
        <div class="page-card">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <h1 class="page-title"><?php the_title(); ?></h1>
                <div class="page-content"><?php the_content(); ?></div>
            <?php endwhile; endif; ?>
        </div>
    </div>

<?php get_footer(); ?>