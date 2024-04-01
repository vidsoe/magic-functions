=== Magic Functions ===
Contributors: vidsoe
Donate link: https://vidsoe.org/
Tags: magic, functions
Tested up to: 6.4.3
Requires PHP: 5.6
Stable tag: 0.3.31
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A magical collection of functions for WordPress, plugins and themes.

== Description ==

= For plugins =

Add the following code to the main plugin file:

`add_action('plugins_loaded', function(){`
`  if(did_action('magic_loaded')){`
`    'do your magic here'`
`  }`
`});`

= For themes =

Create a new file named `magic-functions.php` and do your magic there or add the following code to the functions.php file:

`if(did_action('magic_loaded')){`
`  'do your magic here'`
`}`

== Changelog ==

To see what’s changed, visit the [GitHub repository](https://github.com/vidsoe/magic-functions).
