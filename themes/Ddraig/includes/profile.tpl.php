<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http:// www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: profile.tpl.php
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

// Remove table hardcoded width, and add CSS class
$search_profile = array("@<table cellpadding='0' cellspacing='1' width='400' class='profile(_category)? tbl-border center'>@i");
$replace_profile = array("<table cellpadding='0' cellspacing='1' class='profile$1 tbl-border center'>");
$search_profile[] .= "@<tr>\n<td rowspan='(.*?)' valign='top' class='tbl profile_user_avatar' width='1%'><!--profile_user_avatar--><img src='(.*?)' class='avatar' alt='(.*?)' title='(.*?)' /></td>@i";
$replace_profile[] .= "<tr><td colspan='3' align='center' class='tbl profile_user_avatar responsive'><!--profile_user_avatar_responsive--><span class='user-avatar'><img src='$2' class='avatar' alt='$3' title='$4' /></span></td></tr><tr><td rowspan='$1' valign='top' class='tbl profile_user_avatar' width='1%'><!--profile_user_avatar--><span class='user-avatar'><img src='$2' class='avatar' alt='$3' title='$4' /></span></td>";

// Online/Offline user status
$show_online_status = TRUE;
if ($show_online_status) {
	$search_profile[] .= "@<td align='right' class='profile_user_visit tbl1'>(.*?)</td>@i";
	$replace_profile[] .= "<td align='right' class='profile_user_visit tbl1'>$1 <span class='tag ".(is_online($_GET['lookup']) ? "green'>".$locale['online'] : "gray'>".$locale['offline'])."</span></td>";
}

$output = preg_replace($search_profile, $replace_profile, $output);
?>