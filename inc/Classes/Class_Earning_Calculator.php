<?php 

namespace AMLM\Classes;

class Class_Earning_Calculator
{
    protected $user;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    public function register()
    {
        add_action( 'init', array( $this, 'mainInit' ) );
        
        add_action( 'woocommerce_payment_complete', array( $this, 'earningGenerator' ), 10, 1 );
        add_action( 'woocommerce_order_status_completed', array( $this, 'earningGenerator' ), 10, 1 );
    }
    
    public function mainInit()
    {
        $this->setCurrentPoints();

        $this->earningCalculation();
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

    public function earningCalculation()
    {
        if( $this->amlm_user_current_points < $this->distributor_points ) {
            return;
        }

        $earning = get_user_meta( $this->user->ID, 'amlm_earning', true );

        // if( $earning ) {
        //     echo 'earning exists';
        // } else {
        //     echo 'earning not exists';
        // }
    }

    public function earningGenerator( $order_id )
    {
        // get the order object
        $order = wc_get_order( $order_id );
        $order_total = $order->get_total();
        $user_id = $order->get_user_id();

        if( $user_id ){

            $earning = get_user_meta( $this->user->ID, 'amlm_earning', true );

            // If member already earned
            if( ! empty( $earning ) && $earning > 0 ) {
                
                // Set the bonus point after purchasing
                $earning_value = ( 10 / 100 ) * $order_total;
                $previous_points = $earning;

                // update the user meta
                update_user_meta( $user_id, 'amlm_earning', $previous_points + $earning_value );

            } else {
                // Set the bonus point after purchasing
                $earning_value = ( 10 / 100 ) * $order_total;

                // update the user meta
                update_user_meta( $user_id, 'amlm_earning', $earning_value );
            }
        }
    }

}