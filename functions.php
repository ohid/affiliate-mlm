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

if (! function_exists('getEarningHistory')) {

    /**
     * Get the earning history of the user
     * 
     * @return void
     */
    function getEarningHistory()
    {
        global $wpdb;

        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            
            $history = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}amlm_earning_history WHERE user_id=$user->ID");

            if ( $history ) {
                return $history;
            }

            return;
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

if (! function_exists('amlmMemberWithdrawCount')) {
    
    /**
     * Get the withdraw count of the user
     *
     * @param integer $user_id gets the user ID
     * @param string $status gets the payment_status value
     * 
     * @return void
     */
    function amlmMemberWithdrawCount($user_id, $status)
    {
        global $wpdb;
        $requests_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}amlm_withdraw WHERE user_id = '{$user_id}' AND payment_status = '{$status}'");
        return $requests_count;
    }
}

if (! function_exists('amlmBankDetailsInsertOrUpdate')) {

    /**
     * Insert or update the bank details for the user
     *
     * @param integer $user_id
     * @param string $table_name
     * @param array $data
     * @param array $format
     * 
     * @return void
     */
    function amlmBankDetailsInsertOrUpdate($user_id, $table_name, $data, $format)
    {
        global $wpdb;

        $bank_details = $wpdb->get_var("SELECT ID FROM {$wpdb->prefix}amlm_bank_details WHERE user_id= '{$user_id}'");

        // If the bank details exists then update the data
        if ($bank_details) {
            $wpdb->update(
                $table_name,
                $data,
                array('user_id' => $user_id),
                $format,
                array('%d')
            );
        } else {
            // If the bank details doesn't exists then create the row
            $wpdb->insert(
                $table_name,
                $data,
                $format
            );
        }
    }
}

if (! function_exists('amlmFilemtime')) {

    /**
     * Return the filemtime of a given file path
     *
     * @param [string] $file
     * @return [string] filemtime string
     */
    function amlmFilemtime($file) {
        // Retrieve the theme data.
        $the_theme = wp_get_theme();

        // Get the current version of the theme
        $theme_version = $the_theme->get('Version');

        if (file_exists(AMLM_PLUGIN_PATH . $file)) {
            return $theme_version . '.' . filemtime(wp_normalize_path(AMLM_PLUGIN_PATH . $file));
        }
    }
}

/**
 * Check if the given user has referral users
 *
 * @param inteter $user_id
 * 
 * @return boolean
 */
function hasReferralUsers($user_id)
{
    global $wpdb;

    $users = $wpdb->get_results("SELECT referral_id from {$wpdb->prefix}amlm_referrals WHERE user_id = $user_id");

    return (count($users) > 0);
}

/**
 * Add additional fields on the registration form
 *
 * @return void
 */
function amlmAdditionalRegistrationFields() {?>
    <p class="form-row form-row-first">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?><span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>
    <p class="form-row form-row-last">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?><span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>
    <div class="clear"></div>

    <p class="form-row form-row-wide">
    <label for="reg_billing_phone"><?php _e( 'Phone', 'woocommerce' ); ?><span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e( $_POST['billing_phone'] ); ?>" />
    </p>
    <?php
}
add_action( 'woocommerce_register_form_start', 'amlmAdditionalRegistrationFields' );

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