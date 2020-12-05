<?php
/** 
 * The class module that specially deals with the customization part of WooCommerce various functions including form fields, customer role, etc.
 * PHP version 7.0
 * 
 * @category   Component
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

namespace AMLM\Classes;

class Class_Custom_Woo
{
    public function register()
    {
        add_action( 'woocommerce_created_customer', [$this, 'saveAdditionalFieldsData'] );
        add_action( 'woocommerce_register_post', [$this, 'validateAdditionalFields'], 10, 3 );
        add_action( 'woocommerce_save_account_details_errors', [$this, 'actionWoocommerceSaveAccountDetailsErrors'], 10, 1 );
        add_action( 'woocommerce_edit_account_form_tag', [$this, 'actionWoocommerceEditAccountFormTag'] );       
        add_action( 'woocommerce_save_account_details', [$this, 'myaccountAdditionalFields'], 10, 1 );
        add_filter('woocommerce_new_customer_data', [$this, 'wcAssignCustomRole'], 10, 1);
    }

    /**
    * Save the additional registration form fields in to the database
    */
    public function saveAdditionalFieldsData( $customer_id ) {
        if ( isset( $_POST['billing_phone'] ) ) {
                    // Phone input filed which is used in WooCommerce
                    update_user_meta( $customer_id, 'amlm_user_phone', sanitize_text_field( $_POST['billing_phone'] ) );
                    update_user_meta( $customer_id, 'billing_phone', sanitize_text_field( $_POST['billing_phone'] ) );
            }
        if ( isset( $_POST['billing_first_name'] ) ) {
                //First name field which is by default
                update_user_meta( $customer_id, 'first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
                // First name field which is used in WooCommerce
                update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        }
        if ( isset( $_POST['billing_last_name'] ) ) {
                // Last name field which is by default
                update_user_meta( $customer_id, 'last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
                // Last name field which is used in WooCommerce
                update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        }
    }

    /**
    * Additional register fields Validating.
    */
    public function validateAdditionalFields( $username, $email, $validation_errors ) {
        if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
            $validation_errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'amlm-locale' ) );
        }

        if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
            $validation_errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'amlm-locale' ) );
        }

        if ( isset( $_POST['billing_phone'] ) && empty( $_POST['billing_phone'] ) ) {
            $validation_errors->add( 'billing_phone_error', __( '<strong>Error</strong>: Billing phone is required!.', 'amlm-locale' ) );
        }

        return $validation_errors;
    }

    /**
     * Validate the uploaded profile picture iamge
     *
     * @return void
     */
    public function actionWoocommerceSaveAccountDetailsErrors( $args ){
        if ( isset($_POST['amlm_image']) && empty($_POST['amlm_image']) ) {
            $args->add( 'image_error', __( 'Please provide a valid image', 'amlm-locale' ) );
        }
    }

    /**
     * Add enctype to form to allow image upload
     *
     * @return void
     */
    public function actionWoocommerceEditAccountFormTag() {
        echo 'enctype="multipart/form-data"';
    }

    /**
    * Additional my-account fields
    *
    * @param integer $user_id
    * 
    * @return void
    */
    public function myaccountAdditionalFields( $user_id ) {

       // Check if the account_phone field is set then update the value
       if ( isset( $_POST['account_phone'] ) ) {
           // $phoneNumber = filter_var($_POST['account_phone'], FILTER_VALIDATE_INT);
           $phoneNumber = sanitize_text_field($_POST['account_phone']);

           if($phoneNumber) {
               update_user_meta( $user_id, 'amlm_user_phone', $phoneNumber );
           }
       }
       
       // Check if the amlm_iamge field is set then update the value
       if ( isset( $_FILES['amlm_image']['tmp_name'] ) && ! empty( $_FILES['amlm_image']['tmp_name'] ) ) {
           require_once( ABSPATH . 'wp-admin/includes/image.php' );
           require_once( ABSPATH . 'wp-admin/includes/file.php' );
           require_once( ABSPATH . 'wp-admin/includes/media.php' );

           $attachment_id = media_handle_upload( 'amlm_image', 0 );

           if ( is_wp_error( $attachment_id ) ) {
               update_user_meta( $user_id, 'amlm_image', $_FILES['amlm_image'] . ": " . $attachment_id->get_error_message() );
           } else {
               update_user_meta( $user_id, 'amlm_image', $attachment_id );
           }
       }
   }

   /**
     * Assign a custom role for the new registered users via WooCommerce
     *
     * @param [type] $args gets the funciton argument
     * 
     * @return array
     */
    public function wcAssignCustomRole($args)
    {
        $args['role'] = 'amlm_distributor';

        return $args;
    }

}
