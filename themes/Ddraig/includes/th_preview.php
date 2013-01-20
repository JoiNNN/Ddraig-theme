<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: th_preview.php
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
require_once "../../../maincore.php";
//Check if this files exists within the theme set at the moment the request is made
if (!file_exists(THEME."includes/th_preview.php")) {exit("...");}

//Show error function
function showerror($error) {
	if (!empty($error)) {
		$error = "<div class='preview-wrap' style='display:none'><div class='admin-message'>".$error."</div></div>";
	}
	return $error;
}

//Prevent direct file access
if ((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest'))) {
	redirect(FORUM."index.php");

} elseif (isset($_GET['thread_id']) && isnum($_GET['thread_id'])) {
	@header("Content-type: text/html; charset=".$locale['charset']);

	//Locales
	if (file_exists(THEME."locale/".$settings['locale'].".php")) {
		include THEME."locale/".$settings['locale'].".php";
	} else {
		include THEME."locale/English.php";
	}

	//The request is made via ajax. thread_id is set and is a number
	$result = dbquery(
		"SELECT t.*, f.*, f2.forum_name AS forum_cat_name
		FROM ".DB_THREADS." t
		LEFT JOIN ".DB_FORUMS." f ON t.forum_id=f.forum_id
		LEFT JOIN ".DB_FORUMS." f2 ON f.forum_cat=f2.forum_id
		WHERE t.thread_id='".$_GET['thread_id']."' AND t.thread_hidden='0'
		LIMIT 1"
	);
	if (dbrows($result)) {
		$fdata = dbarray($result);
		if (!checkgroup($fdata['forum_access']) || !$fdata['forum_cat'] || $fdata['thread_hidden'] == "1") {
			//The user doesn`t have access to the forum containing this thread
			echo showerror("Oops! You don't have access to view this thread :(");
		} else {
			//The user can view this thread
			//Function to select first or last post in a thread
			function selectpost($id, $position) {	
				$result = dbquery(
					"SELECT thread_id, post_id, post_message, post_author, post_smileys
					FROM ".DB_POSTS."
					WHERE thread_id='".$id."'
					ORDER BY post_datestamp ".($position == 'first' ? 'ASC' : 'DESC')."
					LIMIT 1");
				return $result;
			}
			//Function to strip BBCodes from string
			function stripBBCode($text) {
     			$pattern = '#[[\/\!]*?[^\[\]]*?]#si';
     			$replace = '';
     			return preg_replace($pattern, $replace, $text);
    		}
			//Function to get user info
			function userinfoarray($id) {
				$user_status = " AND (user_status='0' OR user_status='3' OR user_status='7')";
				if (iADMIN) {
					$user_status = "";
				}
				$result = dbquery(
					"SELECT user_id, user_name, user_status, user_avatar
					FROM ".DB_USERS."
					WHERE user_id='".$id."'".$user_status."
					LIMIT 1");
				$data = dbarray($result);
				return $data;
			}
			//Function to build user avatar image link
			function getavatarimg($id) {
				global $settings;
				$src = $settings['siteurl']."images/avatars/noavatar50.png";
				$data = userinfoarray($id);
					if ($data['user_avatar'] && file_exists(IMAGES."avatars/".$data['user_avatar'])) {
						$src = $settings['siteurl']."images/avatars/".$data['user_avatar'];
					}
				return $src;
			}
			//First post data to array
			$postdataf = dbarray(selectpost($_GET['thread_id'],'first'));
			//Last post data to array
			$postdatal = dbarray(selectpost($_GET['thread_id'],'last'));
			//Function to wrap stuff with profile link
			function profilelink($id, $data="") {
				global $settings;
				$link = "<a class='profile-link' href='".$settings['siteurl']."profile.php?lookup=".$id."'>".$data."</a>";
				//Profiles disabled for public view?
				if (!iMEMBER && $settings['hide_userprofiles'] == 1) {
					$link = $data;
				}
				return $link;
			}
			//Output the HTML
			echo "<div class='preview-wrap' style='display:none'>";
			require_once INCLUDES."bbcode_include.php";
			//First post
			$data = userinfoarray($postdataf['post_author']);
			echo profilelink($postdataf['post_author'], "<span class='user-avatar flleft'><img class='avatar' width='54' src='".getavatarimg($postdataf['post_author'])."' alt='Avatar' /></span>");
			echo "<div class='preview-text'><span class='post-info'>".profilelink($postdataf['post_author'], $data['user_name'])." ".$locale['made']." <a href='viewthread.php?thread_id=".$postdataf['thread_id']."&amp;pid=".$postdataf['post_id']."#post_".$postdataf['post_id']."'>".$locale['first']."</a></span><hr style='margin:0' />".trimlink(stripBBCode(strip_bbcodes($postdataf['post_message'])), 450)."</div>";
			//Last post
			if ($postdatal['post_id'] != $postdataf['post_id']) {
				$data = userinfoarray($postdatal['post_author']);
				echo "<br />";
				echo profilelink($postdatal['post_author'], "<span class='user-avatar flleft'><img class='avatar' width='54' src='".getavatarimg($postdatal['post_author'])."' alt='Avatar' /></span>");
				echo "<div class='preview-text'><span class='post-info'>".profilelink($postdatal['post_author'], $data['user_name'])." ".$locale['made']." <a href='viewthread.php?thread_id=".$postdatal['thread_id']."&amp;pid=".$postdatal['post_id']."#post_".$postdatal['post_id']."'>".$locale['last']."</a></span><hr style='margin:0' />".trimlink(stripBBCode(strip_bbcodes($postdatal['post_message'])), 450)."</div>";
			}
			echo "<br />";
			echo "<div><a href='viewthread.php?thread_id=".$_GET['thread_id']."' class='button'><span class='rightarrow icon'>".$locale['view_thread']."</span></a></div>\n";
			echo "</div>";
		}
	} else {
		//The thread id cannot be found in DB
		echo showerror("Oh no, the ID cannot be found :(");
	}
} else {
	//The thread id is not a number
	echo showerror("Oh no, the ID is not a number :(");
}
?>