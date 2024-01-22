<?php
/**
 * Plugin Name:     ACF Fullname
 * Plugin URI:      https://github.com/lewebsimple/acf-fullname
 * Description:     Full name field for Advanced Custom Fields.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://websimple.com
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     acf-fullname
 * Domain Path:     /languages
 * Version:         2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'lws_include_acf_field_fullname' );
/**
 * Registers the ACF field type.
 */
function lws_include_acf_field_fullname() {
	if ( ! function_exists( 'acf_register_field_type' ) ) {
		return;
	}

	require_once __DIR__ . '/class-lws-acf-field-fullname.php';

	acf_register_field_type( 'lws_acf_field_fullname' );
}
