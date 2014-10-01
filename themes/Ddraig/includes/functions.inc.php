<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http:// www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: functions.inc.php
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

// Set cookie for hiding TCP warning when not installed
if (isset($_GET['hidetcpwarning'])) {
	setcookie(COOKIE_PREFIX.'tcpnotice', 'no', strtotime("+1 day"), $settings['site_path']);
	exit;
}

set_image("pollbar", THEME."images/btn.png");
set_image("edit", THEME."images/edit.png");
set_image("printer", THEME."images/printer.png' class='print-item");
set_image("link", THEME."images/link.png");
// Arrows
set_image("up", THEME."images/up.png' class='up-arrow");
set_image("down", THEME."images/down.png' class='down-arrow");
set_image("left", THEME."images/left.png' class='left-arrow");
set_image("right", THEME."images/right.png' class='right-arrow");
// Forum folders icons
set_image("folder", THEME."forum/folder.png");
set_image("foldernew", THEME."forum/foldernew.png");
set_image("folderlock", THEME."forum/folderlock.png");
set_image("stickythread", THEME."forum/stickythread.png");
// Forum buttons
set_image("reply", "reply");
set_image("newthread", "newthread");
set_image("web", "web");
set_image("pm", "pm");
set_image("quote", "quote");
set_image("forum_edit", "forum_edit");

