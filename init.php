<?php

/**
 * @package Poster
 */

/*
Plugin Name: Poster
Description: share posts
Version: 1.0.0
Author: Kamran Kamilli
License: GPLv2 or later
Text Domain: Poster
*/

//for security purpose
defined('ABSPATH') or die('Dont try access to this file!');

include 'include/VKsettings.php';
include 'include/callbacks/Callbacks.php';




if (!class_exists('VKposter')) {

    class VKposter
    {

        public $settings;

        public $pages = array();

        public $sub_pages = array();

        public function __construct()
        {
            //instance of settings class
            $this->settings = new VKSettings();

            $this->callbacks = new Callbacks();

            //custom column
            add_filter('manage_post_posts_columns', function ($columns) {
                return array_merge($columns, ['share' => __('Share')]);
            });

            add_action('manage_post_posts_custom_column', function ($column_key, $post_id) {
                if ($column_key == 'share') {

                    echo '<a href = "admin.php?page=poster_share&id=' . '"style="color:red;">Share</a>';
                }
            }, 10, 2);

            add_filter('manage_product_posts_columns', function ($columns) {
                return array_merge($columns, ['share' => __('Share')]);
            });

            add_action('manage_product_posts_custom_column', function ($column_key, $post_id) {
                if ($column_key == 'share') {

                    echo '<a href = "admin.php?page=poster_share'.' "style="color:red;">Share</a>';
                }
            }, 10, 2);

            add_action('add_meta_boxes', array($this->callbacks, 'register_post_meta_boxes'));
            add_action('add_meta_boxes', array($this->callbacks, 'register_product_meta_boxes'));

            //creating array of menus
            $this->pages = array(
                array(
                    'page_title' => 'Poster Plugin',
                    'menu_title' => 'Poster',
                    'capability' => 'manage_options',
                    'menu_slug' => 'poster_plugin',
                    'callback' => array($this->callbacks, 'admin_page'),
                    'icon_url' => plugins_url('VKposter/images/poster.png'),
                    'position' => null
                )
            );
            //creating array of submenus
            $this->sub_pages = array(
                array(
                    'parent_slug' => 'poster_plugin',
                    'page_title' => 'Settings',
                    'menu_title' => 'Settings',
                    'capability' => 'manage_options',
                    'menu_slug' => 'poster_settings',
                    'callback' => array($this->callbacks, 'admin_page_settings'),
                    'position' => null
                ),
                array(
                    'parent_slug' => 'poster_plugin',
                    'page_title' => 'Accounts',
                    'menu_title' => 'Accounts',
                    'capability' => 'manage_options',
                    'menu_slug' => 'poster_accounts',
                    'callback' => array($this->callbacks, 'admin_page_accounts'),
                    'position' => null
                ),
                array(
                    'parent_slug' => 'poster_plugin',
                    'page_title' => 'Share',
                    'menu_title' => 'Share',
                    'capability' => 'manage_options',
                    'menu_slug' => 'poster_share',
                    'callback' => array($this->callbacks, 'admin_page_share'),
                    'position' => null
                )
            );
        }
        function register()
        {

            //creating menu and submenus

            $this->settings->addPages($this->pages)->subPage('Dashboard')->addSubPages($this->sub_pages)->register();
        }

        function activate()
        {
            $this->create_plugin_database_table();
            require_once plugin_dir_path(__FILE__) . 'include/VKposter_activate.php';
            VKposterActivate::activate();
        }



        function create_plugin_database_table()
        {
            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();
            $okusers = $wpdb->prefix . 'okusers';
            $okgroups  = $wpdb->prefix . 'okgroups';
            $oksharedposts  = $wpdb->prefix . 'oksharedposts';
            $oksharedproducts = $wpdb->prefix . 'oksharedproducts';
            $vkusers = $wpdb->prefix . 'vkusers';
            $vkcommunities = $wpdb->prefix . 'vkcommunities';
            $vksharedposts = $wpdb->prefix . 'vksharedposts';
            $vksharedproducts = $wpdb->prefix . 'vksharedproducts';

            // Check to see if the table exists already, if not, then create it
            if ($wpdb->get_var("show tables like '$okusers'") != $okusers) {

                $sql = "CREATE TABLE $okusers (
                id BIGINT NOT NULL PRIMARY KEY,
                first_name varchar(255) NOT NULL,
                last_name varchar(255) NOT NULL,
                is_active BOOLEAN NOT NULL,
                token varchar(255) NOT NULL,
                session_key varchar(255) NOT NULL
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            if ($wpdb->get_var("show tables like '$okgroups'") != $okgroups) {
                $sql = "CREATE TABLE $okgroups (
                    group_id BIGINT NOT NULL PRIMARY KEY,
                    user_id BIGINT NOT NULL,
                    group_name varchar(255) NOT NULL,
                    is_active BOOLEAN NOT NULL,
                    FOREIGN KEY (user_id) REFERENCES $okusers(id)
                    ON DELETE CASCADE
                    ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            if ($wpdb->get_var("show tables like '$oksharedposts'") != $oksharedposts) {

                $sql = "CREATE TABLE $oksharedposts(
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                shared_post_id int(11) NOT NULL,
                user_id BIGINT NULL,
                group_id BIGINT NULL,
                title varchar(255) NOT NULL,
                content varchar(255) NOT NULL,
                link varchar(255) NOT NULL,
                shared_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                FOREIGN KEY (user_id) REFERENCES $okusers(id)
                ON DELETE CASCADE,
                FOREIGN KEY (group_id) REFERENCES $okgroups(group_id)
                ON DELETE CASCADE
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            if ($wpdb->get_var("show tables like '$oksharedproducts'") != $oksharedproducts) {

                $sql = "CREATE TABLE $oksharedproducts(
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                shared_product_id int(11) NOT NULL,
                user_id BIGINT NULL,
                group_id BIGINT NULL,
                title varchar(255) NOT NULL,
                content varchar(255) NOT NULL,
                link varchar(255) NOT NULL,
                shared_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                FOREIGN KEY (user_id) REFERENCES $okusers(id)
                ON DELETE CASCADE,
                FOREIGN KEY (group_id) REFERENCES $okgroups(group_id)
                ON DELETE CASCADE
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
            if ($wpdb->get_var("show tables like '$vkusers'") != $vkusers) {

                $sql = "CREATE TABLE $vkusers (
                id int NOT NULL PRIMARY KEY,
                first_name varchar(255) NOT NULL,
                last_name varchar(255) NOT NULL,
                is_active BOOLEAN NOT NULL,
                token varchar(255) NOT NULL
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {

                $row = $wpdb->get_row("SELECT * FROM $vkusers");

                if (!isset($row->is_active)) {
                    $wpdb->query("ALTER TABLE $vkusers ADD is_active BOOLEAN NOT NULL DEFAULT 1");
                }
            }
            if ($wpdb->get_var("show tables like '$vkcommunities'") != $vkcommunities) {

                $sql = "CREATE TABLE $vkcommunities (
                community_id int(11) NOT NULL PRIMARY KEY,
                user_id int NOT NULL,
                community_name varchar(255) NOT NULL,
                community_type varchar(255) NOT NULL,
                is_active BOOLEAN NOT NULL,
                FOREIGN KEY (user_id) REFERENCES $vkusers(id)
                ON DELETE CASCADE
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {
                $row = $wpdb->get_row("SELECT * FROM $vkcommunities");

                if (!isset($row->is_active)) {
                    $wpdb->query("ALTER TABLE $vkcommunities ADD is_active BOOLEAN NOT NULL DEFAULT 1");
                }
            }
            if ($wpdb->get_var("show tables like '$vksharedposts'") != $vksharedposts) {

                $sql = "CREATE TABLE $vksharedposts(
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                shared_post_id int(11) NOT NULL,
                user_id int(11),
                community_id int(11),
                title varchar(255) NOT NULL,
                content varchar(255) NOT NULL,
                link varchar(255) NOT NULL,
                shared_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                FOREIGN KEY (user_id) REFERENCES $vkusers(id)
                ON DELETE CASCADE,
                FOREIGN KEY (community_id) REFERENCES $vkcommunities(community_id)
                ON DELETE CASCADE
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {
                $row = $wpdb->get_row("SELECT * FROM $vksharedposts");

                if (!isset($row->community_id)) {
                    $wpdb->query("ALTER TABLE $vksharedposts MODIFY user_id int(11) NULL");
                    $wpdb->query("ALTER TABLE $vksharedposts ADD community_id int(11) AFTER user_id");
                    $wpdb->query("ALTER TABLE $vksharedposts ADD FOREIGN KEY (community_id) REFERENCES $vkcommunities (community_id) ON DELETE CASCADE");
                }
            }
            if ($wpdb->get_var("show tables like '$vksharedproducts'") != $vksharedproducts) {

                $sql = "CREATE TABLE $vksharedproducts(
                id int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
                shared_product_id int(11) NOT NULL,
                user_id int(11),
                community_id int(11),
                title varchar(255) NOT NULL,
                content varchar(255) NOT NULL,
                link varchar(255) NOT NULL,
                shared_time timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
                FOREIGN KEY (user_id) REFERENCES $vkusers(id)
                ON DELETE CASCADE,
                FOREIGN KEY (community_id) REFERENCES $vkcommunities(community_id)
                ON DELETE CASCADE
                ) $charset_collate;";
                require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
                dbDelta($sql);
            } else {
                $row = $wpdb->get_row("SELECT * FROM $vksharedproducts");

                if (!isset($row->community_id)) {
                    $wpdb->query("ALTER TABLE $vksharedproducts MODIFY user_id int(11) NULL");
                    $wpdb->query("ALTER TABLE $vksharedproducts ADD community_id int(11) AFTER user_id");
                    $wpdb->query("ALTER TABLE $vksharedproducts ADD FOREIGN KEY (community_id) REFERENCES $vkcommunities (community_id) ON DELETE CASCADE");
                }
            }
        }
    }

    $vk = new VKposter();
    $vk->register();


    //activation
    register_activation_hook(__FILE__, array($vk, 'activate'));

    //deactivation
    require_once plugin_dir_path(__FILE__) . 'include/VKposter_deactivate.php';
    register_deactivation_hook(__FILE__, array('VKposterDeactivate', 'deactivate'));
}
