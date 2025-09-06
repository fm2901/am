<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Базовая настройка темы
add_action( 'after_setup_theme', function() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails', ['post','page','azp_product'] );
    add_theme_support( 'html5', [ 'search-form','comment-form','comment-list','gallery','caption','style','script' ] );
    load_theme_textdomain( 'azizi-main', get_template_directory() . '/languages' );
});

// Регистрация меню
if ( ! function_exists('azizi_theme_setup') ) {
    function azizi_theme_setup() {
        add_theme_support('menus');

        register_nav_menus([
            'main-menu'   => __('Главное меню (шапка)', 'azizi'),
            'footer-menu' => __('Меню в подвале', 'azizi'),
            'mobile-menu' => __('Мобильное меню', 'azizi'),
        ]);
    }
}
add_action('after_setup_theme', 'azizi_theme_setup');


// Кастомный Walker для меню
class Ant_Menu_Walker extends Walker_Nav_Menu {

    public function start_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat("\t", $depth);

        if ($depth === 0) {
            $output .= "\n{$indent}<div class=\"_submenuWrapper_1md32_24\" style=\"opacity:0;pointer-events:none;transform:translateY(-10px);height:0;\">\n";
            $output .= "{$indent}<ul class=\"_submenu_1md32_24\">\n";
            $output .= "{$indent}<li class=\"_submenuCategory_1md32_38\"><ul>\n";
        } else {
            $output .= "\n{$indent}<ul class=\"_submenu_1md32_24\">\n";
        }
    }

    public function end_lvl( &$output, $depth = 0, $args = [] ) {
        $indent = str_repeat("\t", $depth);

        if ($depth === 0) {
            $output .= "{$indent}</ul></li></ul></div>\n";
        } else {
            $output .= "{$indent}</ul>\n";
        }
    }

    public function start_el( &$output, $item, $depth = 0, $args = [], $id = 0 ) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        if ($depth === 0) {
            // верхний уровень
            $output .= $indent . '<li class="_item_1md32_7">';
            $output .= '<div class="_itemWrapper_1md32_11" style="background-color: transparent;" tabindex="0">';
            $output .= '<a class="_link_1md32_17" href="' . esc_url($item->url) . '" aria-expanded="false">';
            $output .= esc_html($item->title) . '</a>';

            // стрелка если есть подменю
            if (in_array('menu-item-has-children', $item->classes)) {
                $output .= '<button type="button" class="menu-arrow"><svg fill="none" height="16" viewBox="0 0 16 16" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M4 6.6665L8 10.6665L12 6.6665H4Z" fill="currentColor"></path></svg></button>';
            }
            $output .= '</div>';

        } else {
            $icon =  get_post_meta($item->ID, '_menu_item_icon_class', true);
            // подменю
            $output .= $indent . '<li class="_submenuItem_1md32_52">';
          //  $output .= '<svg width="20" height="20" viewBox="0 0 55 64" fill="none" xmlns="http://www.w3.org/2000/svg" color="#2955D9"><path fill-rule="evenodd" clip-rule="evenodd" d="M12.998 43.3974H21.4826C21.4591 41.9258 22.2535 38.9827 22.2535 38.9827H32.4195C32.7701 39.7055 33.4712 41.6003 33.4712 43.3974H42.0948C40.4589 36.1769 35.1809 20.9096 31.3679 16.8984H23.0247C20.0328 22.2947 14.6141 35.1751 12.998 43.3974ZM26.9112 26.5435C26.9969 26.3618 27.2555 26.3618 27.3412 26.5435L30.5967 33.4456H27.1262H23.6557L26.9112 26.5435Z" fill="#00008F"></path><path d="M23.2734 4.42285C25.9395 2.88361 29.2246 2.8836 31.8906 4.42285L49.4131 14.54C52.0791 16.0792 53.7216 18.9236 53.7217 22.002V42.2363C53.7216 45.3147 52.0791 48.159 49.4131 49.6982L31.8906 59.8154C29.2246 61.3547 25.9395 61.3547 23.2734 59.8154L5.75098 49.6982C3.08501 48.159 1.4425 45.3147 1.44238 42.2363V22.002C1.4425 18.9236 3.08501 16.0792 5.75098 14.54L23.2734 4.42285Z" stroke="#00008F" stroke-width="1.7828"></path></svg>';
            $output .= '<span class="menu-icon '.$icon.'"></span>';
            $output .= '<a class="_submenuLink_1md32_66" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
        }
    }

    public function end_el( &$output, $item, $depth = 0, $args = [] ) {
        $output .= "</li>\n";
    }
}

