<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

$user = wp_get_current_user();
?>

<h4><b> <?php esc_html_e('Reports', 'amlm-locale'); ?><b/></h4>

<?php

// Get the referral users
$reports = $wpdb->get_results("SELECT report, payment_type, amount, service_charge from {$wpdb->prefix}amlm_report WHERE user_id = $user->ID");

?>

<table class="report-table">
    <tr>
        <th>Name</th>
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
                    $report->amount,
                    ($report->service_charge) ? $report->service_charge : 'n/a'
                );
            }

        endforeach;
    ?>
</table>