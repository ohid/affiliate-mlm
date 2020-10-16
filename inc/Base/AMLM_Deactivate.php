<?php 

namespace AMLM\Base;

class AMLM_Deactivate 
{
    
    public static function deactivate(){

        // Remove all the defined roles
        // remove_role('amlm_sales_representative');
        // remove_role('amlm_distributor');
        // remove_role('amlm_unit_manager');
        // remove_role('amlm_manager');
        // remove_role('amlm_senior_manager');
        // remove_role('amlm_executive_manager');
        // remove_role('amlm_ass_g_manager');
        // remove_role('amlm_general_manager');

        // global $wpdb;
        // $table_name = $wpdb->prefix . 'amlm_referrals';

        // $wpdb->query('TRUNCATE TABLE ' . $table_name);

        // Flush the rewrite rules
        flush_rewrite_rules();
    }
}