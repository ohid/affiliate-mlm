<?php 

namespace AMLM\Base;

class AMLM_Activate {

    public static function activate()
    {
        
        self::setRole();

        self::createTable();
        
        // Flush the rewrite rules
        flush_rewrite_rules();
    }

     /**
     * Define the new user roles for the plugin
     *
     * @return void
     */
    public static function setRole() 
    {
        add_role( 
            'amlm_sales_representative', 
            __( 'Sales Representative', 'amlm'), 
            array( 'read' => true ) 
        );

        add_role( 
            'amlm_distributor', 
            __( 'Distributor', 'amlm'), 
            array( 'read' => true ) 
        );
        
        add_role( 
            'amlm_unit_manager', 
            __( 'Unit Manager', 'amlm'), 
            array( 'read' => true ) 
        );
        
        add_role( 
            'amlm_manager', 
            __( 'Manager', 'amlm'), 
            array( 'read' => true ) 
        );        

        add_role( 
            'amlm_senior_manager', 
            __( 'Senior Manager', 'amlm'), 
            array( 'read' => true ) 
        );
   
        add_role( 
            'amlm_executive_manager', 
            __( 'Executive Manager', 'amlm'), 
            array( 'read' => true ) 
        );
                
        add_role( 
            'amlm_ass_g_manager', 
            __( 'Ass. G. Manager', 'amlm'), 
            array( 'read' => true ) 
        );  

        add_role( 
            'amlm_general_manager', 
            __( 'General Manager', 'amlm'), 
            array( 'read' => true ) 
        );
    }

    public static function createTable()
    {

        global $wpdb;
        
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "";

        // Check if the amlm_referrals table does not exists then create the table
        $amlm_referrals_table = $wpdb->prefix.'amlm_referrals';

        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $amlm_referrals_table ) );
 
        if ( $wpdb->get_var( $query ) !== $amlm_referrals_table ) {
            $sql .= "
CREATE TABLE {$amlm_referrals_table} (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
    referral_id mediumint(9) NOT NULL,
    UNIQUE KEY id (id)
)$charset_collate;";
        }

        // Check if the amlm_affiliates_link table does not exists then create the table
        $amlm_affiliates_link_table = $wpdb->prefix.'amlm_affiliates_link';

        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $amlm_affiliates_link_table ) );
 
        if ( $wpdb->get_var( $query ) !== $amlm_affiliates_link_table ) {
            $sql .= "
CREATE TABLE {$amlm_affiliates_link_table} (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    user_id mediumint(9) NOT NULL,
    affiliate_link varchar(255) NOT NULL,
    campaign_name varchar(255) NOT NULL,
    visits mediumint(9) NOT NULL,
    orders mediumint(9) NOT NULL,
    UNIQUE KEY id (id)
)$charset_collate;";
        }

        // Check if the amlm_affiliate_earnings table does not exists then create the table
        $amlm_affiliate_earnings_table = $wpdb->prefix.'amlm_affiliate_earnings';

        $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $amlm_affiliate_earnings_table ) );
 
        if ( $wpdb->get_var( $query ) !== $amlm_affiliate_earnings_table ) {
            $sql .= "
CREATE TABLE {$amlm_affiliate_earnings_table} (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    affiliate_link_id mediumint(9) NOT NULL,
    user_id mediumint(9) NOT NULL,
    order_id varchar(255) NOT NULL,
    order_status text NOT NULL,
    paid_status text NOT NULL,
    UNIQUE KEY id (id)
)$charset_collate;";
        }

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}