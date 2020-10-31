<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;

?>
<div class="wrap amlm-wrap withdraw-requests-wrap">
    <h2><?php esc_html_e('Withdraw Requests', 'amlm-locale'); ?></h2>

    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>
    
    <div class="content-body clearfix">
        <div class="content-info-box">
        
            <div class="info-box">
                <h3>4</h3>
                <p><?php esc_html_e('New witdhraw requests'); ?></p>
            </div>
            <div class="info-box">
                <h3>15</h3>
                <p><?php esc_html_e('Approved widthdraw requests'); ?></p>
            </div>
            <div class="info-box">
                <h3>19</h3>
                <p><?php esc_html_e('All widthdraw requests'); ?></p>
            </div>

        </div>
    </div>

    <div class="all-requests">
        <h3><?php esc_html_e( "Requests", 'amlm-locale' ); ?></h3>
        <table>
            <tr>
                <th>User 1</th>
                <td>
                    <span class="cell-label">Amount requested</span>
                    <span class="cell-value">560</span>
                </td>
                
                <td>
                    <span class="cell-label">Payment method</span>
                    <span class="cell-value">bKash</span>
                </td>
                
                <td class="cell-actios">
                    <a href="#" class="overview-button">Action</a>
                </td>
            </tr>
        </table>
    </div>