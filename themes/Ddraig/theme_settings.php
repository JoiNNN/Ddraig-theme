<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: theme_settings.php
| Author: JoiNNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------*/
if (!defined("IN_FUSION")) { die("Access Denied"); }

function getsettings($setting_inf) {
	$settings_arr = array();
	$set_result = dbquery("SELECT settings_name, settings_value FROM ".DB_SETTINGS_INF." WHERE settings_inf='".$setting_inf."'");
	if (dbrows($set_result)) {
		while ($set_data = dbarray($set_result)) {
			$settings_arr[$set_data['settings_name']] = $set_data['settings_value'];
		}
		return $settings_arr;
	} else {
		return false;
	}
}

$setting = getsettings('ddraig_theme_tcpanel');

if (!$setting) {
//If TCP is not infused use these settings
define("TCPINFUSED", FALSE);
	//Lines below can be changed as an alternative to TCP
	$theme_maxwidth 		= 1300;	//Integer (Must be higher than $theme_minwidth)
	$theme_minwidth 		= 986;	//Integer (Must be lower or equal to $theme_maxwidth)
	$theme_maxwidth_forum	= 0;	//Integer (0 - Disabled. Must be higher than $theme_minwidth)
	$theme_maxwidth_admin	= 0;	//Integer (0 - Disabled. Must be higher than $theme_minwidth)
	$home_icon				= 1;	//Boolean (1/0)
	$winter_mode			= 0;	//Boolean (1/0)
	$search_in_header		= 1;	//Boolean (1/0)
	$relative_time			= 1;	//Boolean (1/0)
	$relative_time_elements	= ".dated, .shoutboxdate, .last-post-date"; //Text (News, Shoutbox, Last post)
	$thread_preview			= 1;	//Boolean (1/0)
	$latest_news 			= 1;	//Boolean (1/0)
	$latest_articles 		= 0;	//Boolean (1/0)
	$newest_threads 		= 1;	//Boolean (1/0)
	$hottest_threads 		= 1;	//Boolean (1/0)
	$latest_links 			= 1;	//Boolean (1/0)
	$custom_links			= 0;	//Boolean (1/0)
	$custom_links_list		= "";	//Text (Example: "1.2.3.4.5" - the numbers are the weblinks IDs which have to be separated by a .(dot))

} else {
//If TCP is infused use settings from DB
define("TCPINFUSED", TRUE);
	$theme_maxwidth			= $setting['theme_maxwidth'];
	$theme_minwidth			= $setting['theme_minwidth'];
	$theme_maxwidth_forum 	= $setting['theme_maxwidth_forum'];
	$theme_maxwidth_admin 	= $setting['theme_maxwidth_admin'];
	$home_icon 				= $setting['home_icon'];
	$winter_mode 			= $setting['winter_mode'];
	$search_in_header		= $setting['search_in_header'];
	$relative_time			= $setting['relative_time'];
	$relative_time_elements	= $setting['relative_time_elements'];
	$thread_preview 		= $setting['thread_preview'];
	$latest_news 			= $setting['latest_news'];
	$latest_articles 		= $setting['latest_articles'];
	$newest_threads 		= $setting['newest_threads'];
	$hottest_threads 		= $setting['hottest_threads'];
	$latest_links 			= $setting['latest_links'];
	$custom_links			= $setting['custom_links'];
	$custom_links_list		= $setting['custom_links_list'];
}

//Check if different width is set for Forum
if ($theme_maxwidth_forum >= $theme_minwidth) {
	if (strpos(TRUE_PHP_SELF, '/forum/') !== FALSE) {
		$theme_maxwidth = $theme_maxwidth_forum;
	}
}
//Check if different width is set for Administration
if ($theme_maxwidth_admin >= $theme_minwidth) {
	if (strpos(TRUE_PHP_SELF, '/administration/') !== FALSE) {
		$theme_maxwidth = $theme_maxwidth_admin;
	}
}

define("THEME_MAXWIDTH", $theme_maxwidth."px");
define("THEME_MINWIDTH", $theme_minwidth."px");
define("THEME_WIDTH",	$theme_maxwidth."px"); //For compatibility
define("HOME_AS_ICON",	$home_icon);
define("WINTER", 		$winter_mode);
define("SEARCH_HEAD",	$search_in_header);
define("REL_TIME",		$relative_time);
define("REL_TIME_EL",	$relative_time_elements);
define("THREAD_PREV", 	$thread_preview);
define("L_NEWS", 		$latest_news);
define("L_ARTICLES", 	$latest_articles);
define("N_THREADS", 	$newest_threads);
define("H_THREADS", 	$hottest_threads);
define("L_LINKS", 		$latest_links);
define("C_LINKS", 		$custom_links);
define("C_LINKS_LIST", 	$custom_links_list);
?>