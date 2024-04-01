<?php

if(!function_exists('__array_keys_exists')){
	/**
	 * @return bool
	 */
	function __array_keys_exists($keys = [], $array = []){
		if(!is_array($keys) or !is_array($array)){
			return false;
		}
		foreach($keys as $key){
			if(!array_key_exists($key, $array)){
				return false;
			}
		}
		return true;
	}
}

if(!function_exists('__is_associative_array')){
	/**
	 * @return bool
	 */
	function __is_associative_array($array = []){
		if(!is_array($array)){
			return false;
		}
		if(empty($array)){
			return false;
		}
		$end = count($array) - 1;
		if(array_keys($array) === range(0, $end)){
			return false;
		}
		return $array;
	}
}

if(!function_exists('__ksort_deep')){
	/**
	 * @return array
	 */
	function __ksort_deep($array = []){
		if(!__is_associative_array($array)){
			return $array;
		}
		ksort($array);
		foreach($array as $key => $value){
			$array[$key] = __ksort_deep($value);
		}
		return $array;
	}
}

if(!function_exists('__list')){
	/**
	 * @return array
	 */
	function __list($list = [], $index_key = ''){
		$newlist = [];
		foreach($list as $value){
			if(is_object($value)){
				if(isset($value->$index_key)){
					$newlist[$value->$index_key] = $value;
				} else {
					$newlist[] = $value;
				}
			} else {
				if(isset($value[$index_key])){
					$newlist[$value[$index_key]] = $value;
				} else {
					$newlist[] = $value;
				}
			}
		}
		return $newlist;
	}
}
