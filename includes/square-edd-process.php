<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Square_EDD_Process' ) ) {

	class Square_EDD_Process {

		public $gateway_id = 'square';

		function __construct() {
			add_filter( 'edd_settings_sections_gateways', array( $this, 'edd_register_square_gateway_section' ), 5, 1 );
			add_filter( 'edd_settings_gateways', array( $this, 'edd_register_square_gateway_settings' ), 5, 1 );
			add_filter( 'edd_accepted_payment_icons', array(
				$this,
				'edd_register_square_accepted_payment_settings'
			), 5, 1 );
			add_filter( 'edd_accepted_payment_square_image', array( $this, 'square_edd_image' ) );
		}

		public function edd_register_square_gateway_section( $gateway_sections ) {
			$gateway_sections['square'] = __( 'Square', 'square-edd' );

			return $gateway_sections;
		}

		function edd_register_square_gateway_settings( $gateway_settings ) {
			$square_settings = array(
				'square_settings' => array(
					'id'   => 'square_settings',
					'name' => '<strong>' . __( 'Square Settings', 'square-edd' ) . '</strong>',
					'type' => 'header',
				),
			);
			$square_mod_desc = sprintf( __( 'Get square account key from <a href="%s" target="_blank">here</a> ', 'square-edd' ), 'https://connect.squareup.com/apps' );

			$square_settings['square_mode']             = array(
				'id'      => 'square_mode',
				'name'    => __( 'Mode', 'square-edd' ),
				'desc'    => $square_mod_desc,
				'type'    => 'radio',
				'std'     => 'no',
				'options' => array(
					'test' => __( 'Test', 'square-edd' ),
					'live' => __( 'Live', 'square-edd' ),
				),
			);
			$square_currency['square_payment_currency'] = array(
				'id'      => 'square_payment_currency',
				'name'    => __( 'Currency', 'square-edd' ),
				'desc'    => '',
				'type'    => 'select',
				'std'     => 'no',
				'options' => array(
					'USD' => __( 'USD', 'square-edd' ),
					'JPY' => __( 'JPY', 'square-edd' ),
					'GBP' => __( 'GBP', 'square-edd' ),
					'CAD' => __( 'CAD', 'square-edd' ),
					'AUD' => __( 'AUD', 'square-edd' ),
				),
			);
			$api_key_settings                           = array(
				'square_api_test_key'      => array(
					'id'   => 'square_api_test_key',
					'name' => __( 'Test Application ID', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular',
					'desc' => sprintf(
						__( 'Enter test app key.', 'square-edd' )
					)
				),
				'square_api_test_token'    => array(
					'id'   => 'square_api_test_token',
					'name' => __( 'Test Token', 'square-edd' ),
					'desc' => __( 'Enter test app token. ', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'square_api_test_location' => array(
					'id'   => 'square_api_test_location',
					'name' => __( 'Test Location ID', 'square-edd' ),
					'desc' => __( 'Enter test app location id.', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'square_api_live_key'      => array(
					'id'   => 'square_api_live_key',
					'name' => __( 'Live Application ID', 'square-edd' ),
					'desc' => __( 'Enter live app key.', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'square_api_live_token'    => array(
					'id'   => 'square_api_live_token',
					'name' => __( 'Live Token', 'square-edd' ),
					'desc' => __( 'Enter live app token.', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular'
				),
				'square_api_live_location' => array(
					'id'   => 'square_api_live_location',
					'name' => __( 'Live Location ID', 'square-edd' ),
					'desc' => __( 'Enter test app location id.', 'square-edd' ),
					'type' => 'text',
					'size' => 'regular'
				),
			);
			$square_settings                            = array_merge( $square_settings, $square_currency );
			$square_settings                            = array_merge( $square_settings, $api_key_settings );

			$gateway_settings['square'] = $square_settings;

			return $gateway_settings;
		}

		public function edd_register_square_accepted_payment_settings( $accepted_gateway ) {
			$accepted_gateway['square'] = __( 'Square', 'square-edd' );

			return $accepted_gateway;
		}

		public function square_edd_image() {
			$image = SQUARE_EDD_URL . 'assets/images/square-icon.jpg';

			return $image;
		}

	}

	$obj = new Square_EDD_Process();
}


