<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

?>
<div class="wrap amlm-wrap">
    <h2><?php esc_html_e('Affiliate MLM', 'amlm-locale'); ?></h2>

    <div class="header ">
        <div class="user-info">
            <div class="user-detail">
                <h4><?php echo userFullName(); ?></h4>
                <p><?php echo aMLMCurrentUserRole(); ?></p>
            </div>
            <div class="user-avater">
                <img src="http://0.gravatar.com/avatar/369ddaf0a5c93430811ad4a48c3ca84a?s=60&d=mm&r=g" alt="">
            </div>
        </div>
    </div>

    <div class="content-body clearfix">
        <div class="content-left">
            <div class="content-info-box">
                <div class="info-box">
                    <h3>Total Members</h3>
                    <p>21354</p>
                </div>
                <div class="info-box">
                    <h3>Total</h3>
                    <p>21354</p>
                </div>
            </div>

            <div class="growth-performance chart-box">
                <h3>Growth Performance</h3>

                <canvas id="growthChart"></canvas>
            </div>
        </div>
        <div class="content-right">
            <div class="content-info-box">
                <div class="info-box">
                    <h3>Total Market Due</h3>
                    <p>21354</p>
                </div>
            </div>

            <div class="members-circle-chart chart-box">
                <h3>Top</h3>
                <canvas id="membersChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>

    <div class="clients-overview">
        <h3>Client's overview: High to Low</h3>
        <table>

        <tr>
                <th>Jerry Mattedi</th>
                <td class="cell-role">
                    <span class="role-label">Position</span>
                    <span class="role-title">General Manager</span>
                </td>
                <td class="cell-point">
                    <span class="point-label">Point</span>
                    <span class="point-value">4578</span>
                </td>
                <td class="cell-payment">
                    <span class="payment-label">Payment</span>
                    <span class="payment-value">48785</span>
                </td>
                <td class="cell-actions">
                    <a href="#" class="options overview-button">Options</a>
                    <a href="#" class="details overview-button">Details</a>
                </td>
            </tr>
            
            <tr>
                <th>Jerry Mattedi</th>
                <td class="cell-role">
                    <span class="role-label">Position</span>
                    <span class="role-title">General Manager</span>
                </td>
                <td class="cell-point">
                    <span class="point-label">Point</span>
                    <span class="point-value">4578</span>
                </td>
                <td class="cell-payment">
                    <span class="payment-label">Payment</span>
                    <span class="payment-value">48785</span>
                </td>
                <td class="cell-actions">
                    <a href="#" class="options overview-button">Options</a>
                    <a href="#" class="details overview-button">Details</a>
                </td>
            </tr>
            
            <tr>
                <th>Jerry Mattedi</th>
                <td class="cell-role">
                    <span class="role-label">Position</span>
                    <span class="role-title">General Manager</span>
                </td>
                <td class="cell-point">
                    <span class="point-label">Point</span>
                    <span class="point-value">4578</span>
                </td>
                <td class="cell-payment">
                    <span class="payment-label">Payment</span>
                    <span class="payment-value">48785</span>
                </td>
                <td class="cell-actions">
                    <a href="#" class="options overview-button">Options</a>
                    <a href="#" class="details overview-button">Details</a>
                </td>
            </tr>
            <tr>
                <th>Jerry Mattedi</th>
                <td class="cell-role">
                    <span class="role-label">Position</span>
                    <span class="role-title">General Manager</span>
                </td>
                <td class="cell-point">
                    <span class="point-label">Point</span>
                    <span class="point-value">4578</span>
                </td>
                <td class="cell-payment">
                    <span class="payment-label">Payment</span>
                    <span class="payment-value">48785</span>
                </td>
                <td class="cell-actions">
                    <a href="#" class="options overview-button">Options</a>
                    <a href="#" class="details overview-button">Details</a>
                </td>
            </tr>
            <tr>
                <th>Jerry Mattedi</th>
                <td class="cell-role">
                    <span class="role-label">Position</span>
                    <span class="role-title">General Manager</span>
                </td>
                <td class="cell-point">
                    <span class="point-label">Point</span>
                    <span class="point-value">4578</span>
                </td>
                <td class="cell-payment">
                    <span class="payment-label">Payment</span>
                    <span class="payment-value">48785</span>
                </td>
                <td class="cell-actions">
                    <a href="#" class="options overview-button">Options</a>
                    <a href="#" class="details overview-button">Details</a>
                </td>
            </tr>
            
        </table>
    </div>
</div>