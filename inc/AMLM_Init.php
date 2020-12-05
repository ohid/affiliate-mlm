<?php
/** 
 * The the initializer class of the plugin
 * It includes all the required classes
 * 
 * PHP version 7.0
 * 
 * @category   Class
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

namespace AMLM;

final class AMLM_Init
{
    /**
     * Store all the classes inside an array
     *
     * @return array full list of classes
     */
    public static function getServices()
    {
        return [
            Classes\Class_Admin::class,
            Classes\Class_Main::class,
            Classes\Class_Earning_Calculator::class,
            Classes\Class_Affiliate_Link::class,
            Classes\Class_Withdraw::class,
            Classes\Class_MyAccount_Tabs::class,
            Classes\Class_User_Rank::class,
            Classes\Class_Enqueue::class,
            Classes\Class_Custom_Woo::class,
        ];
    }

    /**
     * Loop through the classes, initialize them
     * and call the register() method if exists
     *
     * @return void
     */
    public static function registerClasses()
    {
        foreach (self::getServices() as $service) {
            $service = self::instantiate($service);
            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the classes
     *
     * @param class $class from the service array  
     * 
     * @return class instance new instance of the new class
     */
    public static function instantiate($class)
    {
        return new $class;
    }
}