<?php

if(!function_exists('__alert_html')){
	/**
	 * @return string
	 */
	function __alert_html($message = '', $class = '', $is_dismissible = false){
		if(!in_array($class, ['danger', 'dark', 'info', 'light', 'primary', 'secondary', 'success', 'warning'])){
			$class = 'warning';
		}
		if($is_dismissible){
			$class .= ' alert-dismissible fade show';
		}
		if($is_dismissible){
			$message .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
		}
		return '<div class="alert alert-' . $class . '">' . $message . '</div>';
	}
}

if(!function_exists('__alert_danger')){
	/**
	 * @return string
	 */
	function __alert_danger($message = '', $is_dismissible = false){
		return __alert_html($message, 'danger', $is_dismissible);
	}
}

if(!function_exists('__alert_dark')){
	/**
	 * @return string
	 */
	function __alert_dark($message = '', $is_dismissible = false){
		return __alert_html($message, 'dark', $is_dismissible);
	}
}

if(!function_exists('__alert_info')){
	/**
	 * @return string
	 */
	function __alert_info($message = '', $is_dismissible = false){
		return __alert_html($message, 'info', $is_dismissible);
	}
}

if(!function_exists('__alert_light')){
	/**
	 * @return string
	 */
	function __alert_light($message = '', $is_dismissible = false){
		return __alert_html($message, 'light', $is_dismissible);
	}
}

if(!function_exists('__alert_primary')){
	/**
	 * @return string
	 */
	function __alert_primary($message = '', $is_dismissible = false){
		return __alert_html($message, 'primary', $is_dismissible);
	}
}

if(!function_exists('__alert_secondary')){
	/**
	 * @return string
	 */
	function __alert_secondary($message = '', $is_dismissible = false){
		return __alert_html($message, 'secondary', $is_dismissible);
	}
}

if(!function_exists('__alert_success')){
	/**
	 * @return string
	 */
	function __alert_success($message = '', $is_dismissible = false){
		return __alert_html($message, 'success', $is_dismissible);
	}
}

if(!function_exists('__alert_warning')){
	/**
	 * @return string
	 */
	function __alert_warning($message = '', $is_dismissible = false){
		return __alert_html($message, 'warning', $is_dismissible);
	}
}
