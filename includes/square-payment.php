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

if ( ! class_exists( 'EDD_Square_Payments' ) ) {

	class EDD_Square_Payments {

		public $gateway_id = 'square';

		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'square_edd_enqueue_script' ) );
			add_action( 'edd_purchase_form_bottom', array( $this, 'edd_load_scripts' ) );
			add_action( 'edd_square_cc_form', array( $this, 'square_edd_custom_form' ) );
			add_filter( 'edd_payment_gateways', array( $this, 'register_square_gateway' ), 2, 1 );
			add_action( 'edd_gateway_square', array( $this, 'edd_square_process_purchase' ) );
		}

		public function square_edd_enqueue_script() {
			wp_enqueue_script( 'edd-paymentform', 'https://js.squareup.com/v2/paymentform' );
			wp_enqueue_style( 'edd-square-style', SQUARE_EDD_URL . 'assets/css/custom-style.css' );
		}

		public function edd_load_scripts() {
			if ( edd_get_chosen_gateway() == 'square' || $_GET['payment-mode'] == 'square' ) {
				$mode_type        = edd_get_option( 'square_mode' );
				$test_app_id      = edd_get_option( 'square_api_test_key' );
				$test_location_id = edd_get_option( 'square_api_test_location' );
				$live_app_id      = edd_get_option( 'square_api_live_key' );
				$live_location_id = edd_get_option( 'square_api_live_token' );
				$data             = array(
					'application_id'               => $mode_type == 'test' ? $test_app_id : $live_app_id,
					'location_id'                  => $mode_type == 'test' ? $test_location_id : $live_location_id,
					'placeholder_card_number'      => __( '•••• •••• •••• ••••', 'edd-square' ),
					'placeholder_card_expiration'  => __( 'MM / YY', 'edd-square' ),
					'placeholder_card_cvv'         => __( 'CVV', 'edd-square' ),
					'placeholder_card_postal_code' => __( 'Card Postal Code', 'edd-square' ),
					'payment_form_input_styles'    => $this->get_input_styles()
				);
				if ( $data['application_id'] && $data['location_id'] ) {
					?>
                    <script>
                        var eddcheckout = <?php echo json_encode( $data ) ?>;
                        var paymentForm = new SqPaymentForm({
                            applicationId: eddcheckout.application_id,
                            locationId: eddcheckout.location_id,
                            inputClass: 'eddsq-input',
                            inputStyles: jQuery.parseJSON(eddcheckout.payment_form_input_styles),
                            cardNumber: {
                                elementId: 'edd-square-card-number',
                                placeholder: eddcheckout.placeholder_card_number
                            },
                            cvv: {
                                elementId: 'edd-square-cvv',
                                placeholder: eddcheckout.placeholder_card_cvv
                            },
                            expirationDate: {
                                elementId: 'edd-square-expiration-date',
                                placeholder: eddcheckout.placeholder_card_expiration
                            },
                            postalCode: {
                                elementId: 'edd-square-postal-code',
                                placeholder: eddcheckout.placeholder_card_postal_code
                            },
                            callbacks: {
                                cardNonceResponseReceived: function (errors, nonce, cardData) {
                                    if (errors) {
                                        var html = '';
                                        // handle errors                                    
                                        jQuery(errors).each(function (index, error) {
                                            html += '<div class="edd_square_error">' + error.message + '</div>';
                                        });
                                        // append it to DOM    
                                        jQuery('.edd_square_container  .messages').html(html);
                                        jQuery('#edd-purchase-button').removeAttr('disabled');
                                    } else {
                                        // inject nonce to a hidden field to be submitted     
                                        jQuery('.edd_square_container ').append('<input type="hidden" class="edd_square_nonce" name="edd_square_nonce" value="' + nonce + '" />');
                                        jQuery(' #edd-purchase-button').removeAttr('disabled');
                                        //submit form                   
                                        jQuery('#edd-purchase-button').closest("form").submit();
                                    }
                                },
                                paymentFormLoaded: function () {
                                },
                                unsupportedBrowserDetected: function () {
                                }
                            }
                        });
                        paymentForm.build();
                        jQuery(' #edd-purchase-button').click(function (event) {
                            event.preventDefault();
                            jQuery('.edd_square_container  .messages').html("");
                            jQuery(this).attr('disabled', 'disabled');
                            paymentForm.requestCardNonce();
                            return false;
                        });
                    </script>
					<?php
				}
			}
		}

		/**
		 * square form style
		 */
		public function get_input_styles() {
			$styles = array(
				array(
					'fontSize'        => '16px',
					'padding'         => '0.7em',
					'backgroundColor' => '#fff'
				)
			);

			return wp_json_encode( $styles );
		}

		public function are_api_credentials() {
			$mode_type   = edd_get_option( 'square_mode' );
			$app_id      = '';
			$token       = '';
			$location_id = '';
			if ( $mode_type == 'test' ) {
				$app_id      = edd_get_option( 'square_api_test_key' );
				$token       = edd_get_option( 'square_api_test_token' );
				$location_id = edd_get_option( 'square_api_test_location' );
			} elseif ( $mode_type == 'live' ) {
				$app_id      = edd_get_option( 'square_api_live_key' );
				$token       = edd_get_option( 'square_api_live_token' );
				$location_id = edd_get_option( 'square_api_live_token' );
			}
			if ( empty( $app_id ) && empty( $token ) && empty( $location_id ) ) {
				return false;
			} else {
				return true;
			}
		}

		public function register_square_gateway( $gateways ) {

			$default_square_info = array(
				$this->gateway_id => array(
					'admin_label'    => __( 'Square', 'square-edd' ),
					'checkout_label' => __( 'Square', 'square-edd' ),
					'supports'       => array(),
				),
			);

			$default_square_info = apply_filters( 'edd_register_square_gateway', $default_square_info );
			$gateways            = array_merge( $gateways, $default_square_info );

			return $gateways;
		}

		public function square_edd_custom_form() {
			if ( $this->are_api_credentials() ) {
				?>
                <fieldset id="edd_cc_fields" class="edd-do-validate">
                    <legend><?php _e( 'Credit Card Info', 'square-edd' ); ?></legend>
                    <div class="edd_square_container " id="edd_square_container_id">
                        <div class="messages"></div>
                        <div class="edd-card-number-wrap" id="edd-card-number-wrap">
                            <label class="edd-label"
                                   for="edd-square-card-"><?php _e( 'Card Number', 'square-edd' ); ?></label>
                            <div id="edd-square-card-number"></div>
                        </div>
                        <div class="edd-card-date-wrap date">
                            <label class="edd-label"
                                   for="edd-square-expiration-date-"> <?php _e( 'Expiry (MM/YY)', 'square-edd' ); ?></label>
                            <div id="edd-square-expiration-date"></div>
                        </div>
                        <div class="edd-card-cvc-wrap cvv">
                            <label class="edd-label"
                                   for="edd-square-cvv-"><?php _e( 'Card Code', 'square-edd' ); ?></label>
                            <div id="edd-square-cvv"></div>
                        </div>
                        <div class="edd-card-cvc-wrap">
                            <label class="edd-label"
                                   for="edd-square-postal-code-"><?php _e( 'Card Postal Code', 'square-edd' ); ?></label>
                            <div id="edd-square-postal-code"></div>
                        </div>

                    </div>
                </fieldset>
				<?php
			} else {
				?>
                <fieldset id="edd_cc_fields" class="edd-do-validate">
                    <legend><?php _e( 'Credit Card Info', 'square-edd' ); ?></legend>
                    <p>
						<?php
						echo __( 'Square API credentials missing.', 'square-edd' );
						?>
                    </p>
                </fieldset>
				<?php
			}
		}

		public function edd_square_process_purchase( $purchase_data ) {
			if ( isset( $purchase_data['post_data']['edd-gateway'] ) && $purchase_data['post_data']['edd-gateway'] == 'square' ) {
				$app_id      = "";
				$token       = "";
				$location_id = "";
				$nonce       = isset( $purchase_data['post_data']['edd_square_nonce'] ) ? $purchase_data['post_data']['edd_square_nonce'] : '';
				$amount      = round( $purchase_data['price'], 2 ) * 100;

				try {
					$transaction_api = new \SquareConnect\Api\TransactionApi();
					$idempotencyKey  = time();
					$mode_type       = edd_get_option( 'square_mode' );
					if ( $mode_type == 'test' ) {
						$app_id      = edd_get_option( 'square_api_test_key' );
						$token       = edd_get_option( 'square_api_test_token' );
						$location_id = edd_get_option( 'square_api_test_location' );
					} elseif ( $mode_type == 'live' ) {
						$app_id      = edd_get_option( 'square_api_live_key' );
						$token       = edd_get_option( 'square_api_live_token' );
						$location_id = edd_get_option( 'square_api_live_token' );
					}
					$currency        = edd_get_option( 'square_payment_currency' );
					$currency        = ! empty( $currency ) ? $currency : 'USD';
					$transaction     = $transaction_api->charge( $token, $location_id, array(
						'idempotency_key' => (string) $idempotencyKey,
						'amount_money'    => array(
							'amount'   => (int) $amount,
							'currency' => $currency
						),
						'card_nonce'      => $nonce,
						'reference_id'    => (string) $idempotencyKey
					) );
					$transactionData = json_decode( $transaction, true );
					if ( isset( $transactionData['transaction']['id'] ) ) {
						// Setup payment data to be recorded
						$payment_data = array(
							'price'        => $purchase_data['price'],
							'date'         => $purchase_data['date'],
							'user_email'   => $purchase_data['user_email'],
							'purchase_key' => $purchase_data['purchase_key'],
							'currency'     => edd_get_currency(),
							'downloads'    => $purchase_data['downloads'],
							'user_info'    => $purchase_data['user_info'],
							'cart_details' => $purchase_data['cart_details'],
							'gateway'      => $this->gateway_id,
							'status'       => 'pending',
						);

						$payment_id = edd_insert_payment( $payment_data );
						edd_set_payment_transaction_id( $payment_id, $transactionData['transaction']['id'] );

						edd_update_payment_status( $payment_id, 'publish' );

						// Empty the shopping cart
						edd_empty_cart();
						edd_send_to_success_page();
					}
				} catch ( Exception $e ) {
					$errors = isset( $e->getResponseBody()->errors ) ? $e->getResponseBody()->errors : '';
					if ( ! empty( $errors ) ) {
						foreach ( $errors as $error ) {
							$message = $error->detail;
							if ( isset( $error->field ) ) {
								$message = $error->field . ' - ' . $error->detail;
							}
							edd_set_error( 'square_gateway_transaction_error', $message );
							edd_send_back_to_checkout( '?payment-mode=square' );
						}
					} else {
						edd_set_error( 'square_gateway_transaction_error', __( 'Failed Authorization', 'square-edd' ) );
						edd_send_back_to_checkout( '?payment-mode=square' );
					}
				}
			}
		}
	}
}


