<?php 

namespace AMLM\Classes;

class Class_Earning_Calculator
{
    protected $user;

    protected $wpdb;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    public function  __construct(){
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function register()
    {
        add_action( 'init', array( $this, 'mainInit' ) );
        
        add_action( 'woocommerce_payment_complete', array( $this, 'earningGenerator' ), 10, 1 );
        add_action( 'woocommerce_order_status_completed', array( $this, 'earningGenerator' ), 10, 1 );
    }

    public function mainInit()
    {
        $this->setCurrentPoints();
    }

    /**
     * Set current user points
     *
     * @return void
     */
    public function setCurrentPoints() {
        if( is_user_logged_in() ) {
    
            $this->user = wp_get_current_user();
            
            $amlm_points = get_user_meta( $this->user->ID, 'amlm_points', true );
    
            $this->amlm_user_current_points = $amlm_points;

        }
    
        return;
    }
    
    /**
     * Earning generator
     *
     * @param [type] $order_id
     * @return void
     */
    public function earningGenerator( $order_id )
    {
        // Users can only earn when they have minimum 400 poinsts and equal or above distributor role
        if( $this->amlm_user_current_points < $this->distributor_points ) {
            return;
        }

        // get the order object
        $order = wc_get_order( $order_id );
        $order_total = $order->get_total();
        $user_id = $order->get_user_id();

        if( $user_id ){

            // Update the user balance
            $this->updateUserBalance($user_id, $order_total, $bonus = 10);


            $this->parentEarningCalculation( $user_id, $order_total );
        }
    }

    /**
     * Parent earning calculation
     *
     * @param integer $user_id
     * @param [type] $order_total
     * @return void
     */
    public function parentEarningCalculation(int $user_id, $order_total)
    {
        $parentLevelTwo = $this->findParent($user_id);

        if( $parentLevelTwo ) {
            // Check if the parent exists 
            // then update their balance
            $this->updateUserBalance($parentLevelTwo, $order_total, $bonus = 7.5);

            $parentLevelThree = $this->findParent($parentLevelTwo);
            
            if( $parentLevelThree ) {
                // Check if the parent exists 
                // then update their balance
                $this->updateUserBalance($parentLevelThree, $order_total, $bonus = 6.5);
    
                $parentLevelFour = $this->findParent($parentLevelThree);

                if( $parentLevelFour ) {
                    // Check if the parent exists 
                    // then update their balance
                    $this->updateUserBalance($parentLevelFour, $order_total, $bonus = 5.5);
        
                    $parentLevelFive = $this->findParent($parentLevelFour);

                    if( $parentLevelFive ) {
                        // Check if the parent exists 
                        // then update their balance
                        $this->updateUserBalance($parentLevelFive, $order_total, $bonus = 4.5);

                        $parentLevelSix = $this->findParent($parentLevelFive);

                        if( $parentLevelSix ) {
                            // Check if the parent exists 
                            // then update their balance
                            $this->updateUserBalance($parentLevelSix, $order_total, $bonus = 3.5);
    
                            $parentLevelSeven = $this->findParent($parentLevelSix);
    
                            if( $parentLevelSeven ) {
                                // Check if the parent exists 
                                // then update their balance
                                $this->updateUserBalance($parentLevelSeven, $order_total, $bonus = 2.5);
        
                            }
    
                        }

                    }
                    
                }
                
            }
        }
    }

    /**
     * Find the parent user id
     *
     * @param integer $user_id
     * @return void
     */
    public function findParent(int $user_id)
    {
        $parentUserId = $this->wpdb->get_var("SELECT user_id from {$this->wpdb->prefix}amlm_referrals WHERE referral_id = $user_id");

        return $parentUserId;
    }

    /**
     * Update the parent balance
     *
     * @param integer $user_id
     * @param [type] $order_total
     * @param [type] $bonus
     * @return void
     */
    public function updateUserBalance(int $user_id, $order_total, $bonus)
    {
        $currentBalance = get_user_meta( $user_id, 'amlm_earning', true );

        // If member already earned
        if( ! empty( $currentBalance ) && $currentBalance > 0 ) {

            // Give bonus balance depending on purchase
            $bonus_balance = ( $bonus / 100 ) * $order_total;

            // update the user meta
            update_user_meta( $user_id, 'amlm_earning', $currentBalance + $bonus_balance );

        } else {
            // Give bonus balance depending on purchase
            $bonus_balance = ( $bonus / 100 ) * $order_total;

            // update the user meta
            update_user_meta( $user_id, 'amlm_earning', $bonus_balance );
        }
    }
}