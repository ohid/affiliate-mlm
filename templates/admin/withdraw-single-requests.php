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

$withdraw = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}amlm_withdraw WHERE id= '{$withdraw_id}'");

// If the withdraw request does not exists
// Redirect the request to the affiliate-amlm page
if (!$withdraw) {
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
                    <?php
                        $user = get_user_by( 'id', $withdraw->user_id );

                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Name'), userFullName($user));

                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Payment type'), ucfirst($withdraw->payment_type) );

                        if ($withdraw->payment_type == 'bkash' || $withdraw->payment_type == 'rocket') {
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Mobile no'), $withdraw->mobile_number );
                        }
                        
                        if ($withdraw->payment_type == 'bank') {
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank account name'), $withdraw->bank_account_name );
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank account number'), $withdraw->bank_account_number );
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank name'), $withdraw->bank_name );
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Branch'), $withdraw->bank_branch );
                        }

                        echo '<br>';

                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Requested Amount'), get_option('woocommerce_currency') . ' ' .$withdraw->amount);

                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Amount should pay'), get_option('woocommerce_currency') . ' ' . ( (70 / 100) * $withdraw->amount ) );
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Service charge deduct'), get_option('woocommerce_currency') . ' ' . ( (30 / 100) * $withdraw->amount ));

                        echo '<br>';

                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Withdraw status'), ucfirst($withdraw->payment_status));

                    ?>
                    
                </p>
            </div>
        </div>

        <div class="request-action">
            <h5><?php esc_html_e( 'Take action', 'amlm-locale' ); ?></h5>

            <?php if ($withdraw->payment_status == 'pending'): ?>
                <form action="#" method="post" id="withdraw-action-form">
                    <div class="form-group">

                        <label for="approve-request">
                            <input type="radio" name="withdraw-action" id="approve-request" value="approved"> <?php esc_html_e('Approve request'); ?>
                        </label>
                        
                        <label for="decline-request">
                            <input type="radio" name="withdraw-action" id="decline-request" value="declined"> <?php esc_html_e('Decline request'); ?>
                        </label>
                    </div>

                    <input type="hidden" name="witdhraw_id" value="<?php echo $withdraw->id; ?>">
                    <input type="hidden" name="action" value="withdraw_action">
                    <?php wp_nonce_field( 'amlm_nonce', 'withdraw_action' ); ?>

                    <button type="submit" class="amlm-wc-btn"><?php esc_html_e('Submit', 'amlm-locale'); ?></button>

                    <p class="form-response">ad</p>
                </form>  
            <?php elseif ($withdraw->payment_status == 'approved' || $withdraw->payment_status == 'declined') : ?>
                <p><?php echo __('The withdraw request is ', 'amlm-locale') . $withdraw->payment_status . __(' and no further action can be taken!', 'amlm-locale'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>
