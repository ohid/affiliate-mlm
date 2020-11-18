<?php
/** 
 * Various important functions of the plugin
 * 
 * PHP version 7.0
 * 
 * @category   Functions
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

/**
 * Get the current user role
 * 
 * @param object $user requires to pass the user object
 *
 * @return void
 */
function aMLMCurrentUserRole($user = null)
{
    if (is_user_logged_in()) {
        if ($user === null) {
            $user = wp_get_current_user();
        }
        $roles = ( array ) $user->roles;
        $current_role = \array_shift($roles);
        return $current_role;
    } else {
        return false;
    }
}

if (!function_exists('userFullName')) {

    /**
     * Return the user full name
     *
     * @return void
     */
    function userFullName( $user = null)
    {
        $name = '';

        if (is_user_logged_in()) {
            if ($user === null) {
                $user = wp_get_current_user();
            }

            if ($user->user_firstname || $user->user_lastname) {
                $name = $user->user_firstname . ' ' . $user->user_lastname;
            } else {
                $name = $user->display_name;
            }

            return $name;
        }
    }
}

if (!function_exists('dd')) {

    /**
     * The die and dump function for the plugin
     *
     * @param [type] $data requires to pass any type value
     * 
     * @return void
     */
    function dd($data)
    {
        ini_set("highlight.comment", "#969896; font-style: italic");
        ini_set("highlight.default", "#FFFFFF");
        ini_set("highlight.html", "#D16568");
        ini_set("highlight.keyword", "#7FA3BC; font-weight: bold");
        ini_set("highlight.string", "#F2C47E");
        $output = highlight_string("<?php\n\n" . var_export($data, true), true);
        echo "<div style=\"background-color: #1C1E21; padding: 1rem\">{$output}</div>";
    }
}

if (! function_exists('amlmEarningMoney')) {

    /**
     * Get the balance of the current user
     * 
     * @return void
     */
    function amlmEarningMoney()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            
            // Get the amlm_earning
            $amlm_earning = get_user_meta($user->ID, 'amlm_earning', true);

            return $amlm_earning;
        }

        return;
    }
}

if (! function_exists('amlmMemberPaymentValue')) {
    
    /**
     * Get the payment value of the user
     *
     * @param integer $user_id gets the user ID
     * @param string $status gets the payment_status value
     * 
     * @return void
     */
    function amlmMemberPaymentValue($user_id, $status)
    {
        global $wpdb;

        $amount = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}amlm_withdraw WHERE user_id = '{$user_id}' AND payment_status = '{$status}'");
        return $amount;
    }
}

/**
 * Replace 'customer' role (WooCommerce use by default) with your own one.
**/
add_filter('woocommerce_new_customer_data', 'wcAssignCustomRole', 10, 1);

/**
 * Assign a custom role for the new registered users via WooCommerce
 *
 * @param [type] $args gets the funciton argument
 * 
 * @return array
 */
function wcAssignCustomRole($args)
{
    $args['role'] = 'amlm_distributor';

    return $args;
}

/**
 * Generate the pagiation for the affilaites link template
 *
 * @param object  $wpdb pass the $wpdb object
 * @param object  $user pass the $user object
 * @param integer $pageno pass the pageno value
 * @param integer $offset pass the page offset value
 * @param integer $no_of_records_per_page pass the value
 * 
 * @return void
 */
function amlmLinksPagination($wpdb, $table, $user, $pageno, $offset, $no_of_records_per_page)
{
    $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}$table WHERE user_id = $user->ID");
    $total_pages = ceil($total_rows / $no_of_records_per_page);

    if ($total_pages > 1) {
        printf('<p class="page-current">%s: %s</p>', esc_html__('Currently at page', 'amlm-locale'), $pageno);
    }
    ?>

    <ul class="links-pagination">
        <?php 
        // Display the first button 
            
        if ($pageno != 1) { 
            printf('<li><a href="?pageno=%s">&#8656;</a></li>', 1);
        }

        // Display the previous button
        if ($pageno > 1) { 
            printf('<li><a href="?pageno=%s">%s</a></li>', ($pageno - 1), esc_html__('Prev', 'amlm-locale'));
        }

        // Display the next button
        if ($pageno < $total_pages) { 
            printf('<li><a href="?pageno=%s">%s</a></li>', ($pageno + 1), esc_html__('Next', 'amlm-locale'));
        }

        // Display the last button 
        if ($pageno < $total_pages) { 
            printf('<li><a href="?pageno=%s">&#8658;</a></li>', $total_pages);
        }
        ?>
    </ul>

    <?php
}



