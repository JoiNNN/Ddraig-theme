<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: functions.php
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

set_image("pollbar", THEME."images/btn.png");
set_image("edit", THEME."images/edit.png");
set_image("printer", THEME."images/printer.png");
set_image("link", THEME."images/link.png");
//Arrows
set_image("up", THEME."images/up.png");
set_image("down", THEME."images/down.png");
set_image("left", THEME."images/left.png");
set_image("right", THEME."images/right.png");
//Forum folders icons
set_image("folder", THEME."forum/folder.png");
set_image("foldernew", THEME."forum/foldernew.png");
set_image("folderlock", THEME."forum/folderlock.png");
set_image("stickythread", THEME."forum/stickythread.png");
//Forum buttons
set_image("reply", "reply");
set_image("newthread", "newthread");
set_image("web", "web");
set_image("pm", "pm");
set_image("quote", "quote");
set_image("forum_edit", "forum_edit");

function theme_output($output) {
	global $locale, $settings;
	$search = array(
		"@<a href='".ADMIN."comments.php(.*?)&amp;ctype=(.*?)&amp;cid=(.*?)'>(.*?)</a>@si", 		//Manage comments button
		"@<div class='quote'><a (.*?)><strong>(.*?)</strong></a>(<br />)?@si",						//Quote
		"@<img src='(.[^>]*?)/forum/stickythread.png'(.*?)/>@i",									//Sticky thread image
		"@<span class='small' style='font-weight:bold'>\[".$locale['global_051']."\]</span>@i",		//Poll thread text (forum_threads_list panel)
	);
	$replace = array(
		"<a href='".ADMIN."comments.php$1&amp;ctype=$2&amp;cid=$3' class='big button flright'><span class='settings-button icon'>$4</span></a>",
		"<div class='quote extended'><p class='citation'><img src='".THEME."images/quote_icon.png' alt='>' /><a $1><strong>$2</strong></a></p>",
		"<span class='tag green'>".$locale['sticky']."</span>",
		"<span class='tag blue small'>".$locale['global_051']."</span>",
	);

	$page = FUSION_SELF;
	//Check if we are in forum
	$inforum = FALSE;
	if (strpos(TRUE_PHP_SELF, '/forum/') !== FALSE) {
		$inforum = TRUE;
	}
	
	//Forums and "Latest Active Forum Threads" users last post avatar
	$result = dbquery("SELECT panel_filename FROM ".DB_PANELS." WHERE panel_filename='forum_threads_list_panel' AND panel_status='1' LIMIT 1");
	if ($inforum && in_array($page, array("index.php", "viewforum.php")) || dbarray($result)) { //add avatar only when viewing the forum or when forum_threads_list_panel is enabled
		function replace_avatar($m) {
			global $locale;
			$r = "<td width='1%' style='white-space:nowrap' class='tbl".$m[1]."'>".$locale['deleted_user']."</td>";
			$class = $m[1];
			$id = $m[6];
			$name = $m[7];
			$date = $m[9];
			if ($m[3] != "") {
				$date = $m[3];
			}
			$src = IMAGES."avatars/noavatar50.png";
			$result = dbquery("SELECT user_avatar FROM ".DB_USERS." WHERE user_id='".$id."' LIMIT 1");
			while ($data = dbarray($result)) {
				if ($data['user_avatar'] && file_exists(IMAGES."avatars/".$data['user_avatar'])) {
					$src = IMAGES."avatars/".$data['user_avatar'];
				}
			$r = "<td width='1%' class='tbl".$class." last-post'><a href='".BASEDIR."profile.php?lookup=".$id."' class='profile-link flleft'><span class='user-avatar'><img class='avatar small' src='".$src."' alt='Avatar' /></span></a><span class='last-post-author'><a href='".BASEDIR."profile.php?lookup=".$id."' class='profile-link'>".$name."</a></span><br /><span class='last-post-date'>".$date."</span></td>";
			}
			return $r;
		}
		$searchlink = "#<td width='1%' class='tbl(1|2)' style='(.*?)?white-space:nowrap'>(.*?)?(<br />\n<span class='small'>)?(.*?)?<a href='".BASEDIR."profile.php\?lookup=(.*?)' class='profile-link'>(.*?)</a>(<br />\n|</span>)(.*?)?</td>#i";
		$output = preg_replace_callback($searchlink, 'replace_avatar', $output);
	}

	//Replacements that only occur in forums should be searched for only when viewing the forums
	if ($inforum && in_array($page, array("index.php", "viewforum.php", "viewthread.php", "post.php"))) {
		$searchforum = array(
		"@><img src='reply' alt='(.*?)' style='border:0px' />@si",								//Reply button (viewthread.php)
		"@><img src='newthread' alt='(.*?)' style='border:0px;?' />@si",						//New thread button (viewforum.php|viewthread.php)
		"@><img src='web' alt='(.*?)' style='border:0;vertical-align:middle' />@si",			//Website button (viewthread.php)
		"@><img src='pm' alt='(.*?)' style='border:0;vertical-align:middle' />@si",				//PM button (viewthread.php)
		"@><img src='quote' alt='(.*?)' style='border:0px;vertical-align:middle' />@si",		//Quote button (viewthread.php)
		"@><img src='forum_edit' alt='(.*?)' style='border:0px;vertical-align:middle' />@si",	//Edit button (viewthread.php)
		"@<input (.*?) name='move_posts' value='(.*?)' (.*?) />@i",								//Move posts button (viewthread.php)
		"@<input (.*?) name='(delete_posts|delete_threads)' value='(.*?)' class='(.*?)' (.*?) />@i",//Delete posts button (viewforum.php|viewthread.php)
		"@forum_thread_user_info' style='width:140px'>\n<img src='(.*?)' alt='(.*?)' />@si",	//User avatar in forum (viewthread.php)
		"@forum_thread_ip' style='width:140px;white-space:nowrap'>@si",							//User IP in forum (viewthread.php)
		"@<table cellpadding='0' cellspacing='1' width='100%' class='tbl-border (.*?)'>@i",		//No more cellspacing in forum's tables (needed for IE7 as it can't apply CSS rules to overwrite cellspacing) (index.php|viewforum.php|viewthread.php)
		"@<td colspan='2' class='tbl1 forum_thread_post_space' style='height:10px'></td>@si",	//Space between forum posts (viewthread.php)
		"@<hr />\n<span class='small'>(.*?)</span>@si",											//Edit note in threads (viewthread.php)
		"@</div>\n<br /><div class='edit_reason'>(.*?)</div>@si"								//Edit reason (viewthread.php)
		);
		$replaceforum = array(
		" class='button big'><span class='reply-button icon'>$1</span>",
		" class='button big'><span class='newthread-button icon'>$1</span>",
		" class='button' rel='nofollow' title='$1'><span class='web-button icon'>Web</span>",
		" class='button' title='$1'><span class='pm-button icon'>PM</span>",
		" class='button' title='$1'><span class='quote-button icon'>$1</span>",
		" class='negative button' title='$1'><span class='edit-button icon'>$1</span>",
		"<button $1 name='move_posts' $3><span class='move-button icon'>$2</span></button>&nbsp;",
		"<button $1 class='$4 negative' name='$2' $5><span class='del-button icon'>$3</span></button>",
		"forum_thread_user_info'><div class='user-avatar'><img class='avatar' src='$1' alt='$2' /></div>",
		"forum_thread_ip'>",
		"<table cellpadding='0' cellspacing='0' width='100%' class='tbl-border $1'>",
		"<td colspan='2' class='tbl1 forum_thread_post_space'></td>",
		"<br /><div class='post-edited small'>$1</div>",
		"<div class='edit_reason'>$1</div></div>"
		);

		if ($page == "viewforum.php") {
		//Locked thread tag (viewforum.php)
		$searchforum[] .= "@src='(.*?)/forum/folderlock.png'(.*?)<td width='100%' class='(.*?)'>(.*?)<a@si";
		$replaceforum[] .= "src='$1/forum/folderlock.png'$2<td width='100%' class='$3 thread-locked'>$4<span class='tag red'>".$locale['locked']."</span> <a";
		//Thread pages numbers (viewforum.php)	
		$searchforum[] .= "@\(".$locale['455']."(.*?)\)@i";
		$replaceforum[] .= "<span class='pages small'>".$locale['455']."$1</span>";
		//Thread title class and thread preview link (viewforum.php)
		$searchforum[] .= "@src='(.*?)/forum/(folder|foldernew|folderlock)(.*?)<td width='100%' class='(.*?)'>(.*?)<a (.*?)thread_id=(.*?)>(.*?)</a>@si";
		$replaceforum[] .= "src='$1/forum/$2$3<td width='100%' class='$4'>$5<a class='thread-title' $6thread_id=$7>$8</a>".((THREAD_PREV == 1) ? "<a title='".$locale['prev_thread']."' class='preview-link expand flright' $6thread_id=$7></a>" : "");
		//Thread Replies/Views
		$searchforum[] .= "@<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>(.*?)</td>\n<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>(.*?)</td>@si";
		$replaceforum[] .= "<td width='2%' class='tbl1 thread-stats'><dl class='major'><dt class='flleft'>Replies: </dt><dd class='flright'>$2</dd></dl><dl class='minor small'><dt class='flleft'>Views: </dt><dd class='flright'>$1</dd></dl></td>";
		$searchforum[] .= "@<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap' align='center' >".$locale['global_045']."</td>\n<td class='tbl2 forum-caption' width='1%' style='white-space:nowrap' align='center'>".$locale['global_046']."</td>@si";
		$replaceforum[] .= "<td class='tbl2 forum-caption' width='1%' align='center'>".$locale['global_046']." / ".$locale['global_045']."</td>";
		}

		$output = preg_replace($searchforum, $replaceforum, $output);

		//Remove special characters and html entities function
		function clean_name($text) {
			$text = strtolower($text);
			$text = preg_replace(array("/&(?:[a-z\d]+|#\d+|#x[a-f\d]+);/", "/[^a-z\d\s]/", "/\s+/"), array("", "", "-"), $text);
			return $text;
		}

		//Add ID and link to forum category (index.php)
		$search_forumcat = "@<td colspan='2' class='forum-caption forum_cat_name'><!--forum_cat_name-->(.*?)</td>@i";
		function replace_forumcat($m) {
			$r = "<td id='fcat-".clean_name($m[1])."' colspan='2' class='forum-caption forum_cat_name'><!--forum_cat_name--><a class='fcat-link scroll' href='#fcat-".clean_name($m[1])."'>".$m[1]."</a></td>";
			return $r;
		}
		$output = preg_replace_callback($search_forumcat, 'replace_forumcat', $output);

		//Add forum category link to breadcrumb and reformat the breadcrumbs (viewforum.php|viewthread.php|post.php)
		$search_breadcrumb = "@<div class='tbl2 forum_breadcrumbs'(.*?)>(<span class='small'>)?(.*?) &raquo; (.*?) &raquo; (.*?)( &raquo; (.*?))?(</span>)?</div>@i";
		function replace_breadcrumb($m) {
			$a = "<span class='crust'><span class='crumb'>";
			$b = "</span><span class='arrow'><span>&raquo;</span></span></span>";
			$r = "<div class='tbl2 forum_breadcrumbs'".$m[1]."><span class='crust first'><span class='crumb'>".$m[3].$b." ".$a."<a href='index.php#fcat-".clean_name($m[4])."'>".$m[4]."</a>".$b." ".$a.$m[5].((!isset($m[7]) || empty($m[7])) ? "</span></span>" : $b." ".$a.$m[7]."</span></span>")."</div>";
			return $r;
		}
		$output = preg_replace_callback($search_breadcrumb, 'replace_breadcrumb', $output, 1); //occurs only once
	}

	$output = preg_replace($search, $replace, $output);

	return $output;

}
?>