<?php
/** 
 * Enqueues the styles and scripts of the theme
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

class Class_Enqueue
{
    private $user;
    
    private $current_point = 0;
    
    private $current_balance = 0;

    /**
     * Register the enqueue actions
     * 
     * @return void
     */
    public function register()
    {
        add_action('init', [$this, 'mainInit']);
        add_action('wp_enqueue_scripts', [$this, 'siteEnqueue']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueue']);
    }

    public function mainInit()
    {

        if (is_user_logged_in()) {

            $this->user = wp_get_current_user();
            
            $amlm_points = get_user_meta($this->user->ID, 'amlm_points', true);
            $amlm_earning = get_user_meta($this->user->ID, 'amlm_earning', true);
    
            $this->current_point = $amlm_points;
            $this->current_balance = $amlm_earning;
        }
    }

    /**
     * Enqueue the site scripts and styles
     * 
     * @return void
     */
    public function siteEnqueue()
    {
        wp_enqueue_style('amlm-style', AMLM_PLUGIN_URL . 'assets/css/main.css');
        wp_enqueue_script('amlm-script', AMLM_PLUGIN_URL . 'assets/js/build/script.js', ['jquery'], '', true);
        
        wp_localize_script( 'amlm-script', 'amlm_object',
            array( 
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'current_points' => $this->current_point,
                'current_balance' => $this->current_balance,
            )
        );
    }

    /**
     * Enqueue the admin scripts and styles
     * 
     * @return void
     */
    public function adminEnqueue()
    {
        wp_enqueue_style('amlm-admin-style', AMLM_PLUGIN_URL . 'assets/css/admin.css');
        wp_enqueue_script('chart-js', '//cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js', ['jquery'], '', false);
        wp_enqueue_script('amlm-script', AMLM_PLUGIN_URL . 'assets/js/build/admin-script.js', ['jquery'], '', true);
    }
}