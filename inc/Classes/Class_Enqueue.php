<?php 

namespace AMLM\Classes;

class Class_Enqueue {

    /**
     * Register the enqueue actions
     */
    public function register()
    {
        add_action( 'wp_enqueue_scripts', array( $this, 'siteEnqueue' ) );
    }

    /**
     * Enqueue the admin scripts and styles
     */
    public function siteEnqueue() {
        wp_enqueue_style( 'amlm-style', AMLM_PLUGIN_URL . 'assets/style.css' );
        wp_enqueue_script( 'amlm-script', AMLM_PLUGIN_URL . 'assets/script.js', array('jquery'), true );
    }

}