<?php
/** 
 * The main class of the plugin
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

class Class_Main
{

    protected $wpdb;

    protected $user;

    protected $referral_limit = 3;

    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register()
    {
        add_action('init', array($this, 'mainInit'));

        add_filter('woocommerce_locate_template', [$this, 'woocommerceLocaleTemplate'], 10, 3);

        add_action('wp_ajax_referral_form', [$this, 'referralForm']);
        add_action('wp_ajax_affiliate_form', [$this, 'affiliateForm']);
        add_action('wp_ajax_expand_referral_users', [$this, 'expandReferralUsers']);

        add_action( 'delete_user', [$this, 'afterDeleteUser'] );

        add_filter( 'plugin_action_links_' . AMLM_PLUGIN, [$this, 'amlmPluginLinks'] );

    }

    /**
     * Adds plugin page links
     */
    function amlmPluginLinks( $links ) {

        $plugin_links = array(
            '<a href="' . admin_url( 'admin.php?page=novozatra-mlm' ) . '">' . __( 'Dashboard', 'amlm-locale' ) . '</a>'
        );

        return array_merge( $plugin_links, $links );
    }


    /**
     * Initialize the main class
     *
     * @return void
     */
    public function mainInit()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
        // If the user logged in
        if (is_user_logged_in()) {
            $this->user = wp_get_current_user();
        }
    
        return;
    }
    
    /**
     * Custom WooCommerce templates
     * 
     * @param [type] $template      get the template
     * @param [type] $template_name get the template name
     * @param [type] $template_path get the template path
     * 
     * @return void
     */
    public function woocommerceLocaleTemplate($template, $template_name, $template_path)
    {
        global $woocommerce;
    
        $_template = $template;
    
        if (!$template_path) $template_path = $woocommerce->template_url;
        
        $plugin_path  = AMLM_PLUGIN_PATH . '/woocommerce/';
    
        // Look within passed path within the theme - this is priority
        $template = locate_template(
            [
                $template_path . $template_name,
                $template_name
            ]
        );
    
        // Modification: Get the template from this plugin, if it exists
        if (! $template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;
    
        // Use default template
        if (! $template)
        $template = $_template;
    
        // Return what we found
        return $template;
    }
    
    /**
     * Get the current user referral count
     *
     * @return void
     */
    public function userReferralCount()
    {
        $user = wp_get_current_user();

        $user_referrals_count = $this->wpdb->get_var("SELECT COUNT(*) from {$this->wpdb->prefix}amlm_referrals WHERE user_id = $user->ID");
        return $user_referrals_count;
    }

    /**
     * Referral Form uses on AJAX action
     *
     * @return void
     */
    public function referralForm()
    {
        // Check if the request is an AJAX request
        if (DOING_AJAX) {

            if ($this->userReferralCount() >= $this->referral_limit) {
                $this->returnJSON('error', 'You can not add more referral users.');
            }

            if (isset($_POST['referral_nonce']) && wp_verify_nonce($_POST['referral_nonce'], 'amlm_nonce')) {

                $first_name = sanitize_text_field($_POST['first_name']);
                $last_name = sanitize_text_field($_POST['last_name']);
                $username = sanitize_text_field($_POST['username']);
                $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
                $phone = sanitize_text_field( $_POST['phone'] );
                $password = sanitize_text_field($_POST['password']);

                if ($email === false) {
                    $this->returnJSON('error', 'Email is invalid.');
                }

                if (strlen($phone) <= 10) {
                    $this->returnJSON('error', 'Phone should be at least 11 characters.');
                }

                if ($this->phoneNumberExists($phone)) {
                    $this->returnJSON('error', 'The phone number already exists, please try a different phone number.');
                }

                if (8 > strlen($password)) {
                    $this->returnJSON('error', 'Password should be at least 8 characters.');
                }

                $this->createReferralUser($first_name, $last_name, $username, $email, $phone, $password);
            } else {
                $this->returnJSON('error', 'Form validation failed.');
            }
        }
    }

    /**
     * Create the referral user
     *
     * @param string $first_name get the first_name
     * @param string $last_name get the last_name
     * @param string $username get the username
     * @param string $email    get the phone
     * @param integer $phone    get the email
     * @param string $password    get the password
     * 
     * @return void
     */
    public function createReferralUser($first_name, $last_name, $username, $email, $phone, $password)
    {
        global $wpdb;

        // Check if the username is already exists
        $user_id = username_exists($username);

        if (! $user_id && false == email_exists($email)) {

            // Create the users
            $user_id = wp_create_user($username, $password, $email);

            if ($user_id) {
                // Set the referral relation
                $wpdb->insert($wpdb->prefix . 'amlm_referrals', ['user_id' => $this->user->ID, 'referral_id' => $user_id]);

                // Update the user first and last name
                update_user_meta($user_id, 'first_name', $first_name);
                update_user_meta($user_id, 'last_name', $last_name);

                // Add the amlm_points meta data for the user
                add_user_meta($user_id, 'amlm_points', 0);
                
                add_user_meta($user_id, 'amlm_user_phone', $phone);

                $user = get_user_by('id', $user_id);

                $user->set_role('amlm_distributor');

                // Send a notification to the user
                wp_send_new_user_notifications($user_id, 'both');
                
                // Send the JSON success message
                $this->returnJSON('success', 'Referral user created successfully!');
            }
        } else {
            // Send the JSON error message
            $this->returnJSON('error', 'Username or email already exists.');
        }
    }

    /**
     * Affiliate Form uses on AJAX action
     *
     * @return void
     */
    public function affiliateForm() 
    {
        if (DOING_AJAX) {

            if (! isset($_POST['affiliate_nonce']) && ! wp_verify_nonce($_POST['affiliate_nonce'], 'amlm_nonce')) {
                $this->returnJSON('error', 'Form validation failed.');
                return; 
            }
                        
            $product_link = filter_var($_POST['product_link'], FILTER_VALIDATE_URL);

            $product_link = esc_url_raw($_POST['product_link']);
            $campaign_name = sanitize_text_field($_POST['campaign_name']);

            $this->createAffiliateLink($product_link, $campaign_name);
        }
    }

    /**
     * Create the affiliate link
     * Using AJAX method
     *
     * @param [string] $product_link  link of the product grab from the form
     * @param [string] $campaign_name get the campaign name
     * 
     * @return void
     */
    public function createAffiliateLink($product_link, $campaign_name)
    {
        global $wpdb;

        // Generate the affiliate link
        $affiliate_link = add_query_arg(
            [
                'ref' => $this->user->ID,
                'campaign' => $campaign_name
            ],
            $product_link
        );

        $link_exists = $wpdb->get_var("SELECT affiliate_link from {$wpdb->prefix}amlm_affiliates_link WHERE affiliate_link = '{$affiliate_link}'");

        if ($link_exists) {
            $this->returnJSON('error', __('You have already created a affiliate for the URL with same campaign name, try creating a new one', 'amlm-locale'));
            return;
        }

        // Inserting the data into the table
        $wpdb->insert(
            "{$wpdb->prefix}amlm_affiliates_link",
            [
                'user_id' => $this->user->ID,
                'affiliate_link' => $affiliate_link,
                'campaign_name' => $campaign_name,
                'visits' => 0,
                'orders' => 0,
                'created_at' => date('Y-m-d H:i:s'),
            ],
            ["%d", "%s", "%s", "%d", "%d", "%s"]
        );
        
        $this->returnJSON('success', $affiliate_link);
    }

    /**
     * Expand referral users
     *
     * @return void
     */
    public function expandReferralUsers()
    {
        global $wpdb, $wp_roles;

        $user_id = filter_var($_POST['user_id'], FILTER_VALIDATE_INT);

        $childUsers = [];
        
        $users = $wpdb->get_col("SELECT referral_id from {$wpdb->prefix}amlm_referrals WHERE user_id = $user_id");
        
        if (! count($users) > 0) {
            $this->returnJSON('error', __("Doesn't have referral users", 'amlm-locale'));
        }
        
        foreach ($users as $user) {
            $childNodes = $wpdb->get_results("SELECT id, user_login, user_email from $wpdb->users WHERE id = $user");
        
            foreach ($childNodes as $user) {
                $point = get_user_meta( $user->id, 'amlm_points', true );
                $phone = get_user_meta( $user->id, 'amlm_user_phone', true );
        
                $userS = get_user_by( 'id', $user->id );
                $current_role = aMLMCurrentUserRole( $userS );
                $role = $wp_roles->roles[ $current_role ]['name'];
                $referral_users = hasReferralUsers($user->id) ? 'has-referral' : 'no-referral';
        
                $childUsers[] = array(
                    'id' => $user->id,
                    'user_login' => $user->user_login,
                    'user_email' => $user->user_email,
                    'phone' => $phone,
                    'role' => $role,
                    'points' => $point,
                    'referral_users' => $referral_users
                );
            }
        }

        $this->returnJSON('success', $childUsers);
    }

    /**
     * Check if the phone number already exists in the database
     *
     * @param [type] $phone
     * @return void
     */
    public function phoneNumberExists($phone)
    {
        $phone = $this->wpdb->get_results("SELECT meta_key FROM {$this->wpdb->usermeta} WHERE meta_key = 'amlm_user_phone' AND meta_value = '{$phone}'");

        return $phone;
    }

    /**
     * Delete the plugin related data associated with user
     *
     * @param integer $user_id
     * 
     * @return void
     */
    public function afterDeleteUser($user_id)
    {

        if ($user_id) {
            // Delete from referral users link
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_referrals WHERE referral_id = $user_id";
            $this->wpdb->query($query);

            // Delete bank details
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_bank_details WHERE user_id = $user_id";
            $this->wpdb->query($query);
            
            // Delete withdraw
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_withdraw WHERE user_id = $user_id";
            $this->wpdb->query($query);
            
            // Delete report
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_report WHERE user_id = $user_id";
            $this->wpdb->query($query);

            // Delete affiliate link
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_affiliates_link WHERE user_id = $user_id";
            $this->wpdb->query($query);

            // Delete affiliate earning
            $query = "DELETE FROM {$this->wpdb->prefix}amlm_affiliate_earnings WHERE user_id = $user_id";
            $this->wpdb->query($query);
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
