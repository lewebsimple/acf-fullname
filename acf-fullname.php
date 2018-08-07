<?php
/**
 * Plugin Name:     ACF Fullname
 * Plugin URI:      https://github.com/lewebsimple/acf-fullname
 * Description:     Full name field for Advanced Custom Fields v5.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://lewebsimple.ca
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     acf-fullname
 * Domain Path:     /languages
 * Version:         1.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_fullname_plugin' ) ) :

	class acf_fullname_plugin {

		function __construct() {
			$this->settings = array(
				'version' => '1.0.1',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) );
		}

		function include_field_types( $version ) {
			load_plugin_textdomain( 'acf-fullname', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			include_once( 'fields/class-acf-fullname-v5.php' );
		}

		/**
		 * Helper for prefix definition
		 *
		 * @param string $value Value to
		 *
		 * @return array|mixed
		 */
		static function get_prefix( $value = '' ) {
			$prefixes = array(
				'-'   => '',
				'Mr'  => __( "Mr.", 'acf-fullname' ),
				'Mrs' => __( "Mrs.", 'acf-fullname' ),
				'Mx'  => __( "Mx.", 'acf-fullname' ),
			);
			if ( ! empty( $value ) ) {
				if ( isset( $prefixes[ $value ] ) ) {
					return $prefixes[ $value ];
				} else {
					return '';
				}
			}

			return $prefixes;
		}

	}

	new acf_fullname_plugin();

endif;
