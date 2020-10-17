<?php 

namespace AMLM\Classes;

class Affiliate_Link
{
    public function register() {
        
        add_action( 'init', array( $this, 'mainInit' ) );
    }

    public function mainInit() {
        
    }
}