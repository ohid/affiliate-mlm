<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$amlm_earning_money = amlm_earning_money();

if ($amlm_earning_money) {
    printf(__('Your current balance: <b>%1$s %2$s</b>', 'amlm-locale'), $amlm_earning_money, get_option('woocommerce_currency'));
} else {
    echo __('You have not earned yet.', 'amlm-locale');
}