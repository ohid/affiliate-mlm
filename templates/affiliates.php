<?php

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb;
$user = wp_get_current_user();

if (isset($_GET['pageno'])) {
    $pageno = $_GET['pageno'];
} else {
    $pageno = 1;
}

$no_of_records_per_page = 5;
$offset = ($pageno-1) * $no_of_records_per_page;

?>

<?php 

printf(
    '<p>Affiliates links <button id="test" class="add-new-link create-btn button">Create link </button></p>'
);

?>

<div class="affiliate-form amlm-form" >
    <form action="" method="post" id="affiliate-form">
        <div class="form-group">
            <label for="product-link"><?php esc_html_e('Product link:', 'amlm-locale'); ?></label>
            <input type="url" id="product-link" name="product_link" placeholder="Enter product link" class="form-control">
        </div>

        <div class="form-group">
            <label for="campaign-name"><?php esc_html_e('Campaign name:', 'amlm-locale'); ?></label>
            <input type="text" id="campaign-name" name="campaign_name" placeholder="Enter campaign name" class="form-control">
        </div>
        
        <div class="form-group">
            <label for="affiliate-link"><?php esc_html_e('Affiliate link:', 'amlm-locale'); ?></label>
            <input type="url" id="affiliate-link" name="affiliate_link" placeholder="Affiliated link will be generated here" class="form-control">
        </div>

        <input type="hidden" name="action" value="affiliate_form">
        <?php wp_nonce_field( 'amlm_nonce', 'affiliate_nonce' ); ?>
        <input type="submit" value="Create link">

        <p class="form-response"></p>
    </form>
</div>

<?php

// Get the referral users
$affiliate_links = $wpdb->get_results(
    "SELECT * FROM {$wpdb->prefix}amlm_affiliates_link 
    WHERE user_id = $user->ID
    ORDER BY created_at DESC
    LIMIT $offset, $no_of_records_per_page"
);

// Only show the table when there are referral users
if( $affiliate_links > 0 ) :

?>

<table class="affiliate-links-table">
    <tr>
        <th>Affiliate link</th>
        <th>Campaign</th>
        <th>Visits</th>
        <th>Order</th>
    </tr>

    <?php 
        foreach( $affiliate_links as $link ) :

            if( $link ) {
                printf(
                    '<tr>
                        <td class="affiliate-link"><input value="%s"/></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>', 
                    $link->affiliate_link,
                    $link->campaign_name,
                    $link->visits,
                    $link->orders
                );
            }

        endforeach;

endif;

    ?>
</table>

<?php amlmLinksPagination($wpdb, 'amlm_affiliates_link', $user, $pageno, $offset, $no_of_records_per_page); ?>
