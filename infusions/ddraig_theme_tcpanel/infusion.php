<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: infusion.php
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

if (file_exists(INFUSIONS."ddraig_theme_tcpanel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."ddraig_theme_tcpanel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."ddraig_theme_tcpanel/locale/English.php";
}

// Infusion general information
$inf_title = "Ddraing Theme Control Panel";
$inf_description = "Ddraig Theme Control Panel";
$inf_version = "1.1";
$inf_developer = "JoiNNN";
$inf_email = "Spo0kye@yahoo.com";
$inf_weburl = "http://www.php-fusion.co.uk";

$inf_folder = "ddraig_theme_tcpanel";

$inf_insertdbrow[1] = DB_SETTINGS_INF."
	(settings_name, settings_value, settings_inf)
VALUES
	('theme_maxwidth',			'1300', '".$inf_folder."'),
	('theme_minwidth',			'980',	'".$inf_folder."'),
	('theme_maxwidth_forum',	'0', 	'".$inf_folder."'),
	('theme_maxwidth_admin',	'0', 	'".$inf_folder."'),
	('home_icon',				'1', 	'".$inf_folder."'),
	('winter_mode',				'0', 	'".$inf_folder."'),
	('search_in_header',		'1', 	'".$inf_folder."'),
	('relative_time',			'1', 	'".$inf_folder."'),
	('relative_time_elements',	'.dated, .shoutboxdate, .last-post-date', 	'".$inf_folder."'),
	('thread_preview',			'1', 	'".$inf_folder."'),
	('latest_news',				'1', 	'".$inf_folder."'),
	('latest_articles',			'0', 	'".$inf_folder."'),
	('newest_threads',			'1', 	'".$inf_folder."'),
	('hottest_threads',			'1', 	'".$inf_folder."'),
	('latest_links',			'1', 	'".$inf_folder."'),
	('custom_links',			'0', 	'".$inf_folder."'),
	('custom_links_list',		'', 	'".$inf_folder."')";

$inf_deldbrow[1] = DB_SETTINGS_INF." WHERE settings_inf='".$inf_folder."'";

$inf_adminpanel[1] = array(
	"title" => "Ddraig Theme Control Panel",
	"image" => "ddraigtcp.png",
	"panel" => "ddraig_tcpanel_admin.php",
	"rights" => "DDCP"
);
?>