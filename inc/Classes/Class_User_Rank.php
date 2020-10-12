<?php 

namespace AMLM\Classes;

class Class_User_Rank
{   
    protected $user;

    protected $wpdb;
    
    protected $current_rank;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    protected $became_unit_manager = false;

    public function  __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function register() {
        add_action( 'init', array( $this, 'userRankInit' ) );
    }

    /**
     * Initialize all the necessary things
     *
     * @return void
     */
    public function userRankInit() {
        $this->setCurrentPoints();
        $this->userRank();
    }

    /**
     * Set current user points
     *
     * @return void
     */
    public function setCurrentPoints() {
        if( is_user_logged_in() ) {
    
            $this->user = wp_get_current_user();
            $this->setUserRank( $this->user );
            
            $amlm_points = get_user_meta( $this->user->ID, 'amlm_points', true );
    
            $this->amlm_user_current_points = $amlm_points;

        }
    
        return;
    }

    public function setUserRank( $user ) {

        $roles = $user->roles;
        $first_role = \array_shift( $roles );
        $this->current_rank = $first_role;
        
        return;
    }

    public function addRole($user, $role) {
        $user->add_role( $role );

        $this->setUserRank( $user );
    }

    public function removeRole($user, $role) {
        $user->remove_role( $role );

        $this->setUserRank( $user );
    }

    public function userRank() {

        if( is_user_logged_in() ) {

            // echo $this->current_rank;
            $user_id = $this->user->ID;

            if( $this->amlm_user_current_points >= $this->distributor_points ) {

                // The user become a distributor 
                // Remove the amlm_sales_representative role 
                $this->removeRole( $this->user, 'amlm_sales_representative' );
                // Add the amlm_distributor role
                $this->addRole( $this->user, 'amlm_distributor' );

                // echo 'Your are a <b>Distributor</b> now <br>';
                // echo 'current rank ' . $this->current_rank . '<br>';

                // Check if the user is a unit manager
                // Count the referral user of the current user
                $user_referrals_count = $this->wpdb->get_var("SELECT COUNT(*) from {$this->wpdb->prefix}amlm_referrals WHERE user_id = $user_id");

                // If there are more than 3 referral users then proceed
                if( $user_referrals_count == 3 ) {
                    
                    // The distributor has 3 referral users
                    // Check if they all have rquired 400 points 
                    // Query for the referral users
                    $first_level_referrals = $this->wpdb->get_col("SELECT referral_id from {$this->wpdb->prefix}amlm_referrals WHERE user_id = $user_id");

                    // Array to store the refferal users distributorship
                    $all_flr_distributorship = array();

                    // loop through the users and store their points/distributor eligibleship
                    foreach( $first_level_referrals as $flr ) {
                        $flr_points = get_user_meta( $flr, 'amlm_points', true );

                        if( $flr_points >= $this->distributor_points ) {
                            // printf('Child %s has %s points and he is a <b>Distributor</b> now. <br> ', $flr, $flr_points);
                            $all_flr_distributorship[] = true;
                        } else {
                            $all_flr_distributorship[] = false;
                        }
                    }
                    
                    // Check if all flr users has =< 400 points then the current logged in user is a unit manager
                    if ( count( array_unique( $all_flr_distributorship ) ) === 1 && end($all_flr_distributorship) === true) {
                        // echo '<br>So you become a <b>Unit Manager<b> <br><br>';
                        // The user become a Unit Manager 
                        // Remove the amlm_distributor role 
                        $this->removeRole( $this->user, 'amlm_distributor' );
                        // Add the amlm_unit_manager role
                        $this->addRole( $this->user, 'amlm_unit_manager' );

                        // echo 'current rank ' . $this->current_rank . '<br>';

                        // Now check if the member is a Manager
                        
                        $second_level_referrals = $this->checkForManager( $first_level_referrals );

                        if( $second_level_referrals['count'] >= 3 ) {
                            // echo '<br>So you are a Manager now <br><br>';
                            // The user become a Manager 
                            // Remove the amlm_unit_manager role 
                            $this->removeRole( $this->user, 'amlm_unit_manager' );
                            // Add the amlm_manager role
                            $this->addRole( $this->user, 'amlm_manager' );

                            // Now check if the member is a Senior Manager
                            $third_level_referrals = $this->checkForManager( $second_level_referrals['users'] );
                            
                            
                            if( $third_level_referrals['count'] >= 9 ) {
                                // echo '<br>So you are a Senior Manager now <br><br>';
                                // The user become a Senior Manager 
                                // Remove the amlm_manager role 
                                $this->removeRole( $this->user, 'amlm_manager' );
                                // Add the amlm_senior_manager role
                                $this->addRole( $this->user, 'amlm_senior_manager' );
                                
                                $fourth_level_referrals = $this->checkForManager( $third_level_referrals['users'] );

                                if( $fourth_level_referrals['count'] >= 27 ) {
                                    // echo '<br>So you are a Executive Manager now <br><br>';
                                    // The user become a Senior Manager 
                                    // Remove the amlm_senior_manager role 
                                    $this->removeRole( $this->user, 'amlm_senior_manager' );
                                    // Add the amlm_executive_manager role
                                    $this->addRole( $this->user, 'amlm_executive_manager' );
                                    
                                    $fifth_level_referrals = $this->checkForManager( $fourth_level_referrals['users'] );

                                    if( $fifth_level_referrals['count'] >= 81 ) {
                                        // echo '<br>So you are a Ass. G. Manager now <br><br>';
                                        // The user become a Senior Manager 
                                        // Remove the amlm_executive_manager role 
                                        $this->removeRole( $this->user, 'amlm_executive_manager' );
                                        // Add the amlm_ass_g_manager role
                                        $this->addRole( $this->user, 'amlm_ass_g_manager' );

                                        $sixth_level_referrals = $this->checkForManager( $fifth_level_referrals['users'] );

                                        if( $sixth_level_referrals['count'] >= 243 ) {
                                        // The user become a Senior Manager 
                                        // Remove the amlm_ass_g_manager role 
                                        $this->removeRole( $this->user, 'amlm_ass_g_manager' );
                                        // Add the amlm_general_manager role
                                        $this->addRole( $this->user, 'amlm_general_manager' );
                                            // echo '<br>So you are a General Manager now <br><br>';
                                        }
                                    }
                                }
                            }
                        }
                
                    } else {
                        // echo 'Distributor has 3 referrals but they aren\'t a distributor yet.';
                    }

                } else {
                    // echo 'Not yet a Unit manager';
                }

            }

        }

    }

