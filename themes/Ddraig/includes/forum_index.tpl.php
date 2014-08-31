<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http:// www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: forum_index.tpl.php
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

// Add ID and link to forum category (index.php)
$search_forumcap = "@<tr>\n<td colspan='2' class='forum-caption forum_cat_name'><!--forum_cat_name-->(.*?)</td>\n<td align='center' width='1%' class='forum-caption' style='white-space:nowrap'>(.*?)</td>\n<td align='center' width='1%' class='forum-caption' style='white-space:nowrap'>(.*?)</td>\n<td width='1%' class='forum-caption' style='white-space:nowrap'>(.*?)</td>\n</tr>@i";
function replace_forumcap($m) {
	$r = "<tr><td id='fcat-".clean_name($m[1])."' colspan='2' class='forum-caption forum_cat_name tbl1'><!--forum_cat_name--><a class='fcat-link scroll' href='#fcat-".clean_name($m[1])."'>".$m[1]."</a></td>";
	$r .= "<td align='center' class='forum-caption stats-caption tbl2'>".$m[2]." / ".$m[3]."</td>";
	$r .= "<td width='1%' class='forum-caption last-post-caption tbl2'>".$m[4]."</td></tr>";
	return $r;
}
$output = preg_replace_callback($search_forumcap, 'replace_forumcap', $output);

// Replace forum rows
$search_forum_row = "@<tr>\n<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><img src='(.*?)' alt='(.*?)' /></td>\n<td class='tbl1 forum_name'><!--forum_name-->(.*?)<br />\n<span class='small'>(.*?)</span>\n\n</td>\n<td align='center' width='1%' class='tbl2' style='white-space:nowrap'>(.*?)</td>\n<td align='center' width='1%' class='tbl1' style='white-space:nowrap'>(.*?)</td>\n<td width='1%' class='tbl2' style='white-space:nowrap'>(.*?)(<br />\n<span class='small'>(.*?)<a href='\.\./profile.php\?lookup=(.*?)' class='profile-link'>(.*?)</a></span>)?</td>\n</tr>@si";
function replace_forum_row($m) {
	global $locale;

	//var_dump($m);
	$r = "<tr>";
	$r .= "<td align='center' width='1%' class='tbl2' style='white-space:nowrap'><img src='".$m[1]."' alt='".$m[2]."' /></td>";
	$r .= "<td class='tbl1 forum_name'><!--forum_name-->".$m[3]."<br />";
	$r .= "<span class='small'>".$m[4]."</span>";
	$r .= "</td>";
	$r .= "<td class='thread-stats tbl1'><dl class='major'><dt>".$locale['402'].": </dt><dd>".$m[5]."</dd></dl><dl class='minor small'><dt>".$locale['403'].": </dt><dd>".$m[6]."</dd></dl></td>\n";
	$r .= "<td width='1%' class='thread-last-post tbl1'>".(isset($m[8]) ? "<div class='last-post-avatar flleft'>".build_avatar($m[10])."</div><span class='last-post-author'>".(!empty($m[11]) ? "<a href='".BASEDIR."profile.php?lookup=".$m[10]."' class='profile-link'>".$m[11]."</a>" : $locale['deleted_user'])."</span><br /><span class='last-post-date small'>".$m[7]."</span>" : $m[7])."</td>";
	$r .= "</tr>";

	return $r;
}
$output = preg_replace_callback($search_forum_row, 'replace_forum_row', $output);

// Add the links next to the title (index.php)
$search_forum_title = "@<span class='title'>".$locale['400']."</span>@i";
$replace_forum_title = "<span class='title'>".$locale['400']."</span><ul class='findex clearfix'><li><a class='forum-category button small' href='".FUSION_SELF."'>".$locale['all']."</a></li>
<li class='link".(isset($_GET['latest']) ? " active" : "")."'><a class='forum-category button ".(isset($_GET['latest']) ? "active" : "")." small' href='".FUSION_SELF."?latest'>".$locale['latest_active_threads']."</a></li>
<li class='link".(isset($_GET['participated']) ? " active" : "")."'><a class='forum-category button ".(isset($_GET['participated']) ? "active" : "")." small' href='".FUSION_SELF."?participated'>".$locale['participated_threads']."</a></li>
<li class='link".(isset($_GET['unanswered']) ? " active" : "")."'><a class='forum-category button ".(isset($_GET['unanswered']) ? "active" : "")." small' href='".FUSION_SELF."?unanswered'>".$locale['unanswered_threads']."</a></li></ul>";
$output = preg_replace($search_forum_title, $replace_forum_title, $output);

