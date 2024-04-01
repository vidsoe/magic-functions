<?php

if(!function_exists('__add_external_rule')){
	/**
	 * @return void
	 */
	function __add_external_rule($regex = '', $query = '', $plugin_file = ''){
		$rule = [
			'plugin_file' => $plugin_file,
			'query' => str_replace(site_url('/'), '', $query),
			'regex' => str_replace(site_url('/'), '', $regex),
		];
		$md5 = __md5($rule);
		if(doing_action('generate_rewrite_rules')){ // Just in time.
			if(__isset_array_cache('external_rules', $md5)){
				return; // Already exists.
			}
			__maybe_add_external_rule($rule);
			__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
			return;
		}
		if(did_action('generate_rewrite_rules')){ // Too late.
			return;
		}
		__set_array_cache('external_rules', $md5, $rule);
	    __add_action_once('generate_rewrite_rules', '__maybe_add_external_rules');
		__add_action_once('admin_notices', '__maybe_add_external_rules_notice');
	}
}

if(!function_exists('__external_rule_exists')){
	/**
	 * @return bool
	 */
	function __external_rule_exists($regex = '', $query = ''){
		$regex = str_replace('.+?', '.+', $regex); // Apache 1.3 does not support the reluctant (non-greedy) modifier.
		$rewrite_rules = __get_rewrite_rules();
		$rule = 'RewriteRule ^' . $regex . ' ' . __home_root() . $query . ' [QSA,L]';
		return in_array($rule, $rewrite_rules);
	}
}

if(!function_exists('__get_rewrite_rules')){
	/**
	 * @return array
	 */
	function __get_rewrite_rules(){
		if(__isset_cache('rewrite_rules')){
			$rewrite_rules = (array) __get_cache('rewrite_rules', []);
			return $rewrite_rules;
		}
		$rewrite_rules = array_filter(extract_from_markers(get_home_path() . '.htaccess', 'WordPress'));
		__set_cache('rewrite_rules', $rewrite_rules);
		return $rewrite_rules;
	}
}

if(!function_exists('__home_root')){
	/**
	 * @return string
	 */
	function __home_root(){
		$home_root = parse_url(home_url());
		if(isset($home_root['path'])){
			$home_root = trailingslashit($home_root['path']);
		} else {
			$home_root = '/';
		}
		return $home_root;
	}
}

if(!function_exists('__is_external_rule')){
	/**
	 * @return bool
	 */
	function __is_external_rule($rule = []){
		if(!__array_keys_exists(['plugin_file', 'query', 'regex'], $rule)){
			return false;
		}
		$count = count($rule);
		if(3 !== $count){
			return false;
		}
	    return true;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_add_external_rule')){
	/**
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rule($rule = []){
		global $wp_rewrite;
		if(!doing_action('generate_rewrite_rules')){ // Too early or too late.
	        return;
	    }
		if(!__is_external_rule($rule)){
			return;
		}
		if(__is_plugin_deactivating($rule['plugin_file'])){
			return;
		}
		$wp_rewrite->add_external_rule($rule['regex'], $rule['query']);
	}
}

if(!function_exists('__maybe_add_external_rules')){
	/**
	 * This function MUST be called inside the 'generate_rewrite_rules' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules($wp_rewrite){
		if(!doing_action('generate_rewrite_rules')){ // Too early or too late.
	        return;
	    }
		$external_rules = (array) __get_cache('external_rules', []);
	    if(!$external_rules){
	        return;
	    }
	    foreach($external_rules as $rule){
			__maybe_add_external_rule($rule);
	    }
	}
}

if(!function_exists('__maybe_add_external_rules_notice')){
	/**
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_add_external_rules_notice(){
		if(!doing_action('admin_notices')){ // Too early or too late.
	        return;
	    }
		if(!current_user_can('manage_options')){
			return;
		}
		$external_rules = (array) __get_cache('external_rules', []);
	    if(!$external_rules){
	        return;
	    }
	    $add_admin_notice = false;
		foreach($external_rules as $rule){
			if(!__external_rule_exists($rule['regex'], $rule['query'])){
				$add_admin_notice = true;
				break;
			}
		}
		if(!$add_admin_notice){
	        return;
		}
	    $message = sprintf(translate('You should update your %s file now.'), '<code>.htaccess</code>');
	    $message .= ' ';
	    $message .= sprintf('<a href="%s">%s</a>', esc_url(admin_url('options-permalink.php')), translate('Flush permalinks')) . '.';
	    __add_admin_notice($message);
	}
}