    public function checkForManager( $first_level_referrals ) {

        $unit_manager_count = 0;
        $unit_managers_non_flatten = array();

        // Check if each flr has 3 more referrals with 
        foreach( $first_level_referrals as $flr ) {
            // Count the referral user of the current user
            $user_referrals_count = $this->wpdb->get_var("SELECT COUNT(*) from {$this->wpdb->prefix}amlm_referrals WHERE user_id = $flr");
            
            // If there are more than 3 referral users then proceed
            if( $user_referrals_count >= 3 ) {
                // Query for the referral users
                $second_level_referrals = $this->wpdb->get_col("SELECT referral_id from {$this->wpdb->prefix}amlm_referrals WHERE user_id = $flr");
                $unit_managers_non_flatten[] = $second_level_referrals;

                 // Array to store the refferal users distributorship
                $all_slr_distributorship = array();

                foreach( $second_level_referrals as $slr ) {
                    $slr_points = get_user_meta( $slr, 'amlm_points', true );

                    if( $slr_points >= $this->distributor_points ) {
                        $all_slr_distributorship[] = true;
                    } else {
                        $all_slr_distributorship[] = false;
                    }
                }

                // Check if all slr users has =< 400 points then the current logged in user is a manager
                if ( count( array_unique( $all_slr_distributorship ) ) === 1 && end($all_slr_distributorship) === true) {
                    // echo 'Child '. $flr .' is a unit manager <br>';
                    $unit_manager_count++;
                } else {
                    // echo 'Child distributor '. $flr .' has 3 referrals but they aren\'t a distributor yet. <br>';
                }

            }
        }
        
        // echo '<br>Current unit manager has ' . $unit_manager_count . ' child unit manager <br>';
        // dd($unit_managers_non_flatten);

        $unit_managers = (object) array('usersFlat' => array());
        array_walk_recursive($unit_managers_non_flatten, function(&$v, $k, &$t){
            $t->usersFlat[] = $v;
        }, $unit_managers);

        return array(
            'count' => $unit_manager_count,
            'users' => $unit_managers->usersFlat
        );
    }



}