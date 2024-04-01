<?php

if(!function_exists('__custom_interim_login_page')){
    /**
     * @return void
     */
    function __custom_interim_login_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'login', $post->ID);
    }
}

if(!function_exists('__custom_login_page')){
    /**
     * @return void
     */
    function __custom_login_page($post_id = 0, $interim_login = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'login', $post->ID);
        __add_action_once('login_form_login', '__maybe_redirect_login_form_login');
        if(!$interim_login){
            return;
        }
        __custom_interim_login_page($interim_login);
    }
}

if(!function_exists('__custom_lostpassword_page')){
    /**
     * @return void
     */
    function __custom_lostpassword_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'lostpassword', $post->ID);
        __add_action_once('login_form_lostpassword', '__maybe_redirect_login_form_lostpassword');
        __add_action_once('login_form_retrievepassword', '__maybe_redirect_login_form_lostpassword');
    }
}

if(!function_exists('__custom_retrievepassword_page')){
    /**
	 * Alias for __custom_lostpassword_page.
	 *
     * @return void
     */
    function __custom_retrievepassword_page($post_id = 0){
        __custom_lostpassword_page($post_id);
    }
}

if(!function_exists('__custom_register_page')){
    /**
     * @return void
     */
    function __custom_register_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'register', $post->ID);
        __add_action_once('login_form_register', '__maybe_redirect_login_form_register');
    }
}

if(!function_exists('__custom_resetpass_page')){
    /**
     * @return void
     */
    function __custom_resetpass_page($post_id = 0){
        $post = get_post($post_id);
        if(is_null($post) or 'page' !== $post->post_type or 'publish' !== $post->post_status){
            return;
        }
        __set_array_cache('login_forms', 'resetpass', $post->ID);
        __add_action_once('login_form_resetpass', '__maybe_redirect_login_form_resetpass');
        __add_action_once('login_form_rp', '__maybe_redirect_login_form_resetpass');
    }
}

if(!function_exists('__custom_rp_page')){
    /**
	 * Alias for __custom_resetpass_page.
	 *
     * @return void
     */
    function __custom_rp_page($post_id = 0){
        __custom_resetpass_page($post_id);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_redirect_login_form_login')){
    /**
	 * This function MUST be called inside the 'login_form_login' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_login(){
        if(!doing_action('login_form_login')){ // Too early or too late.
	        return;
	    }
        $action = (isset($_REQUEST['interim-login']) ? 'interim_login' : 'login');
        if(!__isset_array_cache('login_forms', $action)){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', $action, 0);
        $url = get_permalink($post_id);
        if($_GET){
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_lostpassword')){
    /**
	 * This function MUST be called inside the 'login_form_lostpassword' or 'login_form_retrievepassword' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_lostpassword(){
        if(!doing_action('login_form_lostpassword') and !doing_action('login_form_retrievepassword')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'lostpassword')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'lostpassword', 0);
        $url = get_permalink($post_id);
        if($_GET){
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_register')){
    /**
	 * This function MUST be called inside the 'login_form_register' action hook.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_register(){
        if(!doing_action('login_form_register')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'register')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'register', 0);
        $url = get_permalink($post_id);
        if($_GET){
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}

if(!function_exists('__maybe_redirect_login_form_resetpass')){
    /**
	 * This function MUST be called inside the 'login_form_resetpass' or 'login_form_rp' action hooks.
	 *
     * @return void
     */
    function __maybe_redirect_login_form_resetpass(){
        if(!doing_action('login_form_resetpass') and !doing_action('login_form_rp')){ // Too early or too late.
	        return;
	    }
        if(!__isset_array_cache('login_forms', 'resetpass')){
            return;
        }
        $post_id = (int) __get_array_cache('login_forms', 'resetpass', 0);
        $url = get_permalink($post_id);
        if($_GET){
            $url = add_query_arg($_GET, $url);
        }
        wp_safe_redirect($url);
        exit;
    }
}
