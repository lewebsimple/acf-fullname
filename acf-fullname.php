<?php
/*
Plugin Name: Advanced Custom Fields: Full Name
Plugin URI: https://gitlab.ledevsimple.ca/wordpress/acf-fullname
Description: Full Name number field for ACF.
Version: 0.1.0
Author: Pascal Martineau <pascal@lewebsimple.ca>
Author URI: https://lewebsimple.ca
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_plugin_fullname' ) ) :

	class acf_plugin_fullname {

		function __construct() {
			$this->settings = array(
				'version' => '0.1.0',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);

			load_plugin_textdomain( 'acf-fullname', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) ); // v5
		}

		function include_field_types( $version = false ) {
			if ( ! $version ) {
				$version = 5;
			}
			include_once( 'fields/acf-fullname-v' . $version . '.php' );
		}

	}

	new acf_plugin_fullname();

endif;
