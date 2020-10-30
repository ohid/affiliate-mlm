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
        add_menu_page(
            'Affiliate MLM', 
            'Affiliate MLM', 
            'manage_options', 
            'affiliate-mlm', 
            [$this, 'affiliateMLMFunc']
        );
    }

    /**
     * The affiliate MLM settings page template file
     *
     * @return void
     */
    public function affiliateMLMFunc()
    {
        require_once AMLM_PLUGIN_PATH . 'templates/admin.php';
    }
}