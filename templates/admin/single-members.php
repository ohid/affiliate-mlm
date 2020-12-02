<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;
$currency = get_option('woocommerce_currency');

// Redirect the user if id is not set
if ( ! isset($_GET['id'])) {
    wp_safe_redirect(admin_url('admin.php?page=affiliate-mlm'));
    exit;
}

// sanitize the ID variable
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

// Retrieve the user from the database
$member = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE id= '{$id}'");

// If the member doesn't exists, redirect and exit
if (!$member) {
    wp_safe_redirect(admin_url('admin.php?page=affiliate-mlm'));
    exit;
}

// Get the withdraw information
$withdraw = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}amlm_withdraw WHERE user_id = '{$member->ID}'");

?>

<div class="wrap amlm-wrap withdraw-requests-wrap">
    <h2><?php printf('%s', __('Member Information', 'amlm-locale')); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>
    
    <div class="request-body">

        <h4><?php esc_html_e( 'Personal Information', 'amlm-locale' ); ?></h4>

        <div class="review-request">
            <div class="request-information">
                <p>
                    <?php
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Name', 'amlm-locale'), userFullName($member));
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Email', 'amlm-locale'), $member->user_email);

                    ?>
                </p>
            </div>
        </div>

        <?php if($withdraw) : ?>
            <br>
            <h4><?php esc_html_e( 'Bank Information', 'amlm-locale' ); ?></h4>

            <div class="review-request">
                <div class="request-information">
                    <p>
                        <?php
                            printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Name', 'amlm-locale'), 'Ohidul Islam');

                        ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <?php if($withdraw) : ?>
            <br>
            <h4><?php esc_html_e( 'Site Information', 'amlm-locale' ); ?></h4>

            <div class="review-request">
                <div class="request-information">
                    <p>
                        <?php
                            printf(
                                '<span class="info-label">%s</span>: <span class="info-value">%s - %s %s</span> <br>', 
                                __('New withdraw requests', 'amlm-locale'),
                                amlmMemberWithdrawCount($member->ID, 'pending') ?: '0',
                                $currency,
                                amlmMemberPaymentValue($member->ID, 'pending')?:'0'
                            );

                            printf(
                                '<span class="info-label">%s</span>: <span class="info-value">%s - %s %s</span> <br>', 
                                __('Approved withdraw requests', 'amlm-locale'),
                                amlmMemberWithdrawCount($member->ID, 'approved')?: '0',
                                $currency,
                                amlmMemberPaymentValue($member->ID, 'approved')?:'0'
                            );

                            printf(
                                '<span class="info-label">%s</span>: <span class="info-value">%s - %s %s</span> <br>', 
                                __('Declined withdraw requests', 'amlm-locale'),
                                amlmMemberWithdrawCount($member->ID, 'declined')?: '0',
                                $currency,
                                amlmMemberPaymentValue($member->ID, 'declined')?:'0'
                            );

                        ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
    </div>
</div>
