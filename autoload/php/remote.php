<?php

if(!function_exists('__download_url')){
	/**
	 * Alias for download_url.
	 *
	 * WARNING: The file is not automatically deleted, the script must unlink() the file.
	 *
	 * @return string|WP_Error
	 */
	function __download_url($url = '', $dir = '', $args = []){
		if(!$url){
			return __error(__('Invalid URL Provided.'));
		}
		if($dir){
			$dir = __check_upload_dir($dir);
		} else {
			$dir = __download_dir();
		}
		if(is_wp_error($dir)){
			return $dir;
		}
		$url_filename = __basename($url);
		$tmpfname = wp_tempnam($url_filename, $dir);
		if(!$tmpfname){
			return __error(__('Could not create temporary file.'));
		}
		$args = wp_parse_args($args, [
			'timeout' => 300,
		]);
		$args = __sanitize_remote_args($args, $url);
		$args['filename'] = $tmpfname;
		$args['stream'] = true;
		$response = wp_safe_remote_get($url, $args);
		if(is_wp_error($response)){
			unlink($tmpfname);
			return $response;
		}
		$code = wp_remote_retrieve_response_code($response);
		if(!is_success($code)){
			$body = __get_file_sample($tmpfname);
			$message = __get_response_message($response);
			$data = [
				'body' => $body,
				'code' => $code,
			];
			unlink($tmpfname);
			return __error($message, $data);
		}
		$tmpfname_disposition = __get_content_disposition_filename($response);
		if($tmpfname_disposition){
			$tmpdname = dirname($tmpfname);
			$tmpfname_disposition = $tmpdname . '/' . wp_unique_filename($tmpdname, $tmpfname_disposition);
			if(rename($tmpfname, $tmpfname_disposition)){
				$tmpfname = $tmpfname_disposition;
			}
			if($tmpfname !== $tmpfname_disposition and file_exists($tmpfname_disposition)){
				unlink($tmpfname_disposition);
			}
		}
		$content_md5 = __get_content_md5($response);
		if($content_md5){
			$md5_check = verify_file_md5($tmpfname, $content_md5);
			if(is_wp_error($md5_check)){
				unlink($tmpfname);
				return $md5_check;
			}
		}
		return $tmpfname;
	}
}

if(!function_exists('__get_content_disposition_filename')){
	/**
	 * @return string
	 */
	function __get_content_disposition_filename($r = []){
		if(!__is_wp_http_request($r) and !__is_wp_http_requests_response($r)){
			return '';
		}
		$content_disposition = (array) wp_remote_retrieve_header($r, 'Content-Disposition');
		if(!$content_disposition){
			return '';
		}
		$content_disposition = strtolower($content_disposition[0]);
		if(!str_starts_with($content_disposition, 'attachment; filename=')){
			return '';
		}
		$filename = sanitize_file_name(substr($content_disposition, 21));
		if(!$filename){
			return ''; // Potential file name must be valid string.
		}
		if(0 !== validate_file($filename)){
			return '';
		}
		return $filename;
	}
}

if(!function_exists('__get_content_md5')){
	/**
	 * @return string
	 */
	function __get_content_md5($r = []){
		if(!__is_wp_http_request($r) and !__is_wp_http_requests_response($r)){
			return '';
		}
		$content_md5 = (array) wp_remote_retrieve_header($r, 'Content-MD5');
		if(!$content_md5){
			return '';
		}
		return $content_md5[0];
	}
}

if(!function_exists('__get_content_type')){
	/**
	 * Alias for WP_REST_Request::get_content_type.
	 *
	 * Retrieves the Content-Type of the request or response.
	 *
	 * @return array
	 */
	function __get_content_type($r = []){
		if(!__is_wp_http_request($r) and !__is_wp_http_requests_response($r)){
			return [];
		}
		$content_type = (array) wp_remote_retrieve_header($r, 'Content-Type');
		if(!$content_type){
			return [];
		}
		$value = $content_type[0];
		$parameters = '';
		if(strpos($value, ';')){
			list($value, $parameters) = explode(';', $value, 2);
		}
		$value = strtolower($value);
		if(!str_contains($value, '/')){
			return [];
		}
		list($type, $subtype) = explode('/', $value, 2); // Parse type and subtype out.
		$data = compact('value', 'type', 'subtype', 'parameters');
		$data = array_map('trim', $data);
		return $data;
	}
}

