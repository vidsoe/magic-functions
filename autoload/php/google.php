<?php

if(!function_exists('__hide_recaptcha_badge')){
	/**
	 * @return void
	 */
	function __hide_recaptcha_badge(){
		if(doing_action('wp_head')){ // Just in time.
	        __echo_hide_recaptcha_badge();
			return;
	    }
		if(did_action('wp_head')){ // Too late.
			return;
		}
		__add_action_once('wp_head', '__maybe_hide_recaptcha_badge');
	}
}

if(!function_exists('__is_google_workspace')){
	/**
	 * @return bool|string
	 */
	function __is_google_workspace($email = ''){
		if(!is_email($email)){
			return false;
		}
		list($local, $domain) = explode('@', $email, 2);
		if('gmail.com' === strtolower($domain)){
			return 'gmail.com';
		}
		if(!getmxrr($domain, $mxhosts)){
			return false;
		}
		if(!in_array('aspmx.l.google.com', $mxhosts)){
			return false;
		}
		return $domain;
	}
}

if(!function_exists('__recaptcha_branding')){
	/**
	 * @return string
	 */
	function __recaptcha_branding(){
		return 'This site is protected by reCAPTCHA and the Google <a href="https://policies.google.com/privacy" target="_blank">Privacy Policy</a> and <a href="https://policies.google.com/terms" target="_blank">Terms of Service</a> apply.';
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__echo_hide_recaptcha_badge')){
	/**
	 * This function MUST be called inside the 'wp_head' action hook.
	 *
	 * @return void
	 */
	function __echo_hide_recaptcha_badge(){
		if(!doing_action('wp_head')){
	        return;
	    } ?>
	    <style type="text/css">
	        .grecaptcha-badge {
	            visibility: hidden !important;
	        }
	    </style><?php
	}
}

if(!function_exists('__maybe_hide_recaptcha_badge')){
	/**
	 * This function MUST be called inside the 'wp_head' action hook.
	 *
	 * @return void
	 */
	function __maybe_hide_recaptcha_badge(){
		if(!doing_action('wp_head')){ // Too early or too late.
	        return;
	    }
		__echo_hide_recaptcha_badge();
	}
}
