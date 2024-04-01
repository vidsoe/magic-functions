<?php

if(!function_exists('__absint')){
	/**
	 * @return int
	 */
	function __absint($maybeint = 0){
		if(!is_numeric($maybeint)){
			return 0; // Make sure the value is numeric to avoid casting objects, for example, to int 1.
		}
		return absint($maybeint);
	}
}

if(!function_exists('__breadcrumbs')){
	/**
	 * @return string
	 */
	function __breadcrumbs($breadcrumbs = [], $separator = '>'){
	    $elements = [];
	    foreach($breadcrumbs as $breadcrumb){
	        if(!isset($breadcrumb['text'])){
	            continue;
	        }
	        $text = $breadcrumb['text'];
	        if(isset($breadcrumb['link'])){
	            $href = $breadcrumb['link'];
	            $target = isset($breadcrumb['target']) ? $breadcrumb['target'] : '_self';
	            $element = sprintf('<a href="%1$s" target="%2$s">%3$s</a>', esc_url($href), esc_attr($target), esc_html($text));
	        } else {
	            $element = sprintf('<span>%1$s</span>', esc_html($text));
	        }
	        $elements[] = $element;
	    }
	    $separator = ' ' . trim($separator) . ' ';
		return implode($separator, $elements);
	}
}

if(!function_exists('__clone_role')){
	/**
	 * @return void|WP_Role
	 */
	function __clone_role($source = '', $destination = '', $display_name = ''){
		$role = get_role($source);
		if(is_null($role)){
			return;
		}
		$destination = __canonicalize($destination);
		return add_role($destination, $display_name, $role->capabilities);
	}
}

if(!function_exists('__current_screen_in')){
	/**
	 * @return bool
	 */
	function __current_screen_in($ids = []){
		global $current_screen;
		if(!is_array($ids)){
			return false;
		}
		if(!isset($current_screen)){
			return false;
		}
		return in_array($current_screen->id, $ids);
	}
}

if(!function_exists('__current_screen_is')){
	/**
	 * @return bool
	 */
	function __current_screen_is($id = ''){
		global $current_screen;
		if(!is_string($id)){
			return false;
		}
		if(!isset($current_screen)){
			return false;
		}
		return ($current_screen->id === $id);
	}
}

if(!function_exists('__format_function')){
	/**
	 * @return string
	 */
	function __format_function($function_name = '', $args = []){
		$str = '<span style="color: #24831d; font-family: monospace; font-weight: 400;">' . $function_name . '(';
		$function_args = [];
		foreach($args as $arg){
			$arg = shortcode_atts([
				'default' => 'null',
				'name' => '',
				'type' => '',
			], $arg);
			if($arg['default'] and $arg['name'] and $arg['type']){
				$function_args[] = '<span style="color: #cd2f23; font-family: monospace; font-style: italic; font-weight: 400;">' . $arg['type'] . '</span> <span style="color: #0f55c8; font-family: monospace; font-weight: 400;">$' . $arg['name'] . '</span> = <span style="color: #000; font-family: monospace; font-weight: 400;">' . $arg['default'] . '</span>';
			}
		}
		if($function_args){
			$str .= ' ' . implode(', ', $function_args) . ' ';
		}
		$str .= ')</span>';
		return $str;
	}
}

if(!function_exists('__go_to')){
	/**
	 * @return bool
	 */
	function __go_to($str = ''){
		return trim(str_replace('&larr;', '', sprintf(translate_with_gettext_context('&larr; Go to %s', 'site'), $str)));
	}
}

if(!function_exists('__has_btn_class')){
	/**
	 * @return bool
	 */
	function __has_btn_class($class = ''){
	    $class = __remove_whitespaces($class);
	    preg_match_all('/btn-[A-Za-z][-A-Za-z0-9_:.]*/', $class, $matches);
		$matches = array_filter($matches[0], function($match){
			return !in_array($match, ['btn-block', 'btn-lg', 'btn-sm']);
		});
		return (bool) $matches;
	}
}