if(!function_exists('__get_file_sample')){
	/**
	 * @return string
	 */
	function __get_file_sample($tmpfname = ''){
		if(!is_file($tmpfname)){
			return '';
		}
		$tmpf = fopen($tmpfname, 'rb'); // Retrieve a sample of the response body for debugging purposes.
		if(!$tmpf){
			return '';
		}
		$response_size = apply_filters('download_url_error_max_body_size', KB_IN_BYTES); // Filters the maximum error response body size. Default 1 KB.
		$sample = fread($tmpf, $response_size);
		fclose($tmpf);
		return $sample;
	}
}

if(!function_exists('__get_response_message')){
	/**
	 * @return string
	 */
	function __get_response_message($response = []){
		if(!__is_wp_http_requests_response($response)){
			return '';
		}
		$message = trim(wp_remote_retrieve_response_message($response));
		if($message){
			return $message;
		}
		$code = wp_remote_retrieve_response_code($response);
		$message = trim(get_status_header_desc($code));
		if($message){
			return $message;
		}
		$message = __('Something went wrong.');
		return $message;
	}
}

if(!function_exists('__is_cloudflare')){
	/**
	 * @return bool
	 */
	function __is_cloudflare(){
		return isset($_SERVER['CF-ray']); // TODO: Check for Cloudflare Enterprise.
	}
}

if(!function_exists('__is_content_type')){
	/**
	 * @return bool
	 */
	function __is_content_type($content_type = []){
		if(!__array_keys_exists(['parameters', 'subtype', 'type', 'value'], $content_type)){
			return false;
		}
		$count = count($content_type);
		if(4 !== $count){
			return false;
		}
	    return true;
	}
}

if(!function_exists('__is_json_content_type')){
	/**
	 * Checks if the request or response has specified a JSON Content-Type.
	 *
	 * @return bool
	 */
	function __is_json_content_type($content_type = []){
		if(empty($content_type)){
			return wp_is_json_request(); // Checks whether current request is a JSON request, or is expecting a JSON response.
		}
		if(!__is_content_type($content_type)){
			$content_type = __get_content_type($content_type);
			if(empty($content_type)){
				return false;
			}
		}
		return wp_is_json_media_type($content_type['value']);
	}
}

if(!function_exists('__is_wp_http_request')){
	/**
	 * @return bool
	 */
	function __is_wp_http_request($args = []){
		if(!is_array($args)){
			return false;
		}
		if(!$args){
			return true;
		}
		$wp_http_request_args = ['body', 'blocking', 'compress', 'cookies', 'decompress', 'filename', 'headers', 'httpversion', 'limit_response_size', 'method', 'redirection', 'reject_unsafe_urls', 'sslcertificates', 'sslverify', 'stream', 'timeout', 'user-agent'];
		$wp_http_request = true;
		foreach(array_keys($args) as $arg){
			if(!in_array($arg, $wp_http_request_args)){
				$wp_http_request = false;
				break;
			}
		}
		if(!isset($args['method'])){
			return $wp_http_request;
		}
		if(!in_array($args['method'], ['DELETE', 'GET', 'HEAD', 'OPTIONS', 'PATCH', 'POST', 'PUT', 'TRACE'])){
			return false;
		}
		return true;
	}
}

if(!function_exists('__is_wp_http_requests_response')){
	/**
	 * @return bool
	 */
	function __is_wp_http_requests_response($response = []){
		if(!__array_keys_exists(['body', 'cookies', 'filename', 'headers', 'http_response', 'response'], $response)){
			return false;
		}
		if(!$response['http_response'] instanceof \WP_HTTP_Requests_Response){
			return false;
		}
	    return true;
	}
}

if(!function_exists('__json_decode')){
	/**
	 * Alias for json_decode.
	 *
	 * Differs from json_decode in that it will return a WP_Error on failure.
	 *
	 * Retrieves the parameters from a JSON-formatted body.
	 *
	 * @return array
	 */
	function __json_decode($json = '', $associative = null, $depth = 512, $flags = 0){
		$json = trim($json);
		if($associative or ($flags & JSON_OBJECT_AS_ARRAY)){
			$empty = [];
		} else {
			$empty = new \stdClass;
		}
		if(empty($json)){
			return $empty;
		}
		$params = json_decode($json, $associative, $depth, $flags); // Parses the JSON parameters.
		if(is_null($params) and JSON_ERROR_NONE !== json_last_error()){ // Check for a parsing error.
			$error_data = [
				'json_error_code' => json_last_error(),
				'json_error_message' => json_last_error_msg(),
				'status' => \WP_Http::BAD_REQUEST,
			];
			return __error(__('Invalid JSON body passed.'), $error_data);
		}
		return $params;
	}
}

