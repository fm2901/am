<?php
$categories = get_terms([
    'taxonomy'   => 'doc_category',
    'hide_empty' => false,
    'orderby'    => 'meta_value_num',
    'meta_key'   => 'term_order',
    'order'      => 'ASC',
]);
?>

<div class="documents-wrapper">
    <h1><?php _e('Documents', 'wp-documents'); ?></h1>
    <div class="active-documents">
        <!-- Левая колонка -->
        <div class="documents-categories">
        <ul>
            <?php foreach ($categories as $index => $cat): ?>
                <li data-cat="cat-<?php echo $cat->term_id; ?>" class="<?php echo $index === 0 ? 'active-cat' : ''; ?>">
                    <a href="javascript:void(0);"><?php echo esc_html($cat->name); ?></a>
                    <?php if ($index === 0): ?>
                        <span class="category-indicator"></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
        <!-- Правая колонка -->
        <div class="documents-list">
            <?php foreach ($categories as $index => $cat):
                $docs = get_posts([
                    'post_type'   => 'documents',
                    'tax_query'   => [[
                        'taxonomy' => 'doc_category',
                        'terms'    => $cat->term_id,
                    ]],
                    'meta_key'    => '_doc_order',
                    'orderby'     => 'meta_value_num',
                    'order'       => 'ASC',
                    'numberposts' => -1,
                ]);
                ?>
                <div class="documents-cat-block <?php echo $index === 0 ? 'active-block' : ''; ?>" id="cat-<?php echo $cat->term_id; ?>">
                    <ul>
                        <?php foreach ($docs as $d):
                            $file   = get_post_meta($d->ID, '_doc_file', true);
                            $active = get_post_meta($d->ID, '_doc_active', true);
                            if ($active != '1') continue; ?>
                            <li>
                                <span class="doc-icon">📄</span>
                                <a href="<?php echo esc_url($file); ?>" target="_blank"><?php echo esc_html($d->post_title); ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php foreach ($categories as $index => $cat):
    $docs = get_posts([
        'post_type'   => 'documents',
        'tax_query'   => [[
            'taxonomy' => 'doc_category',
            'terms'    => $cat->term_id,
        ]],
        'meta_key'    => '_doc_order',
        'orderby'     => 'meta_value_num',
        'order'       => 'ASC',
        'numberposts' => -1,
    ]);
    ?>
    <?php if ($index === 0): ?>
        <!-- Аккордеон архива -->
        <div class="archive-accordion">
            <button class="archive-accordion-toggle">
                <?php _e('Archived Documents', 'wp-documents'); ?>
                <span class="arrow"></span>
            </button>
            <div class="archive-accordion-content">
                <?php foreach ($categories as $cat2):
                    $docs_all = get_posts([
                        'post_type'   => 'documents',
                        'tax_query'   => [[
                            'taxonomy' => 'doc_category',
                            'terms'    => $cat2->term_id,
                        ]],
                        'numberposts' => -1,
                    ]);

                    $arch = [];
                    foreach ($docs_all as $d) {
                        $active = get_post_meta($d->ID, '_doc_active', true);
                        if ($active !== '1') {
                            $arch[] = $d;
                        }
                    }
                    if (!$arch) continue; ?>
                    <div class="archive-group">
                        <h4><?php echo esc_html($cat2->name); ?></h4>
                        <ul class="archive-grid">
                            <?php foreach ($arch as $d):
                                $file  = get_post_meta($d->ID, '_doc_file', true);
                                $title = $d->post_title ?: __('Untitled', 'wp-documents'); ?>
                                <li>
                                    <span class="doc-icon">📄</span>
                                    <?php if ($file): ?>
                                        <a href="<?php echo esc_url($file); ?>" target="_blank"><?php echo esc_html($title); ?></a>
                                    <?php else: ?>
                                        <span><?php echo esc_html($title); ?> (<?php _e('file not attached', 'wp-documents'); ?>)</span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>
</div>
