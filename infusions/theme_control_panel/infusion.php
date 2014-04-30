<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
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

// Infusion general information
$inf_title = "Theme Control Panel";
$inf_description = "Theme Control Panel";
$inf_version = "1.0";
$inf_developer = "JoiNNN";
$inf_email = "Spo0kye@yahoo.com";
$inf_weburl = "http://www.php-fusion.co.uk";

$inf_folder = "theme_control_panel";

$inf_adminpanel[1] = array(
	"title" => "Theme Control Panel",
	"image" => "tcp.png",
	"panel" => "theme_settings_admin.php",
	"rights" => "TCP"
);
?>