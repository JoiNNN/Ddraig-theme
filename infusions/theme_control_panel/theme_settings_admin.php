<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2014 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: theme_settings_admin.php
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
require_once "../../maincore.php";
// Check rights
if (!checkrights("TCP") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

// Locale
if (file_exists(INFUSIONS."theme_control_panel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."theme_control_panel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."theme_control_panel/locale/English.php";
}

require_once THEMES."templates/admin_header.php";

if (file_exists(THEMES.$settings['theme']."/settings.php")) {
	// Include the Theme Control Panel
	require_once THEMES.$settings['theme']."/settings.php";
} else {
	// Theme has no Control Panel
	opentable($locale['tcp_title']);
	echo "<div class='admin-message'>".sprintf($locale['tcp_message'], $settings['theme'])."</div>";
	closetable();
}

require_once THEMES."templates/footer.php";
?>