if(!function_exists('__parse_response')){
	/**
	 * @return array|string|WP_Error
	 */
	function __parse_response($response = []){
		if(is_wp_error($response)){
			return $response;
		}
		if(!__is_wp_http_requests_response($response)){
	 		return __error(__('Invalid data provided.'), $response);
	 	}
		return new \Magic_Response($response);
	}
}

if(!function_exists('__remote_country')){
	/**
	 * @return string
	 */
	function __remote_country(){
		switch(true){
			case !empty($_SERVER['HTTP_CF_IPCOUNTRY']):
				$country = $_SERVER['HTTP_CF_IPCOUNTRY']; // Cloudflare.
				break;
			case is_callable(['wfUtils', 'IP2Country']):
				$country = \wfUtils::IP2Country(__remote_ip()); // Wordfence.
				break;
			default:
				$country = '';
		}
		return strtoupper($country); // ISO 3166-1 alpha-2.
	}
}

if(!function_exists('__remote_delete')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_delete($url = '', $args = []){
		return __remote_request('DELETE', $url, $args);
	}
}

if(!function_exists('__remote_get')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_get($url = '', $args = []){
		return __remote_request('GET', $url, $args);
	}
}

if(!function_exists('__remote_head')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_head($url = '', $args = []){
		return __remote_request('HEAD', $url, $args);
	}
}

if(!function_exists('__remote_ip')){
	/**
	 * @return string
	 */
	function __remote_ip($default = ''){
		switch(true){
			case !empty($_SERVER['HTTP_CF_CONNECTING_IP']):
				$ip = $_SERVER['HTTP_CF_CONNECTING_IP']; // Cloudflare.
				break;
			case (__is_plugin_active('wordfence/wordfence.php') and is_callable(['wfUtils', 'getIP'])):
				$ip = \wfUtils::getIP(); // Wordfence.
				break;
			case !empty($_SERVER['HTTP_X_FORWARDED_FOR']):
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				break;
			case !empty($_SERVER['HTTP_X_REAL_IP']):
				$ip = $_SERVER['HTTP_X_REAL_IP'];
				break;
			case !empty($_SERVER['REMOTE_ADDR']):
				$ip = $_SERVER['REMOTE_ADDR'];
				break;
			default:
				return $default;
		}
		if(false === strpos($ip, ',')){
			$ip = trim($ip);
		} else {
			$ip = explode(',', $ip);
			$ip = array_map('trim', $ip);
			$ip = array_filter($ip);
			if(empty($ip)){
				return $default;
			}
			$ip = $ip[0];
		}
		if(!\WP_Http::is_ip_address($ip)){
			return $default;
		}
		return $ip;
	}
}

//pendiente
if(!function_exists('__remote_lib')){
	/**
	 * @return string|WP_Error
	 */
	function __remote_lib($url = '', $expected_dir = ''){
	    $key = md5($url);
	    if(__isset_cache($key)){
	        return (string) __get_cache($key, '');
	    }
		$download_dir = __download_dir();
		if(is_wp_error($download_dir)){
			return $download_dir;
		}
		$fs = __fs_direct();
		if(is_wp_error($fs)){
			return $fs;
		}
		$name = 'remote_lib_' . $key;
		$to = $download_dir . '/' . $name;
		if(empty($expected_dir)){
			$expected_dir = $to;
		} else {
			$expected_dir = ltrim($expected_dir, '/');
			$expected_dir = untrailingslashit($expected_dir);
			$expected_dir = $to . '/' . $expected_dir;
		}
		$dirlist = $fs->dirlist($expected_dir, false);
		if(!empty($dirlist)){
	        __set_cache($key, $expected_dir);
			return $expected_dir; // Already exists.
		}
		$file = __download_url($url, $download_dir);
		if(is_wp_error($file)){
			return $file;
		}
		$result = unzip_file($file, $to);
		@unlink($file);
		if(is_wp_error($result)){
			$fs->rmdir($to, true);
			return $result;
		}
		if(!$fs->dirlist($expected_dir, false)){
			$fs->rmdir($to, true);
			return __error(translate('Destination directory for file streaming does not exist or is not writable.'));
		}
	    __set_cache($key, $expected_dir);
		return $expected_dir;
	}
}

if(!function_exists('__remote_options')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_options($url = '', $args = []){
		return __remote_request('OPTIONS', $url, $args);
	}
}

if(!function_exists('__remote_patch')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_patch($url = '', $args = []){
		return __remote_request('PATCH', $url, $args);
	}
}

if(!function_exists('__remote_post')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_post($url = '', $args = []){
		return __remote_request('POST', $url, $args);
	}
}

