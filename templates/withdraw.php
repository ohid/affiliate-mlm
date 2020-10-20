<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

$amlmEarningMoney = amlmEarningMoney();

if ($amlmEarningMoney) {
    // printf(__('Your current balance: <b>%1$s %2$s</b>', 'amlm-locale'), $amlmEarningMoney, get_option('woocommerce_currency'));
} else {
    _e('You have not earned yet.', 'amlm-locale');
}

?>

<h4><b> <?php esc_html_e('Make a withdraw request', 'amlm-locale'); ?><b/></h4>

<form action="#" class="withdraw-form amlm-form" id="withdraw-form" method="post">
    <div class="form-group">
        <label for="payment-type"><?php esc_html_e('Payment type', 'amlm-locale'); ?></label>
        <select name="" id="payment-type" class="form-control payment-type">
            <option value="bkash">bKash</option>
            <option value="rocket">Rocket</option>
            <option value="bank">Bank</option>
        </select>
    </div>

    <div class="form-group group-bkash-number">
        <label for="bkash-number"><?php esc_html_e('bKash number', 'amlm-locale'); ?></label>
        <input type="text" class="form-control bkash-number" id="bkash-number" placeholder="<?php esc_html_e('Enter your bKash number', 'amlm-locale'); ?>">
    </div>

    <div class="form-group group-rocket-number">
        <label for="rocket-number"><?php esc_html_e('Rocket number', 'amlm-locale'); ?></label>
        <input type="text" class="form-control rocket-number" id="rocket-number" placeholder="<?php esc_html_e('Enter your Rocket number', 'amlm-locale'); ?>">
    </div>

    <div class="form-group group-bank-account">
        <label for="bank-account-name"><?php esc_html_e('Bank account name', 'amlm-locale'); ?></label>
        <input type="text" class="form-control bank-account-name" id="bank-account-name" placeholder="<?php esc_html_e('Enter your bank account name', 'amlm-locale'); ?>">
    </div>

    <div class="form-group group-bank-account">
        <label for="bank-account-number"><?php esc_html_e('Bank account number', 'amlm-locale'); ?></label>
        <input type="text" class="form-control bank-account-number" id="bank-account-number" placeholder="<?php esc_html_e('Enter your bank account number', 'amlm-locale'); ?>">
    </div>

    <div class="form-group group-bank-account">
        <label for="bank-name"><?php esc_html_e('Name of the bank', 'amlm-locale'); ?></label>
        <input type="text" class="form-control bank-name" id="bank-name" placeholder="<?php esc_html_e('Enter the name of the bank', 'amlm-locale'); ?>">
    </div>

    <div class="form-group group-bank-account">
        <label for="bank-branch"><?php esc_html_e('Branch', 'amlm-locale'); ?></label>
        <input type="text" class="form-control bank-branch" id="bank-branch" placeholder="<?php esc_html_e('Enter the branch name', 'amlm-locale'); ?>">
    </div>

    <div class="form-group">
        <label for="withdraw-amount"><?php esc_html_e('Withdraw amount', 'amlm-locale'); ?></label>
        <input type="text" class="form-control withdraw-amount" id="withdraw-amount" placeholder="<?php esc_html_e('Amount e.g. 100', 'amlm-locale'); ?>">
    </div>

    <div class="form-group">
        <button type="submit" class="request-withdraw"><?php esc_html_e('Request withdraw', 'amlm-locale'); ?></button>
    </div>

</form>