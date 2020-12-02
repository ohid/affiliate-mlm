<?php
/**
 * Plugin Name: NeerLab MLM
 * Plugin URI: 
 * Description: This is a custom built plugin for NeerLab MLM.
 * Version: 1.0
 * Requires at least: 4.6
 * Requires PHP: 7.0
 * Author: NeerLab MLM
 * Licence: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: affiliate-mlm
 * Domain Path: /languages
 */

defined('ABSPATH') or die('Hey, what are you doing here? You silly human!'); 

if (file_exists(dirname(__FILE__) . '/vendor/autoload.php')) {
    include_once dirname(__FILE__) . '/vendor/autoload.php';
}

/**
 * Define plugin variables
 */
if (!defined('AMLM_PLUGINS_PATH')) {
    define('AMLM_PLUGIN_PATH', plugin_dir_path(__FILE__));
    define('AMLM_PLUGIN_URL', plugin_dir_url(__FILE__));
    define('AMLM_PLUGIN', plugin_basename(__FILE__));
}

// Load the plugin text-domain
load_plugin_textdomain('amlm-locale', false, plugin_basename(__DIR__) . '/languages');

/**
 * Register the actiation hook
 *
 * @return class
 */
function Amlm_Plugin_activate()
{
    AMLM\Base\AMLM_Activate::activate();
}
register_activation_hook(__FILE__, 'Amlm_Plugin_activate');

/**
 * Register the deactiation hook
 *
 * @return class
 */
function Amlm_Plugin_deactivate()
{
    AMLM\Base\AMLM_Deactivate::deactivate();
}
register_deactivation_hook(__FILE__, 'Amlm_Plugin_deactivate');

if (file_exists(dirname(__FILE__) . '/functions.php')) {
    include_once dirname(__FILE__) . '/functions.php';
}

// Initializes the plugin services
if (class_exists('AMLM\AMLM_Init')) {
    AMLM\AMLM_Init::registerClasses();
}

if (file_exists(AMLM_PLUGIN_PATH . '/inc/Gateways/Balance_Payment_Gateway.php')) {
    require_once AMLM_PLUGIN_PATH . '/inc/Gateways/Balance_Payment_Gateway.php';
}