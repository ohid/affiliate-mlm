<?php
/** 
 * The settings class of the plugin
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

class Class_Settings
{

    protected $wpdb;

    protected $user;

    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register()
    {
        add_action('wp_ajax_commission_settings', [$this, 'commission_settings']);
    }

    /**
     * Update the commission settings of the plugin
     */
    public function commission_settings() {
        if (! isset($_POST['commission_settings']) && ! wp_verify_nonce($_POST['commission_settings'], 'amlm_nonce')) {
            $this->returnJSON('error', 'Form validation failed.');
        }

        // Store the user submission data in array
        $data = $this->sanitizeFields();

        $this->saveSettings( $data );

        $this->returnJSON('success', 'Commission percentages are saved!');
    }

    /**
     * Save the commission level settings
     * 
     * @param array $data
     */
    public function saveSettings( $data ) {
        foreach( $data as $field => $value ) {
            if ( ! empty( $field ) ) {
                update_option( $field, $value );
            }
        }
    }

    /**
     * Sanitize the fields
     * 
     * @return array $data
     */
    public function sanitizeFields() {
        $data = array();
        unset( $_POST['action'] );
        unset( $_POST['commission_settings'] );
        unset( $_POST['_wp_http_referer'] );

        foreach( $_POST as $key => $field ) {
            $data[ $key ] = filter_var( $field, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );
        }

        return $data;
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
