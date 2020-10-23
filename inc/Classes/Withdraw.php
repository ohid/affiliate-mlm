<?php
/** 
 * The withdrawal processor of the plugin
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

class Withdraw
{
    protected $wpdb;

    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        add_action('wp_ajax_withdraw_form', [$this, 'withdrawForm']);
    }
    
    /**
     * Withdraw Form uses on AJAX action
     *
     * @return void
     */
    public function withdrawForm()
    {
        if (DOING_AJAX) {

            if (! isset($_POST['withdraw_nonce']) && ! wp_verify_nonce($_POST['withdraw_nonce'], 'amlm_nonce')) {
                $this->returnJSON('error', 'Form validation failed.');
                return;
            }

            if (! isset($_POST['payment-type']) || $_POST['payment-type'] == 'selectcard' ) {
                $this->returnJSON('error', __('Please select a payment type', 'amlm-locale'));
                return;
            }

            $paymentType = filter_var($_POST['payment-type'], FILTER_SANITIZE_STRING);

            // Validation for the bkash payment method
            if ($paymentType === 'bkash') {
                if (! isset($_POST['bkash-number']) || strlen($_POST['bkash-number']) < 11) {
                    $this->returnJSON('error', __('Please provide a valid bKash number', 'amlm-locale'));
                    return;
                }

                if ( ! is_numeric($_POST['bkash-number'])) {
                    $this->returnJSON('error', __('bKash number should not contain strings/characters, only numbers are allowed', 'amlm-locale'));
                    return;
                }
            }

            // Validation for the rocket payment method
            if ($paymentType === 'rocket') {
                if (! isset($_POST['rocket-number']) || strlen($_POST['rocket-number']) < 11) {
                    $this->returnJSON('error', __('Please provide a valid Rocket number', 'amlm-locale'));
                    return;
                }
                
                if ( ! is_numeric($_POST['rocket-number'])) {
                    $this->returnJSON('error', __('Rocket number should not contain strings/characters, only numbers are allowed', 'amlm-locale'));
                    return;
                }
            }
            
            // Validation for the bank payment method
            if ($paymentType === 'bank') {
                if (! isset($_POST['bank-account-name']) || strlen($_POST['bank-account-name']) < 1) {
                    $this->returnJSON('error', __('Please enter bank account name', 'amlm-locale'));
                }
                
                if (! isset($_POST['bank-account-number']) || strlen($_POST['bank-account-number']) < 10) {
                    $this->returnJSON('error', __('Please enter bank account number', 'amlm-locale'));
                }
                
                if (! isset($_POST['bank-name']) || strlen($_POST['bank-name']) < 1) {
                    $this->returnJSON('error', __('Please enter bank name', 'amlm-locale'));
                }
                
                if (! isset($_POST['bank-branch']) || strlen($_POST['bank-branch']) < 1) {
                    $this->returnJSON('error', __('Please enter bank branch name', 'amlm-locale'));
                }
            }

            // Validate the ammount field
            if (! isset($_POST['withdraw-amount']) || filter_var($_POST['withdraw-amount'], FILTER_SANITIZE_NUMBER_INT) < 1) {
                $this->returnJSON('error', __('Please enter amount', 'amlm-locale'));
            }

            if (! is_numeric($_POST['withdraw-amount'])) {
                $this->returnJSON('error', __('Please enter numbers, characters not accepted', 'amlm-locale'));
            }

            // filter_var($_POST['bkash-number'], FILTER_SANITIZE_NUMBER_INT)

            $this->returnJSON('error', 'test');
        }
    }

    /**
     * Return a JSON message to the AJAX call
     *
     * @param $status  get the JSON status
     * @param $message get the JSON message
     * 
     * @return void
     */
    public function returnJSON($status, $message = null)
    {

        wp_send_json(
            [
                'status' => $status,
                'message' => $message
            ]
        );

        wp_die();
    }
}