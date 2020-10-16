<?php 

namespace AMLM\Classes;

class Class_Main
{

    protected $user;

    protected $referral_limit = 3;

    /**
     * Plugin initialization
     *
     * @return void
     */
    public function register() {

        add_action( 'init', array( $this, 'mainInit' ) );

        // add_action( 'woocommerce_payment_complete', array( $this, 'amlm_payment_complete' ), 10, 1 );
        // add_action( 'woocommerce_order_status_completed', array( $this, 'amlm_payment_complete' ), 10, 1 );

        add_filter( 'woocommerce_locate_template', array( $this, 'amlm_woocommerce_locate_template'), 10, 3 );

        add_action( 'wp_ajax_referral_form', array( $this, 'referralForm' ) );
    }

    /**
     * Initialize the main class
     *
     * @return void
     */
    public function mainInit() {

        global $wpdb;

        $table_name = $wpdb->prefix.'amlm_referrals';

        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
 
        if ( $wpdb->get_var( $query ) !== $table_name ) {
            echo 'table does not exists';
        }


        if( is_user_logged_in() ) {
    
            $this->user = wp_get_current_user();

        }
    
        return;
    }

    /**
     * After the WooCommerce payment / order has been completed 
     * Update the user points
     *
     * @param [type] $order_id
     * @return void
     */
    // public function amlm_payment_complete( $order_id ){
        
    //     // get the order object
    //     $order = wc_get_order( $order_id );
    //     $order_total = $order->get_total();
    //     $user_id = $order->get_user_id();


    // }
    
    /**
     * Custom WooCommerce templates
     *
     * @param [type] $template
     * @param [type] $template_name
     * @param [type] $template_path
     * @return void
     */
    public function amlm_woocommerce_locate_template( $template, $template_name, $template_path ) {
        global $woocommerce;
    
        $_template = $template;
    
        if ( ! $template_path ) $template_path = $woocommerce->template_url;
        
        $plugin_path  = AMLM_PLUGIN_PATH . '/woocommerce/';
    
        // Look within passed path within the theme - this is priority
        $template = locate_template(
            array(
                $template_path . $template_name,
                $template_name
            )
        );
    
        // Modification: Get the template from this plugin, if it exists
        if ( ! $template && file_exists( $plugin_path . $template_name ) )
        $template = $plugin_path . $template_name;
    
        // Use default template
        if ( ! $template )
        $template = $_template;
    
        // Return what we found
        return $template;
    }
    
    /**
     * Get the current user referral count
     *
     * @return void
     */
    public function userReferralCount() {
        global $wpdb;

        $user = wp_get_current_user();

        $user_referrals_count = $wpdb->get_var("SELECT COUNT(*) from {$wpdb->prefix}amlm_referrals WHERE user_id = $user->ID");
        return $user_referrals_count;
    }

    /**
     * Referral Form uses on AJAX action
     *
     * @return void
     */
    public function referralForm() {

        if( DOING_AJAX ) {

            if( $this->userReferralCount() >= $this->referral_limit ) {
                $this->returnJSON( 'error', 'You can not add more referral users.' );
            }

            if( isset( $_POST['referral_nonce'] ) && wp_verify_nonce( $_POST['referral_nonce'], 'amlm_nonce' ) ) {

                $username = sanitize_text_field( $_POST['username'] );
                $email = filter_var( $_POST['email'], FILTER_VALIDATE_EMAIL );

                if( $email === false ) {
                    $this->returnJSON( 'error', 'Email is invalid.' );
                }

                $this->createReferralUser($username, $email);

            } else {
                $this->returnJSON( 'error', 'Form validation failed.' );
            }
        }
    }

    /**
     * Create the referral user
     *
     * @param [type] $username
     * @param [type] $email
     * @return void
     */
    public function createReferralUser( $username, $email ) {
        global $wpdb;

        // Check if the username is already exists
        $user_id = username_exists( $username );

        if( ! $user_id && false == email_exists( $email ) ) {
            // Generate a random password
            $random_password =  wp_generate_password( 12, $false );

            // Create the users
            $user_id = wp_create_user( $username, $random_password, $email );

            if( $user_id ) {
                // Set the referral relation
                $wpdb->insert( $wpdb->prefix . 'amlm_referrals', array( 'user_id' => $this->user->ID, 'referral_id' => $user_id ) );

                // Add the amlm_points meta data for the user
                add_user_meta( $user_id, 'amlm_points', 0 );

                $user = get_user_by( 'id', $user_id );

                $user->remove_role('subscriber');
                $user->add_role('amlm_sales_representative');

                // Send a notification to the user
                wp_send_new_user_notifications( $user_id, 'both' );
                
                // Send the JSON success message
                $this->returnJSON( 'success', 'Referral user created successfully!' );
            }

        } else {
            // Send the JSON error message
            $this->returnJSON( 'error', 'Username or email already exists.' );
        }
    }

    /**
     * Return a JSON message to the AJAX call
     *
     * @param $status
     * @param $message
     * @return void
     */
    public function returnJSON( $status, $message = null ) {
        
        wp_send_json( array(
            'status' => $status,
            'message' => $message
        ) );

        wp_die();
    }
}