// Code below based on Atom-X by hien
$search_forum_index = "@<!--pre_forum_idx-->(.*?)<!--sub_forum_idx-->@si";
// Main Page Router for ?latest, etc. (index.php) 
function replace_forum_index($m) {
	global $locale;
	$html = $m[0];

	if (isset($_GET['latest'])) {
		$core = forum_latest_topics();
		$html = forum_item($core);
	} elseif (isset($_GET['participated'])) {
		$core = forum_participated_topics();
		$html = forum_item($core);
	} elseif (isset($_GET['unanswered'])) {
		$core = forum_unanswered();
		$html = forum_item($core);
	}

	return $html;
}
$output = preg_replace_callback($search_forum_index, 'replace_forum_index', $output, 1); // occurs only once

// Find Latest Topics - The LAFT
function forum_latest_topics() {
	$list = array();
	$list['title'] = "Latest Active Discussions";
	$list['items_per_page'] = 10;
	$settings['numofthreads'] = 200;

	if (!isset($lastvisited) || !isnum($lastvisited)) { $lastvisited = time(); }
	$data = dbarray(dbquery(
		"SELECT tt.thread_lastpost
		FROM ".DB_FORUMS." tf
		INNER JOIN ".DB_THREADS." tt ON tf.forum_id = tt.forum_id
		WHERE ".groupaccess('tf.forum_access')." AND thread_hidden='0'
		ORDER BY tt.thread_lastpost DESC LIMIT ".($settings['numofthreads']-1).", ".$settings['numofthreads']
	));
	$timeframe = empty($data['thread_lastpost']) ? 0 : $data['thread_lastpost'];

	// rowstart bug fix
	$list['rows'] = dbcount("('thread_id')", 
		DB_THREADS." a
		INNER JOIN ".DB_FORUMS." tt on tt.forum_id=a.forum_id",
		groupaccess('tt.forum_access')." AND a.thread_lastpost >= ".$timeframe." AND a.thread_hidden='0'"
	);
	$_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && ($_GET['rowstart'] <= $list['rows'])) ? $_GET['rowstart'] : 0;

	$result2 = dbquery(
		"SELECT tt.thread_id, tt.thread_subject, tt.thread_views, tt.thread_lastuser, tt.thread_lastpost, tt.thread_sticky, tt.thread_poll, tt.thread_poll, tt.thread_lastpostid, tt.thread_postcount, tt.thread_locked,
		mm.forum_id as master_forum, mm.forum_name as master_forum_name,
		tf.forum_id, tf.forum_name, tf.forum_access,
		tc.user_id as author_id, tc.user_name as author_name, tc.user_status as author_status, tc.user_avatar as author_avatar,
		tu.user_id as lastposter_id, tu.user_name as lastposter_name, tu.user_status as lastposter_status, tu.user_avatar as lastposter_avatar
		FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		LEFT JOIN ".DB_FORUMS." mm ON tf.forum_cat=mm.forum_id
		INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser=tu.user_id
		LEFT JOIN ".DB_USERS." tc ON tt.thread_author=tc.user_id
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_lastpost >= ".$timeframe." AND tt.thread_hidden='0'
		ORDER BY tt.thread_lastpost DESC LIMIT ".$_GET['rowstart'].",".$list['items_per_page']
	);
	
	// Add each thread data to the array
	$i = 0;
	if (dbrows($result2)) {
		while ($_thread = dbarray($result2)) {
			$list['data'][$i] = $_thread;
			$i++;
		}
	}

	$list['navigation'] = ($list['rows'] > $list['items_per_page']) ?  makepagenav($_GET['rowstart'], $list['items_per_page'], $list['rows'], 3, FUSION_SELF."?latest&amp;") : '';

	return $list;
}

