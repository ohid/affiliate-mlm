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
