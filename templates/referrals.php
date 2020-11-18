<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

global $wpdb, $wp_roles;


$referral_limit = 3;
$user = wp_get_current_user();

// Get the referral counts
$referral_count = $wpdb->get_var("SELECT COUNT(*) from {$wpdb->prefix}amlm_referrals WHERE user_id = $user->ID");

if( $referral_count >= $referral_limit ) {
    // do nothing
} else {
    printf(
        '<p>You can add <b>%s Distributors</b> <button id="test" class="add-new-referral create-btn button">Add new</button> </p>',
        $referral_limit - $referral_count
    );
    

    ?>

    <div class="referral-form amlm-form">
        <form action="" method="post" id="referral-form">
            <div class="form-group">
                <label for="username"><?php esc_html_e('Username:', 'amlm-locale'); ?></label>
                <input type="text" id="username" name="username" placeholder="Enter username" class="form-control">
            </div>
            <div class="form-group">
                <label for="email"><?php esc_html_e('Email:', 'amlm-locale'); ?></label>
                <input type="email" id="email" name="email" placeholder="Enter email" class="form-control">
            </div>
            <div class="form-group">
                <label for="password"><?php esc_html_e('Password:', 'amlm-locale'); ?></label>
                <input type="password" id="password" name="password" placeholder="Enter password" class="form-control">
            </div>

            <input type="hidden" name="action" value="referral_form">
            <?php wp_nonce_field( 'amlm_nonce', 'referral_nonce' ); ?>
            <input type="submit" value="Create user">

            <p class="form-response"></p>
        </form>
    </div>

<?php
}

// Get the referral users
$referral_users = $wpdb->get_col("SELECT referral_id from {$wpdb->prefix}amlm_referrals WHERE user_id = $user->ID");

// Only show the table when there are referral users
if( $referral_count > 0 ) :

?>


<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Emails</th>
        <th>Rank</th>
        <th>Points</th>
    </tr>

    <?php 
        foreach( $referral_users as $user_id ) :
            // $user = get_user_by( $user );
            $points = get_user_meta( $user_id, 'amlm_points', true);
            
            $user = get_user_by( 'id', $user_id );
            $current_role = aMLMCurrentUserRole( $user );

            if( $user ) {
                printf(
                    '<tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                    </tr>', 
                    $user->id,
                    $user->user_login,
                    $user->user_email,
                    $wp_roles->roles[ $current_role ]['name'],
                    $points
                );
            }

        endforeach;
    ?>
</table>

<?php 

endif;
