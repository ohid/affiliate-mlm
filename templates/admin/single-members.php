<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;
$currency = get_option('woocommerce_currency');

// Redirect the user if id is not set
if ( ! isset($_GET['id'])) {
    wp_safe_redirect(admin_url('admin.php?page=novozatra-mlm'));
    exit;
}

// sanitize the ID variable
$id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

// Retrieve the user from the database
$member = $wpdb->get_row("SELECT * FROM $wpdb->users WHERE id= '{$id}'");

// If the member doesn't exists, redirect and exit
if (!$member) {
    wp_safe_redirect(admin_url('admin.php?page=novozatra-mlm'));
    exit;
}

// Get the withdraw information
$bank_details = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}amlm_bank_details WHERE user_id = '{$member->ID}'");

?>

<div class="wrap amlm-wrap withdraw-requests-wrap">
    <h2><?php printf('%s', __('Member Information', 'amlm-locale')); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>
    
    <div class="request-body">

        <h4><?php esc_html_e( 'Personal Information', 'amlm-locale' ); ?></h4>

        <div class="review-request">
            <div class="request-information">
                
                    <?php
                        $user = get_user_by( 'id', $member->ID );

                        $phone = get_user_meta($member->ID, 'amlm_user_phone', true);

                        // Get the profile picture attachment ID
                        $image_id = get_user_meta($member->ID, 'amlm_image', true);

                        if($image_id) {
                            $img = wp_get_attachment_image_src( $image_id, 'medium' );
                
                            printf('<div class="profile-picture"><img src="%s" alt="Profile picture"/></div>', $img[0]);
                        }

                        echo '<p>';
                        printf('<span class="info-label">%s</span>: <span class="info-value">#%s</span> <br>', __('User ID', 'amlm-locale'), $member->ID);
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Name', 'amlm-locale'), userFullName($user));
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Email', 'amlm-locale'), $member->user_email);
                        printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Phone', 'amlm-locale'), $phone ?: 'n/a' );
                        echo '</p>';

                    ?>
            </div>
        </div>

        <?php if($bank_details) : ?>
            <br>
            <h4><?php esc_html_e( 'Bank Information', 'amlm-locale' ); ?></h4>

            <div class="review-request">
                <div class="request-information">
                    <p>
                        <?php
                            if ($bank_details->bank_account_name) :
                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank Account Name', 'amlm-locale'), $bank_details->bank_account_name);

                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank Account Number', 'amlm-locale'), $bank_details->bank_account_number);
                                
                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank Name', 'amlm-locale'), $bank_details->bank_name);
                                
                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Bank Branch', 'amlm-locale'), $bank_details->bank_branch);
                            endif;

                            if ($bank_details->bkash_number) :
                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('bKash Number', 'amlm-locale'), $bank_details->bkash_number);
                            endif;

                            if ($bank_details->rocket_number) :
                                printf('<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', __('Rocket Number', 'amlm-locale'), $bank_details->rocket_number);
                            endif;
                        ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

            <br>
            <h4><?php esc_html_e( 'Site Information', 'amlm-locale' ); ?></h4>

            <div class="review-request">
                <div class="request-information">
                    <p>
                        <?php
                            $current_points = get_user_meta($member->ID, 'amlm_points', true);
                            $current_earning = get_user_meta($member->ID, 'amlm_earning', true);

                            printf(
                                '<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', 
                                __('Current Points', 'amlm-locale'),
                                $current_points ? round($current_points, 2) : 0
                            );

                            printf(
                                '<span class="info-label">%s</span>: <span class="info-value">%s</span> <br>', 
                                __('Current Earning', 'amlm-locale'),
                                $current_earning ? round($current_earning, 2) : 0
                            );
                            
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
        
    </div>
</div>
