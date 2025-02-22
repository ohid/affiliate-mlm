<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb, $wp_roles;

?>
<div class="wrap amlm-wrap">
    <h2><?php esc_html_e('Members', 'amlm-locale'); ?></h2>
    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>

    <div class="all-members">
        <div class="before-table">
            <h3><?php esc_html_e( "All members", 'amlm-locale' ); ?></h3>

            <div class="sorting-form">
                <form action="?page=novozatra-members" method="get">
                    <input type="hidden" name="page" value="novozatra-members">
                    <?php
                        printf(
                            '<input type="search" name="member-search" class="member-search" value="%s" placeholder="%s">',
                            isset($_GET['member-search']) ? $_GET['member-search'] : '',
                            esc_attr__('Enter id, username, email, or phone', 'amlm-locale')
                        );
                    ?>
                    <input type="submit" value="Search">
                </form>
            </div>
        </div>

        <table>
            <tr>
                <th><?php _e('ID', 'amlm-locale'); ?></th>
                <th><?php _e('Name', 'amlm-locale'); ?></th>
                <th><?php _e('Email', 'amlm-locale'); ?></th>
                <th><?php _e('Phone', 'amlm-locale'); ?></th>
                <th><?php _e('Designation', 'amlm-locale'); ?></th>
                <th><?php _e('Points', 'amlm-locale'); ?></th>
                <th><?php _e('Current Balance', 'amlm-locale'); ?></th>
                <th><?php _e('Payment Received', 'amlm-locale'); ?></th>
                <th><?php _e('Payment Due', 'amlm-locale'); ?></th>
            </tr>
            
            <?php
                $line_break = "\r\n"; // we are using line break to format the HTML table in page source code

                //
                // Get the pageno value for pagination
                //
                if (isset($_GET['pageno'])) {
                    $pageno = $_GET['pageno'];
                } else {
                    $pageno = 1;
                }

                $argPaymentStatus = null;

                $no_of_records_per_page = 10;
                $offset = ($pageno-1) * $no_of_records_per_page;

                $output = '';

                // Define the user search query arguments
                $args = [
                    'role__in' => ['amlm_sales_representative', 'amlm_distributor', 'amlm_unit_manager', 'amlm_manager', 'amlm_senior_manager', 'amlm_executive_manager', 'amlm_ass_g_manager', 'amlm_general_manager'],
                    'number' => $no_of_records_per_page,
                    'offset' => $offset
                ];

                if (isset($_GET['member-search'])) {
                    // Sanitize the search field
                    $search_query = sanitize_text_field( $_GET['member-search'] );

                    $args['search'] = $search_query;
                    $args['search_columns'] = ['user_login', 'user_email', 'ID', 'user_nicename'];
                }

                $members = new WP_User_Query($args);
                
                if ($members > 0) {
                    foreach ($members->results as $member) {
                        $currency = get_option('woocommerce_currency');
                        
                        $user_points = get_user_meta($member->ID, 'amlm_points', true);
                        $user_balance = get_user_meta($member->ID, 'amlm_earning', true);
                        $user_phone = get_user_meta($member->ID, 'amlm_user_phone', true);
                        $user_approved_balance = amlmMemberPaymentValue($member->ID, 'approved');
                        $user_due_balance = amlmMemberPaymentValue($member->ID, 'pending');

                        $member_url = add_query_arg( ['id' => $member->ID], admin_url( 'admin.php?page=novozatra-member' ) );

                        $output .= '<tr>' . $line_break;

                        $output .= sprintf('<td>%s</td>', $member->ID) . $line_break;
                        $output .= sprintf('<td><a href="%s">%s</a></td>', $member_url, userFullName($member)) . $line_break;
                        $output .= sprintf('<td>%s</td>', $member->user_email) . $line_break;
                        $output .= sprintf('<td>%s</td>', $user_phone) . $line_break;
                        $output .= sprintf('<td>%s</td>', $wp_roles->roles[aMLMCurrentUserRole($member)]['name']) . $line_break;
                        $output .= sprintf('<td>%s</td>', ($user_points) ? round($user_points, 2) : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_balance) ? round($user_balance, 2) : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_approved_balance) ? round($user_approved_balance, 2) : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_due_balance) ? round($user_due_balance, 2) : 0) . $line_break;
        
                        $output .= '</tr>' . $line_break;
                    }
                }
                
                if($members->total_users == 0) {
                    printf('<tr><td>%s</td></tr>', esc_html__('Sorry, nothing found!', 'amlm-locale'));
                }

                echo $output;
            ?>
        </table>

        <?php amlmMembersPagination($pageno, $no_of_records_per_page, $members->get_total());?>

    </div>
</div>
