<?php

if(!function_exists('__build_update_checker')){
	/**
	 * @return YahnisElsts\PluginUpdateChecker\v5p4\Vcs\BaseChecker|WP_Error
	 */
	function __build_update_checker(...$args){
		$md5 = __md5($args);
		if(__isset_array_cache('update_checkers', $md5)){
			return __get_array_cache('update_checkers', $md5);
		}
		$remote_lib = __use_plugin_update_checker();
		if(is_wp_error($remote_lib)){
			return $remote_lib;
		}
		$update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(...$args);
		__set_array_cache('update_checkers', $md5, $update_checker);
		return $update_checker;
	}
}

if(!function_exists('__set_update_license')){
	/**
	 * @return void
	 */
	function __set_update_license($slug = '', $license = ''){
		if(empty($slug) or empty($license)){
			return;
		}
		__set_array_cache('update_licenses', $slug, $license);
		__add_filter_once('puc_request_info_query_args-' . $slug, '__maybe_set_update_license');
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_set_update_license')){
	/**
	 * This function MUST be called inside the 'puc_request_info_query_args-SLUG' filter hook.
	 *
	 * @return array
	 */
	function __maybe_set_update_license($queryArgs){
		$current_filter = current_filter();
		if(!str_starts_with($current_filter, 'puc_request_info_query_args-')){ // Too early or too late.
	        return;
	    }
		$slug = str_replace('puc_request_info_query_args-', '', $current_filter);
		if(!__isset_array_cache('update_licenses', $slug)){
			return $queryArgs;
		}
		$queryArgs['license'] = (string) __get_array_cache('update_licenses', $slug, '');
		return $queryArgs;
	}
}

if(!function_exists('__use_plugin_update_checker')){
	/**
	 * @return string|WP_Error
	 */
	function __use_plugin_update_checker($ver = '5.4'){
		$key = 'plugin-update-checker-' . $ver;
	    if(__isset_cache($key)){
	        return (string) __get_cache($key, '');
	    }
		$class = 'YahnisElsts\PluginUpdateChecker\v5\PucFactory';
		if(class_exists($class)){
			return ''; // Already handled outside of this function.
		}
		$dir = __remote_lib('https://github.com/YahnisElsts/plugin-update-checker/archive/refs/tags/v' . $ver . '.zip', 'plugin-update-checker-' . $ver);
		if(is_wp_error($dir)){
			return $dir;
		}
		$file = $dir . '/plugin-update-checker.php';
		if(!file_exists($file)){
			return __error(translate('File doesn&#8217;t exist?'), $file);
		}
		require_once($file);
		if(!class_exists($class)){
			return __error(sprintf(translate('Missing parameter(s): %s'), $class) . '.');
		}
		__set_cache($key, $dir);
		return $dir;
	}
}
