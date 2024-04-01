<?php
/*
 * Plugin Name: Magic Functions
 * Plugin URI: https://magicfunctions.com/
 * Description: A magical collection of functions for WordPress, plugins and themes.
 * Version: 0.3.31
 * Requires at least: 5.9
 * Requires PHP: 5.6
 * Author: Vidsoe
 * Author URI: https://vidsoe.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: magic-functions
 * Network: true
 * Update URI: https://vidsoe.com/magic-functions/
 */

/*
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * Copyright 2024 Vidsoe.
 */

// Make sure we don't expose any info if called directly.
if(!defined('ABSPATH')){
    echo 'Hi there! I\'m just a plugin, not much I can do when called directly.';
    exit;
}

// Load PHP classes and functions.
foreach(glob(plugin_dir_path(__FILE__) . 'autoload/php/*.php') as $magic_file){
    require_once($magic_file);
}
unset($magic_file);

// Check for updates.
__build_update_checker('https://github.com/vidsoe/magic-functions', __FILE__, 'magic-functions');

// Load JavaScript classes and functions.
__enqueue_functions();

// Load theme functions.
__require_theme_functions();

// Fires when magic is fully loaded.
do_action('magic_loaded');
