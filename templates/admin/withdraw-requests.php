<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

//
// Build the requests URL
//
$new_requests = add_query_arg( array(
    'payment_status' => 'pending',
), $_SERVER['REQUEST_URI'] );

$approved_requests = add_query_arg( array(
    'payment_status' => 'approved',
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

$no_of_records_per_page = 5;
$offset = ($pageno-1) * $no_of_records_per_page;


    // Build the SQL query
    $sql = "SELECT * FROM {$wpdb->prefix}amlm_withdraw ";

    // If the payment status is selected
    if (isset($_GET['payment_status'])) {
        $argPaymentStatus = filter_var($_GET['payment_status'], FILTER_SANITIZE_STRING);
        if ($argPaymentStatus !== 'all') {
            $sql .= "WHERE payment_status = '{$argPaymentStatus}'";
        }
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
                    <h3>4</h3>
                    <p><?php esc_html_e('New witdhraw requests'); ?></p>
                </a>
            </div>
            <div class="info-box">
                <a href="<?php echo $approved_requests; ?>">
                    <h3>15</h3>
                    <p><?php esc_html_e('Approved widthdraw requests'); ?></p>
                </a>
            </div>
            <div class="info-box">
                <a href="<?php echo $all_requests; ?>">
                    <h3>19</h3>
                    <p><?php esc_html_e('All widthdraw requests'); ?></p>
                </a>
            </div>

        </div>
    </div>

    <div class="all-requests">
        <h3><?php

        $paymentStatusTxt = '';

        if (isset($_GET['payment_status'])) {
           $argPaymentStatus = filter_var($_GET['payment_status'], FILTER_SANITIZE_STRING);

           if ($argPaymentStatus == 'all' || $argPaymentStatus == 'pending' || $argPaymentStatus == 'approved' ) {
               if ($argPaymentStatus == 'pending') {
                   $paymentStatusTxt = 'New';
               } else {
                   $paymentStatusTxt = $argPaymentStatus;
               }
           }
        }

        printf('%s %s', ucfirst($paymentStatusTxt), __('Withdraw Requests', 'amlm-locale'));

        // echo printf("%$1s %$2s", $paymentStatusTxt, esc_html__( "Withdraw Requests", 'amlm-locale' )) ?>

        </h3>
        <table>
            <?php

            $output = '';

            foreach ($withdraw_requests as $request) {
                $requestedUser = get_user_by('id', $request->user_id);

                $output .= '<tr>';
                    
                    $output .= sprintf('<th>%s</th>', userFullName($requestedUser));

                    $output .= '<td>
                        <span class="cell-label">Amount requested</span>';
                        $output .= sprintf('<span class="cell-value">%s</span>', $request->amount);

                    $output .= '</td>';
                    
                    $output .= '<td>
                        <span class="cell-label">Payment method</span>';
                        $output .= sprintf('<span class="cell-value">%s</span>', ucfirst($request->payment_type));
                
                    $output .= '</td>';
                
                    $output .= '<td>

                        <span class="cell-label">Date requested</span>';
                        $output .= sprintf('<span class="cell-value">%s</span>', date(get_option('date_format'), strtotime($request->created_at)));
                    
                    $output .= '</td>';

                    $output .= '<td class="cell-actios">';
                    $output .= sprintf(
                        '<a href="%s" class="overview-button">%s</a>',
                        add_query_arg(['withdraw_id' => $request->id], admin_url('admin.php?page=amlm-single-requests')),
                        esc_html__('Action', 'amlm-locale')
                    );
                    
                    $output .= '</td>
                </tr>';
            }
            
            echo $output;
            ?>
        </table>

    </div>