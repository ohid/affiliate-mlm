<?php 

/**
 * Get the current user role
 *
 * @return void
 */

function amlm_current_user_role( $user = null ) {
    if( is_user_logged_in() ) {
        if( $user === null ) {
            $user = wp_get_current_user();
        }
        $roles = ( array ) $user->roles;
        $current_role = \array_shift( $roles );
        return $current_role;
    } else {
        return false;
    }
}

if (!function_exists('dd')) {
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

if (! function_exists('amlm_earning_money')) {
    function amlm_earning_money()
    {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            
            // Get the amlm_earning
            $amlm_earning = get_user_meta( $user->ID, 'amlm_earning', true );

            return $amlm_earning;
        }

        return;
    }
}


/**
 * Replace 'customer' role (WooCommerce use by default) with your own one.
**/
add_filter('woocommerce_new_customer_data', 'wc_assign_custom_role', 10, 1);

function wc_assign_custom_role($args) {
  $args['role'] = 'amlm_sales_representative';
  
  return $args;
}


session_start();

$_SESSION['fav-color'] = 'red';

if (isset($_SESSION['fav-color'])) {
    $sessions = $_SESSION;
    $cookie = $_COOKIE;

    // dd($cookie);

    // 'affwp_ref' => '1',
    // 'affwp_ref_visit_id' => '1',
    // 'affwp_campaign' => 'monday',
}

function affiliate_links_pagination($wpdb, $user, $pageno, $offset, $no_of_records_per_page) {

    $total_rows = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}amlm_affiliates_link WHERE user_id = $user->ID");
    $total_pages = ceil($total_rows / $no_of_records_per_page);

    ?>

    <p class="page-current"><?php printf('Currently at page: %s', $pageno); ?></p>
    <ul class="links-pagination">
        <?php 
            // Display the first button 
            
            if($pageno != 1) { 
                printf('<li><a href="?pageno=%s">&#8656;</a></li>', 1);
            }

            // Display the previous button
            if($pageno > 1) { 
                printf('<li><a href="?pageno=%s">Prev</a></li>', ($pageno - 1));
            }

            // Display the next button
            if($pageno < $total_pages) { 
                printf('<li><a href="?pageno=%s">Next</a></li>', ($pageno + 1));
            }

            // Display the last button 
            if($pageno < $total_pages) { 
                printf('<li><a href="?pageno=%s">&#8658;</a></li>', $total_pages);
            }
        ?>
    </ul>

<?php
}