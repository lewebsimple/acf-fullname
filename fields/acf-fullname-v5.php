<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_field_fullname' ) ) :

	class acf_field_fullname extends acf_field {

		public $settings;

		/**
		 * acf_field_fullname constructor.
		 *
		 * This function will setup the field type data
		 *
		 * @param $settings (array) The plugin settings
		 */
		function __construct( $settings ) {
			$this->name     = 'fullname';
			$this->label    = __( 'Full Name', 'acf-fullname' );
			$this->category = 'basic';
			$this->defaults = array(
				'return_format' => 'last_first',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Create extra settings for your field. These are visible when editing a field
		 *
		 * @param $field (array) the $field being edited
		 */
		function render_field_settings( $field ) {
			// Return Format
			acf_render_field_setting( $field, array(
				'label'        => __( 'Return Format', 'acf-fullname' ),
				'instructions' => __( 'Specify the value returned in the template.', 'acf-fullname' ),
				'type'         => 'select',
				'choices'      => array(
					'first_last' => __( "First Last", 'acf-fullname' ),
					'last_first' => __( "Last, First", 'acf-fullname' ),
					'array'      => __( "Values (array)", 'acf-fullname' ),
				),
				'name'         => 'return_format',
			) );
		}

		/**
		 * Create the HTML interface for your field
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			?>
            <div class="acf-fullname">
                <div class="form-group first">
                    <label for="first"><?= __( "First Name", 'acf-fullname' ) ?></label>
                    <input id="first" type="text" name="<?= $field['name'] ?>[first]"
                           value="<?= esc_attr( $field['value']['first'] ) ?>"/>
                </div>
                <div class="form-group last">
                    <label for="last"><?= __( "Last Name", 'acf-fullname' ) ?></label>
                    <input id="last" type="text" name="<?= $field['name'] ?>[last]"
                           value="<?= esc_attr( $field['value']['last'] ) ?>"/>
                </div>
            </div>
			<?php
		}

		/**
		 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
		 *  Use this action to add CSS + JavaScript to assist your render_field() action.
		 */
		function input_admin_enqueue_scripts() {
			$url     = $this->settings['url'];
			$version = $this->settings['version'];
			wp_register_script( 'acf-input-fullname', "{$url}assets/js/input.js", array( 'acf-input' ), $version );
			wp_enqueue_script( 'acf-input-fullname' );

			wp_register_style( 'acf-fullname', "{$url}assets/css/acf-fullname.css", array( 'acf-input' ), $version );
			wp_enqueue_style( 'acf-fullname' );
		}

		/**
		 *  This action is called in the admin_head action on the edit screen where your field is created.
		 *  Use this action to add CSS and JavaScript to assist your render_field() action.
		 */
		function input_admin_head() {
		}

		/**
		 *  This function is called once on the 'input' page between the head and footer
		 *  There are 2 situations where ACF did not load during the 'acf/input_admin_enqueue_scripts' and
		 *  'acf/input_admin_head' actions because ACF did not know it was going to be used. These situations are
		 *  seen on comments / user edit forms on the front end. This function will always be called, and includes
		 *  $args that related to the current screen such as $args['post_id']
		 *
		 * @param $args
		 */
		function input_form_data( $args ) {
		}


		/**
		 *  This action is called in the admin_footer action on the edit screen where your field is created.
		 *  Use this action to add CSS and JavaScript to assist your render_field() action.
		 */
		function input_admin_footer() {
		}

		/**
		 *  This action is called in the admin_enqueue_scripts action on the edit screen where your field is edited.
		 *  Use this action to add CSS + JavaScript to assist your render_field_options() action.
		 */
		function field_group_admin_enqueue_scripts() {
		}

		/**
		 *  This action is called in the admin_head action on the edit screen where your field is edited.
		 *  Use this action to add CSS and JavaScript to assist your render_field_options() action.
		 */
		function field_group_admin_head() {
		}

		/**
		 * This filter is applied to the $value after it is loaded from the db
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function load_value( $value, $post_id, $field ) {
			$parts = explode( '|', $value );

			return array(
				'first' => $parts[1],
				'last'  => $parts[0],
			);
		}

		/**
		 * This filter is applied to the $value before it is saved in the db
		 *
		 * @param  $value (mixed) the value found in the database
		 * @param  $post_id (mixed) the $post_id from which the value was loaded
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return $value
		 */
		function update_value( $value, $post_id, $field ) {
			return $value['last'] . '|' . $value['first'];
		}

		/**
		 * This filter is appied to the $value after it is loaded from the db and before it is returned to the template
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

				case 'array':
				default:
					return $value;
			}
		}

		/**
		 *  This filter is used to perform validation on the value prior to saving.
		 *  All values are validated regardless of the field's required setting. This allows you to validate and return
		 *  messages to the user if the value is not correct
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
				$valid = __( "Illegal characters", 'acf-fullname' );
			}
			// Check for empty values when field is required
			if ( $field['required'] ) {
				if ( empty( $value['first'] ) || empty( $value['last'] ) ) {
					$valid = __( "First and last names are required.", 'acf-fullname' );
				}
			}

			return $valid;
		}

		/**
		 *  This action is fired after a value has been deleted from the db.
		 *  Please note that saving a blank value is treated as an update, not a delete
		 *
		 * @param  $post_id (mixed) the $post_id from which the value was deleted
		 * @param  $key (string) the $meta_key which the value was deleted
		 */
		function delete_value( $post_id, $key ) {
		}

		/**
		 *  This filter is applied to the $field after it is loaded from the database
		 *
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return  $field
		 */
		function load_field( $field ) {
			return $field;
		}

		/**
		 *  This filter is applied to the $field before it is saved to the database
		 *
		 * @param  $field (array) the field array holding all the field options
		 *
		 * @return  $field
		 */
		function update_field( $field ) {
			return $field;
		}


		/**
		 *  This action is fired after a field is deleted from the database
		 *
		 * @param  $field (array) the field array holding all the field options
		 */
		function delete_field( $field ) {
		}

	}

	new acf_field_fullname( $this->settings );

endif;
