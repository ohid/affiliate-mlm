<?php
/** 
 * Uses the my account tabs API of WooCommerce to 
 * Add new endpoints and generate templates content
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

class Class_MyAccount_Tabs
{

    protected $is_distributor = false;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    protected $user;
        
    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register() 
    {
        add_action('init', array($this, 'tabInit'));

        add_action('woocommerce_account_referrals_endpoint', [$this, 'referralsEndpointContent']);
        add_action('woocommerce_account_affiliates_endpoint', [$this, 'affiliatesEndpointContent']);
        add_action('woocommerce_account_withdraw_endpoint', [$this, 'withdrawEndpointContent']);
        add_action('woocommerce_account_earnings_endpoint', [$this, 'earningsEndpointContent']);
        add_action('woocommerce_account_reports_endpoint', [$this, 'reportsEndpointContent']);
        
        add_filter('woocommerce_account_menu_items', [$this, 'myAccountMenuOrder']);
    }

    /**
     * Initialize all the necessary things
     *
     * @return void
     */
    public function tabInit()
    {
        $this->setCurrentPoints();
        $this->setDistributor();
        $this->myAccountNewEndpoints();
    }

    /**
     * Set current user points
     *
     * @return void
     */
    public function setCurrentPoints()
    {
        if (is_user_logged_in()) {
    
            $this->user = wp_get_current_user();
            $amlm_points = get_user_meta($this->user->ID, 'amlm_points', true);
    
            $this->amlm_user_current_points = $amlm_points;
        }
    
        return;
    }
        
    /**
     * Check if the current user is a distributor
     *
     * @return void
     */
    public function setDistributor()
    {
        if ($this->amlm_user_current_points > $this->distributor_points) {
            $this->is_distributor = true;

            $referral_count = get_user_meta($this->user->ID, 'referral_count', true);

            if (! $referral_count) {
                update_user_meta($this->user->ID, 'referral_count', 0);
            }
        }
    }

    /**
     * Add new endpoints for my-account tab
     *
     * @return void
     */
    public function myAccountNewEndpoints()
    {
        if ($this->is_distributor ) {
            add_rewrite_endpoint('referrals', EP_ROOT | EP_PAGES);
            add_rewrite_endpoint('affiliates', EP_ROOT | EP_PAGES);
            add_rewrite_endpoint('withdraw', EP_ROOT | EP_PAGES);
            add_rewrite_endpoint('earnings', EP_ROOT | EP_PAGES);
            add_rewrite_endpoint('reports', EP_ROOT | EP_PAGES);

            $referral_activated = get_user_meta($this->user->ID, 'amlm_url_flushed', true);

            if (empty($referral_activated)) {
                update_user_meta($this->user->ID, 'amlm_url_flushed', true);
                flush_rewrite_rules();
            }
        }

    }

    /**
     * Load the template for referrals endpoint
     *
     * @return void
     */
    public function referralsEndpointContent() 
    {
        include_once AMLM_PLUGIN_PATH . '/templates/referrals.php';
    }

    /**
     * Load the template for affiliates endpoint
     *
     * @return void
     */
    public function affiliatesEndpointContent() 
    {
        include_once AMLM_PLUGIN_PATH . '/templates/affiliates.php';
    }

    /**
     * Load the template for withdraw endpoint
     *
     * @return void
     */
    public function withdrawEndpointContent()
    {
        include_once AMLM_PLUGIN_PATH . '/templates/withdraw.php';
    }

    /**
     * Load the template for earnings endpoint
     *
     * @return void
     */
    public function earningsEndpointContent()
    {
        include_once AMLM_PLUGIN_PATH . '/templates/earnings.php';
    }

    /**
     * Load the template for reports endpoint
     *
     * @return void
     */
    public function reportsEndpointContent()
    {
        include_once AMLM_PLUGIN_PATH . '/templates/reports.php';
    }

    /**
     * Set the new menu order and new new tabs in my-account page
     *
     * @return void
     */
    public function myAccountMenuOrder()
    {
        $menuOrder = [
            'dashboard' => __('Dashboard', 'woocommerce'),
            'edit-account' => __('Profile', 'woocommerce'),
            'orders' => __('Orders', 'woocommerce'),
            'downloads' => __('Download', 'woocommerce')
        ];

        if ($this->is_distributor) {
            $menuOrder['referrals'] = __('Referrals', 'woocommerce');
            $menuOrder['affiliates'] = __('Affiliates', 'woocommerce');
            $menuOrder['withdraw'] = __('Withdraw', 'woocommerce');
            $menuOrder['earnings'] = __('Earnings', 'woocommerce');
            $menuOrder['reports'] = __('Reports', 'woocommerce');
        }

        $menuOrder['edit-address'] = __('Addresses', 'woocommerce');
        $menuOrder['customer-logout'] = __('Logout', 'woocommerce');

        return $menuOrder;
    }
}