// The participated
function forum_participated_topics() {
	global $userdata;
	$list = array();
	$list['items_per_page'] = 10;
	$list['title'] = "My Participated Discussions";

	$result = dbquery(
		"SELECT tp.post_id FROM ".DB_POSTS." tp
		INNER JOIN ".DB_THREADS." tt ON tp.thread_id = tt.thread_id
		INNER JOIN ".DB_FORUMS." tf ON tp.forum_id = tf.forum_id
		WHERE ".groupaccess('forum_access')." AND post_author='".$userdata['user_id']."' AND post_hidden='0' AND thread_hidden='0'
		GROUP BY tt.thread_id"
	);
	$list['rows'] = dbrows($result);

	if (dbrows($result)>0) {

		$_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && ($_GET['rowstart'] <= $list['rows'])) ? $_GET['rowstart'] : 0;

		$result = dbquery(
			"SELECT tp.forum_id, tp.thread_id, tp.post_id, tp.post_author, tp.post_datestamp,
			tf.forum_name, tf.forum_access,
			mm.forum_id as master_forum, mm.forum_name as master_forum_name,
			tt.thread_subject, tt.thread_lastuser, tt.thread_lastpostid, tt.thread_lastpost, tt.thread_views, tt.thread_postcount, tt.thread_poll, tt.thread_sticky, tt.thread_locked,
			tc.user_id as author_id, tc.user_name as author_name, tc.user_status as author_status, tc.user_avatar as author_avatar,
			uu.user_id as lastposter_id, uu.user_name as lastposter_name, uu.user_status as lastposter_status, uu.user_avatar as lastposter_avatar
			FROM ".DB_POSTS." tp
			INNER JOIN ".DB_FORUMS." tf ON tp.forum_id=tf.forum_id
			LEFT JOIN ".DB_FORUMS." mm ON tf.forum_cat=mm.forum_id
			INNER JOIN ".DB_THREADS." tt ON tp.thread_id=tt.thread_id
			LEFT JOIN ".DB_USERS." uu ON (tt.thread_lastuser=uu.user_id)
			LEFT JOIN ".DB_USERS." tc ON tt.thread_author=tc.user_id
			WHERE ".groupaccess('tf.forum_access')." AND tp.post_author='".$userdata['user_id']."' AND post_hidden='0' AND thread_hidden='0'
			GROUP BY tt.thread_id ORDER BY tt.thread_lastpost DESC LIMIT ".$_GET['rowstart'].", ".$list['items_per_page']
		);

		// Add each thread data to the array
		$i = 0;
		if (dbrows($result)) {
			while ($_thread = dbarray($result)) {
				$list['data'][$i] = $_thread;
				$i++;
			}
		}
	}

	$list['navigation'] = ($list['rows'] > $list['items_per_page']) ?  makepagenav($_GET['rowstart'], $list['items_per_page'], $list['rows'], 3, FUSION_SELF."?participated&amp;") : '';

	return $list;
}

// The Unanswered
function forum_unanswered() {
	global $userdata;
	$list = array();
	$list['items_per_page'] = 10;
	$list['title'] = "Unanswered Threads";

	// rowstart bug fix
	$list['rows'] = dbcount("('tt.thread_id')",
		DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id",
		groupaccess('tf.forum_access')." AND tt.thread_postcount='1' AND tt.thread_hidden='0'"
	);
	$_GET['rowstart'] = (isset($_GET['rowstart']) && isnum($_GET['rowstart']) && ($_GET['rowstart'] <= $list['rows'])) ? $_GET['rowstart'] : 0;

	$result = dbquery(
		"SELECT tt.thread_id, tt.thread_subject, tt.thread_views, tt.thread_lastuser, tt.thread_lastpost, tt.thread_poll, tt.thread_lastpostid, tt.thread_postcount, tt.thread_locked,
		mm.forum_id as master_forum, mm.forum_name as master_forum_name,
		tf.forum_id, tf.forum_name, tf.forum_access, 
		tc.user_id as author_id, tc.user_name as author_name, tc.user_status as author_status, tc.user_avatar as author_avatar,
		tu.user_id as lastposter_id, tu.user_name as lastposter_name, tu.user_status as lastposter_status, tu.user_avatar as lastposter_avatar
		FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		LEFT JOIN ".DB_FORUMS." mm ON tf.forum_cat=mm.forum_id
		INNER JOIN ".DB_USERS." tu ON tt.thread_lastuser=tu.user_id
		LEFT JOIN ".DB_USERS." tc ON tt.thread_author=tc.user_id
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_postcount='1' AND tt.thread_hidden='0'
		ORDER BY tt.thread_lastpost DESC LIMIT ".$_GET['rowstart'].",".$list['items_per_page']
	);
	// Add each thread data to the array
	$i = 0;
	if (dbrows($result)) {
		while ($_thread = dbarray($result)) {
			$list['data'][$i] = $_thread;
			$i++;
		}
	}

	$list['navigation'] = ($list['rows'] > $list['items_per_page']) ?  makepagenav($_GET['rowstart'], $list['items_per_page'], $list['rows'], 3, FUSION_SELF."?unanswered&amp;") : '';

	return $list;
}

