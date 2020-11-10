<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb, $wp_roles;

?>
<div class="wrap amlm-wrap">
    <h2><?php esc_html_e('Members', 'amlm-locale'); ?></h2>
    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>

    <div class="all-members">
        <h3><?php esc_html_e( "All members", 'amlm-locale' ); ?></h3>

        <table>
            <tr>
                <th><?php _e('Name', 'amlm-locale'); ?></th>
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

                $members = new WP_User_Query([
                    'role__in' => ['amlm_sales_representative', 'amlm_distributor', 'amlm_unit_manager', 'amlm_manager', 'amlm_senior_manager', 'amlm_executive_manager', 'amlm_ass_g_manager', 'amlm_general_manager'],
                    'number' => $no_of_records_per_page,
                    'offset' => $offset
                ]);

                if ($members > 0) {
                    foreach ($members->results as $member) {
                        $currency = get_option('woocommerce_currency');
                        
                        $user_points = get_user_meta($member->ID, 'amlm_points', true);
                        $user_balance = get_user_meta($member->ID, 'amlm_earning', true);
                        $user_approved_balance = amlmMemberPaymentValue($member->ID, 'approved');
                        $user_due_balance = amlmMemberPaymentValue($member->ID, 'pending');

                        $output .= '<tr>' . $line_break;

                        $output .= sprintf('<td>%s</td>', userFullName($member)) . $line_break;
                        $output .= sprintf('<td>%s</td>', $wp_roles->roles[aMLMCurrentUserRole($member)]['name']) . $line_break;
                        $output .= sprintf('<td>%s</td>', ($user_points) ? $user_points : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_balance) ? $user_balance : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_approved_balance) ? $user_approved_balance : 0) . $line_break;
                        $output .= sprintf('<td>%1$s %2$s</td>', $currency . ' ', ($user_due_balance) ? $user_due_balance : 0) . $line_break;
        
                        $output .= '</tr>' . $line_break;
                    }
                }

                echo $output;
            ?>
        </table>

        <?php amlmMembersPagination($pageno, $no_of_records_per_page, $members->get_total());?>

    </div>
</div>