/**
 * Withdraw requests pagination 
 * 
 * @param object  $wpdb pass the $wpdb object
 * @param object  $user pass the $user object
 * @param integer $pageno pass the pageno value
 * @param integer $offset pass the page offset value
 * @param integer $no_of_records_per_page pass the value
 * 
 * @return void
 */
function withdrawRequestsPagination($wpdb, $table, $payment_status = null, $pageno, $offset, $no_of_records_per_page)
{

    if ($payment_status == null || $payment_status == 'all') {
        $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}$table");
    } else {
        $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}$table WHERE payment_status = '{$payment_status}'");
    }
    $total_pages = ceil($total_rows / $no_of_records_per_page);
    
    if ($total_pages > 1) {
        printf('<p class="page-current">%s: %s</p>', esc_html__('Currently at page', 'amlm-locale'), $pageno);
    }
    ?>
    
    <ul class="links-pagination">
        <?php 
        // Display the first button 

        $current_page_url = $_SERVER['REQUEST_URI'];
            
        if ($pageno != 1) {
            printf(
                '<li><a href="%s">&#8656;</a></li>', 
                add_query_arg(array('pageno' => 1), $current_page_url)
            );
        }

        // Display the previous button
        if ($pageno > 1) { 
            printf(
                '<li><a href="%s">%s</a></li>', 
                add_query_arg(array('pageno' => $pageno - 1), $current_page_url),
                esc_html__('Prev', 'amlm-locale')
            );
        }

        // Display the next button
        if ($pageno < $total_pages) {
            printf(
                '<li><a href="%s">%s</a></li>',
                add_query_arg(array('pageno' => $pageno + 1), $current_page_url),
                esc_html__('Next', 'amlm-locale')
            );
        }

        // Display the last button 
        if ($pageno < $total_pages) { 
            printf(
                '<li><a href="%s">&#8658;</a></li>', 
                add_query_arg(array('pageno' => $total_pages), $current_page_url)
            );
        }
        ?>
    </ul>

    <?php
}

/**
 * Members pagination
 *
 * @param integer $pageno gets the current pageno
 * @param integer $no_of_records_per_page gets the no of records to show per page
 * 
 * @return void
 */
function amlmMembersPagination($pageno, $no_of_records_per_page, $total_member)
{
    $total_pages = ceil($total_member / $no_of_records_per_page);

    if ($total_pages > 1) {
        printf('<p class="page-current">%s: %s</p>', esc_html__('Currently at page', 'amlm-locale'), $pageno);
    }
    ?>
    
    <ul class="links-pagination">
        <?php 
        
        // Display the first button 
        $current_page_url = $_SERVER['REQUEST_URI'];
            
        if ($pageno != 1) {
            printf(
                '<li><a href="%s">&#8656;</a></li>', 
                add_query_arg(array('pageno' => 1), $current_page_url)
            );
        }

        // Display the previous button
        if ($pageno > 1) { 
            printf(
                '<li><a href="%s">%s</a></li>', 
                add_query_arg(array('pageno' => $pageno - 1), $current_page_url),
                esc_html__('Prev', 'amlm-locale')
            );
        }

        // Display the next button
        if ($pageno < $total_pages) {
            printf(
                '<li><a href="%s">%s</a></li>',
                add_query_arg(array('pageno' => $pageno + 1), $current_page_url),
                esc_html__('Next', 'amlm-locale')
            );
        }

        // Display the last button 
        if ($pageno < $total_pages) { 
            printf(
                '<li><a href="%s">&#8658;</a></li>', 
                add_query_arg(array('pageno' => $total_pages), $current_page_url)
            );
        }
        ?>
    </ul>

    <?php
}