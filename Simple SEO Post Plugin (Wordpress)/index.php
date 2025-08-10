<?php
/*
Plugin Name: Simple SEO Post Plugin
Description: A plugin to add SEO fields (meta title, description, and keywords) to posts.
Version: 1.0
Author: Sergey Muzharovsky
Author URI: https://muzharovsky.ru
*/

// Добавляем метабокс для SEO полей
function seo_plugin_add_meta_box() {
    add_meta_box(
        'seo_meta_box', // Идентификатор
        'SEO Settings', // Название
        'seo_plugin_meta_box_callback', // Функция отображения
        'post', // Тип записи
        'normal', // Место на экране
        'high' // Приоритет
    );
}
add_action('add_meta_boxes', 'seo_plugin_add_meta_box');

// Отображение метабокса
function seo_plugin_meta_box_callback($post) {
    // Получаем текущие значения мета-полей
    $meta_description = get_post_meta($post->ID, '_seo_meta_description', true);
    $meta_keywords = get_post_meta($post->ID, '_seo_meta_keywords', true);

    // HTML для ввода данных
    ?>
    <p>
        <label for="seo_meta_description">Meta Description:</label><br>
        <textarea id="seo_meta_description" name="seo_meta_description" rows="4" style="width: 100%;"><?php echo esc_textarea($meta_description); ?></textarea>
    </p>
    <p>
        <label for="seo_meta_keywords">Meta Keywords:</label><br>
        <input type="text" id="seo_meta_keywords" name="seo_meta_keywords" value="<?php echo esc_attr($meta_keywords); ?>" style="width: 100%;">
    </p>
    <?php
}

// Сохраняем значения мета-полей
function seo_plugin_save_meta_box($post_id) {
    // Проверка автосохранения
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Проверяем права пользователя
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Сохраняем данные
    if (isset($_POST['seo_meta_description'])) {
        update_post_meta($post_id, '_seo_meta_description', sanitize_textarea_field($_POST['seo_meta_description']));
    }
    if (isset($_POST['seo_meta_keywords'])) {
        update_post_meta($post_id, '_seo_meta_keywords', sanitize_text_field($_POST['seo_meta_keywords']));
    }
}
add_action('save_post', 'seo_plugin_save_meta_box');

// Вывод мета-данных в заголовке
function seo_plugin_add_meta_tags() {
    if (is_single()) {
        global $post;

        $meta_description = get_post_meta($post->ID, '_seo_meta_description', true);
        $meta_keywords = get_post_meta($post->ID, '_seo_meta_keywords', true);

        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }
        if ($meta_keywords) {
            echo '<meta name="keywords" content="' . esc_attr($meta_keywords) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'seo_plugin_add_meta_tags');

// Добавление полей SEO для страниц
function my_seo_plugin_add_meta_boxes() {
    add_meta_box(
        'my_seo_meta_box',
        'SEO Настройки',
        'my_seo_plugin_meta_box_callback',
        'page', // Применяется к страницам
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'my_seo_plugin_add_meta_boxes');

// Callback функция для мета-бокса
function my_seo_plugin_meta_box_callback($post) {
    // Добавление nonce для безопасности
    wp_nonce_field('my_seo_plugin_save_meta_box', 'my_seo_plugin_nonce');
    
    // Получаем текущие значения
    $meta_description = get_post_meta($post->ID, '_my_seo_meta_description', true);
    $focus_keyword = get_post_meta($post->ID, '_my_seo_focus_keyword', true);
    
    // HTML форма для ввода данных
    echo '<label for="my_seo_meta_description">Описание:</label>';
    echo '<textarea id="my_seo_meta_description" name="my_seo_meta_description" rows="3" style="width:100%">' . esc_textarea($meta_description) . '</textarea>';
    
    echo '<label for="my_seo_focus_keyword">Ключевые слова:</label>';
    echo '<textarea id="my_seo_focus_keyword" name="my_seo_focus_keyword" rows="2" style="width:100%">' . esc_textarea($focus_keyword) . '</textarea>';
}

// Сохранение данных SEO при сохранении страницы
function my_seo_plugin_save_meta_box_data($post_id) {
    // Проверка на безопасность
    if (!isset($_POST['my_seo_plugin_nonce']) || !wp_verify_nonce($_POST['my_seo_plugin_nonce'], 'my_seo_plugin_save_meta_box')) {
        return;
    }

    // Проверка на автосохранение
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Сохранение данных
    if (isset($_POST['my_seo_meta_description'])) {
        update_post_meta($post_id, '_my_seo_meta_description', sanitize_text_field($_POST['my_seo_meta_description']));
    }

    if (isset($_POST['my_seo_focus_keyword'])) {
        update_post_meta($post_id, '_my_seo_focus_keyword', sanitize_text_field($_POST['my_seo_focus_keyword']));
    }
}
add_action('save_post', 'my_seo_plugin_save_meta_box_data');

// Вставка мета-тегов на страницах
function my_seo_plugin_page_meta_tags() {
    if (is_page()) {
        global $post;

        $meta_description = get_post_meta($post->ID, '_my_seo_meta_description', true);
        $focus_keyword = get_post_meta($post->ID, '_my_seo_focus_keyword', true);

        if ($meta_description) {
            echo '<meta name="description" content="' . esc_attr($meta_description) . '">' . "\n";
        }

        if ($focus_keyword) {
            echo '<meta name="keywords" content="' . esc_attr($focus_keyword) . '">' . "\n";
        }
    }
}
add_action('wp_head', 'my_seo_plugin_page_meta_tags');