// The Item Template
function forum_item($data) {
	global $userdata, $locale;

	// print_r($data);
	$html = "";
	$threads_per_page = 20;

	if (!empty($data['data'])) {
		$html .= "<table class='forum_table forum_extension tbl-border'>\n";
		$html .= "<tr><td class='tbl2 forum-caption'>".$locale['451']."</td>\n";
		$html .= "<td class='forum-caption stats-caption tbl2' style='white-space:nowrap' align='center'>".$locale['454']." / ".$locale['453']."</td>\n";
		$html .= "<td class='forum-caption last-post-caption tbl2'>".$locale['404']."</td>\n</tr>\n";
		foreach($data['data'] as $arr => $thread_data) {
			// Thread pages
			$thread_pages = "";
			$reps = ceil($thread_data['thread_postcount'] / $threads_per_page);
			if ($reps > 1) {
				$ctr = 0; $ctr2 = 1; $pages = ""; $middle = false;
				while ($ctr2 <= $reps) {
					if ($reps < 5 || ($reps > 4 && ($ctr2 == 1 || $ctr2 > ($reps-3)))) {
						$pnum = "<a href='viewthread.php?thread_id=".$thread_data['thread_id']."&amp;rowstart=$ctr'>$ctr2</a> ";
					} else {
						if ($middle == false) {
							$middle = true; $pnum = "... ";
						} else {
							$pnum = "";
						}
					}
					$pages .= $pnum; $ctr = $ctr + $threads_per_page; $ctr2++;
				}
				$thread_pages .= "<span class='pages small'>".trim($pages)."</span>";
			}

			$html .= "<tr class='thread-row'>\n";
			$html .= "<td class='thread-name tbl1'>\n";
			$html .= ($thread_data['thread_locked'] == 1 ? "<span class='tag red'>".$locale['locked']."</span> " : "");
			$html .= ($thread_data['thread_poll'] == 1 ? "<span class='tag blue'>".$locale['global_051']."</span> " : "");
			$html .= "<a class='thread-title' href='".FORUM."viewthread.php?thread_id=".$thread_data['thread_id']."&amp;pid=".$thread_data['thread_lastpostid']."#post_".$thread_data['thread_lastpostid']."'>".$thread_data['thread_subject']."</a>\n";
			$html .= ((THREAD_PREV == 1) ? "<a title='".$locale['prev_thread']."' class='preview-link expand flright' href='".FORUM."viewthread.php?thread_id=".$thread_data['thread_id']."'></a><br />" : "")."\n";
			$html .= "<span class='thread-starter small'>".sprintf($locale['started_by']." ", profile_link($thread_data['author_id'], $thread_data['author_name'], $thread_data['author_status']), $locale['in'], "<a href='".FORUM."viewforum.php?forum_id=".$thread_data['forum_id']."'>".$thread_data['forum_name']."</a>")."</span>\n";
			$html .= "<span class='thread-stats-responsive faint small'><br />".$locale['global_046'].": <span class='darker'>".number_format($thread_data['thread_postcount']-1)."</span> / ".$locale['global_045'].": <span class='darker'>".number_format($thread_data['thread_views'])."</span></span>";
			$html .= $thread_pages."</td>\n";
			$html .= "<td class='thread-stats tbl1'><dl class='major'><dt>".$locale['global_046'].": </dt><dd>".number_format($thread_data['thread_postcount']-1)."</dd></dl><dl class='minor small'><dt>".$locale['global_045'].": </dt><dd>".number_format($thread_data['thread_views'])."</dd></dl></td>\n";
			$avatar = "<img class='avatar' src='".IMAGES."avatars/noavatar100.png' alt='".$locale['567']."' />";
			if ($thread_data['lastposter_avatar'] && file_exists(IMAGES."avatars/".$thread_data['lastposter_avatar']) && $thread_data['lastposter_status']!=6 && $thread_data['lastposter_status']!=5) {
				$avatar = "<img class='avatar' src='".IMAGES."avatars/".$thread_data['lastposter_avatar']."' alt='".$locale['567']."' />";
			}
			$html .= "<td class='thread-last-post tbl1' style='white-space:nowrap'><div class='last-post-avatar'><span class='user-avatar'>".$avatar."</span></div>\n";
			$html .= "<div class='last-post-info'><span class='last-post-author'>".(!empty($thread_data['lastposter_id']) ? profile_link($thread_data['lastposter_id'], $thread_data['lastposter_name'], $thread_data['lastposter_status']) : "<span class='deleted-user'>".$locale['deleted_user']."</span>")."</span><br />";
			$html .= "<span class='last-post-date small'><a title='".$locale['go_to_last_post']."' href='viewthread.php?thread_id=".$thread_data['thread_id']."&amp;pid=".$thread_data['thread_lastpostid']."#post_".$thread_data['thread_lastpostid']."'>".showdate("forumdate", $thread_data['thread_lastpost'])."</a></span>";
			$html .= "</div>\n";
			$html .= "</td>\n";

			$html .= "</tr>\n";
		}
		$html .= "</table>\n";
		// navigation
		$html .= (isset($data['navigation']) && $data['navigation']) ? "<div class='text-center'>".$data['navigation']."</div>" : '';
	} else {
		$html .= "<p class='no-threads text-center'>".$locale['no_threads_available']."</p>";
	}

	return $html;
}

?>