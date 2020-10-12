<?php 

namespace AMLM\Api;

class MyAccountTabAPI
{

    protected $is_distributor = false;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    public function __construct() {

    }

    public function register() {



    }
    
    /**
     * Set current user points
     *
     * @return void
     */
    public function set_current_points() {
        if( is_user_logged_in() ) {
    
            $user = wp_get_current_user();
            $amlm_points = get_user_meta( $user->ID, 'amlm_points', true);
    
            $this->amlm_user_current_points = $amlm_points;
        }
    
        return;
    }
        
    /**
     * Check if the current user is a distributor
     *
     * @return void
     */
    public function set_distributor() {
        if( $this->amlm_user_current_points > $this->distributor_points ) {
            $this->is_distributor = true;
        }

    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function my_account_new_endpoints() {
        
        if( $this->is_distributor ) {
            add_rewrite_endpoint( 'referrals', EP_ROOT | EP_PAGES );
            flush_rewrite_rules();
        }
    }

    public function addTabEndPoint( $name ) {
        $this->tabEndpoints[] = $name;
    }

    public function addTab( $array ) {
        $this->tabs[] = $array;
    }

    public function addTabContent( $name ) {
        $this->tabContents[] = $name;
        // include_once AMLM_PLUGIN_PATH . '/templates/referrals.php';
      
    }



}