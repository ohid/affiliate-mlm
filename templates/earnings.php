<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$amlmEarningMoney = amlmEarningMoney();

if ($amlmEarningMoney) {
    printf(__('Your current balance: <b>%1$s %2$s</b>', 'amlm-locale'), $amlmEarningMoney, get_option('woocommerce_currency'));
} else {
    _e('You have not earned yet.', 'amlm-locale');
}