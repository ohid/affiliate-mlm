<?php
 
defined( 'ABSPATH' ) or exit;

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	return;
}

/**
 * Add the gateway to WC Available Gateways
 * 
 * @since 1.0.0
 * @param array $gateways all available WC gateways
 * @return array $gateways all WC gateways + offline gateway
 */
function amlm_wc_add_balance_payment_gateways( $gateways ) {
	// The balance payment gateway is only for site logged in users
	if (is_user_logged_in()) {
		$gateways[] = 'AMLM_Balance_Payment_Gateway';
	}
	return $gateways;
}
add_filter( 'woocommerce_payment_gateways', 'amlm_wc_add_balance_payment_gateways' );

/**
 * AMLM Balance Payment Gateway
 *
 * Provides an way to purchase products through the earned balance of Affiliate MLM users
 * 
 * @class 		AMLM_Balance_Payment_Gateway
 * @extends		WC_Payment_Gateway
 * @version		1.0.0
 * @author 		Ohidul Islam
 */
add_action( 'plugins_loaded', 'amlm_wc_balanace_payment_gateway_init', 11 );

function amlm_wc_balanace_payment_gateway_init() {

	class AMLM_Balance_Payment_Gateway extends WC_Payment_Gateway {

		/**
		 * Constructor for the gateway.
		 */
		public function __construct() {
	  
			$this->id                 = 'balance_payment_gateway';
			$this->icon               = apply_filters('woocommerce_offline_icon', '');
			$this->has_fields         = false;
			$this->method_title       = __( 'Site Balance Payment', 'wc-gateway-offline' );
			$this->method_description = __( 'Allows offline payments. Very handy if you use your cheque gateway for another payment method, and can help with testing. Orders are marked as "on-hold" when received.', 'wc-gateway-offline' );
		  
			// Load the settings.
			$this->init_form_fields();
			$this->init_settings();
		  
			// Define user set variables
			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->balance_payment_instruction();
			$this->instructions = $this->get_option( 'instructions', $this->description );
		  
			// Actions
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		  
			// Customer Emails
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}

		public function balance_payment_instruction() {
			$points = 0;
			$currency = get_option('woocommerce_currency');
			
			if (is_user_logged_in()){
				$points = get_user_meta( get_current_user_id(), 'amlm_points', true );
			}
			$information = sprintf('<p>Your current point balance is <b>%1$s %2$s.</b></p>', $currency, $points ? $points : 0);

			if ($points >= 400) {
				$information .= sprintf('</br><p>You need to keep minimum balance of <b>BDT 400</b> and you can now shop with <b>%1$s %2$s.</b></p>', $currency, $points - 400);
			}

			return $information;
		}
	
	
		/**
		 * Initialize Gateway Settings Form Fields
		 */
		public function init_form_fields() {
	  
			$this->form_fields = apply_filters( 'wc_offline_form_fields', array(
		  
				'enabled' => array(
					'title'   => __( 'Enable/Disable', 'wc-gateway-offline' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable Offline Payment', 'wc-gateway-offline' ),
					'default' => 'yes'
				),
				
				'title' => array(
					'title'       => __( 'Title', 'wc-gateway-offline' ),
					'type'        => 'text',
					'description' => __( 'This controls the title for the payment method the customer sees during checkout.', 'wc-gateway-offline' ),
					'default'     => __( 'Balance Payment', 'wc-gateway-offline' ),
					'desc_tip'    => true,
				),
				
				'instructions' => array(
					'title'       => __( 'Instructions', 'wc-gateway-offline' ),
					'type'        => 'textarea',
					'description' => __( 'Instructions that will be added to the thank you page and emails.', 'wc-gateway-offline' ),
					'default'     => '',
					'desc_tip'    => true,
				),
			) );
		}
	
	
		/**
		 * Output for the order received page.
		 */
		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wpautop( wptexturize( $this->instructions ) );
			}
		}
	
	
		/**
		 * Add content to the WC emails.
		 *
		 * @access public
		 * @param WC_Order $order
		 * @param bool $sent_to_admin
		 * @param bool $plain_text
		 */
		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		
			if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method && $order->has_status( 'on-hold' ) ) {
				echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
			}
		}
	
	
		/**
		 * Process the payment and return the result
		 *
		 * @param int $order_id
		 * @return array
		 */
		public function process_payment( $order_id ) {
	
			$order = wc_get_order( $order_id );
			
			// Mark as on-hold (we're awaiting the payment)
			$order->update_status( 'on-hold', __( 'Awaiting offline payment', 'wc-gateway-offline' ) );

			// Reduce stock levels
			$order->reduce_order_stock();
			
			// Remove cart
			WC()->cart->empty_cart();
			
			// Return thankyou redirect
			return array(
				'result' 	=> 'success',
				'redirect'	=> $this->get_return_url( $order )
			);
		}
	
  } // end \AMLM_Balance_Payment_Gateway class
}