<?php

/**
 * @package Poster
 */

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}


global $wpdb;
$vkusers = $wpdb->prefix . 'vkusers';
$sql = "DROP TABLE IF EXISTS $vkusers";
$wpdb->query($sql);
