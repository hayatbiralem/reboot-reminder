<?php

/**
 * Plugin Name: Reboot Reminder
 * Description: A simple reminder plugin for clients and their repeatable products. Founder: <a href="http://kbcelik.com">Kenan Barış Çelik</a> | <a href="https://brain.work">Brain Work</a>.
 * Version:     1.0.0
 * Author:      Reboot
 * Author URI:  https://reboot.com.tr
 * Text Domain: reboot-reminder
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit('No direct script access allowed');
}

if (!class_exists('REBOOT_REMINDER')) {

    define('REBOOT_REMINDER_VERSION', '1.0.0');

    define('REBOOT_REMINDER_PATH', plugin_dir_path(__FILE__));
    define('REBOOT_REMINDER_URL', plugin_dir_url(__FILE__));

    define('REBOOT_REMINDER_ASSETS_VERSION', REBOOT_REMINDER_VERSION);
    define('REBOOT_REMINDER_ASSETS_PATH', REBOOT_REMINDER_PATH . 'assets/');
    define('REBOOT_REMINDER_ASSETS_URL', REBOOT_REMINDER_URL . 'assets/');

    define('REBOOT_REMINDER_NONCE_KEY', '17ef271c2bd5e3d6cc1d1bdd7e6c2405'); // You can use md5_file( __FILE__ ) for new cool nonce key ;)
    define('REBOOT_REMINDER_TEXT_DOMAIN', 'reboot-reminder');

    define('REBOOT_REMINDER_TITLE', 'Reminder');
    define('REBOOT_REMINDER_SLUG', 'reminder');

    class REBOOT_REMINDER
    {
        private static $hook = 'reboot_reminder_cron_hook';
        private static $interval = 'reboot_reminder_interval';
        private static $instance = null;

        private function __construct()
        {
            add_action(self::$hook, [$this, 'exec']);
            add_filter('cron_schedules', [$this, 'add_cron_interval']);

            register_activation_hook(__FILE__, [$this, 'activate']);
            register_deactivation_hook(__FILE__, [$this, 'deactivate']);

            add_action('wp_dashboard_setup', [$this, 'add_dashboard_widgets']);

            add_action('after_setup_theme', [$this, 'register_custom_post_types']);
            add_action('admin_menu', [$this, 'add_settings_menu']);
            add_action('admin_notices', [$this, 'admin_notices']);

            add_action('admin_print_styles', [$this, 'admin_print_styles']);

            add_filter('acf/load_field/type=message', [$this, 'acf_shortcode_support_for_message_field'], 10);
            add_shortcode('reboot_reminder_history', [$this, 'reboot_reminder_history_render']);

            add_action('acf/init', [$this, 'meta_boxes']);
        }

        function meta_boxes(){
            if( function_exists('acf_add_local_field_group') ):

                acf_add_local_field_group(array(
                    'key' => 'group_5cabbeb29dde2',
                    'title' => 'Reminder Data',
                    'fields' => array(
                        array(
                            'key' => 'field_5cabbedba13f7',
                            'label' => 'Reminder Data',
                            'name' => 'reminder_data',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'row',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_5cabc2dde0bbc',
                                    'label' => 'Client',
                                    'name' => 'client',
                                    'type' => 'text',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_5cabbefda13f8',
                                    'label' => 'Product',
                                    'name' => 'product',
                                    'type' => 'text',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_5cabdd8357c6c',
                                    'label' => 'Price',
                                    'name' => 'price',
                                    'type' => 'text',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                    'maxlength' => '',
                                ),
                                array(
                                    'key' => 'field_5cabbf08a13f9',
                                    'label' => 'Interval',
                                    'name' => 'interval',
                                    'type' => 'group',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => 'reboot-reminder-hide-acf-label',
                                        'id' => '',
                                    ),
                                    'layout' => 'table',
                                    'sub_fields' => array(
                                        array(
                                            'key' => 'field_5cabc0aea13fb',
                                            'label' => 'Number',
                                            'name' => 'number',
                                            'type' => 'number',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'default_value' => '',
                                            'placeholder' => '',
                                            'prepend' => 'Repeat Every',
                                            'append' => '',
                                            'min' => 1,
                                            'max' => 24,
                                            'step' => '',
                                        ),
                                        array(
                                            'key' => 'field_5cabc0d9a13fd',
                                            'label' => 'Type',
                                            'name' => 'type',
                                            'type' => 'select',
                                            'instructions' => '',
                                            'required' => 0,
                                            'conditional_logic' => 0,
                                            'wrapper' => array(
                                                'width' => '',
                                                'class' => '',
                                                'id' => '',
                                            ),
                                            'choices' => array(
                                                'minute' => 'minute(s)',
                                                'hour' => 'hour(s)',
                                                'day' => 'day(s)',
                                                'week' => 'week(s)',
                                                'month' => 'month(s)',
                                                'year' => 'year(s)',
                                            ),
                                            'default_value' => array(
                                                0 => 'year',
                                            ),
                                            'allow_null' => 0,
                                            'multiple' => 0,
                                            'ui' => 0,
                                            'return_format' => 'value',
                                            'ajax' => 0,
                                            'placeholder' => '',
                                        ),
                                    ),
                                ),
                                array(
                                    'key' => 'field_5cabc185a13fe',
                                    'label' => 'Date',
                                    'name' => 'date',
                                    'type' => 'date_time_picker',
                                    'instructions' => '',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'display_format' => 'j F Y H:i:s',
                                    'return_format' => 'Y-m-d H:i:s',
                                    'first_day' => 1,
                                ),
                            ),
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'reminder',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'seamless',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                ));

                acf_add_local_field_group(array(
                    'key' => 'group_5cab9177d8117',
                    'title' => 'Reminder Settings',
                    'fields' => array(
                        array(
                            'key' => 'field_5cab9193c449b',
                            'label' => '',
                            'name' => 'reminder_settings',
                            'type' => 'group',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'layout' => 'block',
                            'sub_fields' => array(
                                array(
                                    'key' => 'field_5cab91b2c449c',
                                    'label' => 'Email',
                                    'name' => 'email',
                                    'type' => 'email',
                                    'instructions' => 'Email address to send notifications',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'placeholder' => '',
                                    'prepend' => '',
                                    'append' => '',
                                ),
                                array(
                                    'key' => 'field_5cab91dfc449d',
                                    'label' => 'Email Body',
                                    'name' => 'email_body',
                                    'type' => 'wysiwyg',
                                    'instructions' => 'Available variables:
<code>[title]</code>
<code>[product]</code>
<code>[start_date]</code>
<code>[interval]</code>
<code>[price]</code>',
                                    'required' => 0,
                                    'conditional_logic' => 0,
                                    'wrapper' => array(
                                        'width' => '',
                                        'class' => '',
                                        'id' => '',
                                    ),
                                    'default_value' => '',
                                    'tabs' => 'all',
                                    'toolbar' => 'full',
                                    'media_upload' => 1,
                                    'delay' => 0,
                                ),
                            ),
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'options_page',
                                'operator' => '==',
                                'value' => 'reminder-settings',
                            ),
                        ),
                    ),
                    'menu_order' => 0,
                    'position' => 'normal',
                    'style' => 'seamless',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                ));

                acf_add_local_field_group(array(
                    'key' => 'group_5cabc35881fae',
                    'title' => 'Reminder History',
                    'fields' => array(
                        array(
                            'key' => 'field_5cabc363d52dd',
                            'label' => 'Reminder History',
                            'name' => '',
                            'type' => 'message',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => 'reboot-reminder-hide-acf-label',
                                'id' => '',
                            ),
                            'message' => '[reboot_reminder_history]',
                            'new_lines' => '',
                            'esc_html' => 0,
                        ),
                    ),
                    'location' => array(
                        array(
                            array(
                                'param' => 'post_type',
                                'operator' => '==',
                                'value' => 'reminder',
                            ),
                        ),
                    ),
                    'menu_order' => 1,
                    'position' => 'normal',
                    'style' => 'default',
                    'label_placement' => 'top',
                    'instruction_placement' => 'label',
                    'hide_on_screen' => '',
                    'active' => true,
                    'description' => '',
                ));

            endif;
        }

        function reboot_reminder_history_render($atts)
        {
            global $post;

            if (!$post) {
                return;
            }

            $history = get_post_meta($post->ID, REBOOT_REMINDER_SLUG . '_history');

            if (empty($history)) {
                printf('<p>%s</p>', __('No history record found', REBOOT_REMINDER_TEXT_DOMAIN));
                return;
            }

            $history = array_reverse($history);

            ?>
            <table class="widefat striped fixed_">
                <tbody>
                <?php foreach ($history as $item) : ?>
                    <?php if (!is_array($item)) {
                        continue;
                    } ?>
                    <tr>
                        <td>
                            <strong><?= $item['date'] ?></strong>
                        </td>
                        <td>
                            <small><?= $item['message'] ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php
        }

        function acf_shortcode_support_for_message_field($field)
        {

            if (empty($field['message']) || (is_admin() && (!function_exists('get_current_screen') || (get_current_screen())->post_type === "acf-field-group"))) {
                return $field;
            }

            $field['message'] = do_shortcode($field['message']);

            return $field;

        }

        function admin_print_styles()
        {
            ?>
            <style>
                .acf-field:not(.acf-field-group).reboot-reminder-hide-acf-label > .acf-label {
                    display: none !important;
                }

                .acf-field-group.reboot-reminder-hide-acf-label > .acf-input > .acf-table > thead > tr > th {
                    display: none !important;
                }
            </style>
            <?php
        }

        function register_custom_post_types()
        {
            register_post_type(REBOOT_REMINDER_SLUG, [
                'menu_position' => 5,
                'public' => true,
                'menu_icon' => sprintf('dashicons-calendar', REBOOT_REMINDER_TEXT_DOMAIN), // 'dashicons-layout',
                'supports' => ['title'],

                'publicly_queryable' => false, // only admin
                'exclude_from_search' => true, // only admin
                'rewrite' => false, // only admin
                'query_var' => false, // only admin

                'labels' => self::get_post_type_labels(
                    __('Reminder', REBOOT_REMINDER_TEXT_DOMAIN),
                    __('Reminders', REBOOT_REMINDER_TEXT_DOMAIN)
                ),
            ]);
        }

        public static function get_post_type_labels($singular, $plural)
        {
            return [
                'name' => $plural,
                'singular_name' => $singular,
                // 'add_new' => __('Add New', REBOOT_REMINDER_TEXT_DOMAIN),
                'add_new_item' => sprintf(__('Add New %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'edit_item' => sprintf(__('Edit %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'new_item' => sprintf(__('Add New %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'view_item' => sprintf(__('View %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'view_items' => sprintf(__('View %s', REBOOT_REMINDER_TEXT_DOMAIN), $plural),
                'search_items' => sprintf(__('Search %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'not_found' => sprintf(__('No %s found', REBOOT_REMINDER_TEXT_DOMAIN), $plural),
                'not_found_in_trash' => sprintf(__('No %s found in Trash', REBOOT_REMINDER_TEXT_DOMAIN), $plural),
                'parent_item_colon' => sprintf(__('Parent %s:', REBOOT_REMINDER_TEXT_DOMAIN), $singular),

                // 'all_items' => sprintf(__('All %s', REBOOT_REMINDER_TEXT_DOMAIN), $plural),
                'all_items' => $plural,

                'archives' => sprintf(__('%s Archives', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'attributes' => sprintf(__('%s Attributes', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'insert_into_item' => sprintf(__('Insert into %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'uploaded_to_this_item' => sprintf(__('Uploaded to this %s', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                // 'featured_image' => __('Featured Image', REBOOT_REMINDER_TEXT_DOMAIN),
                // 'set_featured_image' => __('Set featured image', REBOOT_REMINDER_TEXT_DOMAIN),
                // 'remove_featured_image' => __('Remove featured image', REBOOT_REMINDER_TEXT_DOMAIN),
                // 'use_featured_image' => __('Use as featured image', REBOOT_REMINDER_TEXT_DOMAIN),
                // 'menu_name' => $plural,
                'filter_items_list' => sprintf(__('Filter %s list', REBOOT_REMINDER_TEXT_DOMAIN), $plural),
                'items_list_navigation' => sprintf(__('%s list navigation', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'items_list' => sprintf(__('%s list', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                // 'name_admin_bar' => $singular,
                'item_published' => sprintf(__('%s published.', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'item_published_privately' => sprintf(__('%s published privately.', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'item_reverted_to_draft' => sprintf(__('%s reverted to draft.', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'item_scheduled' => sprintf(__('%s scheduled.', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
                'item_updated' => sprintf(__('%s updated.', REBOOT_REMINDER_TEXT_DOMAIN), $singular),
            ];
        }

        function admin_notices()
        {
            if (!function_exists('acf_add_options_sub_page')) {
                ?>
                <div class="notice notice-warning">
                    <p><?= sprintf(
                            __('Reminder plugin needs the %s plugin! Please install it, first!', REBOOT_REMINDER_TEXT_DOMAIN),
                            sprintf(
                                '<a href="%s" target="_blank">%s</a>',
                                'https://www.advancedcustomfields.com/pro/',
                                __('Advanced Custom Fields PRO', REBOOT_REMINDER_TEXT_DOMAIN)
                            )
                        ) ?></p>
                </div>
                <?php
            }
        }

        function add_settings_menu()
        {
            if (function_exists('acf_add_options_sub_page')) {

                acf_add_options_sub_page(array(
                    'page_title' => __('Reminder Settings', REBOOT_REMINDER_TEXT_DOMAIN),
                    'menu_title' => __('Settings', REBOOT_REMINDER_TEXT_DOMAIN),
                    'capability' => 'manage_options',
                    'parent_slug' => sprintf('edit.php?post_type=%s', REBOOT_REMINDER_SLUG),
                    'menu_slug' => REBOOT_REMINDER_SLUG . '-settings',
                ));

            }
        }

        function add_dashboard_widgets()
        {
            wp_add_dashboard_widget(REBOOT_REMINDER_SLUG . '_dashboard_widget', REBOOT_REMINDER_TITLE, [$this, 'dashboard_widget_render']);
        }

        function dashboard_widget_render($post, $callback_args)
        {
            $reminders = $this->get_reminders( false );

            if (empty($reminders)) {
                echo __('No reminders found') . ' ' . sprintf('<a href="%s">%s</a>', admin_url('post-new.php?post_type=reminder'), __('Add one &raquo;', REBOOT_REMINDER_TEXT_DOMAIN));
                return;
            }

            printf('<a href="%s">%s</a>', admin_url('edit.php?post_type=' . REBOOT_REMINDER_SLUG), sprintf(__('%s reminders found'), count($reminders)));
        }

        public function get_reminders($only_available_to_process = true)
        {
            $args = [
                'post_type' => REBOOT_REMINDER_SLUG,
                'post_status' => 'publish',
                'posts_per_page' => -1,
            ];

            if ($only_available_to_process) {
                $args['meta_query'] = [
                    [
                        'type' => 'DATETIME',

                        'key' => 'reminder_data_first_reminder_date',
                        'compare' => '<=',
                        'value' => date_i18n("Y-m-d H:i:s"),
                    ]
                ];
            }

            return get_posts($args);
        }

        public function get_next_date($interval, $date = '')
        {
            if (empty($date)) {
                $date = date_i18n('Y-m-d H:i:s');
            }

            $increase = sprintf('+%s %s', $interval['number'], $interval['type']);
            $next_date = strtotime($increase, strtotime($date));
            return date_i18n('Y-m-d H:i:s', $next_date);
        }

        public function activate()
        {
            if (!wp_next_scheduled(self::$hook)) {
                wp_schedule_event(time(), self::$interval, self::$hook);
            }
        }

        public function deactivate()
        {
            $timestamp = wp_next_scheduled(self::$hook);
            wp_unschedule_event($timestamp, self::$hook);
        }

        public function exec()
        {
            $reminders = $this->get_reminders();

            if (empty($reminders)) {
                return;
            }

            foreach ($reminders as $reminder) {

                $data = get_field('reminder_data', $reminder->ID);

                if (empty($data['date'])) {
                    continue;
                }

                if (strtotime($data['date']) > strtotime(date_i18n('Y-m-d H:i:s'))) {
                    continue;
                }

                $settings = get_field('reminder_settings', 'option');

                $subject = sprintf(__('Reminder for "%s"', REBOOT_REMINDER_TEXT_DOMAIN), $reminder->post_title);

                $replace = [
                    '[title]' => $reminder->post_title,
                    '[client]' => $data['client'],
                    '[product]' => $data['product'],
                    '[price]' => $data['price'],
                    '[date]' => strtotime($data['date']),
                    '[interval]' => sprintf('%s %s(s)', $data['interval']['number'], $data['interval']['type']),
                ];
                $message = str_replace(
                    array_keys($replace),
                    array_values($replace),
                    $settings['email_body']
                );

                $headers = array('Content-Type: text/html; charset=UTF-8');
                wp_mail($settings['email'], $subject, $message, $headers);

                $next_date = $this->get_next_date($data['interval']);
                $next_data = array_merge($data, ['date' => $next_date]);
                update_field('reminder_data', $next_data, $reminder->ID);

                $log_date = date_i18n('Y-m-d H:i:s');
                $log_message = sprintf(__('A notification email sent to %s and the reminder scheduled again to %s', REBOOT_REMINDER_TEXT_DOMAIN), $settings['email'], $next_date);
                $log = [
                    'date' => $log_date,
                    'message' => $log_message,
                ];
                add_post_meta($reminder->ID, REBOOT_REMINDER_SLUG . '_history', $log);
            }
        }

        public function add_cron_interval($schedules)
        {
            $schedules[self::$interval] = array(
                'interval' => 5,
                'display' => esc_html__('Reboot Reminder Interval'),
            );

            return $schedules;
        }

        public static function getInstance()
        {
            if (static::$instance == null) {
                static::$instance = new static();
            }

            return static::$instance;
        }

        // prevent copy
        private function __clone()
        {
        }

        // prevent recreation with unserialize method
        private function __wakeup()
        {
        }
    }

    $reboot_reminder = REBOOT_REMINDER::getInstance();

}