<?php
/**
 * Uninstall
 *
 * Does delete the created tables and all the plugin options
 * when uninstalling the plugin
 *
 * @package Services For SureCart
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// global varibale.
global $wpdb;

$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_services' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_messages' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_requirements' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_activities' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_contracts' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_recurring_services' );
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'surelywp_sv_recurring_services_setting' );

// check if the plugin really gets uninstalled.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

// Remove general setting options.
delete_option( 'surelywp_sv_gen_settings_options' );

// Remove service setting options.
delete_option( 'surelywp_sv_settings_options' );

// Remove email templates options.
delete_option( 'surelywp_sv_email_templates_options' );

// Remove licensce.
delete_option( 'servicesforsurecart_license_options' );

// delete the plugin db version.
delete_option( 'surelywp_services_db_version' );
