<?php
/*
Plugin Name: Sitemap для Wordpress
Description: Создает XML-карту сайта по адресу /sitemap.xml
Version: 1.0
Author: Sergey Muzharovsky
URL: https://sergeymuzharovsky.com
*/

if (!defined('ABSPATH')) exit;

// Регистрируем перезапись для sitemap.xml
function ssg_add_rewrite_rule() {
    add_rewrite_rule('^sitemap\.xml$', 'index.php?simple_sitemap=1', 'top');
}
add_action('init', 'ssg_add_rewrite_rule');

// Регистрируем query var
function ssg_add_query_var($vars) {
    $vars[] = 'simple_sitemap';
    return $vars;
}
add_filter('query_vars', 'ssg_add_query_var');

// Выводим XML при обращении к sitemap.xml
function ssg_template_redirect() {
    if (get_query_var('simple_sitemap')) {
        header('Content-Type: application/xml; charset=utf-8');
        echo ssg_generate_sitemap();
        exit;
    }
}
add_action('template_redirect', 'ssg_template_redirect');

// Генерация XML карты сайта
function ssg_generate_sitemap() {
    $posts = get_posts([
        'numberposts' => -1,
        'post_type' => ['post', 'page'],
        'post_status' => 'publish'
    ]);

    $home_url = esc_url(home_url('/'));
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    $xml .= '<url><loc>' . $home_url . '</loc><changefreq>weekly</changefreq><priority>1.0</priority></url>';

    foreach ($posts as $post) {
        $url = get_permalink($post);
        $lastmod = get_the_modified_time('Y-m-d\TH:i:sP', $post);
        $xml .= '<url>';
        $xml .= '<loc>' . esc_url($url) . '</loc>';
        $xml .= '<lastmod>' . $lastmod . '</lastmod>';
        $xml .= '<changefreq>monthly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';
    return $xml;
}

// Обновляем правила перезаписи при активации
function ssg_activate() {
    ssg_add_rewrite_rule();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'ssg_activate');

// Сброс правил при деактивации
function ssg_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'ssg_deactivate');
