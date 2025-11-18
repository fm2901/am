<?php
/**
 * Single News Template — Card Layout (matches page Variant D)
 */
get_header(); ?>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        .news-wrapper {
            max-width: 980px;
            margin: 60px auto;
            padding: 0 20px;
            font-family: 'Montserrat', sans-serif;
        }

        .news-card {
            background: #fff;
            border: 1px solid rgba(0,0,0,.06);
            border-radius: 16px;
            padding: 40px 38px;
            box-shadow: 0 10px 28px rgba(0,0,0,.06);
        }

        .news-title {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 20px;
            line-height: 1.25;
            color: #222;
        }

        .news-meta {
            font-size: 14px;
            color: #777;
            margin-bottom: 24px;
        }

        .news-content {
            font-size: 18px;
            line-height: 1.7;
            color: #444;
        }

        .news-content img {
            max-width: 100%;
            border-radius: 12px;
            margin: 28px 0;
        }

        .news-content p { margin-bottom: 18px; }

        .news-content h2,
        .news-content h3,
        .news-content h4 {
            font-weight: 600;
            margin: 32px 0 16px;
            color: #222;
        }

        @media (max-width: 768px) {
            .news-wrapper {
                margin: 30px auto;
            }
            .news-card {
                padding: 26px 22px;
            }
            .news-title {
                font-size: 28px;
                margin-bottom: 18px;
            }
            .news-content {
                font-size: 16px;
            }
        }
    </style>

    <div class="news-wrapper">
        <div class="news-card">
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <h1 class="news-title"><?php the_title(); ?></h1>

                <div class="news-meta">
                    <?php echo get_the_date(); ?> • <?php the_author(); ?>
                </div>

                <div class="news-content"><?php the_content(); ?></div>
            <?php endwhile; comments_template(); endif; ?>
        </div>
    </div>

<?php get_footer(); ?>