<?php

if(!function_exists('__md5')){
	/**
	 * @return string|WP_Error
	 */
	function __md5($data = ''){
		if(is_object($data)){
			if($data instanceof \Closure){
				$md5_closure = __md5_closure($data); // String or WP_Error.
				return $md5_closure;
			}
			$data = __object_to_array($data); // Array or WP_Error.
			if(is_wp_error($data)){
				return $data;
			}
		}
		if(is_array($data)){
			$data = __ksort_deep($data);
			$data = serialize($data);
		}
		return md5($data);
	}
}

if(!function_exists('__md5_closure')){
	/**
	 * @return string|WP_Error
	 */
	function __md5_closure($data = null, $spl_object_hash = false){
		if(!$data instanceof \Closure){
			return __error(translate('Invalid object type.'));
		}
		$wrapper = __serializable_closure($data);
		if(is_wp_error($wrapper)){
			return $wrapper;
		}
		$serialized = serialize($wrapper);
		if(!$spl_object_hash){
			$spl_object_hash = spl_object_hash($data);
			$serialized = str_replace($spl_object_hash, 'spl_object_hash', $serialized);
		}
		return md5($serialized);
	}
}

if(!function_exists('__md5_to_uuid4')){
	/**
	 * @return string
	 */
	function __md5_to_uuid4($md5 = ''){
		if(32 !== strlen($md5)){
			return '';
		}
		return substr($md5, 0, 8) . '-' . substr($md5, 8, 4) . '-' . substr($md5, 12, 4) . '-' . substr($md5, 16, 4) . '-' . substr($md5, 20, 12);
	}
}