function theme_output($output) {
	global $locale, $settings;

	// Build a user avatar
	function build_avatar($userid) {
		global $locale;

		$src = IMAGES."avatars/noavatar50.png";
		if (isnum($userid)) {
			$result = dbquery("SELECT user_avatar FROM ".DB_USERS." WHERE user_id='".$userid."'");
			while ($data = dbarray($result)) {
				if ($data['user_avatar'] && file_exists(IMAGES."avatars/".$data['user_avatar'])) {
					$src = IMAGES."avatars/".$data['user_avatar'];
				}
			}
		}

		return "<span class='user-avatar'><img class='avatar small' src='".$src."' alt='".$locale['567']."' /></span>";
	}

	// Check if a user is online
	function is_online($userid) {
		if (isnum($userid)) {
			return dbcount("(online_user)", DB_ONLINE, "online_user='".$userid."'");
		}

		return NULL;
	}

	$search_site = array(
		"@<body>@i",																				// Add relevant class to body based on area and page we are on
		"@<a href='".ADMIN."comments.php(.*?)&amp;ctype=(.*?)&amp;cid=(.*?)'>(.*?)</a>@si", 		// Manage comments button
		"@<div class='quote'><a (.*?)><strong>(.*?)</strong></a>(<br />)?@si",						// Quote
		"@<span class='small' style='font-weight:bold'>\[".$locale['global_051']."\]</span>@i",		// Poll thread text (forum_threads_list panel)
		"@<img src='(.*)' class='print-item' alt='(.*?)' title='(.*?)' style='border:0;vertical-align:middle' />@i"
	);
	$replace_site = array(
		"<body class='".((INFORUM ? "inforum " : ((defined('ADMINPANEL') && ADMINPANEL) ? "adminpanel " : "")).str_replace(array('_', '.php'), array('-', ''), FUSION_SELF))."-page ".(iMEMBER ? "user-member" : "user-guest")."'>",
		"<a href='".ADMIN."comments.php$1&amp;ctype=$2&amp;cid=$3' class='big button flright'><span class='icon-cog'>$4</span></a>",
		"<div class='quote extended'><p class='citation'><img src='".THEME."images/quote_icon.png' alt='>' /><a $1><strong>$2</strong></a></p>",
		"<span class='tag blue small'>".$locale['global_051']."</span>",
		"<span class='print-item icon-print' title='$2'></span>",
	);

	if (FUSION_SELF == "profile.php") {
		include_once THEME."includes/profile.tpl.php"; // not required, include just once
	}

	// Replacements that only occur in forums should be searched for only when viewing the forums
	if (INFORUM && in_array(FUSION_SELF, array("index.php", "viewforum.php", "viewthread.php", "post.php"))) {
		// Remove special characters and html entities function
		function clean_name($text) {
			$text = strtolower($text);
			$text = preg_replace(array("/&(?:[a-z\d]+|#\d+|#x[a-f\d]+);/", "/[^a-z\d\s]/", "/\s+/"), array("", "", "-"), $text);
			return $text;
		}
	
		$search_forum = array(
		"@><img src='newthread' alt='(.*?)' style='border:0px;?' />@si",							// New thread button (viewforum.php|viewthread.php)
		"@<input (.*?) name='(delete_posts|delete_threads)' value='(.*?)' class='(.*?)' (.*?) />@i",// Delete posts button (viewforum.php|viewthread.php)
		);
		$replace_forum = array(		
		" class='button big'><span class='icon-plus-circle'>$1</span>",
		"<button $1 class='$4 negative' name='$2' $5><span class='icon-trash'>$3</span></button>",	
		);
		$output = preg_replace($search_forum, $replace_forum, $output);

		if (FUSION_SELF == "index.php") {
			include_once THEME."includes/forum_index.tpl.php"; // not required, include just once
		}

		if (FUSION_SELF == "viewthread.php") {
			include_once THEME."includes/forum_viewthread.tpl.php"; // not required, include just once
		}

		if (FUSION_SELF == "viewforum.php") {
			include_once THEME."includes/forum_viewforum.tpl.php"; // not required, include just once
		}

		// Add forum category link to breadcrumb and reformat the breadcrumbs also includes forum and thread details and stats (viewforum.php|viewthread.php|post.php)
		$search_breadcrumb = "@<div class='tbl2 forum_breadcrumbs'(.*?)>(<span class='small'>)?(.*?) &raquo; (.*?) &raquo; (.*?)( &raquo; (.*?))?(</span>)?</div>@i";
		function replace_breadcrumb($m) {
			global $settings, $userdata, $locale;

			$a = "<span class='crust'><span class='crumb'>";
			$b = "</span><span class='arrow'><span>&raquo;</span></span></span>";
			$r = "<div class='tbl2 forum_breadcrumbs'".$m[1]."><span class='crust first'><span class='crumb'>".$m[3].$b." ".$a."<a href='index.php#fcat-".clean_name($m[4])."'>".$m[4]."</a>".$b." ".$a.$m[5].((!isset($m[7]) || empty($m[7])) ? "</span></span>" : $b." ".$a.$m[7]."</span></span>")."</div>";
			// Forum details and stats
			if (FUSION_SELF == "viewforum.php") {
				$result = dbquery(
					"SELECT forum_name,
							forum_description,
							forum_postcount,
							forum_threadcount
					FROM ".DB_FORUMS."
					WHERE forum_id='".$_GET['forum_id']."'
					LIMIT 1"
				);
				while ($fdata = dbarray($result)) {
					$r .= "<div class='forum-titlebar tbl-border clearfix'>";
					$r .= "<h1>".$fdata['forum_name']."</h1>";
					$r .= "<p class='forum-description faint flleft'>".nl2br(parseubb($fdata['forum_description']))."</p>";
					$r .= "<div class='forum-counts faint flright'><strong class='icon-chat'>".$fdata['forum_threadcount']."</strong> ".$locale['402']." <strong class='icon-reply-all'>".$fdata['forum_postcount']."</strong> ".$locale['403']."</div>";
					$r .= "</div>";
				}
			// Thread details and stats
			} elseif (FUSION_SELF == "viewthread.php") {
				$result = dbquery(
					"SELECT post.forum_id,
							post.post_datestamp,
							post.post_author,
							thread.thread_subject,
							thread.thread_postcount,
							thread.thread_views,
							thread.thread_locked,
							user.user_avatar AS avatar_author,
							user.user_name AS user_author,
							user.user_status AS status_author
					FROM ".DB_POSTS." post
					LEFT JOIN ".DB_THREADS." thread ON post.thread_id = thread.thread_id
					LEFT JOIN ".DB_USERS." user ON post.post_author = user.user_id
					WHERE post.thread_id='".$_GET['thread_id']."'
					ORDER BY post_datestamp ASC
					LIMIT 1"
				);

				while ($pdata = dbarray($result)) {
					$r .= "<div class='thread-titlebar ".($pdata['thread_locked'] ? "thread-locked" : "")." tbl-border clearfix'>";
					$avatar = "<img class='avatar' src='".IMAGES."avatars/noavatar100.png' alt='".$locale['567']."' />";
					if ($pdata['avatar_author'] && file_exists(IMAGES."avatars/".$pdata['avatar_author']) && $pdata['status_author']!=6 && $pdata['status_author']!=5) {
						$avatar = "<img class='avatar' src='".IMAGES."avatars/".$pdata['avatar_author']."' alt='".$locale['567']."' />";
					}
					$r .= "<div class='user-avatar flleft'>".$avatar."</div>\n";
					$r .= "<div class='thread-info clearfix'>";
					$r .= "<h1>".($pdata['thread_locked'] ? "<span class='tag red'>".$locale['locked']."</span> " : "").$pdata['thread_subject']."";
					$r .= "<span class='thread-options flright'>";
					// Tack this thread
					if (iMEMBER && $settings['thread_notify']) {
						if (dbcount("(thread_id)", DB_THREAD_NOTIFY, "thread_id='".$_GET['thread_id']."' AND notify_user='".$userdata['user_id']."'")) {
							$r .= "<a href='postify.php?post=off&amp;forum_id=".$pdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>".$locale['515']."</a>";
						} else {
							$r .= "<a href='postify.php?post=on&amp;forum_id=".$pdata['forum_id']."&amp;thread_id=".$_GET['thread_id']."'>".$locale['516']."</a>";
						}
					}
					// Print this thread
					$r .= "&nbsp;<a href='".BASEDIR."print.php?type=F&amp;thread=".$_GET['thread_id']."&amp;rowstart=".$_GET['rowstart']."'><img src='".get_image("printer")."' alt='".$locale['519']."' title='".$locale['519']."' style='border:0;vertical-align:middle' /></a>\n";
					$r .= "</span></h1>";
					$r .= "<p class='thread-starter flleft'>".sprintf($locale['started_by']." ", profile_link($pdata['post_author'], $pdata['user_author'], $pdata['status_author']), $locale['on'], showdate("forumdate", $pdata['post_datestamp']))."</p>";
					$r .= "<div class='thread-counts faint flright'><strong class='icon-reply-all'>".($pdata['thread_postcount'] - 1)."</strong> ".$locale['454']." <strong class='icon-eye'>".$pdata['thread_views']."</strong> ".$locale['453']."</div>";
					$r .= "</div></div>\n";
				}
			}
			return $r;
		}
		$output = preg_replace_callback($search_breadcrumb, 'replace_breadcrumb', $output, 1); // occurs only once
	}

	$output = preg_replace($search_site, $replace_site, $output);

	return $output;
}
?>