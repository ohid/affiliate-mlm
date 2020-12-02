<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

//
// Count the withdraw requests
//
$new_requests_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}amlm_withdraw WHERE payment_status = 'pending'");

$approved_requests_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}amlm_withdraw WHERE payment_status = 'approved'");

$declined_requests_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}amlm_withdraw WHERE payment_status = 'declined'");

$all_requests_count = $wpdb->get_var("SELECT count(*) FROM {$wpdb->prefix}amlm_withdraw");

//
// Build the requests URL
//
$new_requests = add_query_arg( array(
    'payment_status' => 'pending',
), $_SERVER['REQUEST_URI'] );

$approved_requests = add_query_arg( array(
    'payment_status' => 'approved',
), $_SERVER['REQUEST_URI'] );

$declined_requests = add_query_arg( array(
    'payment_status' => 'declined',
), $_SERVER['REQUEST_URI'] );

$all_requests = add_query_arg( array(
    'payment_status' => 'all',
), $_SERVER['REQUEST_URI'] );

//
// Get the pageno value for pagination
//
if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}

$argPaymentStatus = null;

$no_of_records_per_page = 5;
$offset = ($pageno-1) * $no_of_records_per_page;

    // Build the SQL query
    $sql = "SELECT w.id, w.user_id, w.amount, w.payment_type, w.payment_status, w.created_at, u.user_login FROM {$wpdb->prefix}amlm_withdraw w ";
    
    $sql .= "LEFT JOIN $wpdb->users u on w.user_id = u.id ";

    // If the payment status is selected
    if (isset($_GET['payment_status'])) {
        $argPaymentStatus = filter_var($_GET['payment_status'], FILTER_SANITIZE_STRING);
        if ($argPaymentStatus !== 'all') {
            $sql .= "WHERE w.payment_status = '{$argPaymentStatus}' ";
    }
    }


    if (isset($_GET['withdraw-search'])) {
        $searchWithdrawUserName = filter_var($_GET['withdraw-search'], FILTER_SANITIZE_STRING);
        $searchWithdrawID = filter_var($_GET['withdraw-search'], FILTER_SANITIZE_NUMBER_INT);
        $sql .= "WHERE u.user_login = '$searchWithdrawUserName' OR w.id = $searchWithdrawID ";
    }
    $sql .= "ORDER BY created_at DESC LIMIT $offset, $no_of_records_per_page";

    // Get the withdraw requests
    $withdraw_requests = $wpdb->get_results($sql);

?>
<div class="wrap amlm-wrap withdraw-requests-wrap">
    <h2><?php esc_html_e('Withdraw Requests', 'amlm-locale'); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>

    <div class="content-body clearfix">
        <div class="content-info-box">
            <div class="info-box">
                <a href="<?php echo $new_requests; ?>">
                    <h3><?php echo $new_requests_count; ?></h3>
                    <p><?php esc_html_e('New witdhraw requests', 'amlm-locale'); ?></p>
                </a>
            </div>
            <div class="info-box">
                <a href="<?php echo $approved_requests; ?>">
                    <h3><?php echo $approved_requests_count; ?></h3>
                    <p><?php esc_html_e('Approved widthdraw requests', 'amlm-locale'); ?></p>
                </a>
            </div>
            <div class="info-box">
                <a href="<?php echo $declined_requests; ?>">
                    <h3><?php echo $declined_requests_count; ?></h3>
                    <p><?php esc_html_e('Declined widthdraw requests', 'amlm-locale'); ?></p>
                </a>
            </div>
            <div class="info-box">
                <a href="<?php echo $all_requests; ?>">
                    <h3><?php echo $all_requests_count; ?></h3>
                    <p><?php esc_html_e('All widthdraw requests', 'amlm-locale'); ?></p>
                </a>
            </div>
        </div>
    </div>

    <div class="all-requests">
        <div class="before-table">
            <h3><?php

            $paymentStatusTxt = '';

            if (isset($_GET['payment_status'])) {
            $argPaymentStatus = filter_var($_GET['payment_status'], FILTER_SANITIZE_STRING);

            if ($argPaymentStatus == 'all' || $argPaymentStatus == 'pending' || $argPaymentStatus == 'approved' || $argPaymentStatus == 'declined') {
                if ($argPaymentStatus == 'pending') {
                    $paymentStatusTxt = 'New';
                } else {
                    $paymentStatusTxt = $argPaymentStatus;
                }
            }
            }

            printf('%s %s', ucfirst($paymentStatusTxt), __('Withdraw Requests', 'amlm-locale'));
            ?></h3>

            <div class="sorting-form">
                <form action="?page=amlm-withdraw-requests" method="get">
                    <input type="hidden" name="page" value="amlm-withdraw-requests">
                    <?php
                        printf(
                            '<input type="search" name="withdraw-search" class="withdraw-search" value="%s" placeholder="%s">',
                            isset($_GET['withdraw-search']) ? $_GET['withdraw-search'] : '',
                            esc_attr__('Enter withdraw id', 'amlm-locale')
                        );
                    ?>
                    <input type="submit" value="Search">
                </form>
            </div>
        </div>
        
        <table>
            <?php
            $line_break = "\r\n"; // we are using line break to format the HTML table in page source code

            $output = '';
            
            if (count($withdraw_requests) <= 0) {
                printf('<tr><td>%s</td></tr>', esc_html__('Sorry, nothing found!', 'amlm-locale'));
                exit;
            }
            foreach ($withdraw_requests as $request) {
                $requestedUser = get_user_by('id', $request->user_id);

                $member_url = add_query_arg( ['id' => $request->user_id], admin_url( 'admin.php?page=amlm-member' ) );

                $output .= '<tr>' . $line_break;
                    
                    $output .= sprintf('<th><a href="%s">%s</a></th>', $member_url, userFullName($requestedUser));

                    $output .= '<td>';
                    $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Withdraw #ID', 'amlm-locale'));
                    $output .= sprintf('<span class="cell-value">%s</span>', $request->id);
                    $output .= '</td>' . $line_break;

                    $output .= '<td>';
                    $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Amount requested', 'amlm-locale'));
                    $output .= sprintf('<span class="cell-value">%s</span>', $request->amount);
                    $output .= '</td>' . $line_break;

                    $output .= '<td>';
                    $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Payment method', 'amlm-locale'));
                    $output .= sprintf('<span class="cell-value">%s</span>', ucfirst($request->payment_type));
                    $output .= '</td>' . $line_break;

                    $output .= '<td>';
                    $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Payment status', 'amlm-locale'));
                    $output .= sprintf('<span class="cell-value">%s</span>', ucfirst($request->payment_status));
                    $output .= '</td>' . $line_break;

                    $output .= '<td>';
                    $output .= sprintf('<span class="cell-label">%s</span>', esc_html__('Date requested', 'amlm-locale'));
                    $output .= sprintf('<span class="cell-value">%s</span>', date(get_option('date_format'), strtotime($request->created_at)));
                    $output .= '</td>' . $line_break;

                    $output .= '<td class="cell-actios">';
                    $output .= sprintf(
                        '<a href="%s" class="overview-button">%s</a>',
                        add_query_arg(['withdraw_id' => $request->id], admin_url('admin.php?page=amlm-single-requests')),
                        esc_html__('Action', 'amlm-locale')
                    );
                    $output .= '</td>' . $line_break;

                $output .= '</tr>' . $line_break;
            }

            echo $output;
            ?>
        </table>

        <?php withdrawRequestsPagination($wpdb, 'amlm_withdraw', $argPaymentStatus, $pageno, $offset, $no_of_records_per_page);?>

    </div>