class Footer_Menu_Walker extends Walker_Nav_Menu {

    // Передаём флаг наличия детей в $args
    function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
        if ( !$element )
            return;

        $id_field = $this->db_fields['id'];
        if ( is_object($args[0]) )
            $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );

        parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
    }

    function start_lvl( &$output, $depth = 0, $args = null ) {
        if ($depth === 0) {
            $output .= "<ul class='_menu_17w3r_1144'>\n";
        } else {
            $output .= "<ul class='_submenu_17w3r_200'>\n";
        }
    }

    function end_lvl( &$output, $depth = 0, $args = null ) {
        $output .= "</ul>\n";
    }

    function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        if ($depth === 0) {
            // Выводим только если есть дети
            if (!empty($args->has_children)) {
                $output .= '<li>' . "\n";
                $output .= '<div class="_menuColTitle_17w3r_121">' . esc_html($item->title) . '</div>' . "\n";
            }
        } else {
            $classes = '_menuListItem_17w3r_130';
            $link_classes = '_menuListLink_17w3r_134';

            $output .= '<li class="' . $classes . '">';
            $output .= '<a class="' . $link_classes . '" href="' . esc_url($item->url) . '">' . esc_html($item->title) . '</a>';
            $output .= '</li>' . "\n";
        }
    }

    function end_el( &$output, $item, $depth = 0, $args = null ) {
        if ($depth === 0 && !empty($args->has_children)) {
            $output .= "</li>\n";
        }
    }
}







// Переключатель языков Polylang
function ant_language_switcher_render() {
    if ( function_exists('pll_the_languages') ) {
        $langs = pll_the_languages([
            'raw'                   => 1,
            'hide_if_no_translation'=> 0,
            'hide_if_empty'         => 0,
        ]);

        if ( ! empty($langs) ) {
            echo '<ul class="_list_1v0y3_1 _languages_hf6hk_159">';
            foreach ( $langs as $code => $lang ) {
                $class = '_item_1v0y3_5';
                if ( ! empty($lang['current_lang']) ) {
                    $class .= ' __active_1v0y3_18';
                }

                if ( ! empty($lang['url']) && empty($lang['current_lang']) ) {
                    echo '<li class="' . esc_attr($class) . '"><a href="' . esc_url($lang['url']) . '">' . esc_html(strtoupper($code)) . '</a></li>';
                } else {
                    echo '<li class="' . esc_attr($class) . '">' . esc_html(strtoupper($code)) . '</li>';
                }
            }
            echo '</ul>';
        }
    } else {
        echo '<!-- Polylang не найден -->';
    }
}

// Делаем хук для удобного вызова
add_action('wp', function() {
    add_action('ant_lang_switcher', 'ant_language_switcher_render');
});


function get_rate($nbt=false) {
    global $wpdb;
    $sql = "SELECT cr.id as id,cr.char_code AS char_code, cr.name AS NAME, cr.nominal AS nominal, rt.buy AS buy, rt.buy_fiz AS buy_fiz, rt.sell AS sell, rt.sell_fiz AS sell_fiz, rt.datetime AS DATE 
            FROM currency AS cr, currency_rate".($nbt>0 ? "_nbt" : "")." AS rt 
            WHERE rt.currency IN(2,3,4) AND rt.currency=cr.id GROUP BY rt.`currency`,rt.datetime ORDER BY rt.id DESC,DATETIME DESC LIMIT 3";
    $result = $wpdb->get_results($sql);
    return $result;
}

function load_rate($data, $nbt=0) {
    global $wpdb;
    $rates = $data["rate"];
    $format = (intval($nbt)<1 ? array('%d','%f','%f','%f','%f','%s') : array('%d','%f','%f','%s'));
    $table = (intval($nbt)>0 ? "currency_rate_nbt" : "currency_rate");
    foreach($rates as $k=>$v){
        $rates[$k]["datetime"] = date("Y-m-d H:i:s");
        $wpdb->insert($table,$rates[$k],$format);
    }
}

