<?php

if(!function_exists('__current_utm_param')){
    /**
     * @return string
     */
    function __current_utm_param($name = ''){
        $params = __current_utm_params();
        if(!array_key_exists($name, $params)){
            return '';
        }
        return $params[$name];
    }
}

if(!function_exists('__current_utm_params')){
    /**
     * @return array
     */
    function __current_utm_params(){
        $keys = __utm_keys();
        $utm = [];
        foreach($keys as $key){
            // 1. GET
            if(isset($_GET[$key])){
                $utm[$key] = $_GET[$key];
                continue;
            }
            // 2. COOKIE
            $name = __utm_cookie_name($key);
            if(isset($_COOKIE[$name])){
                $utm[$key] = $_COOKIE[$name];
                continue;
            }
            // 3. EMPTY
            $utm[$key] = '';
        }
        return $utm;
    }
}

if(!function_exists('__enqueue_utm_object')){
    /**
     * @return void
     */
    function __enqueue_utm_object(){
        __set_cache('utm', true);
        if(did_action('after_setup_theme')){
            __maybe_set_utm_cookies();
        } else {
            __add_action_once('after_setup_theme', '__maybe_set_utm_cookies');
        }
        if(doing_action('wp_enqueue_scripts')){ // Just in time.
            __localize_utm_object();
            return;
        }
        if(did_action('wp_enqueue_scripts')){ // Too late.
            return;
        }
        __add_action_once('wp_enqueue_scripts', '__maybe_localize_utm_object');
    }
}

if(!function_exists('__utm_keys')){
    /**
     * @return array
     */
    function __utm_keys(){
        $utm = __utm_pairs();
        return array_keys($utm);
    }
}

if(!function_exists('__utm_pairs')){
    /**
     * @return array
     */
    function __utm_pairs(){
        $utm = [
            'utm_campaign' => 'Name',
            'utm_content' => 'Content',
            'utm_id' => 'ID',
            'utm_medium' => 'Medium',
            'utm_source' => 'Source',
            'utm_term' => 'Term',
        ];
        return $utm;
    }
}

if(!function_exists('__utm_param_name')){
    /**
     * @return string
     */
    function __utm_param_name($name = ''){
        $pairs = __utm_pairs();
        if(!array_key_exists($name, $pairs)){
            return '';
        }
        return $pairs[$name];
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__localize_utm_object')){
    /**
     * This function MUST be called inside the 'wp_enqueue_scripts' action hook.
     *
     * @return void
     */
    function __localize_utm_object(){
        if(!doing_action('wp_enqueue_scripts')){ // Too early or too late.
            return;
        }
        $current = __current_utm_params();
        $handle = __handle();
        $object_name = __str_prefix('current_utm_params');
        $query_string = build_query($current);
        $current['utm_hash'] = md5($query_string);
        $current['utm_query'] = $query_string;
        ksort($current);
        wp_localize_script($handle, $object_name, $current);
    }
}

if(!function_exists('__maybe_localize_utm_object')){
    /**
     * @return void
     */
    function __maybe_localize_utm_object(){
        $continue = (bool) __get_cache('utm', false);
        if(!$continue){
            return;
        }
        __localize_utm_object();
    }
}

if(!function_exists('__maybe_set_utm_cookies')){
    /**
     * @return void
     */
    function __maybe_set_utm_cookies(){
        $continue = (bool) __get_cache('utm', false);
        if(!$continue){
            return;
        }
        $at_least_one = false;
        $keys = __utm_keys();
        foreach($keys as $key){
            if(isset($_GET[$key])){
                $at_least_one = true;
                break;
            }
        }
        if(!$at_least_one){
            return;
        }
        __maybe_unset_utm_cookies();
        $cookie_lifetime = time() + WEEK_IN_SECONDS;
        $secure = ('https' === parse_url(home_url(), PHP_URL_SCHEME));
        foreach($keys as $key){
            if(!isset($_GET[$key])){
                continue;
            }
            $value = wp_unslash($_GET[$key]);
            $value = esc_attr($value);
            $name = __utm_cookie_name($key);
            setcookie($name, $value, $cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN, $secure);
        }
    }
}

if(!function_exists('__maybe_unset_utm_cookies')){
    /**
     * @return void
     */
    function __maybe_unset_utm_cookies(){
        $keys = __utm_keys();
        $past = time() - YEAR_IN_SECONDS;
        foreach($keys as $key){
            $name = __utm_cookie_name($key);
            if(!isset($_COOKIE[$name])){
                continue;
            }
            setcookie($name, ' ', $past, COOKIEPATH, COOKIE_DOMAIN);
        }
    }
}

if(!function_exists('__utm_cookie_name')){
    /**
     * @return string
     */
    function __utm_cookie_name($name = ''){
        $keys = __utm_keys();
        if(!in_array($name, $keys)){
            return '';
        }
        $name = __str_prefix($name . '_' . COOKIEHASH);
        return $name;
    }
}
