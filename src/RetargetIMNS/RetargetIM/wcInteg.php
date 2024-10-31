<?php
/**
 * Integration .
 *
 * @package  wcInteg
 * @category Integration
 * @author   Hadar Shpivak
 */
if ( ! class_exists( 'wcInteg' ) ) :
class wcInteg extends WC_Integration {
	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		global $woocommerce;
		$this->id                 = 'wcInteg';
		$this->method_title       = __( 'wcInteg', 'woocommerce-integration' );
		$this->method_description = __( 'An integration to extend WooCommerce.', 'woocommerce-integration' );
		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();
		// Define user set variables.
		$this->api_key          = $this->get_option( 'api_key' );
		$this->debug            = $this->get_option( 'debug' );
		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( $this, 'process_admin_options' ) );
	}
	/**
	 * Initialize integration settings form fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'api_key' => array(
				'title'             => __( 'API_Key', 'woocommerce-integration' ),
				'type'              => 'text',
				'description'       => __( 'ck_38e512ccd6d470fbbd3deedc9c0853458c50c79b', 'woocommerce-integration' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'debug' => array(
				'title'             => __( 'Debug Log', 'woocommerce-integration' ),
				'type'              => 'checkbox',
				'label'             => __( 'Enable logging', 'woocommerce-integration' ),
				'default'           => 'no',
				'description'       => __( 'Log events such as API requests', 'woocommerce-integration' ),
			),
		);
	}
}
endif;