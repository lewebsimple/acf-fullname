<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_fullname_field' ) ) :

	class acf_fullname_field extends acf_field {

		public $settings;

		function __construct( $settings ) {
			$this->name     = 'fullname';
			$this->label    = __( 'Full name', 'acf-fullname' );
			$this->category = 'basic';
			$this->defaults = array(
				'return_format' => 'last_first',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Render full name field settings
		 *
		 * @param $field (array) the $field being edited
		 */
		function render_field_settings( $field ) {
			// Return Format
			acf_render_field_setting( $field, array(
				'label'        => __( 'Return format', 'acf-fullname' ),
				'instructions' => __( 'Specify the return format used in the template.', 'acf-fullname' ),
				'type'         => 'select',
				'choices'      => array(
					'first_last'        => __( "First Last", 'acf-fullname' ),
					'last_first'        => __( "Last, First", 'acf-fullname' ),
					'prefix_first_last' => __( "Prefix First Last", 'acf-fullname' ),
					'array'             => __( "Values (array)", 'acf-fullname' ),
				),
				'name'         => 'return_format',
			) );
		}

		/**
		 * Render full name field input
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			$name  = $field['name'];
			$value = $field['value'];
			?>
            <div class="acf-input-wrap acf-fullname">
                <div class="form-group prefix">
                    <label for="prefix"><?= __( "Prefix", 'acf-fullname' ) ?></label>
                    <select id="prefix" name="<?= $name ?>[prefix]" class="form-control">
						<?php foreach ( acf_fullname_plugin::get_prefix() as $key => $label ): ?>
                            <option value="<?= $key; ?>" <?= ( $key === $value['prefix'] ) ? 'selected' : '' ?>>
								<?= $label ?>
                            </option>
						<?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group first">
                    <label for="first"><?= __( "First name", 'acf-fullname' ) ?></label>
                    <input id="first" type="text" name="<?= $name ?>[first]" class="form-control"
                           value="<?= esc_attr( $value['first'] ) ?>"/>
                </div>
                <div class="form-group last">
                    <label for="last"><?= __( "Last name", 'acf-fullname' ) ?></label>
                    <input id="last" type="text" name="<?= $name ?>[last]" class="form-control"
                           value="<?= esc_attr( $value['last'] ) ?>"/>
                </div>
            </div>
			<?php
		}

		/**
		 * Enqueue input scripts and styles
		 */
		function input_admin_enqueue_scripts() {
			$url     = $this->settings['url'];
			$version = $this->settings['version'];

			wp_register_style( 'acf-fullname', "{$url}assets/css/acf-fullname.css", array( 'acf-input' ), $version );
			wp_enqueue_style( 'acf-fullname' );
		}

		/**
		 * Load value from database
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function load_value( $value, $post_id, $field ) {
			// Value looks like "Last|First|prefix"
			$parts = explode( '|', $value );

			return count( $parts ) !== 3 ? array(
				'prefix' => '',
				'first'  => '',
				'last'   => '',
			) : array(
				'prefix' => $parts[2],
				'first'  => trim( $parts[1] ),
				'last'   => trim( $parts[0] ),
			);
		}

		/**
		 * Update value to database
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function update_value( $value, $post_id, $field ) {
			return trim( $value['last'] ) . '|' . trim( $value['first'] ) . '|' . $value['prefix'];
		}

		/**
		 * Format full name value according to field settings
		 *
		 * @param  $value (mixed) the value which was loaded from the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value (mixed) the formatted value
		 */
		function format_value( $value, $post_id, $field ) {
			if ( empty( $value ) ) {
				return $value;
			}

			switch ( $field['return_format'] ) {
				case 'first_last':
					return $value['first'] . ' ' . $value['last'];

				case 'last_first':
					return $value['last'] . ', ' . $value['first'];

				case 'prefix_first_last':
					return acf_fullname_plugin::get_prefix( $value['prefix'] ) . ' ' . $value['first'] . ' ' . $value['last'];

				case 'array':
				default:
					return $value;
			}
		}

		/**
		 * Validate full name value
		 *
		 * @param  $valid (boolean) validation status based on the value and the field's required setting
		 * @param  $value (mixed) the $_POST value
		 * @param  $field (array) the field array holding all the field options
		 * @param  $input (string) the corresponding input name for $_POST value
		 *
		 * @return $valid
		 */
		function validate_value( $valid, $value, $field, $input ) {
			// Check for illegal characters
			if ( preg_match( "/[^[:alpha:]-.’ \']/u", stripslashes( $value['first'] ) ) ||
			     preg_match( "/[^[:alpha:]-.’ \']/u", stripslashes( $value['last'] ) )
			) {
				$valid = __( "Illegal characters in first or last name", 'acf-fullname' );
			}
			// Check for empty values when field is required
			if ( $field['required'] ) {
				if ( empty( trim( $value['first'] ) ) || empty( trim( $value['last'] ) ) ) {
					$valid = __( "First and last names are required.", 'acf-fullname' );
				}
			}

			return $valid;
		}

	}

	new acf_fullname_field( $this->settings );

endif;
