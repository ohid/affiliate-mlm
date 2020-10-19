<?php
/** 
 * Users earning calculator
 * The earning generates depending on how much they earn themselves and gets affiliated earning from referral users
 * PHP version 7.0
 * 
 * @category   Component
 * @package    WordPress
 * @subpackage AffiliateMLM
 * @author     Ohid <ohidul.islam951@gmail.com>
 * @license    GPLv2 or later https://www.gnu.org/licenses/gpl-2.0.html
 * @link       https://site.com
 */

namespace AMLM\Classes;

class Class_Earning_Calculator
{
    protected $user;

    protected $amlm_user_current_points;

    protected $distributor_points = 400;

    protected $distributor_order_value = 4000;
    
    /**
     * The function registerer that gets called the the function loads
     *
     * @return void
     */
    public function register()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        add_action('init', [$this, 'mainInit']);
        
        add_action('woocommerce_payment_complete', [$this, 'earningGenerator'], 11, 1);
        add_action('woocommerce_order_status_completed', [$this, 'earningGenerator'], 11, 1);
    }

    /**
     * The initializer function
     *
     * @return void
     */
    public function mainInit()
    {   
        // Set the current point of the user
        $this->setCurrentPoints();
    }

    /**
     * Set current user points
     * 
     * @param [object] $user_id the user object
     * 
     * @return void
     */
    public function setCurrentPoints( $user_id = null )
    {
        if ($user_id !== null) {
            if (is_user_logged_in()) {
    
                $this->user = wp_get_current_user();
                
                $amlm_points = get_user_meta($this->user->ID, 'amlm_points', true);
        
                $this->amlm_user_current_points = $amlm_points;
            }
        } else {
            $amlm_points = get_user_meta($user_id, 'amlm_points', true);
        
            $this->amlm_user_current_points = $amlm_points;
        }
    
        return;
    }
    
    /**
     * Earning generator
     *
     * @param integer $order_id get the order id
     * 
     * @return void
     */
    public function earningGenerator( $order_id )
    {
        // get the order object
        $order = wc_get_order($order_id);
        $order_total = $order->get_total();
        $user_id = $order->get_user_id();

        // Check if the order was an affiliate sale
        $affiliate_user_id = $this->wpdb->get_var("SELECT user_id from {$this->wpdb->prefix}amlm_affiliate_earnings WHERE order_id = $order_id AND paid_status = 'unpaid'");

        if ($affiliate_user_id) {
            $user_id = $affiliate_user_id;
        }

        // If ordered user exists
        if ($user_id) {
            // Get the current amlm_points of the users
            $amlm_points = get_user_meta($user_id, 'amlm_points', true);

            // If the user already have points
            if (! empty($amlm_points) && $amlm_points > 0) {

                // Check if the is already a distributor
                // If not a distribut then proceed
                if ($amlm_points <= $this->distributor_points) {
                    // Subtract the amlm_points from the required distributor points
                    // So we can get the value that needs to become a distributor
                    $need_to_become_distributor = $this->distributor_points - $amlm_points;

                    // Set the bonus point after purchasing                    
                    $bonus_point = ( 10 / 100 ) * $order_total;

                    // Get the total point adding the previous point with new point
                    $sum_of_total_point = $amlm_points + $bonus_point;

                    // Check if the total points exceeds the required distributor point 
                    // So they will earn money that order exceeds more than 400 points
                    if ($sum_of_total_point > $this->distributor_points) {
                        // Get the amount that is applicable for earning balance
                        $applicable_earning_value = $sum_of_total_point - $this->distributor_points;

                        // update the user meta
                        update_user_meta($user_id, 'amlm_points', $amlm_points + $bonus_point);

                        $generate_order_amount = $applicable_earning_value * 10;

                        // Update the user balance for the amount that exceeds 400 points
                        $this->updateUserBalance($user_id, $generate_order_amount, $bonus = 10);

                        // Update the parent earning balance for the amount that exceeds 400 points
                        $this->parentEarningCalculation($user_id, $generate_order_amount);
                    } else {
                        // Set the bonus point after purchasing
                        $bonus_point = ( 10 / 100 ) * $order_total;

                        // update the user meta
                        update_user_meta($user_id, 'amlm_points', $amlm_points + $bonus_point);
                    }

                } else {
                    // Proceed with general things as the member is a distrubutor and 
                    // can have points and earn money for the total order made

                    // Set the bonus point after purchasing                    
                    $bonus_point = ( 10 / 100 ) * $order_total;

                    // update the user meta
                    update_user_meta($user_id, 'amlm_points', $amlm_points + $bonus_point);

                    // Update the user balance for the amount that exceeds 400 points
                    $this->updateUserBalance($user_id, $order_total, $bonus = 10);

                    // Update the parent earning balance for the amount that exceeds 400 points
                    $this->parentEarningCalculation($user_id, $order_total);

                }

            } else {
                // For the first order
                // If the ordered amount is less than 4000 BDT then just calculate the bonus point 
                if ($order_total <= $this->distributor_order_value) {
                    // Set the bonus point after purchasing
                    $bonus_point = ( 10 / 100 ) * $order_total;

                    // update the user meta
                    update_user_meta($user_id, 'amlm_points', $bonus_point);
                } elseif ($order_total > $this->distributor_order_value) {

                    // If the ordered amount is more than 4000 BDT then
                    // User will receive bonus point for 4000 BDT 
                    // And will earn money for the amount that exceeds 4000 BDT limit
                    $earning_value = $order_total - $this->distributor_order_value;
                    $bonus_point = ( 10 / 100 ) * $order_total;
                    
                    // update the user meta
                    update_user_meta($user_id, 'amlm_points', $bonus_point);

                    // Update the user balance
                    // The user will only earn for the amount that exceeds 4000 BDT limit
                    $this->updateUserBalance($user_id, $earning_value, $bonus = 10);

                    // Give referral earning balance for the parent users
                    // The parent users will only also earn for the amount that exceeds 4000 BDT limit
                    $this->parentEarningCalculation($user_id, $earning_value);
                }

            }

            $affiliate_link_id = $this->wpdb->get_var("SELECT affiliate_link_id from {$this->wpdb->prefix}amlm_affiliate_earnings WHERE order_id = $order_id");

            $affiliate = $this->wpdb->get_row("SELECT id, orders from {$this->wpdb->prefix}amlm_affiliates_link WHERE id = $affiliate_link_id");

            $orders = $affiliate->orders;

            // Update the affiliate earning table
            $this->wpdb->update(
                "{$this->wpdb->prefix}amlm_affiliates_link",
                ['orders' => ($orders + 1)],
                ['id' => $affiliate->id],
                ['%d'],
                ['%d']
            );
            
            // Update the affiliate earning table
            $this->wpdb->update(
                "{$this->wpdb->prefix}amlm_affiliate_earnings",
                [
                    'order_status' => $order->get_status(), 
                    'paid_status' => 'paid'
                ],
                ['order_id' => $order_id],
                ['%s', '%s'],
                ['%d']
            );
        }
    }

    /**
     * Parent earning calculation
     *
     * @param integer $user_id     pass the user id
     * @param integer $order_total pass the order total
     * 
     * @return void
     */
    public function parentEarningCalculation(int $user_id, int $order_total)
    {
        $parentLevelTwo = $this->findParent($user_id);

        if ($parentLevelTwo) {
            // Check if the parent exists 
            // then update their balance
            $this->updateUserBalance($parentLevelTwo, $order_total, $bonus = 7.5);

            $parentLevelThree = $this->findParent($parentLevelTwo);
            
            if ($parentLevelThree) {
                // Check if the parent exists 
                // then update their balance
                $this->updateUserBalance($parentLevelThree, $order_total, $bonus = 6.5);
    
                $parentLevelFour = $this->findParent($parentLevelThree);

                if ($parentLevelFour) {
                    // Check if the parent exists 
                    // then update their balance
                    $this->updateUserBalance($parentLevelFour, $order_total, $bonus = 5.5);
        
                    $parentLevelFive = $this->findParent($parentLevelFour);

                    if ($parentLevelFive) {
                        // Check if the parent exists 
                        // then update their balance
                        $this->updateUserBalance($parentLevelFive, $order_total, $bonus = 4.5);

                        $parentLevelSix = $this->findParent($parentLevelFive);

                        if ($parentLevelSix) {
                            // Check if the parent exists 
                            // then update their balance
                            $this->updateUserBalance($parentLevelSix, $order_total, $bonus = 3.5);
    
                            $parentLevelSeven = $this->findParent($parentLevelSix);
    
                            if ($parentLevelSeven) {
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
     * @param integer $user_id pass the user ID
     * 
     * @return void
     */
    public function findParent(int $user_id)
    {
        global $wpdb;

        $parentUserId = $wpdb->get_var("SELECT user_id from {$wpdb->prefix}amlm_referrals WHERE referral_id = $user_id");

        return $parentUserId;
    }

    /**
     * Update the parent balance
     *
     * @param integer $user_id     pass the user id
     * @param integer $order_total pass the order total
     * @param integer $bonus       pass the bonus value
     * 
     * @return void
     */
    public function updateUserBalance(int $user_id, int $order_total, int $bonus)
    {

        $currentBalance = get_user_meta($user_id, 'amlm_earning', true);

        // If member already earned
        if (! empty($currentBalance) && $currentBalance > 0) {

            // Give bonus balance depending on purchase
            $bonus_balance = ( $bonus / 100 ) * $order_total;

            // update the user meta
            update_user_meta($user_id, 'amlm_earning', $currentBalance + $bonus_balance);

        } else {

            // Give bonus balance depending on purchase
            $bonus_balance = ( $bonus / 100 ) * $order_total;

            // update the user meta
            update_user_meta($user_id, 'amlm_earning', $bonus_balance);
        }
    }
}