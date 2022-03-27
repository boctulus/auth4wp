<?php

# INSTALLER

global $wpdb;

$table_name = $wpdb->prefix . "enqueued_mails";
$my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {

    $sql = "CREATE TABLE $table_name (
            `id` INT(11) NOT NULL,
            `data` TEXT NOT NULL DEFAULT '',
            `tries` INT(11) DEFAULT 0,  
            `last_try_at` DATETIME DEFAULT '0000-00-00 00:00:00',
            `created_at` DATETIME DEFAULT NOW(),
            `locked_at` DATETIME DEFAULT '0000-00-00 00:00:00',
            `expiration_at` DATETIME NOT NULL
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $ok = dbDelta($sql);

    if (!$ok){
        return;
    }

    $ok = $wpdb->query("ALTER TABLE `$table_name`
    ADD PRIMARY KEY (`id`);");

    if (!$ok){
        return;
    }

    $ok = $wpdb->query("ALTER TABLE `$table_name`
    MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;");     

    add_option('reactor_db_version', $my_products_db_version);
}
