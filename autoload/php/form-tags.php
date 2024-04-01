<?php

if(!function_exists('__cf7_form_tag_acceptance_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_acceptance_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('acceptance', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_checkbox_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_checkbox_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('checkbox', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_class')){
	/**
	 * Alias for WPCF7_FormTag::get_class_option.
	 *
	 * Differs from WPCF7_FormTag::get_class_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_class($tag = null, $default_classes = ''){
	    if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		return (string) $tag->get_class_option($default_classes);
	}
}

if(!function_exists('__cf7_form_tag_content')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_content($tag = null, $remove_whitespaces = false){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return ($remove_whitespaces ? __remove_whitespaces($tag->content) : trim($tag->content));
	}
}

if(!function_exists('__cf7_form_tag_content_label')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_content_label($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		if(empty($content)){
			return '';
		}
		if(!in_array($tag->basetype, ['checkbox', 'date', 'email', 'file', 'number', 'password', 'radio', 'range', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if('textarea' === $tag->basetype and $tag->has_option('has_content')){
			return '';
		}
		return $content;
	}
}

if(!function_exists('__cf7_form_tag_content_placeholder_equals')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_content_placeholder_equals($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		$placeholder = __cf7_form_tag_placeholder($tag);
		return ($content === $placeholder);
	}
}

if(!function_exists('__cf7_form_tag_date_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_date_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('date', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_fa')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_fa($tag = null){
		$class = __cf7_form_tag_fa_class($tag);
		if(!$class){
			return '';
		}
		return '<i class="' . $class . '"></i>';
	}
}

if(!function_exists('__cf7_form_tag_fa_class')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_fa_class($tag = null){
	    $classes = __cf7_form_tag_fa_classes($tag);
	    if(!$classes){
			return '';
		}
		return implode(' ', $classes);
	}
}

if(!function_exists('__cf7_form_tag_fa_classes')){
	/**
	 * @return array
	 */
	function __cf7_form_tag_fa_classes($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return [];
	    }
		if(!$tag->has_option('fa')){
			return [];
		}
		$classes = [];
		switch(true){
		    case $tag->has_option('fab'):
		        $classes[] = 'fab';
		        break;
		    case $tag->has_option('fad'):
		        $classes[] = 'fad';
		        break;
		    case $tag->has_option('fal'):
		        $classes[] = 'fal';
		        break;
		    case $tag->has_option('far'):
		        $classes[] = 'far';
		        break;
		    case $tag->has_option('fas'):
		        $classes[] = 'fas';
		        break;
		    default:
		        return '';
		}
		$fa = $tag->get_option('fa', 'class', true);
		if(0 !== strpos($fa, 'fa-')){
			$fa = 'fa-' . $fa;
		}
		$classes[] = $fa;
		if($tag->has_option('fw')){
		    $classes[] = 'fa-fw';
		}
	    $rotate = $tag->get_option('rotate', 'int', true);
	    if(in_array($rotate, [90, 180, 270])){
	        $classes[] = 'fa-rotate-' . $rotate;
	    }
	    $flip = $tag->get_option('flip', '', true);
	    if(in_array($flip, ['horizontal', 'vertical', 'both'])){
	        $classes[] = 'fa-flip-' . $flip;
	    }
	    $animate = $tag->get_option('animate', '', true);
	    if(in_array($animate, ['beat', 'fade', 'beat-fade', 'bounce', 'flip', 'shake', 'spin'])){
	        $classes[] = 'fa-' . $animate;
	    }
		return $classes;
	}
}

