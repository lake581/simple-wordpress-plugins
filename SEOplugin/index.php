<?php
/*
Plugin Name: Simple SEO Plugin
Plugin URI: https://sergeymuzharovsky.com/plugins-for-wordpress/
Description: SEO плагин для WordPress: добавляет мета-теги, генерирует sitemap и интегрируется с Яндекс Метрикой.
Version: 1.2.1
Author: Sergey Muzharovsky
Author URI: https://sergeymuzharovsky.com
*/

// ==================== Настройки плагина ====================
function my_seo_plugin_register_settings() {
    add_option('my_seo_site_keywords', '');
    add_option('my_seo_site_description', '');
    add_option('my_seo_yandex_metrika_id', '');
    add_option('my_seo_footer_iks', 'yandex_iks');
    register_setting('my_seo_plugin_options_group', 'my_seo_site_keywords');
    register_setting('my_seo_plugin_options_group', 'my_seo_site_description');
    register_setting('my_seo_plugin_options_group', 'my_seo_yandex_metrika_id');
    register_setting('my_seo_plugin_options_group', 'my_seo_footer_iks');
}
add_action('admin_init', 'my_seo_plugin_register_settings');

// Страница настроек
function my_seo_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>My SEO Plugin</h1>
        <form method="post" action="options.php">
            <?php settings_fields('my_seo_plugin_options_group'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="my_seo_site_keywords">Ключевые слова сайта:</label></th>
                    <td><textarea id="my_seo_site_keywords" name="my_seo_site_keywords" rows="2" style="width:100%;"><?php echo esc_textarea(get_option('my_seo_site_keywords')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="my_seo_site_description">Описание сайта:</label></th>
                    <td><textarea id="my_seo_site_description" name="my_seo_site_description" rows="3" style="width:100%;"><?php echo esc_textarea(get_option('my_seo_site_description')); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="my_seo_yandex_metrika_id">ID Яндекс Метрики:</label></th>
                    <td><input type="text" id="my_seo_yandex_metrika_id" name="my_seo_yandex_metrika_id" value="<?php echo esc_attr(get_option('my_seo_yandex_metrika_id')); ?>" style="width:300px;"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="my_seo_footer_iks">Иконка ИКС в футер (HTML-код):</label></th>
                    <td><textarea id="my_seo_footer_iks" name="my_seo_footer_iks" rows="2" style="width:100%;"><?php echo esc_textarea(get_option('my_seo_footer_iks')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function my_seo_plugin_menu() {
    add_options_page('My SEO Plugin', 'SEO Настройки', 'manage_options', 'my-seo-plugin', 'my_seo_plugin_settings_page');
}
add_action('admin_menu', 'my_seo_plugin_menu');

// Вставка мета-тегов сайта (только на главной странице)
function my_seo_plugin_site_meta_tags() {
    if (is_front_page() || is_home()) {
        $site_keywords = get_option('my_seo_site_keywords', '');
        $site_description = get_option('my_seo_site_description', '');

        if ($site_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($site_keywords) . '">' . "\n";
        }

        if ($site_description) {
            echo '<meta name="description" content="' . esc_attr($site_description) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'my_seo_plugin_site_meta_tags');

// Вставка Яндекс Метрики
function my_seo_plugin_yandex_metrika() {
    $yandex_metrika_id = get_option('my_seo_yandex_metrika_id', '');
    if ($yandex_metrika_id) {
        echo "<!-- Yandex.Metrika counter -->\n";
        echo "<script type='text/javascript'>\n";
        echo "(function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};\n";
        echo "m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})\n";
        echo "(window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym');\n";
        echo "ym($yandex_metrika_id, 'init', { clickmap:true, trackLinks:true, accurateTrackBounce:true, webvisor:true });\n";
        echo "</script>\n";
        echo "<noscript><div><img src='https://mc.yandex.ru/watch/$yandex_metrika_id' style='position:absolute; left:-9999px;' alt='' /></div></noscript>\n";
        echo "<!-- /Yandex.Metrika counter -->\n";
    }
}
add_action('wp_footer', 'my_seo_plugin_yandex_metrika');

// Подключение файла CSS
function my_seo_plugin_enqueue_styles() {
    // Подключаем CSS файл
    wp_enqueue_style(
        'my-seo-plugin-styles',
        plugin_dir_url(__FILE__) . 'style.css', 
        array(), 
        '1.0'
    );
}
add_action('wp_enqueue_scripts', 'my_seo_plugin_enqueue_styles');

// Добавление ИКС в футер
function my_seo_plugin_footer_iks() {
    $iks_icon = get_option('my_seo_footer_iks', '');
    if ($iks_icon) {
        echo '<div class="iks-footer-icon">' . $iks_icon . '</div>';
    }
}
add_action('wp_footer', 'my_seo_plugin_footer_iks');