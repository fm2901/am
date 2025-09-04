<?php
/**
 * Plugin Name: Azizi Rates Widget
 * Description: Виджет курсов валют + конвертер с мультиязычностью (Polylang/WPML).
 * Version:     2.0.1
 * Author:      Azizi Team
 * Text Domain: azizi
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) exit;

class Azizi_Rates_Widget {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_shortcode('azizi_rates_widget', [$this, 'render']);
    }

    public function enqueue_assets() {
        $base = plugin_dir_url(__FILE__) . 'assets/';

        wp_enqueue_style('azizi-cw', $base . 'css/rates.css', [], '2.0.1');
        wp_enqueue_script('azizi-cw', $base . 'js/rates.js', [], '2.0.1', true);

        // Передаём переводы в JS
        wp_localize_script('azizi-cw', 'cwL10n', [
            'title'     => __('Курс валют', 'azizi'),
            'labels'    => [
                'nbt'    => __('НБТ', 'azizi'),
                'card'   => __('По картам', 'azizi'),
                'mt'     => __('Денежных переводов', 'azizi'),
                'beznal' => __('Безналичными', 'azizi'),
                'kassa'  => __('В кассе', 'azizi'),
            ],
            'table' => [
                'currency' => __('ВАЛЮТА', 'azizi'),
                'buy'      => __('ПОКУПКА', 'azizi'),
                'sell'     => __('ПРОДАЖА', 'azizi'),
            ],
            'converter' => [
                'title' => __('Конвертер валют', 'azizi'),
                'sum'   => __('Сумма', 'azizi'),
                'currency' => __('Валюта', 'azizi'),
                'note'  => __('Конвертация выполняется по курсу', 'azizi'),
            ],
            'messages' => [
                'loading' => __('Загрузка…', 'azizi'),
                'empty'   => __('Нет данных', 'azizi'),
                'error'   => __('Ошибка загрузки', 'azizi'),
            ],
            'currencies' => [
                'rub' => __('Росс.рубль', 'azizi'),
                'eur' => __('Евро', 'azizi'),
                'usd' => __('Долл.США', 'azizi'),
            ]
        ]);
    }

    public function render() {
        ob_start(); ?>
        <section class="cw-widget" id="cw-widget">
            <div class="cw-header">
                <h2 class="cw-title"><?php _e('Курс валют', 'azizi'); ?></h2>
                <div class="cw-controls">
                    <button class="cw-mode-btn" id="cw-mode-btn" type="button" aria-expanded="false">
                        <span id="cw-mode-label"><?php _e('По картам', 'azizi'); ?></span>
                        <svg width="16" height="16" viewBox="0 0 16 16" aria-hidden="true"><path d="M4 6.5L8 10.5L12 6.5" fill="currentColor"/></svg>
                    </button>
                    <div class="cw-menu" id="cw-menu" role="menu" aria-hidden="true">
                        <button class="cw-menu-item" data-mode="nbt"><?php _e('НБТ', 'azizi'); ?></button>
                        <button class="cw-menu-item" data-mode="card"><?php _e('По картам', 'azizi'); ?></button>
                        <button class="cw-menu-item" data-mode="mt"><?php _e('Денежных переводов', 'azizi'); ?></button>
                        <button class="cw-menu-item" data-mode="beznal"><?php _e('Безналичными', 'azizi'); ?></button>
                        <button class="cw-menu-item" data-mode="kassa"><?php _e('В кассе', 'azizi'); ?></button>
                    </div>
                    <div class="cw-date" id="cw-date">—</div>
                </div>
            </div>

            <div class="cw-grid">
                <!-- Карточка с курсами -->
                <div class="cw-card cw-rates">
                    <table class="cw-table" aria-describedby="cw-mode-label">
                        <thead>
                        <tr>
                            <th><?php _e('ВАЛЮТА', 'azizi'); ?></th>
                            <th><?php _e('ПОКУПКА', 'azizi'); ?></th>
                            <th><?php _e('ПРОДАЖА', 'azizi'); ?></th>
                        </tr>
                        </thead>
                        <tbody id="cw-body">
                        <tr><td colspan="3" class="cw-empty"><?php _e('Загрузка…', 'azizi'); ?></td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Карточка конвертера -->
                <div class="cw-card cw-converter">
                    <div class="cw-conv-title"><?php _e('Конвертер валют', 'azizi'); ?></div>
                    <div class="cw-conv-row">
                        <input id="cw-amount" class="cw-input" type="number" min="0" step="0.01" value="100" aria-label="<?php esc_attr_e('Сумма', 'azizi'); ?>">
                        <select id="cw-currency" class="cw-select" aria-label="<?php esc_attr_e('Валюта', 'azizi'); ?>">
                            <option value="usd">USD</option>
                            <option value="eur">EUR</option>
                            <option value="rub">RUB</option>
                        </select>
                    </div>
                    <div class="cw-conv-result">= <span id="cw-result">—</span> <span class="cw-tjs">TJS</span></div>
                    <div class="cw-note"><?php _e('Конвертация выполняется по курсу', 'azizi'); ?> <span id="cw-note-mode"><?php _e('по картам', 'azizi'); ?></span></div>
                </div>
            </div>
        </section>
        <?php
        return ob_get_clean();
    }

}

// ✅ Загружаем текстовый домен правильно
add_action('plugins_loaded', function () {
    load_plugin_textdomain(
        'azizi',
        false,
        dirname(plugin_basename(__FILE__)) . '/languages/'
    );
});

new Azizi_Rates_Widget();
