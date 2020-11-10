<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb, $wp_roles;

// Query for the total pending and approved amount
$due_payment = $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->usermeta WHERE meta_key = 'amlm_earning'");
$total_points = $wpdb->get_var("SELECT SUM(meta_value) FROM $wpdb->usermeta WHERE meta_key = 'amlm_points'");
$approved_payment = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}amlm_withdraw WHERE payment_status = 'approved'");

$currency = get_option('woocommerce_currency');

?>
<div class="wrap amlm-wrap">
    <h2><?php esc_html_e('Affiliate MLM', 'amlm-locale'); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>

    <div class="content-body clearfix">
        <div class="content-left">
            <div class="content-info-box">
                <div class="info-box">
                    <h3><?php echo $total_members = count( get_users( [ 'role__in' => 
                        [ 'amlm_sales_representative', 'amlm_distributor', 'amlm_unit_manager', 'amlm_manager', 'amlm_senior_manager', 'amlm_executive_manager', 'amlm_ass_g_manager', 'amlm_general_manager' ] 
                    ] ) );
                    ?></h3>
                    <p><?php esc_html_e('Total Members', 'amlm-locale') ?></p>
                </div>
                <div class="info-box">
                    <h3><?php echo $currency . ' ' . ($total_points ? $total_points : '0'); ?></h3>
                    <p><?php esc_html_e('Total Points in Market', 'amlm-locale') ?></p>
                </div>
                
                <div class="info-box">
                    <h3><?php echo $currency . ' ' . ($approved_payment ? $approved_payment : '0'); ?></h3>
                    <p><?php esc_html_e('Total Payments Approved', 'amlm-locale') ?></p>
                </div>
            </div>

            <div class="growth-performance chart-box">
                <h3><?php esc_html_e('Growth Performance', 'amlm-locale') ?></h3>

                <canvas id="growthChart"></canvas>
            </div>
        </div>
        <div class="content-right">
            <div class="content-info-box">
                <div class="info-box">
                    <h3><?php echo $currency . ' ' . ($due_payment ? $due_payment : '0') ; ?></h3>
                    <p><?php esc_html_e('Total Market Due', 'amlm-locale') ?></p>
                </div>
            </div>

            <div class="members-circle-chart chart-box">
                <h3><?php esc_html_e('Top', 'amlm-locale') ?></h3>
                <canvas id="membersChart" width="400" height="450"></canvas>
            </div>
        </div>
    </div>

    <div class="clients-overview">
        <h3><?php esc_html_e( "Client's overview: High to Low", 'amlm-locale' ); ?></h3>
        <table>

        <?php
            $line_break = "\r\n"; // we are using line break to format the HTML table in page source code

            // Query for the users
            $users = $wpdb->get_results("SELECT u.ID, um.meta_value, convert(um.meta_value, UNSIGNED INTEGER) AS meta_value
            FROM $wpdb->users u 
            LEFT JOIN $wpdb->usermeta um ON u.ID = um.user_id
            WHERE um.meta_key = 'amlm_points'
            ORDER BY meta_value DESC
            LIMIT 5");

            foreach ($users as $user) {
            // Retrieve the payment amount for members
            $payment_amount = $wpdb->get_var("SELECT SUM(amount) FROM {$wpdb->prefix}amlm_withdraw WHERE payment_status = 'approved' and user_id = {$user->ID}");
            
            $output = '<tr>' . $line_break;
                $user_obj = get_user_by( 'id', $user->ID );

                $output .= sprintf('<th>%s</th>', userFullName($user_obj)) . $line_break;

                $output .= sprintf('<td class="cell-role"><span class="cell-label">%s</span>', esc_html__('Position', 'amlm-locale'));
                $output .= sprintf('<span class="cell-value">%s</span>', $wp_roles->roles[ aMLMCurrentUserRole($user_obj) ]['name']);
                $output .= '</td>' . $line_break;

                $output .= sprintf('<td class="cell-point"><span class="cell-label">%s</span>', esc_html__('Point', 'amlm-locale'));
                $output .= sprintf('<span class="cell-value">%s</span>', $user->meta_value ? $user->meta_value : 'n/a') ;
                $output .= '</td>' . $line_break;

                $output .= '<td class="cell-payment">';
                $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Payment', 'amlm-locale'));
                $output .= sprintf('<span class="cell-value">%s</span>', $payment_amount ? $payment_amount : 'n/a');
                $output .= '</td>' . $line_break;

                $output .= '<td class="cell-actions">';
                $output .= sprintf('<a href="#" class="options overview-button">%s</a>', esc_html__('Options', 'amlm-locale'));
                $output .= sprintf('<a href="%s" class="details overview-button">%s</a>', get_edit_user_link($user->ID), esc_html__('Details', 'amlm_locale'));
                $output .= '</td>' . $line_break;
            $output .= '</tr>' . $line_break;

            echo $output;

            }
        ?>
            
        </table>
    </div>
</div>


<?php

// Get the members count
$membersCount = count_users();

// The the members available role
$availRoles = $membersCount['avail_roles'];

// Unset the unnecessary roles
unset($availRoles['administrator']);
unset($availRoles['editor']);
unset($availRoles['author']);
unset($availRoles['contributor']);
unset($availRoles['subscriber']);
unset($availRoles['shop_manager']);
unset($availRoles['customer']);
unset($availRoles['none']);

$membersRoles = array_keys($availRoles);
$membersCount = array_values($availRoles);

// Sorting the array
arsort($availRoles);

$sMembersRoles = array_keys($availRoles);
$sMembersCount = array_values($availRoles);
?>


<script>
// Groth chart configuration
var growthChartConfig = {
    type: 'line',
    data: {
        labels: [
            <?php
                $countMembers = count($membersRoles);
                $count = 1;

                foreach ($membersRoles as $member) {
                    if ($count == $countMembers) {
                        echo "'" . $wp_roles->roles[ $member ]['name'] . "'";
                    } else {
                        echo "'" . $wp_roles->roles[ $member ]['name'] . "',";
                    }
                    $count++;
                }
            ?>
        ],
        datasets: [{
            backgroundColor: '#70a1ff',
            borderColor: '#1e90ff',
            data: [
                <?php printf("'%s'", implode("','", $membersCount) ); ?>
            ],
            label: 'Dataset',
            fill: 'start'
        }]
    },
    options: {
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: true,
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

// Members chart configuration
var membersChartConfig = {
    type: 'doughnut',
    data: {
        datasets: [{
            data: [
                <?php printf("'%s'", implode("','", $sMembersCount) ); ?>
            ],
            backgroundColor: [
                '#63cdda',
                '#f8a5c2',
                '#34ace0',
            ],
            label: 'Dataset 1'
        }],
        labels: [
            <?php
                $countMembers = count($membersRoles);
                $count = 1;

                foreach ($sMembersRoles as $member) {
                    if ($count == $countMembers) {
                        echo "'" . $wp_roles->roles[ $member ]['name'] . "'";
                    } else {
                        echo "'" . $wp_roles->roles[ $member ]['name'] . "',";
                    }
                    $count++;
                }
            ?>
        ]
    },
    options: {
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: true,
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    }
};

// Members chart
var topCtx = document.getElementById('membersChart').getContext('2d');
window.membersDoughnut = new Chart(topCtx, membersChartConfig);

// Growth chart
var growthCtx = document.getElementById('growthChart').getContext('2d');
window.growthChart = new Chart(growthCtx, growthChartConfig);

</script>