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

    /**
     * Register the enqueue actions
     * 
     * @return void
     */
    public function register()
    {
        add_action('wp_enqueue_scripts', [$this, 'siteEnqueue']);
        add_action('admin_enqueue_scripts', [$this, 'adminEnqueue']);
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
    }

    /**
     * Enqueue the admin scripts and styles
     * 
     * @return void
     */
    public function adminEnqueue()
    {
        wp_enqueue_style('amlm-admin-style', AMLM_PLUGIN_URL . 'assets/css/admin.css');
        wp_enqueue_script('chart-js', '//cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js', ['jquery'], '', true);
        wp_enqueue_script('admin-charts', AMLM_PLUGIN_URL . 'assets/js/build/admin-charts.js', ['jquery', 'chart-js'], '', true);
    }
}