<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$amlmEarningMoney = amlmEarningMoney();
$earningHistory = getEarningHistory();

if ($amlmEarningMoney) {
    printf(__('Your current balance: <b>%1$s %2$s</b>', 'amlm-locale'), round($amlmEarningMoney, 2), get_option('woocommerce_currency'));
} else {
    _e('You have not earned yet.', 'amlm-locale');
}

echo '<div class="earning-history">';
    printf('<h3>%s</h3>', __('Earning history', 'affiliate-mlm'));

    if ( ! $earningHistory ) {
        printf('<p>%s</p>', __('Sorry, you do not have any earning history!', 'affiliate-mlm'));
    } else {
		$currency = get_option('woocommerce_currency');
        echo '<table>';
        echo '<tr>';
            printf('<td>%s</td>', __('Referral name', 'affiliate-mlm'));
            printf('<td>%s</td>', __('Order amount', 'affiliate-mlm'));
            printf('<td>%s</td>', __('Referral nonus', 'affiliate-mlm'));
            printf('<td>%s</td>', __('Date', 'affiliate-mlm'));
        echo '</tr>';
        foreach( $earningHistory as $history ) {
            $user = get_user_by('id', $history->child_user_id);
            $full_name = userFullName( $user );
            echo '<tr>';
                printf('<td>%s</td>', $full_name);
                printf('<td>%s</td>', $currency . ' ' . $history->order_total);
                printf('<td>%s</td>', $currency . ' ' . $history->bonus);
                printf('<td>%s</td>', $history->created_at);
            echo '</tr>';
        }
        echo '</table>';
    }

echo '</div>';