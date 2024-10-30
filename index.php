<?php

/**
 * @package HOT
 * @version 0.1
 */
/*
Plugin Name: Chat life for telegram
Description: Allows you to make a chat on the site and answer online via Telegram chat
Author: Pechenki
Version: 0.1.2
Author URI: https://pechenki.top/
*/
//////////////////////////////////
if (!defined('ABSPATH')) {
    exit;
}

define('TCL_DIR', plugin_dir_path(__FILE__));
define('TCL_DIR_NAME', dirname(plugin_basename(__FILE__)));
define('TCL_URL_PLUGIN',  plugin_dir_url(__FILE__));

require_once(TCL_DIR . 'autoload.php');

use pechenki\ChatLifeForTelegram\init;

$telegramChatLife = init::get_instance();

register_activation_hook(__FILE__, 'tcl_plugin_activation');
/**
 * @return void
 */
function tcl_plugin_activation()
{
    global $wpdb;
    $table_name = $wpdb->get_blog_prefix() . 'tcl_chat';
    if (in_array($table_name, $wpdb->tables))  return;
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $sql = "CREATE TABLE {$table_name} (
        id int(11) unsigned NOT NULL auto_increment,
        data longtext NOT NULL default '',
        name varchar(255) NULL default '',
        message_id int(25) NULL,
        user_id int(25) NULL,
        reply_to_message_id int(25) NULL ,
        chat_id varchar(255) NULL ,
        meta_data varchar(5000) NULL default '',
        session_id varchar(255) NOT NULL default '',
        position int(2) NULL,
        create_at int(25),
        PRIMARY KEY (id)       
    ) {$charset_collate};";
        dbDelta($sql);
    }
}
