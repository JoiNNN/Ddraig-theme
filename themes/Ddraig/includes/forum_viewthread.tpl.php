<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http:// www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: forum_viewthread.tpl.php
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

// Reply button (viewthread.php)
$search_viewthread = array("@><img src='reply' alt='(.*?)' style='border:0px' />@i");
$replace_viewthread = array(" class='button big'><span class='icon-reply'>$1</span>");
// Website button (viewthread.php)
$search_viewthread[] .= "@><img src='web' alt='(.*?)' style='border:0;vertical-align:middle' />@i";
$replace_viewthread[] .= " class='user-web button' rel='nofollow' title='$1'><span class='icon-globe'>Web</span>";
// PM button (viewthread.php)
$search_viewthread[] .= "@><img src='pm' alt='(.*?)' style='border:0;vertical-align:middle' />@i";
$replace_viewthread[] .= " class='user-pm button' title='$1'><span class='icon-email'>PM</span>";
// Quote button (viewthread.php)
$search_viewthread[] .= "@><img src='quote' alt='(.*?)' style='border:0px;vertical-align:middle' />@i";
$replace_viewthread[] .= " class='button' title='$1'><span class='icon-quote-left'>$1</span>";
// Edit button (viewthread.php)
$search_viewthread[] .= "@><img src='forum_edit' alt='(.*?)' style='border:0px;vertical-align:middle' />@i";
$replace_viewthread[] .= " class='negative button' title='$1'><span class='icon-pencil'>$1</span>";
// Move posts button (viewthread.php)
$search_viewthread[] .= "@<input (.*?) name='move_posts' value='(.*?)' (.*?) />@i";
$replace_viewthread[] .= "<button $1 name='move_posts' $3><span class='icon-move'>$2</span></button>&nbsp;";
// User avatar in forum (viewthread.php)
//$search_viewthread[] .= "@forum_thread_user_info' style='width:140px'>\n<img src='(.*?)' alt='(.*?)' />@i";
//$replace_viewthread[] .= "forum_thread_user_info'><div class='user-avatar'><img class='avatar' src='$1' alt='$2' /></div>";
// User IP in forum (viewthread.php)
$search_viewthread[] .= "@forum_thread_ip' style='width:140px;white-space:nowrap'>@si";
$replace_viewthread[] .= "forum_thread_ip'>";
// Space between forum posts (viewthread.php)
$search_viewthread[] .= "@<td colspan='2' class='tbl1 forum_thread_post_space' style='height:10px'></td>@i";
$replace_viewthread[] .= "<td colspan='2' class='tbl1 forum_thread_post_space'></td>";
// Edit note in threads (viewthread.php)
$search_viewthread[] .= "@<hr />\n<span class='small'>(.*?)</span>@i";
$replace_viewthread[] .= "<br /><div class='post-edited small'>$1</div>";
// Edit reason (viewthread.php)
$search_viewthread[] .= "@</div>\n<br /><div class='edit_reason'>(.*?)</div>@si";
$replace_viewthread[] .= "<div class='edit_reason'>$1</div></div>";
// Forum posts wrapper (viewthread.php)
$search_viewthread[] .= "@<tr>\n<td class='tbl2 forum_thread_user_name'@i";
$replace_viewthread[] .= "<tr class='forum-post'><td><table width='100%' cellspacing='0' cellpadding='0' class='forum-post-table'><tr><td class='tbl2 forum_thread_user_name'";
$search_viewthread[] .= "@<!--forum_thread_userbar-->(.*?)\n</div>\n</td>\n</tr>\n@si";
$replace_viewthread[] .= "<!--forum_thread_userbar-->$1</div></td></tr></table></td></tr>";

$output = preg_replace($search_viewthread, $replace_viewthread, $output);

// Add online indicator next to the avatar
$search_poster = "@<td class='tbl2 forum_thread_user_name'(.*?)><!--forum_thread_user_name--><a href='\.\./profile.php\?lookup=([0-9]+)'(.*?)?<img src='\.\./images/avatars/(.*?)' alt='".$locale['567']."' />@si";
function replace_poster($m) {
	global $locale;

	return "<td class='tbl2 forum_thread_user_name icon-user'".$m['1']."><!--forum_thread_user_name--><a href='".BASEDIR."profile.php?lookup=".$m['2']."'".$m['3'].(is_online($m['2']) ? "<span class='online-status tag green' title='".$locale['online']."'> </span>" : "")."<div class='user-avatar'><img class='avatar' src='../images/avatars/".$m['4']."' alt='".$locale['567']."' /></div>";
}
$output = preg_replace_callback($search_poster, 'replace_poster', $output, 20); // occurs 20 max

// Who posted. Replaces the thread subject, 'Track this thread' and 'Print this thread' (viewthread.php)
$search_subject = "@<td colspan='2' class='tbl2 forum-caption'>(.*?)</td>@si";
function replace_subject($m) {
	global $locale;

	$r = "<td class='who-posted forum-caption tbl2'>".$locale['who_posted'].": <ul class='posters'>";
	$result = dbquery(
			"SELECT a.post_author,
					b.user_id,
					b.user_name,
					b.user_status,
					COUNT(a.post_id) as post_count
			FROM ".DB_POSTS." a
			LEFT JOIN ".DB_USERS." b on (a.post_author = b.user_id)
			WHERE thread_id='".$_GET['thread_id']."'
			GROUP BY a.post_author
			ORDER BY post_count DESC"
		);
		if (dbrows($result) > 0) {
			while ($poster = dbarray($result)) {
				if ($poster['user_id']) {
					$r .= "<li id='".strtolower($poster['user_name'])."_".$poster['user_id']."' class='poster'>".profile_link($poster['user_id'], $poster['user_name'], $poster['user_status'])." <span title='".$locale['403']."' class='tag blue'>".number_format($poster['post_count'])."</span></li>\n";
				}
			}
		}
	$r .= "</ul></td>";

	return $r;
}
$output = preg_replace_callback($search_subject, 'replace_subject', $output, 1); // occurs only once
?>