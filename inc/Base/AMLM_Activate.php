<?php 

namespace AMLM\Base;

class AMLM_Activate {

    public static function activate(){
        

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
    public static function setRole() {

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

    public function createTable() {

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'amlm_referrals';
    
        $sql = "CREATE TABLE $table_name (
            id int(9) NOT NULL AUTO_INCREMENT,
            user_id mediumint(9) NOT NULL,
            referral_id mediumint(9) NOT NULL,
            UNIQUE KEY id (id)
        )$charset_collate;";
    
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}