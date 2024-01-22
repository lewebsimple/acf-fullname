<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class lws_acf_field_fullname extends \acf_field {
	/**
	 * Controls field type visibilty in REST requests.
	 *
	 * @var bool
	 */
	public $show_in_rest = true;

	/**
	 * Environment values relating to the theme or plugin.
	 *
	 * @var array $env Plugin or theme context such as 'url' and 'version'.
	 */
	private $env;

	/**
	 * Gender prefixes
	 * 
	 * @var array $prefixes
	 */
	public $prefixes;

	/**
	 * Constructor.
	 */
	public function __construct() {
		/**
		 * Field type reference used in PHP and JS code.
		 * No spaces. Underscores allowed.
		 */
		$this->name = 'fullname';

		/**
		 * Field type label.
		 */
		$this->label = __( 'Full name', 'acf-fullname' );

		/**
		 * The category the field appears within in the field type picker.
		 */
		$this->category = 'basic'; // basic | content | choice | relational | jquery | layout | CUSTOM GROUP NAME

		/**
		 * Field type Description.
		 */
		$this->description = __( 'Full name field for ACF', 'acf-fullname' );

		/**
		 * Field type Doc URL.
		 *
		 * For linking to a documentation page. Displayed in the field picker modal.
		 */
		$this->doc_url = '';

		/**
		 * Field type Tutorial URL.
		 *
		 * For linking to a tutorial resource. Displayed in the field picker modal.
		 */
		$this->tutorial_url = '';

		/**
		 * Defaults for your custom user-facing settings for this field type.
		 */
		$this->defaults = array(
			'return_format' => 'last_first',
		);

		/**
		 * Strings used in JavaScript code.
		 *
		 * Allows JS strings to be translated in PHP and loaded in JS via:
		 *
		 * ```js
		 * const errorMessage = acf._e("fullname", "error");
		 * ```
		 */
		$this->l10n = array(
			//'error'	=> __( 'Error! Please enter a higher value', 'acf-fullname' ),
		);

		$this->env = array(
			'url'     => site_url( str_replace( ABSPATH, '', __DIR__ ) ),
			'version' => '2.0.0',
		);

		$this->prefixes = array(
			'-'   => '',
			'Mr'  => __( "Mr.", 'acf-fullname' ),
			'Mrs' => __( "Mrs.", 'acf-fullname' ),
			'Mx'  => __( "Mx.", 'acf-fullname' ),
		);

		parent::__construct();
	}

	/**
	 * Settings to display when users configure a field of this type.
	 *
	 * These settings appear on the ACF “Edit Field Group” admin page when
	 * setting up the field.
	 *
	 * @param array $field
	 * @return void
	 */
	public function render_field_settings( $field ) {
		/*
		 * Repeat for each setting you wish to display for this field type.
		 */
		acf_render_field_setting(
			$field,
			array(
				'label'			=> __( 'Return format','acf-fullname' ),
				'instructions'	=> __( 'Specify the return format used in the template','acf-fullname' ),
				'type'			=> 'select',
				'name'			=> 'return_format',
				'choices'      => array(
					'first_last'        => __( "First Last", 'acf-fullname' ),
					'last_first'        => __( "Last, First", 'acf-fullname' ),
					'prefix_first_last' => __( "Prefix First Last", 'acf-fullname' ),
					'array'             => __( "Values (array)", 'acf-fullname' ),
				),
			)
		);

		// To render field settings on other tabs in ACF 6.0+:
		// https://www.advancedcustomfields.com/resources/adding-custom-settings-fields/#moving-field-setting
	}

	/**
	 * HTML content to show when a publisher edits the field on the edit screen.
	 *
	 * @param array $field The field settings and values.
	 * @return void
	 */
	public function render_field( $field ) {

		?>
		<div class="acf-input-wrap acf-fullname">
			<div class="form-group prefix">
				<label for="prefix"><?= __( "Prefix", 'acf-fullname' ) ?></label>
				<select id="prefix" name="<?= $field['name'] ?>[prefix]" class="form-control">
					<?php foreach ( $this->prefixes as $key => $label ) : ?>
						<option value="<?= $key ?>" <?= ($key === $field['value']['prefix'] ?? '' ) ? 'selected' : '' ?>>
							<?= $label ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="form-group first">
				<label for="first"><?= __( "First name", 'acf-fullname' ) ?></label>
				<input id="first" type="text" name="<?= $field['name'] ?>[first]" class="form-control"
							value="<?= $field['value']['first'] ?? '' ?>"
							placeholder="<?= __( "First name", 'acf-fullname' ) ?>"/>
			</div>
			<div class="form-group last">
				<label for="last"><?= __( "Last name", 'acf-fullname' ) ?></label>
				<input id="last" type="text" name="<?= $field['name']  ?>[last]" class="form-control"
							value="<?= $field['value']['last'] ?? '' ?>"
							placeholder="<?= __( "Last name", 'acf-fullname' ) ?>"/>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueues CSS and JavaScript needed by HTML in the render_field() method.
	 *
	 * Callback for admin_enqueue_script.
	 *
	 * @return void
	 */
	public function input_admin_enqueue_scripts() {
		$url     = trailingslashit( $this->env['url'] );
		$version = $this->env['version'];
		wp_register_style(
			'acf-fullname',
			"{$url}assets/css/acf-fullname.css",
			array( 'acf-input' ),
			$version
		);
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
			'prefix' => '-',
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
				$prefix = $this->prefixes[ $value['prefix'] ] ?? '';
				return trim("{$prefix} {$value['first']} {$value['last']}");
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
		if ( preg_match( "/[^[:alpha:] \-.\']/u", stripslashes( $value['first'] ) ) ||
				preg_match( "/[^[:alpha:] \-.\']/u", stripslashes( $value['last'] ) )
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
