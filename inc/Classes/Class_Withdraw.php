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

class Class_Withdraw
{
    protected $wpdb;

    protected $user;
    
    protected $min_withdraw_limit = 99;

    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        add_action('init', array($this, 'mainInit'));
        add_action('wp_ajax_withdraw_form', [$this, 'withdrawForm']);
        add_action('wp_ajax_withdraw_action', [$this, 'withdrawAction']);
    }
    
    /**
     * Initialize the main class
     *
     * @return void
     */
    public function mainInit()
    {
        // If the user logged in
        if (is_user_logged_in()) {
            $this->user = wp_get_current_user();
        }
    
        return;
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
            }

            if (! isset($_POST['payment-type']) || $_POST['payment-type'] == 'selectcard' ) {
                $this->returnJSON('error', __('Please select a payment type', 'amlm-locale'));
            }

            $paymentType = filter_var($_POST['payment-type'], FILTER_SANITIZE_STRING);

            // Validation for the bkash payment method
            if ($paymentType === 'bkash') {
                if (! isset($_POST['bkash-number']) || strlen($_POST['bkash-number']) < 11) {
                    $this->returnJSON('error', __('Please provide a valid bKash number', 'amlm-locale'));
                }

                if ( ! is_numeric($_POST['bkash-number'])) {
                    $this->returnJSON('error', __('bKash number should not contain strings/characters, only numbers are allowed', 'amlm-locale'));
                }
            }

            // Validation for the rocket payment method
            if ($paymentType === 'rocket') {
                if (! isset($_POST['rocket-number']) || strlen($_POST['rocket-number']) < 11) {
                    $this->returnJSON('error', __('Please provide a valid Rocket number', 'amlm-locale'));
                }
                
                if ( ! is_numeric($_POST['rocket-number'])) {
                    $this->returnJSON('error', __('Rocket number should not contain strings/characters, only numbers are allowed', 'amlm-locale'));
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

            if ( $this->min_withdraw_limit >= $_POST['withdraw-amount'] ) {
                $this->returnJSON('error', __('You can not withdraw less than 100 BDT', 'amlm-locale'));
            }

            // Make the withdraw request
            $this->makeWithdrawRequest();
        }
    }

    /**
     * Make the user withdraw request
     *
     * @return void
     */
    public function makeWithdrawRequest()
    {
        $withdrawAmount = filter_var($_POST['withdraw-amount'], FILTER_SANITIZE_NUMBER_INT);

        // Get the current balance of the user
        $currentBalance = get_user_meta($this->user->ID, 'amlm_earning', true);

        // Do not proceed if the user doesn't have sufficient balance
        if ($withdrawAmount > $currentBalance) {
            $this->returnJSON('error', __('You do not have sufficient balance to withdraw', 'amlm-locale'));
        }

        // Retrieve the payment type
        $paymentType = filter_var($_POST['payment-type'], FILTER_SANITIZE_STRING);

        // If the payment method is bKash
        if ($paymentType == 'bkash') {
            // Sanitize the bKash number field
            $bKashNumber = filter_var($_POST['bkash-number'], FILTER_SANITIZE_NUMBER_INT);
            
            // Make the bkash withdraw request
            $this->makeMobileWithdrawRequest($paymentType, $bKashNumber, $currentBalance, $withdrawAmount);
        }

        // If the payment method is Rocket
        if ($paymentType == 'rocket') {
            // Sanitize the Rocket number field
            $rocketNumber = filter_var($_POST['rocket-number'], FILTER_SANITIZE_NUMBER_INT);

            // Make the rocket withdraw request
            $this->makeMobileWithdrawRequest($paymentType, $rocketNumber, $currentBalance, $withdrawAmount);
        }

        // If the payment method is Bank
        if ($paymentType == 'bank') {

            // Make the bank withdraw request
            $this->makeBankWithdrawRequest($paymentType, $currentBalance, $withdrawAmount);
        }
    }

    /**
     * Generate the mobile withdraw requests
     *
     * @param string $paymentType pass the payment type
     * @param integer $number pass the phone number
     * @param integer $currentBalance pass the current balance
     * @param integer $withdrawAmount pass the withdraw amount
     * @return void
     */
    public function makeMobileWithdrawRequest($paymentType, $number, $currentBalance, $withdrawAmount)
    {
        // Inserting the data into the amlm_withdraw table
        $paymentRequest = $this->wpdb->insert(
            "{$this->wpdb->prefix}amlm_withdraw",
            [
                'user_id' => $this->user->ID,
                'payment_type' => $paymentType,
                'mobile_number' => $number,
                'amount' => $withdrawAmount,
                'payment_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%s", "%s", "%d", "%s", "%s"]
        );

        // Inserting the data into the amlm_bank_details table
        $number_type = ( $paymentType == 'bkash' ) ? 'bkash_number' : 'rocket_number';

        amlmBankDetailsInsertOrUpdate(
            $this->user->ID,
            "{$this->wpdb->prefix}amlm_bank_details",
            [
                'user_id' => $this->user->ID,
                $number_type => $number,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%d", "%s"]
        );

        if ($paymentRequest) {
            // Calculate the new balance
            $newBalance = $currentBalance - $withdrawAmount;

            // Update the current balance
            update_user_meta($this->user->ID, 'amlm_earning', $newBalance);

            $reportName = __('Your withdraw request has been made', 'amlm-locale');

            // Generate withdraw report
            $this->generateReport($this->user->ID, $this->wpdb->insert_id, $reportName, $paymentType, $withdrawAmount);

            $this->returnJSON('success', __('Payment has been requested', 'amlm-locale'), $newBalance);
        } else {
            $this->returnJSON('error', __('Sorry, some unexpected error occurred', 'amlm-locale'));
        }
    }

    /**
     * Make the bank withdraw request
     *
     * @param string $paymentType provide the payment type 
     * @param int|float $currentBalance provide the current balance
     * @param int|float $withdrawAmount provide the witdhrawal amount
     * 
     * @return void
     */
    public function makeBankWithdrawRequest($paymentType, $currentBalance, $withdrawAmount)
    {
        // Sanitize the Rocket number field
        $bankAccountName = filter_var($_POST['bank-account-name'], FILTER_SANITIZE_STRING);
        $bankAccountNumber = filter_var($_POST['bank-account-number'], FILTER_SANITIZE_NUMBER_INT);
        $bankName = filter_var($_POST['bank-name'], FILTER_SANITIZE_STRING);
        $bankBranch = filter_var($_POST['bank-branch'], FILTER_SANITIZE_STRING);

        // Inserting the data into the amlm_withdraw table
        $paymentRequest = $this->wpdb->insert(
            "{$this->wpdb->prefix}amlm_withdraw",
            [
                'user_id' => $this->user->ID,
                'payment_type' => $paymentType,
                'bank_account_name' => $bankAccountName,
                'bank_account_number' => $bankAccountNumber,
                'bank_name' => $bankName,
                'bank_branch' => $bankBranch,
                'amount' => $withdrawAmount,
                'payment_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%s", "%s", "%d", "%s", "%s", "%d", "%s", "%s"]
        );

        // Insert or update the data into the amlm_bank_details table for the user

        amlmBankDetailsInsertOrUpdate(
            $this->user->ID,
            "{$this->wpdb->prefix}amlm_bank_details",
            [
                'user_id' => $this->user->ID,
                'bank_account_name' => $bankAccountName,
                'bank_account_number' => $bankAccountNumber,
                'bank_name' => $bankName,
                'bank_branch' => $bankBranch,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%s", "%d", "%s", "%s", "%s"]
        );

        if ($paymentRequest) {
            // Calculate the new balance
            $newBalance = $currentBalance - $withdrawAmount;

            // Update the current balance
            update_user_meta($this->user->ID, 'amlm_earning', $newBalance);

            $reportName = __('Your withdraw request has been made', 'amlm-locale');

            // Generate withdraw report
            $this->generateReport($this->user->ID, $this->wpdb->insert_id, $reportName, $paymentType, $withdrawAmount);

            $this->returnJSON('success', __('Payment has been requested', 'amlm-locale'), $newBalance);
        } else {
            $this->returnJSON('error', __('Sorry, some unexpected error occurred', 'amlm-locale'));
        }
    }
    
    /**
     * Take the witdhraw request action
     *
     * @return void
     */
    public function withdrawAction()
    {
        if (DOING_AJAX) {

            if (! isset($_POST['withdraw_action']) && ! wp_verify_nonce($_POST['withdraw_action'], 'amlm_nonce')) {
                $this->returnJSON('error', __('Something went wrong', 'amlm-locale'));
            }

            if (! isset($_POST['withdraw-action']) || empty($_POST['withdraw-action']) ) {
                $this->returnJSON('error', __('', 'amlm-locale'));
            }

            // Sanitize the values
            $withdrawAction = filter_var($_POST['withdraw-action'], FILTER_SANITIZE_STRING);
            $witdhrawID = filter_var($_POST['witdhraw_id'], FILTER_SANITIZE_NUMBER_INT);

            // Get the withdraw request from the database
            $withdrawRow = $this->wpdb->get_row("SELECT * FROM {$this->wpdb->prefix}amlm_withdraw WHERE id = $witdhrawID");

            if (!$withdrawRow) {
                $this->returnJSON('error', __('Don\'t try to hijack us, you silly hooman!'));
            }
            
            if ($withdrawRow->payment_status == 'approved') {
                $this->returnJSON('error', __('You can not take action on already approved requests.'));
            }
            
            // Update the withdraw request
            $this->updateWithdrawRequest($withdrawAction, $withdrawRow);

            // If the withdraw request is declined 
            // Return the money to the users
            if ($withdrawAction == 'declined') {
                $amlm_earning = get_user_meta($withdrawRow->user_id, 'amlm_earning', true);

                update_user_meta($withdrawRow->user_id, 'amlm_earning', $amlm_earning + $withdrawRow->amount);
            }
            
    
            // Calculate the amount should pay and service charge
            $paid_amount = (70 / 100) * $withdrawRow->amount;
            $amount_charge = (30 / 100) * $withdrawRow->amount;
            
            // Generate withdraw report
            if ($withdrawAction == 'approved') {
                
                // Send a report to the user
                $reportName = __('Your withdrawal request has been approved', 'amlm-locale');
                $this->generateReport($withdrawRow->user_id, $withdrawRow->id, $reportName, $withdrawRow->payment_type, $paid_amount, $amount_charge);
            } elseif ($withdrawAction == 'declined') {

                $reportName = __('Your withdrawal request has been declined and the amount returned to your balance.', 'amlm-locale');
                $this->generateReport($withdrawRow->user_id, $withdrawRow->id, $reportName, $withdrawRow->payment_type, $withdrawRow->amount);
            }

            $this->returnJSON('success', sprintf('%s', __('The payment request has been ', 'amlm-locale') . $withdrawAction ));
            
        }
    }

    /**
     * Update the withdrarw request
     *
     * @return void
     */
    public function updateWithdrawRequest($withdrawAction, $withdrawRow)
    {
        // Update the database
        $this->wpdb->update(
            "{$this->wpdb->prefix}amlm_withdraw",
            array('payment_status' => $withdrawAction),
            array('id' => $withdrawRow->id),
            array('%s'),
            array('%d')
        );
    }


    /**
     * Generate the withdraw report
     *
     * @param integer $user pass the user ID
     * @param integer $amount pass the withdraw amount
     * 
     * @return void
     */
    public function generateReport($user_id, $withdraw_id, $reportName, $paymentType, $amount, $serviceCharge = null)
    {
        $this->wpdb->insert(
            "{$this->wpdb->prefix}amlm_report",
            [
                'user_id' => $user_id,
                'withdraw_id' => $withdraw_id,
                'report' => $reportName,
                'payment_type' => $paymentType,
                'amount' => $amount,
                'service_charge' => $serviceCharge,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%d", "%s", "%s", "%d", "%d", "%s"]
        ); 
    }

    /**
     * Return a JSON message to the AJAX call
     *
     * @param $status  get the JSON status
     * @param $message get the JSON message
     * 
     * @return void
     */
    public function returnJSON($status, $message = null, $balance = null)
    {
        wp_send_json(
            [
                'status' => $status,
                'message' => $message,
                'balance' => $balance
            ]
        );

        wp_die();
    }
}