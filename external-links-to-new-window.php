<?php
/*
Plugin Name: External Links to New Window
Plugin URI: http://thisismyurl.com/plugins/external-links-to-new-window/
Description: The external link plugin for WordPress will allow site admins to automatically open external site links to a new window with options for an icon and nofollow rules. 
Author: Christopher Ross
Author URI: http://thisismyurl.com/
Version: 2.0.0
*/

/**
 *  External Links to New Window core file
 *
 * This file contains all the logic required for the plugin
 *
 * @link		http://wordpress.org/extend/plugins/external-links-to-new-window/
 *
 * @package 		External Links to New Window
 * @copyright		Copyright (c) 2008, Chrsitopher Ross
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, v2 (or newer)
 *
 * @since 		External Links to New Window 1.0
 */

function thisismyurl_externallinks_plugin_parse($content) {
	$setting = json_decode(get_option('thisismyurl_externallinks'));
	include_once(dirname(__FILE__).'/lib/simple_html_dom.php');
	$html = str_get_html($content);
	$anchors = $html->find('a');
	foreach($anchors as $a) {
		$href = strtolower($a->href);
		
		if ($setting[0]) {$window = "_blank";}
	    if ($setting[1]) {$nofollow = "nofollow";}
	    if ($setting[2]) {$class = "thisismyurl_external";}
				   
		if(stripos($href, get_bloginfo('url')) === false && substr($href, 0, 4) == 'http') {
			$a->target = $window;
			$a->rel = $nofollow;
			$a->class = $class;
		}
	}
	return $html;
}
add_filter('the_content', 'thisismyurl_externallinks_plugin_parse');

function thisismyurl_externallinks_plugin_css() {
	$setting = json_decode(get_option('thisismyurl_externallinks'));
	if ($setting[2]) {
		$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
		echo '<style type="text/css">
				.thisismyurl_external{
							 background: url("'.$x.'external.png") no-repeat right;
							 padding-right: 14px;
			};
			</style>';
	}
}
add_action('wp_head','thisismyurl_externallinks_plugin_css');

function thisismyurl_externallinks_admin_css() {
	$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	echo '<style type="text/css">
			.thisismyurl{ background: url("'.$x.'icon.png") no-repeat;};
		</style>';
}
add_action('admin_head','thisismyurl_externallinks_admin_css');



function thisismyurl_externallinks_admin_menu() {
	$thisismyurl_externallinks_settings = add_options_page( 'External Links', 'External Links', 'edit_posts', 'thisismyurl_externallinks', 'thisismyurl_externallinks_help_page');
	add_action('load-'.$thisismyurl_externallinks_settings, 'thisismyurl_externallinks_help_page_scripts');
}
add_action('admin_menu', 'thisismyurl_externallinks_admin_menu');

function thisismyurl_externallinks_help_page_scripts() {
	wp_enqueue_style('dashboard');
	wp_enqueue_script('postbox');
	wp_enqueue_script('dashboard');
}

function thisismyurl_externallinks_help_page() {
	
	if ($_POST) {
		$setting = array($_POST['setting1'],$_POST['setting2'],$_POST['setting3']);
		update_option('thisismyurl_externallinks', json_encode($setting));	
	}
	
	if (empty($setting)) {
		$setting = json_decode(get_option('thisismyurl_externallinks'));
	}
	
	$settingcount = 0;
	foreach ($setting as $settingitem) {
		if ($setting[$settingcount]) {$cb[$settingcount] = 'checked="checked"';}	
		$settingcount++;
	}

	
	echo '<div class="wrap">
			<div class="thisismyurl icon32"><br /></div>
			<h2>'.__('Settings for External Links to New Window','thisismyurl_externallinks').'</h2>
			<div class="postbox-container" style="width:70%">
				<form method="post" action="options-general.php?page=thisismyurl_externallinks">
	 
				<div class="metabox-holder">
				<div class="meta-box-sortables">
					
					<div id="edit-pages" class="postbox">
					<div class="handlediv" title="'.__('Click to toggle','thisismyurl_externallinks').'"><br /></div>
					<h3 class="hndle"><span>'.__('Plugin Settings','thisismyurl_externallinks').'</span></h3>
					<div class="inside">
						
						<p><input type="checkbox" name="setting1" id="setting1" '.$cb[0].'>&nbsp;<label for="setting1">'.__('Open external content in new window','thisismyurl_externallinks').'</label></p>
						<p><input type="checkbox" name="setting2" id="setting2" '.$cb[1].'>&nbsp;<label for="setting2">'.__('Add nofollow attribute','thisismyurl_externallinks').'</label></p>
						<p><input type="checkbox" name="setting3" id="setting3" '.$cb[2].'>&nbsp;<label for="setting3">'.__('Include Icon after link','thisismyurl_externallinks').'</label></p>
						
					</div><!-- .inside -->
					</div><!-- #edit-pages -->
					<input type="hidden" name="action" value="update" /> 
					<input type="hidden" name="page_options" value="setting1,setting2,setting3" />
					<input type="submit" name="Submit" class="button-primary" value="'.__('Save Settings','thisismyurl_externallinks').'" />
					</form>
				</div><!-- .meta-box-sortables -->
				</div><!-- .metabox-holder -->
				
			</div><!-- .postbox-container -->
			
			<div class="postbox-container" style="width:20%">
			
				<div class="metabox-holder">
				<div class="meta-box-sortables">
				
					<div id="edit-pages" class="postbox">
					<div class="handlediv" title="'.__('Click to toggle','thisismyurl_externallinks').'"><br /></div>
					<h3 class="hndle"><span>'.__('Plugin Information','thisismyurl_externallinks').'</span></h3>
					<div class="inside">
						<p>'.__('External Links to New Window by Christopher Ross is a free WordPress plugin. If you\'ve enjoyed the plugin please give the plugin 5 stars on WordPress.org.','thisismyurl_externallinks').'</p>
						<p>'.__('Want to help? Please consider translating this pluginto your local language, or offering a hand in the support forums.','thisismyurl_externallinks').'</p>
						<p><a href="http://wordpress.org/extend/plugins/external-links-to-new-window/">WordPress.org</a> | <a href="http://thisismyurl.com">'.__('Plugin Author','thisismyurl_externallinks').'</a></p>
					</div><!-- .inside -->
					</div><!-- #edit-pages -->
				
				</div><!-- .meta-box-sortables -->
				</div><!-- .metabox-holder -->
				
			</div><!-- .postbox-container -->	
	</div><!-- .wrap -->
	
	';
}



?>
