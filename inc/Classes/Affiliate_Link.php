<?php 

namespace AMLM\Classes;

class Affiliate_Link
{
    protected $wpdb;

    public function register() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        add_action( 'init', array( $this, 'mainInit' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'generateAffiliateSale' ) );
    }

    public function mainInit() {

        if( isset( $_GET['ref'] ) && isset( $_GET['campaign']) ) {
            $this->setAffiliateCookie( $_GET['ref'] );
        }
    }

    /**
     * Set the affiliate cookie
     *
     * @param [type] $ref
     * @return void
     */
    public function setAffiliateCookie( $ref ) {
        
        // If the amlm_ref cookie is not set
        if( ! isset( $_COOKIE['amlm_ref'] ) ) {
            $ref = filter_var($ref, FILTER_SANITIZE_NUMBER_INT);

            // Set the cookie;
            setcookie('amlm_ref', $ref, time() + (86400 * 30), "/");

            $this->updateAffiliateLink();
        }

        // If the amlm_affiliate_id cookie is not set
        if( ! isset( $_COOKIE['amlm_affiliate_id'] ) ) {
            $page_link = $this->getCurrentPageURI();

            $affiliate = $this->wpdb->get_var("SELECT id from {$this->wpdb->prefix}amlm_affiliates_link WHERE affiliate_link = '{$page_link}'");

            if( $affiliate ) {
                setcookie('amlm_affiliate_id', $affiliate, time() + (86400 * 30), "/");
            }
        }
    }

    /**
     * Update affiliate link visits count
     *
     * @return void
     */
    public function updateAffiliateLink() {
        $page_link = $this->getCurrentPageURI();

        $affiliate = $this->wpdb->get_row("SELECT id, visits from {$this->wpdb->prefix}amlm_affiliates_link WHERE affiliate_link = '{$page_link}'");
        $visits = $affiliate->visits;

        if( $affiliate ) {
            $this->wpdb->update(
                "{$this->wpdb->prefix}amlm_affiliates_link",
                array('visits' => ($visits + 1)),
                array('id' => $affiliate->id),
                array('%d'),
                array('%d')
            );
        }
    }

    public function generateAffiliateSale( $order_id ) {
        // get the order object
        $order = wc_get_order( $order_id );
        $order_total = $order->get_total();
        $order_status = $order->get_status();
        $user_id = $order->get_user_id();

        if( isset( $_COOKIE['amlm_ref'] ) && isset( $_COOKIE['amlm_affiliate_id']) ) {

            // Check if the order already placed
            $order_placed = $this->wpdb->get_var("SELECT id from {$this->wpdb->prefix}amlm_affiliate_earnings WHERE order_id = $order_id");

            if( ! $order_placed ) {
                
                // Sanitize the referral user id
                $referral_user_id = filter_var($_COOKIE['amlm_ref'], FILTER_SANITIZE_NUMBER_INT);
                $amlm_affiliate_id = filter_var($_COOKIE['amlm_affiliate_id'], FILTER_SANITIZE_NUMBER_INT);

                $paid_status = ( $order_status == 'completed' ) ? 'paid' : 'unpaid';

                // Inserting the data into the table
                $this->wpdb->insert(
                    "{$this->wpdb->prefix}amlm_affiliate_earnings",
                    array(
                        'affiliate_link_id' => $amlm_affiliate_id,
                        'user_id' => $referral_user_id,
                        'order_id' => $order_id,
                        'order_status' => $order_status,
                        'paid_status' => $paid_status,
                    ),
                    array("%d", "%d", "%d", "%s", "%s")
                );

                $this->destroyCookies();
            }
        }
    }

    /**
     * Destroy the cookies after order has been placed
     *
     * @return void
     */
    public function destroyCookies() {

        setcookie('amlm_ref', null, time() - 3600, "/");
        setcookie('amlm_affiliate_id', null, time() - 3600, "/");

    }

    /**
     * Return the current page URL
     *
     * @return void
     */
    public function getCurrentPageURI() {
        
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        $host = filter_var($_SERVER['HTTP_HOST'], FILTER_SANITIZE_STRING);
        $request_uri = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

        $url = $protocol . $host . $request_uri;

        return $url;
    }
}