function load_rate_new($rates) {
    global $wpdb;
    $format = array('%d','%s','%f','%f','%f','%f','%f','%f','%f','%f','%f','%f','%f');
    $table = "currency_rate_new";
    foreach($rates as $k=>$v){
        $wpdb->insert($table,$rates[$k],$format);
    }
}

function get_nbt_rate() {
    $res = file_get_contents("https://nbt.tj/en/kurs/export_xml.php?date=".date("Y-m-d")."&export=xmlout");
    $xml = simplexml_load_string($res);
    $rates = array();

    foreach($xml->Valute as $r) {
        $cur_short = strtolower((string)$r->CharCode);
        $rates[$cur_short] = (float)$r->Value;
    }
    return $rates;
}

function get_rate_new(){
    global $wpdb;
    $sql = "SELECT cr.*,lower(c.`CHAR_CODE`) currency_name 
            FROM `currency_rate_new` cr
            LEFT JOIN `currency` c ON c.`ID` = cr.`CURRENCY`
            WHERE cr.currency IN(2,3,4)
            ORDER BY cr.id DESC
            LIMIT 3";
    $result = $wpdb->get_results($sql, ARRAY_A );

    $list = array();
    foreach ($result as $r) {
        $r["date"] = date("d.m.Y", strtotime($r["DATETIME"]));
        $list[$r["currency_name"]] = $r;
    }

    try {
        $nbt_rate = get_nbt_rate();
        if (count($nbt_rate) > 0) {
            foreach ($list as $currency => $rate) {
                $list[$currency]["nbt"] = $nbt_rate[$currency];
            }
        }
    } catch(Exception $e){}

    return $list;
}


/**
 * Получить ссылку на страницу по slug с учётом мультиязычности
 *
 * @param string $slug Слаг страницы (например: 'client/service-centers')
 * @return string|false  URL страницы или false, если не найдена
 */
function get_page_url_by_slug($slug)
{
    // Находим страницу по slug
    $page = get_page_by_path($slug);

    if (!$page) {
        return false;
    }

    $page_id = $page->ID;

    // Polylang
    if (function_exists('pll_get_post')) {
        $page_id = pll_get_post($page_id);
    }

    // WPML
    if (function_exists('icl_object_id')) {
        $page_id = icl_object_id($page_id, 'page', true);
    }

    return get_permalink($page_id);
}

// Добавляем выпадающий список "Иконка" в пункты меню
add_action( 'wp_nav_menu_item_custom_fields', function( $item_id, $item, $depth, $args ) {
    // Список доступных кастомных классов
    $icon_classes = [
        ''             => '— Без иконки —',
        'icon-home'    => 'Домой',
        'icon-user'    => 'Профиль',
        'icon-cart'    => 'Корзина',
        'icon-mail'    => 'Почта',
    ];

    // Текущее значение
    $value = get_post_meta( $item_id, '_menu_item_icon_class', true );
    ?>
    <p class="description description-wide">
        <label for="edit-menu-item-icon-class-<?php echo $item_id; ?>">
            <?php _e( 'Иконка', 'your-textdomain' ); ?><br>
            <select id="edit-menu-item-icon-class-<?php echo $item_id; ?>" name="menu-item-icon-class[<?php echo $item_id; ?>]">
                <?php foreach ( $icon_classes as $class => $label ) : ?>
                    <option value="<?php echo esc_attr( $class ); ?>" <?php selected( $value, $class ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
    </p>
    <?php
}, 10, 4 );

// Сохраняем значение
add_action( 'wp_update_nav_menu_item', function( $menu_id, $menu_item_db_id ) {
    if ( isset( $_POST['menu-item-icon-class'][ $menu_item_db_id ] ) ) {
        update_post_meta( $menu_item_db_id, '_menu_item_icon_class', sanitize_text_field( $_POST['menu-item-icon-class'][ $menu_item_db_id ] ) );
    }
}, 10, 2 );

// Выводим иконку в меню
add_filter( 'nav_menu_item_title', function( $title, $item ) {
    $icon_class = get_post_meta( $item->ID, '_menu_item_icon_class', true );
    if ( $icon_class ) {
        $title = '<span class="menu-icon ' . esc_attr( $icon_class ) . '"></span> ' . $title;
    }
    return $title;
}, 10, 2 );
