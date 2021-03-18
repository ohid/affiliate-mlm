<?php 

// Exit if accessed directly.
defined('ABSPATH') || exit;

// Get the level values
$amlm_sales_representative = get_option( 'amlm_sales_representative' );
$amlm_unit_manager         = get_option( 'amlm_unit_manager' );
$amlm_manager              = get_option( 'amlm_manager' );
$amlm_senior_manager       = get_option( 'amlm_senior_manager' );
$amlm_executive_manager    = get_option( 'amlm_executive_manager' );
$amlm_ass_g_manager        = get_option( 'amlm_ass_g_manager' );
$amlm_general_manager      = get_option( 'amlm_general_manager' );

?>
<div class="wrap amlm-wrap">
    <h2><?php esc_html_e('Settings', 'amlm-locale'); ?></h2>
    <?php include_once AMLM_PLUGIN_PATH . 'templates/admin/partials/header.php'; ?>

    <div class="main-settings">
        <h3><?php _e( 'Adjust commission percentage on each level', 'amlm-locale' ); ?></h3>

        <form id="main_settings">
            <div class="form-group">
                <label for="amlm_sales_representative"><?php _e('Sales Representative', 'amlm-locale') ?></label>
                <input type="text" name="amlm_sales_representative" id="amlm_sales_representative" class="form-control" value="<?php echo esc_attr( $amlm_sales_representative ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_unit_manager"><?php _e('Unit Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_unit_manager" id="amlm_unit_manager" class="form-control" value="<?php echo esc_attr( $amlm_unit_manager ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_manager"><?php _e('Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_manager" id="amlm_manager" class="form-control" value="<?php echo esc_attr( $amlm_manager ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_senior_manager"><?php _e('Senior Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_senior_manager" id="amlm_senior_manager" class="form-control" value="<?php echo esc_attr( $amlm_senior_manager ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_executive_manager"><?php _e('Executive Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_executive_manager" id="amlm_executive_manager" class="form-control" value="<?php echo esc_attr( $amlm_executive_manager ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_ass_g_manager"><?php _e('Ass. G. Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_ass_g_manager" id="amlm_ass_g_manager" class="form-control" value="<?php echo esc_attr( $amlm_ass_g_manager ); ?>">
            </div>
            <div class="form-group">
                <label for="amlm_general_manager"><?php _e('General Manager', 'amlm-locale') ?></label>
                <input type="text" name="amlm_general_manager" id="amlm_general_manager" class="form-control" value="<?php echo esc_attr( $amlm_general_manager ); ?>">
            </div>

            <p class="form-response"></p>
            
            <input type="hidden" name="action" value="commission_settings">
            <?php wp_nonce_field( 'amlm_nonce', 'commission_settings' ); ?>

            <input type="submit" class="form-submit" value="<?php _e('Save', 'amlm-locale'); ?>">
        </form>
    </div>
</div>