if(!function_exists('__cf7_form_tag_file_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_file_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('file', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_floating_label')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_floating_label($tag = null){
		$content = __cf7_form_tag_content($tag, true);
		$placeholder = __cf7_form_tag_placeholder($tag);
		if(empty($content) and empty($placeholder)){
			return '';
		}
		if(!in_array($tag->basetype, ['date', 'email', 'file', 'number', 'password', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if($placeholder){
			return $placeholder;
		}
		return wp_strip_all_tags($content);
	}
}

if(!function_exists('__cf7_form_tag_has_data_option')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_data_option($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return (bool) $tag->get_data_option();
	}
}

if(!function_exists('__cf7_form_tag_has_content')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_content($tag = null){
	    $content = __cf7_form_tag_content($tag, true);
		return ('' !== $content); // An empty string.
	}
}

if(!function_exists('__cf7_form_tag_has_free_text')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_free_text($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return $tag->has_option('free_text');
	}
}

if(!function_exists('__cf7_form_tag_has_pipes')){
	/**
	 * @return bool
	 */
	function __cf7_form_tag_has_pipes($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
	    if(!WPCF7_USE_PIPE or !$tag->pipes instanceof \WPCF7_Pipes or $tag->pipes->zero()){
	        return false;
	    }
	    foreach($tag->pipes->to_array() as $pipe){
	        if($pipe[0] !== $pipe[1]){
	            return true;
	        }
	    }
		return false;
	}
}

if(!function_exists('__cf7_form_tag_has_option')){
	/**
	 * Alias for WPCF7_FormTag::has_option.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_has_option($tag = null, $option_name = ''){
	    if(!__cf7_is_form_tag($tag)){
	        return false;
	    }
		return $tag->has_option($option_name);
	}
}

if(!function_exists('__cf7_form_tag_id')){
	/**
	 * Important: Avoid WPCF7_FormTag::get_id_option.
	 *
	 * Differs from WPCF7_FormTag::get_id_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_id($tag = null){
	    if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		return __cf7_form_tag_option($tag, 'id', 'id');
	}
}

if(!function_exists('__cf7_form_tag_is_false')){
	/**
	 * Opposite of WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_is_false($tag = null, $option_name = ''){
	    $option_value = __cf7_form_tag_option($tag, $option_name);
	    return __is_false($option_value);
	}
}

if(!function_exists('__cf7_form_tag_is_true')){
	/**
	 * Alias for WPCF7_ContactForm::is_true.
	 *
	 * @return bool
	 */
	function __cf7_form_tag_is_true($tag = null, $option_name = ''){
	    $option_value = __cf7_form_tag_option($tag, $option_name);
	    return __is_true($option_value);
	}
}

if(!function_exists('__cf7_form_tag_number_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_number_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('number', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_option')){
	/**
	 * Alias for WPCF7_FormTag::get_option(@param $single = true).
	 *
	 * Differs from WPCF7_FormTag::get_option in that it will always return a string.
	 *
	 * @return string
	 */
	function __cf7_form_tag_option($tag = null, $option_name = '', $pattern = ''){
	    if(!__cf7_form_tag_has_option($tag, $option_name)){
	        return '';
	    }
	    return (string) $tag->get_option($option_name, $pattern, true);
	}
}

if(!function_exists('__cf7_form_tag_options')){
	/**
	 * Alias for WPCF7_FormTag::get_option(@param $single = false).
	 *
	 * Differs from WPCF7_FormTag::get_option in that it will always return an array.
	 *
	 * @return array
	 */
	function __cf7_form_tag_options($tag = null, $option_name = '', $pattern = ''){
	    if(!__cf7_form_tag_has_option($tag, $option_name)){
	        return '';
	    }
	    return (array) $tag->get_option($option_name, $pattern, false);
	}
}

if(!function_exists('__cf7_form_tag_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_output($form_tag = '', $callback = '', $priority = 10, $accepted_args = 1){
		$hook_name = 'cf7_form_tag_' . $form_tag . '_output';
		$hook = [
			'accepted_args' => $accepted_args,
			'callback' => $callback,
			'hook_name' => $hook_name,
			'priority' => $priority,
		];
		$md5 = __md5($hook);
		if(__isset_array_cache('cf7_hooks', $md5)){
            return; // Prevent hook being added twice.
        }
		__set_array_cache('cf7_hooks', $md5, $hook);
		__add_filter_once($hook_name, $callback, $priority, $accepted_args);
		__add_filter_once('do_shortcode_tag', '__cf7_maybe_filter_shortcode_tag_output', 10, 4);
	}
}

if(!function_exists('__cf7_form_tag_placeholder')){
	/**
	 * @return string
	 */
	function __cf7_form_tag_placeholder($tag = null){
		if(!__cf7_is_form_tag($tag)){
	        return '';
	    }
		if(!in_array($tag->basetype, ['date', 'email', 'file', 'number', 'password', 'select', 'tel', 'text', 'textarea', 'url'])){
	        return '';
	    }
		if('select' === $tag->basetype){
			if($tag->has_option('include_blank') or empty($tag->values)){
				if(version_compare(WPCF7_VERSION, '5.7', '>=')){
					return translate('&#8212;Please choose an option&#8212;', 'contact-form-7'); // Drop-down menu: Uses more friendly label text. https://contactform7.com/2022/12/10/contact-form-7-57/
				} else {
					return '---';
				}
			} elseif($tag->has_option('first_as_label') and !empty($tag->values)){
				return (string) $tag->values[0];
			} else {
				return '';
			}
		} else {
			if(($tag->has_option('placeholder') or $tag->has_option('watermark')) and !empty($tag->values)){
				return (string) $tag->values[0];
			} else {
				return '';
			}
		}
	}
}

if(!function_exists('__cf7_form_tag_select_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_select_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('select', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_submit_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_submit_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('submit', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_text_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_text_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('text', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_form_tag_textarea_output')){
	/**
	 * @return void
	 */
	function __cf7_form_tag_textarea_output($callback = '', $priority = 10, $accepted_args = 1){
		__cf7_form_tag_output('textarea', $callback, $priority, $accepted_args);
	}
}

if(!function_exists('__cf7_is_form_tag')){
	/**
	 * @return bool
	 */
	function __cf7_is_form_tag($tag = null){
		return ($tag instanceof \WPCF7_FormTag);
	}
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//
// These functions’ access is marked private. This means they are not intended for use by plugin or theme developers, only in other core functions.
//
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_maybe_filter_shortcode_tag_output')){
	/**
	 * This function MUST be called inside the 'do_shortcode_tag' filter hook.
	 *
	 * @return string
	 */
	function __cf7_maybe_filter_shortcode_tag_output($output, $tag, $attr, $m){
		if(!doing_filter('do_shortcode_tag')){ // Too early or too late.
	        return $output;
	    }
		if('contact-form-7' !== $tag){
			return $output;
		}
		$contact_form = __cf7_contact_form();
		if(is_null($contact_form)){
			return __cf7_error();
		}
		if(__is_plugin_active('bb-plugin/fl-builder.php')){
			if(\FLBuilderModel::is_builder_active()){
				$post_type = get_post_type();
				$post_type_object = get_post_type_object($post_type);
				$post_type_name = $post_type_object->labels->singular_name;
				$branding = \FLBuilderModel::get_branding();
				$message = sprintf(_x('%1$s is currently active for this %2$s.', '%1$s branded builder name. %2$s post type name.', 'fl-builder'), $branding, strtolower($post_type_name));
				return __cf7_error($message);
			}
		}
		$html = __str_get_html($output); // Test for simple_html_dom.
		if(is_wp_error($html)){
			$message = $error->get_error_message();
			return __cf7_error($message);
		}
		$errors = new \WP_Error;
	    do_action_ref_array('cf7_shortcode_tag_errors', [&$errors, $contact_form, $attr]);
	    if($errors->has_errors()){
			$messages = $errors->get_error_messages();
			foreach($messages as $index => $message){
				$messages[$index] = rtrim($message, '.') . '.';
			}
			$message = implode(' ', $messages);
			return __cf7_error($message);
	    }
		$comments = $html->find('comment');
		foreach($comments as $comment){
			$comment->remove();
		}
	    $tags = $contact_form->scan_form_tags('feature=name-attr');
	    foreach($tags as $tag){
			$wrapper = $html->find('.wpcf7-form-control-wrap[data-name="' . $tag->name . '"]', 0);
			if(is_null($wrapper)){
				continue;
			}
	        $outertext = $wrapper->outertext;
	        $outertext = apply_filters('cf7_form_tag_' . $tag->basetype . '_output', $outertext, $tag, $contact_form);
			$wrapper->outertext = $outertext;
		}
	    $tags = $contact_form->scan_form_tags('type=submit');
		foreach($tags as $idx => $tag){
			$submit = $html->find('.wpcf7-form-control[type="submit"]', $idx);
			if(is_null($submit)){
				continue;
			}
	        $outertext = $submit->outertext;
			$outertext = apply_filters('cf7_form_tag_submit_output', $outertext, $tag, $contact_form);
			$submit->outertext = $outertext;
		}
	    $output = $html->save();
		$output = apply_filters('cf7_shortcode_tag_output', $output, $contact_form, $attr);
		return $output;
	}
}
