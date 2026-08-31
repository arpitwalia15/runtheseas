<?php
// Runs when the plugin is DELETED from wp-admin (not on deactivate). Removes roles/caps, cron and options.
// Custom tables are preserved by default (data safety). Define RTS_UNINSTALL_DROP_TABLES true in wp-config.php to drop them.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }
foreach ( array( 'rts_super_admin', 'rts_administrator', 'rts_content_editor', 'rts_contributor' ) as $r ) { remove_role( $r ); }
$admin = get_role( 'administrator' ); if ( $admin ) { foreach ( array( 'rts_view', 'rts_manage', 'rts_send_bulk', 'rts_manage_admins', 'rts_system', 'rts_content' ) as $c ) { $admin->remove_cap( $c ); } }
foreach ( array( 'rts_cron_campaign_triggers', 'rts_cron_scheduled_reports', 'rts_cron_action_items', 'rts_cron_fr_sync' ) as $h ) { wp_clear_scheduled_hook( $h ); }
foreach ( array( 'rts_settings', 'rts_db_version', 'rts_site_offline', 'rts_site_offline_at', 'rts_site_offline_by', 'rts_cron_last_campaign_triggers', 'rts_cron_last_scheduled_reports', 'rts_cron_last_action_items', 'rts_cron_last_fr_sync' ) as $o ) { delete_option( $o ); }
if ( defined( 'RTS_UNINSTALL_DROP_TABLES' ) && RTS_UNINSTALL_DROP_TABLES ) {
	global $wpdb;
	foreach ( $wpdb->get_col( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $wpdb->prefix . 'rts_' ) . '%' ) ) as $t ) { $wpdb->query( "DROP TABLE IF EXISTS `$t`" ); }
}
