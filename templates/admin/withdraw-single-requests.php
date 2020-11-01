<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

// Check if the withdraw requests exists
// Redirect the request if there's no withdraw_id query arg set
if (! isset($_GET['withdraw_id'])) {
    wp_safe_redirect(admin_url('admin.php?page=affiliate-mlm'));
    exit;
}

$withdraw_id = filter_var($_GET['withdraw_id'], FILTER_SANITIZE_NUMBER_INT);

$withdrawRow = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}amlm_withdraw WHERE id= '{$withdraw_id}'");

// If the withdraw request does not exists
// Redirect the request to the affiliate-amlm page
if (!$withdrawRow) {
    wp_safe_redirect(admin_url('admin.php?page=affiliate-mlm'));
    exit;
}

?>

<div class="wrap amlm-wrap withdraw-requests-wrap">
    <h2><?php printf('%s #%s', __('Withdraw request', 'amlm-locale'), $withdraw_id); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>
    
    <div class="request-body">

        <h4><?php esc_html_e( 'Review request', 'amlm-locale' ); ?></h4>

        <div class="review-request">

            <div class="request-information">
                <p>
                    <span class="info-label">Requester name</span>: <span class="info-value">Ohidul Islam</span> <br>
                    <span class="info-label">Payment type</span>: <span class="info-value">bKash </span> <br>
                    <span class="info-label">Mobile No</span>: <span class="info-value">01587854</span> <br>
                </p>
            </div>
        </div>

        <div class="request-action">
            <h5><?php esc_html_e( 'Take action', 'amlm-locale' ); ?></h5>

            <form action="#" method="post" id="withdrawActionForm">
                <div class="form-group">

                    <label for="approve-request">
                        <input type="radio" name="withdraw-action" id="approve-request" value="approve"> <?php esc_html_e('Approve request'); ?>
                    </label>
                    
                    <label for="decline-request">
                        <input type="radio" name="withdraw-action" id="decline-request" value="decline"> <?php esc_html_e('Decline request'); ?>
                    </label>
                </div>

                <input type="hidden" name="action" value="withdraw_action">
                <?php wp_nonce_field( 'amlm_nonce', 'withdraw_action' ); ?>

                <button type="submit" class="amlm-wc-btn"><?php esc_html_e('Submit', 'amlm-locale'); ?></button>

                <p class="form-response"></p>
            </form>            
        </div>
    </div>
</div>
