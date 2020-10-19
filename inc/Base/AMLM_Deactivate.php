<?php
/** 
 * The deactivator class of the plugin
 * PHP version 7.0
 * 
 * @category   Class
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

namespace AMLM\Base;

class AMLM_Deactivate
{
    /**
     * Run the method on plugin deactivation
     *
     * @return void
     */
    public static function deactivate()
    {
        // Flush the rewrite rules
        flush_rewrite_rules();
    }
}