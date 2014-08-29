<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http:// www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: forum_viewforum.tpl.php
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

$search_viewforum = array();
$replace_viewforum = array();
/*/Locked thread tag (viewforum.php)
$search_viewforum[] .= "@src='(.*?)/forum/folderlock.png'(.*?)<td width='100%' class='(.*?)'>(.*?)<a@si";
$replace_viewforum[] .= "src='$1/forum/folderlock.png'$2<td width='100%' class='$3 thread-locked'>$4<span class='tag red'>".$locale['locked']."</span> <a";
// Thread pages numbers (viewforum.php)	
$search_viewforum[] .= "@\(".$locale['455']."(.*?)\)@i";
$replace_viewforum[] .= "<span class='pages small'>".$locale['455']."$1</span>";
// Thread title class and thread preview link (viewforum.php)
$search_viewforum[] .= "@src='(.*?)/forum/(folder|foldernew|folderlock)(.*?)<td width='100%' class='(.*?)'>(.*?)<a (.*?)thread_id=(.*?)>(.*?)</a>@si";
$replace_viewforum[] .= "src='$1/forum/$2$3<td width='100%' class='$4'>$5<a class='thread-title' $6thread_id=$7>$8</a>".((THREAD_PREV == 1) ? "<a title='".$locale['prev_thread']."' class='preview-link expand flright' $6thread_id=$7></a>" : "");
*///Thread Replies/Views
$search_viewforum[] .= "@<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap'>".$locale['452']."</td>\n<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap' align='center' >".$locale['global_045']."</td>\n<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap' align='center'>".$locale['global_046']."</td>\n<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap'>".$locale['404']."</td>@si";
$replace_viewforum[] .= "<td class='forum-caption stats-caption tbl2 ' align='center'>".$locale['global_046']." / ".$locale['global_045']."</td>\n<td class='forum-caption last-post-caption tbl2'>".$locale['404']."</td>";
$search_viewforum[] .= "@<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap'>&nbsp;</td>@i";
$replace_viewforum[] .= "<td class='folder-caption forum-caption tbl2' width='1%' style='white-space:nowrap'>&nbsp;</td>";

$output = preg_replace($search_viewforum, $replace_viewforum, $output);

// Replace threads rows (looong spaghetty, don't touch unless you what ur doing) (viewforum.php)
$search_threadrow = "@<tr>\n<td align='center' width='(.*?)' class='tbl2'( style='white-space:nowrap')?><img src='(.*?)/forum/(.*?)' alt='(.*?)' /></td><td width='100%' class='tbl1'>(<input type='checkbox' name='check_mark\[\]' value='(.*?)' />\n)?(<img src='(.*?)/forum/stickythread\.png' alt='(.*?)' style='vertical-align:middle;' />\n)?<a href='viewthread\.php\?thread_id=(.*?)'>(.*?)</a>(<br />\(".$locale['455']."(.*?)\))?</td>\n<td width='1%' class='tbl2' style='white-space:nowrap'>(<a href='\.\./profile.php\?lookup=(.*?)' class='profile-link'>(.*?)</a>|(.*?))</td>\n<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>(.*?)</td>\n<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>(.*?)</td>\n<td width='1%' class='tbl1' style='white-space:nowrap'>(.*?)<br />\n<span class='small'>(.*?) (<a href='\.\./profile\.php\?lookup=(.*?)' class='profile-link'>(.*?)</a>|(.*?))</span></td>\n</tr>@i";
function replace_threadrow($m) {
	global $locale;
	//var_dump($m);

	if (!function_exists('select_post')) {
		function select_post($id, $position) {	
			$result = dbquery(
				"SELECT * FROM ".DB_POSTS."
				WHERE thread_id='".$id."'
				ORDER BY post_datestamp ".($position ? $position : 'ASC LIMIT 1')
			);
			return $result;
		}
	}
	$postdatal = dbarray(select_post($m[11], 'DESC LIMIT 1'));

	$hot = FALSE;
	// Check if the tread is HOT must have more than 10 replys
	if ($m[20] >= 10) {
		$postdatabl = dbarray(select_post($m[11], 'DESC LIMIT 1,2'));
		// Check if the last post was not made too long ago
		if (date() - $postdatal['post_datestamp'] < 5*(24*3600)) {
			// Check if the date difference between last post and the one before that is not too big
			if ($postdatal['post_datestamp'] - $postdatabl['post_datestamp'] < 5*(24*3600)) {
				$hot = TRUE;
			}
		}
	}

	$id = (!empty($m[24]) ? $m[24] : null);
	$folder = str_replace('.png', '', $m[4]);

	$html = "<tr class='thread-row ".$folder.($hot ? " thread-hot" : "")."' id='thread_id_".$m[11]."'>\n";
	$html .= "<td align='center' class='thread-folder-icon tbl2' style='white-space:nowrap'><img src='".$m[3]."/forum/".$m[4]."' alt='".$m[5]."' /></td><td class='thread-name tbl1'>".(isset($m[6]) ? $m[6] : "");
	$html .= !empty($m[8]) ? "<img src='".$m[9]."/forum/stickythread.png' alt='".$m[10]."' title='".$m[10]."' class='sticky-icon' />" : "";
	$html .= ($folder == 'folderlock' ? "<span class='tag red'>".$locale['locked']."</span> " : "");
	$html .= " <a class='thread-title' href='viewthread.php?thread_id=".$m[11]."'>".$m[12]."</a>";
	$html .= $hot ? "<span class='tag orange flright'>".$locale['hot']."</span>" : "";
	$html .= "<br /><span class='thread-starter small'>".sprintf($locale['started_by']." ", (!empty($m[16]) ? "<a href='".BASEDIR."profile.php?lookup=".$m[16]."' class='profile-link'>".$m[17]."</a>" : $m[15]), "","")."</span>";
	$html .= ((THREAD_PREV == 1) ? "<a title='".$locale['prev_thread']."' class='preview-link expand flright' href='viewthread.php?thread_id=".$m[11]."'></a>" : "");
	$html .= " <span class='pages small'>".$m[14]."</span>";
	$html .= "<span class='thread-stats-responsive faint small'><br />".$locale['global_046'].": <span class='darker'>".$m[20]."</span> / ".$locale['global_045'].": <span class='darker'>".$m[19]."</span></span></td>\n";
	$html .= "<td class='thread-stats tbl1'><dl class='major'><dt>".$locale['global_046'].": </dt><dd>".$m[20]."</dd></dl><dl class='minor small'><dt>".$locale['global_045'].": </dt><dd>".$m[19]."</dd></dl></td>\n";
	$html .= "<td class='thread-last-post tbl1' style='white-space:nowrap'><div class='last-post-avatar flleft'>".build_avatar($id)."</div>\n";
	$html .= "<div class='last-post-info'><span class='last-post-author'>".($id ? (!empty($m[25]) ? "<a href='".BASEDIR."profile.php?lookup=".$id."' class='profile-link'>".$m[25]."</a>" : "<span class='deleted-user'>".$locale['deleted_user']."</span>") : $m[26])."</span><br />";
	$html .= "<span class='last-post-date small'><a title='".$locale['go_to_last_post']."' href='viewthread.php?thread_id=".$m[11]."&amp;pid=".$postdatal['post_id']."#post_".$postdatal['post_id']."'>".$m[21]."</a></span></div></td>\n";
	$html .= "</tr>";
	
	return $html;
}
$output = preg_replace_callback($search_threadrow, 'replace_threadrow', $output, 20); // occurs 20 times max

?>