<?php

if(!function_exists('__download_dir')){
	/**
	 * @return string|WP_Error
	 */
	function __download_dir($subdir = ''){
		$upload_dir = wp_get_upload_dir();
		if($upload_dir['error']){
			return __error($upload_dir['error']);
		}
		$path = $upload_dir['basedir'];
	    $dir = 'magic-downloads'; // Hardcoded.
		$download_dir = $path . '/' . $dir;
	    $subdir = ltrim($subdir, '/');
	    $subdir = untrailingslashit($subdir);
	    if($subdir){
	        $download_dir .= '/' . $subdir;
	    }
		return __mkdir_p($download_dir);
	}
}

if(!function_exists('__file')){
	/**
	 * @return string
	 */
	function __file(){
	    return (plugin_dir_path(dirname(dirname(__FILE__))) . 'magic-functions.php'); // Hardcoded.
	}
}

if(!function_exists('__handle')){
	/**
	 * @return string
	 */
	function __handle(){
	    return 'magic-functions'; // Hardcoded.
	}
}

if(!function_exists('__l10n')){
	/**
	 * @return string
	 */
	function __l10n(){
	    return 'magic_object'; // Hardcoded.
	}
}

if(!function_exists('__prefix')){
	/**
	 * @return string
	 */
	function __prefix(){
	    return 'magic_functions'; // Hardcoded.
	}
}

if(!function_exists('__require_theme_functions')){
	/**
	 * @return void
	 */
	function __require_theme_functions(){
		if(doing_action('after_setup_theme')){ // Just in time.
			__maybe_require_theme_functions();
			return;
		}
		if(did_action('after_setup_theme')){ // Too late.
			return;
		}
		__add_action_once('after_setup_theme', '__maybe_require_theme_functions');
	}
}

if(!function_exists('__shortinit')){
	/**
	 * @return string
	 */
	function __shortinit(){
	    return (plugin_dir_path(__file()) . 'src/shortinit'); // Hardcoded.
	}
}

if(!function_exists('__singleton')){
	/**
	 * @return Magic_Class|WP_Error
	 */
	function __singleton($class = ''){
	    if(!$class){
	        return __error(sprintf(translate('The "%s" argument must be a non-empty string.'), translate('Name')));
	    }
	    if(!class_exists($class)){
	        return __error('"' . $class . '" ' . translate('(not found)'));
	    }
	    if(!is_subclass_of($class, 'Magic_Class')){ // Hardcoded.
	        return __error(translate('Invalid object type.'));
	    }
	    return call_user_func([$class, 'get_instance']);
	}
}

if(!function_exists('__slug')){
	/**
	 * @return string
	 */
	function __slug(){
	    return 'magic-functions'; // Hardcoded.
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__maybe_require_theme_functions')){
	/**
	 * This function MUST be called inside the 'admin_notices' action hook.
	 *
	 * @return void
	 */
	function __maybe_require_theme_functions(){
		if(!doing_action('after_setup_theme')){ // Too early or too late.
	        return;
	    }
	    $file = get_stylesheet_directory() . '/magic-functions.php'; // Hardcoded.
	    if(!file_exists($file)){
	        return;
	    }
	    require_once($file);
	}
}