if(!function_exists('__has_shortcode')){
	/**
	 * @return array
	 */
	function __has_shortcode($content = '', $tag = ''){
	    if(false === strpos($content, '[')){
	        return [];
	    }
	    if(!shortcode_exists($tag)){
	        return [];
	    }
	    preg_match_all('/' . get_shortcode_regex() . '/', $content, $matches, PREG_SET_ORDER);
	    if(!$matches){
	        return [];
	    }
	    foreach($matches as $shortcode){
	        if($tag === $shortcode[2]){
	            return shortcode_parse_atts($shortcode[3]);
	        }
	        if(!$shortcode[5]){
	            continue;
	        }
	        $attr = __has_shortcode($shortcode[5], $tag);
	        if(!$attr){
	            continue;
	        }
	        return $attr;
	    }
	    return [];
	}
}

if(!function_exists('__is_doing_heartbeat')){
	/**
	 * @return bool
	 */
	function __is_doing_heartbeat(){
		return (wp_doing_ajax() and isset($_POST['action']) and 'heartbeat' === $_POST['action']);
	}
}

if(!function_exists('__is_false')){
	/**
	 * @return bool
	 */
	function __is_false($data = ''){
		return in_array((string) $data, ['0', 'false', 'off'], true);
	}
}

if(!function_exists('__is_revision_or_auto_draft')){
	/**
	 * @return bool
	 */
	function __is_revision_or_auto_draft($post = null){
		return (wp_is_post_revision($post) or 'auto-draft' === get_post_status($post));
	}
}

if(!function_exists('__is_true')){
	/**
	 * @return bool
	 */
	function __is_true($data = ''){
		return in_array((string) $data, ['1', 'on', 'true'], true);
	}
}

if(!function_exists('__object_to_array')){
	/**
	 * @return array|WP_Error
	 */
	function __object_to_array($data = null){
		if(!is_object($data)){
			return __error(__('Invalid data provided.'), $data);
		}
		$data = wp_json_encode($data);
		return __json_decode($data, true);
	}
}

if(!function_exists('__post_type_labels')){
	/**
	 * @return array
	 */
	function __post_type_labels($singular = '', $plural = '', $all = true){
		if(empty($singular)){
			return [];
		}
		if(empty($plural)){
			$plural = $singular;
		}
		return [
			'name' => $plural,
			'singular_name' => $singular,
			'add_new' => 'Add New',
			'add_new_item' => 'Add New ' . $singular,
			'edit_item' => 'Edit ' . $singular,
			'new_item' => 'New ' . $singular,
			'view_item' => 'View ' . $singular,
			'view_items' => 'View ' . $plural,
			'search_items' => 'Search ' . $plural,
			'not_found' => 'No ' . strtolower($plural) . ' found.',
			'not_found_in_trash' => 'No ' . strtolower($plural) . ' found in Trash.',
			'parent_item_colon' => 'Parent ' . $singular . ':',
			'all_items' => ($all ? 'All ' : '') . $plural,
			'archives' => $singular . ' Archives',
			'attributes' => $singular . ' Attributes',
			'insert_into_item' => 'Insert into ' . strtolower($singular),
			'uploaded_to_this_item' => 'Uploaded to this ' . strtolower($singular),
			'featured_image' => 'Featured image',
			'set_featured_image' => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image' => 'Use as featured image',
			'filter_items_list' => 'Filter ' . strtolower($plural) . ' list',
			'items_list_navigation' => $plural . ' list navigation',
			'items_list' => $plural . ' list',
			'item_published' => $singular . ' published.',
			'item_published_privately' => $singular . ' published privately.',
			'item_reverted_to_draft' => $singular . ' reverted to draft.',
			'item_scheduled' => $singular . ' scheduled.',
			'item_updated' => $singular . ' updated.',
		];
	}
}

if(!function_exists('__properties_exists')){
	/**
	 * @return bool
	 */
	function __properties_exists($properties = [], $object = []){
		if(!is_array($properties) or !is_object($object)){
			return false;
		}
		foreach($properties as $property){
			if(!property_exists($object, $property)){
				return false;
			}
		}
		return true;
	}
}

if(!function_exists('__test')){
	/**
	 * @return void
	 */
	function __test(){
		__exit_with_error('Hello, World!');
	}
}

if(!function_exists('__validate_redirect_to')){
	/**
	 * @return string
	 */
	function __validate_redirect_to($url = ''){
		$redirect_to = isset($_REQUEST['redirect_to']) ? wp_http_validate_url($_REQUEST['redirect_to']) : false;
		if(!$redirect_to and !empty($url)){
			$redirect_to = wp_http_validate_url($url);
		}
		return (string) $redirect_to;
	}
}