if(!function_exists('__remote_put')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_put($url = '', $args = []){
		return __remote_request('PUT', $url, $args);
	}
}

if(!function_exists('__remote_request')){
	/**
	 * @return array|WP_Error
	 */
	function __remote_request($method = '', $url = '', $args = []){
		$args = wp_parse_args($args);
		$args['method'] = $method;
		$args = __sanitize_remote_args($args, $url);
		$response = wp_remote_request($url, $args);
		if(is_wp_error($response)){
			return $response;
		}
		return __parse_response($response);
	}
}

if(!function_exists('__remote_trace')){
	/**
	 * @return array|string|WP_Error
	 */
	function __remote_trace($url = '', $args = []){
		return __remote_request('TRACE', $url, $args);
	}
}

if(!function_exists('__sanitize_remote_args')){
	/**
	 * @return array
	 */
	function __sanitize_remote_args($args = [], $url = ''){
		$args = wp_parse_args($args);
		if(!__is_wp_http_request($args)){
			return [
				'body' => $args,
			];
		}
		if(isset($args['timeout'])){
			$args['timeout'] = __sanitize_timeout($args['timeout']);
		}
		if(empty($args['cookies'])){
			if(!empty($url)){
				$location = wp_sanitize_redirect($url);
				if(wp_validate_redirect($location)){
					$args['cookies'] = $_COOKIE;
				}
			}
		}
		if(empty($args['user-agent'])){
			if(empty($_SERVER['HTTP_USER_AGENT'])){
				$args['user-agent'] = 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36'; // Example Chrome UA string: https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/User-Agent#chrome_ua_string
			} else {
				$args['user-agent'] = $_SERVER['HTTP_USER_AGENT'];
			}
		}
		if(isset($args['body']) and is_array($args['body']) and __is_json_content_type($args)){
			$args['body'] = wp_json_encode($args['body']);
		}
		return $args;
	}
}

if(!function_exists('__sanitize_timeout')){
	/**
	 * @return int
	 */
	function __sanitize_timeout($timeout = 0){
		$timeout = (int) $timeout;
		if($timeout < 0){
			$timeout = 0;
		}
		$max_execution_time = (int) ini_get('max_execution_time');
		if(0 !== $max_execution_time){
			if(0 === $timeout or $timeout > $max_execution_time){
				$timeout = $max_execution_time - 1;
			}
		}
		if(__is_cloudflare()){
			if(0 === $timeout or $timeout > 98){
				$timeout = 98; // If the max_execution_time is set to greater than 98 seconds, reduce it a bit to prevent edge-case timeouts that may happen before the page is fully loaded. TODO: Check for Cloudflare Enterprise. See: https://developers.cloudflare.com/support/troubleshooting/cloudflare-errors/troubleshooting-cloudflare-5xx-errors/#error-524-a-timeout-occurred.
			}
		}
		return $timeout;
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These classes’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!class_exists('Magic_Response')){
	class Magic_Response {

		private $body = '', $code = 0, $cookies = [], $headers = [], $is_json = false, $json_params = [], $message = '', $raw_response = [], $success = false;

		/**
		 * @return void
		 */
	    public function __construct($response = []){
			$this->raw_response = $response;
			if(__is_wp_http_requests_response($response)){
				$this->body = trim(wp_remote_retrieve_body($response));
				$this->code = absint(wp_remote_retrieve_response_code($response));
				$this->cookies = wp_remote_retrieve_cookies($response);
				$this->headers = wp_remote_retrieve_headers($response);
				$this->is_json = __is_json_content_type($response);
				$this->message = __get_response_message($response);
				$this->success = is_success($this->code);
				if($this->is_json){
					$this->json_params = __json_decode($this->body, true);
				}
		 	} else {
				$this->message = __('Invalid data provided.');
			}
		}

		/**
		 * @return string
		 */
	    public function body(){
			return $this->body;
		}

		/**
		 * @return int
		 */
	    public function code(){
			return $this->code;
		}

		/**
		 * @return array
		 */
	    public function cookies(){
			return $this->cookies;
		}

		/**
		 * @return array
		 */
	    public function headers(){
			return $this->headers;
		}

		/**
		 * @return bool
		 */
	    public function is_json(){
			return $this->is_json;
		}

		/**
		 * @return bool
		 */
	    public function is_success(){
			return $this->success;
		}

		/**
		 * @return array
		 */
	    public function json_params(){
			return $this->json_params;
		}

		/**
		 * @return string
		 */
	    public function message(){
			return $this->message;
		}

		/**
		 * @return array
		 */
	    public function raw_response(){
			return $this->raw_response;
		}

	}
}
