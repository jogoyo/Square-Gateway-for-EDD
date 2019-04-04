<?php
/*
  Plugin Name: Square Gateway for EDD
  Plugin URL:
  Description: Square payment integration with Easy Digital Downloads.
  Version: 1.0
  Text Domain: square-edd
  Author: Jogoyo
  Author URI: https://github.com/jogoyo/Square-Gateway-for-EDD
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Square_EDD' ) ) {

	class Square_EDD {

		function __construct() {
			define( "SQUARE_EDD_PATH", plugin_dir_path( __FILE__ ) );
			define( "SQUARE_EDD_URL", plugin_dir_url( __FILE__ ) );
			add_action( 'plugins_loaded', array( $this, 'Square_Edd_Init' ) );
		}

		function Square_Edd_Init() {
			if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
				add_action( 'admin_notices', array( $this, 'square_edd_inactive_plugin_notice' ) );
			} else {
				$this->includes();
				$payments = new EDD_Square_Payments();
				if ( ! $payments->are_api_credentials() ) {
					add_action( 'admin_notices', array( $this, 'notice_for_api_credentials' ) );
				}
			}
		}

		public function includes() {
			require_once( SQUARE_EDD_PATH . 'square-sdk/autoload.php' );
			require_once( SQUARE_EDD_PATH . 'includes/square-edd-process.php' );
			require_once( SQUARE_EDD_PATH . 'includes/square-payment.php' );
		}

		public function notice_for_api_credentials() {
			?>
            <div id="message" class="error">
                <p><?php printf( __( 'Square API credentials are required. ', 'square-edd' ) ); ?><a
                            href="<?php echo admin_url() . 'edit.php?post_type=download&page=edd-settings&tab=gateways&section=square' ?>"><?php _e( 'Click here to add them now.' ); ?></a>
                </p>
            </div>
			<?php
		}

		public function square_edd_inactive_plugin_notice() {
			?>
            <div id="message" class="error">
                <p><?php printf( __( 'Square Gateway for EDD requires Easy Digital Download to be installed and activated! ', 'square-edd' ) ); ?></p>
            </div>
			<?php
		}

	}

	$obj = new Square_EDD();
}
