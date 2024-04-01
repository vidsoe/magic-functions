<?php

if(!function_exists('__get_array_cache')){
	/**
	 * @return mixed
	 */
	function __get_array_cache($array_key = '', $key = '', $default = null){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]) ? $array[$key] : $default;
	}
}

if(!function_exists('__get_cache')){
	/**
	 * @return mixed
	 */
	function __get_cache($key = '', $default = null){
		$group = __prefix();
		$value = wp_cache_get($key, $group, false, $found);
		if($found){
			return $value;
		}
	    return $default;
	}
}

if(!function_exists('__isset_array_cache')){
	/**
	 * @return bool
	 */
	function __isset_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		return isset($array[$key]);
	}
}

if(!function_exists('__isset_cache')){
	/**
	 * @return bool
	 */
	function __isset_cache($key = ''){
		$group = __prefix();
		$value = wp_cache_get($key, $group, false, $found);
	    return $found;
	}
}

if(!function_exists('__set_array_cache')){
	/**
	 * @return bool
	 */
	function __set_array_cache($array_key = '', $key = '', $data = null){
		$array = (array) __get_cache($array_key, []);
		$array[$key] = $data;
		return __set_cache($array_key, $array);
	}
}

if(!function_exists('__set_cache')){
	/**
	 * @return bool
	 */
	function __set_cache($key = '', $data = null){
		$group = __prefix();
		return wp_cache_set($key, $data, $group);
	}
}

if(!function_exists('__unset_array_cache')){
	/**
	 * @return bool
	 */
	function __unset_array_cache($array_key = '', $key = ''){
		$array = (array) __get_cache($array_key, []);
		if(isset($array[$key])){
			unset($array[$key]);
		}
		return __set_cache($array_key, $array);
	}
}

if(!function_exists('__unset_cache')){
	/**
	 * @return bool
	 */
	function __unset_cache($key = ''){
		$group = __prefix();
		return wp_cache_delete($key, $group);
	}
}
