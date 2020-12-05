<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

global $wp_roles;
$current_role = aMLMCurrentUserRole();

// get the current points
$points = get_user_meta( $current_user->ID, 'amlm_points', true );
$earning = get_user_meta( $current_user->ID, 'amlm_earning', true );

// Get the profile picture attachment ID
$image_id = get_user_meta($current_user->ID, 'amlm_image', true);

// User phone number
$user_phone = get_user_meta($current_user->ID, 'amlm_user_phone', true);

?>

	<?php
		if($image_id) {
			$img = wp_get_attachment_image_src( $image_id, 'medium' );

			printf('<div class="profile-picture"><img src="%s" alt="Profile picture"></div>', $img[0]);
		}

		echo '<div class="profile-information">';

			printf(
				__('Name: <b>%s</b> '),
				userFullName()
			);

			echo '<br>';
			
			printf(
				__('Username: <b>%s</b> '),
				$current_user->display_name
			);

			echo '<br>';

			printf(
				__('Email: <b>%s</b> '),
				$current_user->user_email
			);

			echo '<br>';

			if($user_phone) {
				printf(
					__('Phone: <b>%s</b> '),
					$user_phone
				);
			}
				
			echo '<br>';

			// Only display the rank when the user has minimum 400 points and is a distributor or has above role
			if( $points >= 400 ) {
				printf(
					__('Rank: <b>%s</b> '),
					$wp_roles->roles[ $current_role ]['name']
				);

				echo '<br>';
			}

			printf(
				__('Points: <b>%s</b> '),
				$points ? round($points, 2) : 0
			);

			echo '<br>';

			printf(
				__('Earning: <b>%s %s</b> '),
				get_option('woocommerce_currency'),
				$earning ? round($earning, 2) : 0
			);

			echo '<br>';

			printf(
				__('Logout? <b><a href="%s">%s</a></b> '),
				esc_url( wc_logout_url() ),
				__('Logout', 'amlm-locale')
			);

		echo '</div>';

	?>

<p>
	<?php
	/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
	$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	if ( wc_shipping_enabled() ) {
		/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce' );
	}
	printf(
		wp_kses( $dashboard_desc, $allowed_html ),
		esc_url( wc_get_endpoint_url( 'orders' ) ),
		esc_url( wc_get_endpoint_url( 'edit-address' ) ),
		esc_url( wc_get_endpoint_url( 'edit-account' ) )
	);
	?>
</p>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */