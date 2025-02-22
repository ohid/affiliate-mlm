<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

$user = wp_get_current_user();

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}

$no_of_records_per_page = 5;
$offset = ($pageno-1) * $no_of_records_per_page;

?>

<h4><b> <?php esc_html_e('Reports', 'amlm-locale'); ?><b/></h4>

<?php

// Get the reports
$reports = $wpdb->get_results(
    "SELECT r.report, r.payment_type, r.amount, r.service_charge, r.created_at, w.id as withdrawID  
    FROM {$wpdb->prefix}amlm_report r
    LEFT JOIN {$wpdb->prefix}amlm_withdraw w on r.withdraw_id = w.id
    WHERE r.user_id = $user->ID
    ORDER BY created_at DESC
    LIMIT $offset, $no_of_records_per_page"
);

?>

<table class="report-table">
    <tr>
        <th>Withdraw ID</th>
        <th>Title</th>
        <th>Payment type</th>
        <th>Amount</th>
        <th>Service charge</th>
        <th>Date</th>
    </tr>

    <?php 
        foreach( $reports as $report ) :

            if( $report ) {
                printf(
                    '<tr>
                        <td>#%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>', 
                    $report->withdrawID,
                    $report->report,
                    ucfirst($report->payment_type),
                    $report->amount . ' ' . get_option('woocommerce_currency'),
                    ($report->service_charge) ? $report->service_charge . ' ' . get_option('woocommerce_currency') : 'n/a',
                    date(get_option('date_format'), strtotime($report->created_at))
                );
            }

        endforeach;
    ?>
</table>

<?php amlmLinksPagination($wpdb, 'amlm_report', $user, $pageno, $offset, $no_of_records_per_page); ?>
