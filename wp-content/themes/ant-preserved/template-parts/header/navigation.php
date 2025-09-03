<header class="_header_hf6hk_1">
    <div class="_top_hf6hk_17"><a href="https://ib.azizi-moliya.tj:4446" target="_blank"><?php _e('Интернет банкинг', 'azizi-main'); ?></a>
        <a data-discover="true" href="<?php echo esc_url( get_page_url_by_slug('/центры-обслуживания/') ); ?>">
            <?php _e('Центры обслуживания', 'azizi-main'); ?>
        </a>
        <?php do_action('ant_lang_switcher'); ?>
    </div>
    <div class="_main_hf6hk_51"><a class="_logo_hf6hk_172" data-discover="true" href="/"><img alt="ant logo" src="<?php echo get_template_directory_uri(); ?>/assets/logo.svg"/></a>
        <?php
        wp_nav_menu([
            'theme_location' => 'main-menu',
            'container'      => 'nav',
            'container_class'=> 'navigation',
            'menu_class'     => '_menu_1md32_1',
            'walker'         => new Ant_Menu_Walker(),
            'fallback_cb'    => '__return_empty_string',
        ]);
        ?>
        <div class="_actions_hf6hk_61"><a class="_iconButton_hf6hk_67" href="tel:+992924449911">
                <svg fill="none" height="24" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_4023_147)">
                        <path d="M5 4H9L11 9L8.5 10.5C9.57096 12.6715 11.3285 14.429 13.5 15.5L15 13L20 15V19C20 19.5304 19.7893 20.0391 19.4142 20.4142C19.0391 20.7893 18.5304 21 18 21C14.0993 20.763 10.4202 19.1065 7.65683 16.3432C4.8935 13.5798 3.23705 9.90074 3 6C3 5.46957 3.21071 4.96086 3.58579 4.58579C3.96086 4.21071 4.46957 4 5 4Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                    </g>
                    <defs>
                        <clippath id="clip0_4023_147">
                            <rect fill="white" height="24" width="24"></rect>
                        </clippath>
                    </defs>
                </svg>
            </a>
            <button class="_iconButton_hf6hk_67" type="button">
                <svg fill="none" height="24" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_4023_151)">
                        <path d="M3 8C3 7.20435 3.31607 6.44129 3.87868 5.87868C4.44129 5.31607 5.20435 5 6 5H18C18.7956 5 19.5587 5.31607 20.1213 5.87868C20.6839 6.44129 21 7.20435 21 8V16C21 16.7956 20.6839 17.5587 20.1213 18.1213C19.5587 18.6839 18.7956 19 18 19H6C5.20435 19 4.44129 18.6839 3.87868 18.1213C3.31607 17.5587 3 16.7956 3 16V8Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                        <path d="M3 10H21" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                        <path d="M7 15H7.01" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                        <path d="M11 15H13" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                    </g>
                    <defs>
                        <clippath id="clip0_4023_151">
                            <rect fill="white" height="24" width="24"></rect>
                        </clippath>
                    </defs>
                </svg>
            </button>
            <button class="sl-button sl-white _loginButton_hf6hk_82" type="button">
                <svg fill="none" height="24" viewbox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_4023_158)">
                        <path d="M10 8V6C10 5.46957 10.2107 4.96086 10.5858 4.58579C10.9609 4.21071 11.4696 4 12 4H19C19.5304 4 20.0391 4.21071 20.4142 4.58579C20.7893 4.96086 21 5.46957 21 6V18C21 18.5304 20.7893 19.0391 20.4142 19.4142C20.0391 19.7893 19.5304 20 19 20H12C11.4696 20 10.9609 19.7893 10.5858 19.4142C10.2107 19.0391 10 18.5304 10 18V16" stroke="#0D0E11" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                        <path d="M4 12H17M17 12L14 9M17 12L14 15" stroke="#0D0E11" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"></path>
                    </g>
                    <defs>
                        <clippath id="clip0_4023_158">
                            <rect fill="white" height="24" transform="matrix(-1 0 0 1 24 0)" width="24"></rect>
                        </clippath>
                    </defs>
                </svg>
                <span>Ҳисоби шахсӣ</span></button>
            <button class="sl-button _primary_pubuv_31" type="button"><span>Пайваст</span></button>
            <button class="burger" type="button">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</header>


