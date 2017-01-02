<?php
/*
Plugin Name: GEO Spam Filter
Plugin URI:
Description: This is a HOOK for Gravity Forms to filter emails that contain SPAM by testing for "real words" and pharma text
Version: 0.1.1.1
Author: Geographics
Author URI: http://mygeographics.info
License: GPL2
*/

add_action( 'wp', 'geo_spamfilter_check', 1 );
function geo_spamfilter_check() {
    if(isset( $_POST['gform_submit'] ) ) {

        $spam = true;
        
        require_once('includes/classes/real-words/realwords.php');
        
        foreach($_POST as $key => $value) {
            
            // if we found spam...then stop testing
            if(false === $spam)continue;
            
            if(strpos($key,'input_') !== false) {
                
                // if empty then we can ignore this field and let GF handle it
                if(trim($value) == '')continue;
                
                // if matches a phone number then skip
                if(preg_match('/\(\d{3}\) \d{3}-\d{4}/',$value))continue;
                
                // run the real-words algorithm
                $spam = JH_RealWords::real_words($value);
                
            }
        }
        if(false === $spam) {
            // if we find SPAM then we will emulate what Gravity Forms would return for a successful submission.
            // This is a soft response so attackers don;t know that their efforts have failed.
            // This way they won't change their method
            
            die("<!DOCTYPE html><html><head><meta charset='UTF-8' /></head><body class='GF_AJAX_POSTBACK'><div id='gform_confirmation_wrapper_1' class='gform_confirmation_wrapper '><div id='gform_confirmation_message_1' class='gform_confirmation_message_1 gform_confirmation_message'>Thanks for contacting us! We will get in touch with you shortly.</div></div></body></html>");
        }
    }
}