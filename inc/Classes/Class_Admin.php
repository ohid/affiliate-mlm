<?php
/** 
 * Register the admin menu
 * PHP version 7.0
 * 
 * @category   Class
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

namespace AMLM\Classes;

class Class_Admin
{

    /**
     * Register the admin menu
     * 
     * @return void
     */
    public function register()
    {
        add_action('admin_menu', [$this, 'adminMenu']);
    }

    /**
     * Create the admin menu
     * 
     * @return void
     */
    public function adminMenu()
    {
        // Add the NeerLab MLM page
        add_menu_page(
            'Novozatra', 
            'Novozatra', 
            'manage_options', 
            'novozatra-mlm',
            [$this, 'affiliateMLMFunc'],
            'dashicons-buddicons-buddypress-logo'
        );

        // Add the withdraw requests page
        add_submenu_page(
            'novozatra-mlm', 
            'Withdraw Requests', 
            'Withdraw Requests', 
            'manage_options', 
            'novozatra-withdraw-requests', 
            [$this, 'withdrawRequestsFunc']
        );

        // Add the NeerLab MLM members page
        add_submenu_page(
            'novozatra-mlm', 
            'Members', 
            'Members', 
            'manage_options', 
            'novozatra-members', 
            [$this, 'membersFunc']
        );

        // Add the NeerLab MLM single member page
        add_submenu_page(
            '', 
            'Members', 
            'Members', 
            'manage_options', 
            'novozatra-member',
            [$this, 'singleMmemberFunc']
        );

        // Add the single withdraw request page
        add_submenu_page(
            '', 
            'Withdraw Requests', 
            'Withdraw Requests', 
            'manage_options', 
            'novozatra-single-requests', 
            [$this, 'withdrawSingleRequestsFunc']
        );
    }

    /**
     * The affiliate MLM settings page template file
     *
     * @return void
     */
    public function affiliateMLMFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin/admin.php';
    }

    /**
     * The admin withdraw requests page template
     *
     * @return void
     */
    public function withdrawRequestsFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin/withdraw-requests.php';
    }

    /**
     * The admin members template
     *
     * @return void
     */
    public function membersFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin/members.php';
    }

    /**
     * The admin single member template
     *
     * @return void
     */
    public function singleMmemberFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin/single-members.php';
    }

    /**
     * The single withdraw requests page template
     *
     * @return void
     */
    public function withdrawSingleRequestsFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin/withdraw-single-requests.php';
    }
}