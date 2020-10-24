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
    "SELECT report, payment_type, amount, service_charge FROM {$wpdb->prefix}amlm_report 
    WHERE user_id = $user->ID
    ORDER BY created_at DESC
    LIMIT $offset, $no_of_records_per_page"
);

?>

<table class="report-table">
    <tr>
        <th>Title</th>
        <th>Payment type</th>
        <th>Amount</th>
        <th>Service charge</th>
    </tr>

    <?php 
        foreach( $reports as $report ) :

            if( $report ) {
                printf(
                    '<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>', 
                    $report->report,
                    ucfirst($report->payment_type),
                    $report->amount . ' ' . get_option('woocommerce_currency'),
                    ($report->service_charge) ? $report->service_charge : 'n/a'
                );
            }

        endforeach;
    ?>
</table>

<?php amlmLinksPagination($wpdb, 'amlm_report', $user, $pageno, $offset, $no_of_records_per_page); ?>
