<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: footer_links.php
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

//Latest news
if (L_NEWS == 1) {
echo "<!-- Latest news -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['latest_news']."</h4><ul>\n";
	$result = dbquery("SELECT news_id, news_subject
				FROM ".DB_NEWS." 
				WHERE ".groupaccess('news_visibility')."
				AND news_draft='0'
				ORDER BY news_datestamp DESC LIMIT 5");
	if (dbrows($result) != 0) {
		while($data = dbarray($result)) {
			$newsid = $data['news_id'];
			$newstitle = trimlink($data['news_subject'], 25);
			echo "<li><a href='".BASEDIR."news.php?readmore=".$data['news_id']."'  title='".$data['news_subject']."' target='_blank'>".$newstitle."</a></li>\n";
		}
	} else {
		echo "<li>".$locale['no_news']."</li>\n";
	}
	echo "</ul>\n";
echo "</div>\n";
}

//Latest articles
if (L_ARTICLES == 1) {
echo "<!-- Latest articles -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['latest_articles']."</h4><ul>\n";
	$result = dbquery(
		"SELECT ta.article_id, ta.article_subject, tac.article_cat_id, tac.article_cat_access FROM ".DB_ARTICLES." ta
		INNER JOIN ".DB_ARTICLE_CATS." tac ON ta.article_cat=tac.article_cat_id
		".(iSUPERADMIN ? "" : "WHERE ".groupaccess('article_cat_access'))." AND article_draft='0' ORDER BY article_datestamp DESC LIMIT 0,5"
	);
	if (dbrows($result)) {
		while($data = dbarray($result)) {
			$itemsubject = trimlink($data['article_subject'], 23);
			echo "<li><a href='".BASEDIR."articles.php?article_id=".$data['article_id']."' title='".$data['article_subject']."'>".$itemsubject."</a></li>\n";
		}
	} else {
		echo "<li>".$locale['no_articles']."</li>\n";
	}
	echo "</ul>\n";
echo "</div>\n";
}

//Newest threads
if (N_THREADS == 1) {
echo "<!-- Newest Threads -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['global_021']."</h4><ul>\n";
	$result = dbquery("
		SELECT tt.forum_id, tt.thread_id, tt.thread_subject, tt.thread_lastpost FROM ".DB_THREADS." tt
		INNER JOIN ".DB_FORUMS." tf ON tt.forum_id=tf.forum_id
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_hidden='0'
		ORDER BY thread_lastpost DESC LIMIT 5");
	if (dbrows($result)) {
		while($data = dbarray($result)) {
			$itemsubject = trimlink($data['thread_subject'], 25);
			echo "<li><a href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."' title='".$data['thread_subject']."'>".$itemsubject."</a></li>\n";
		}
	} else {
		echo "<li>".$locale['global_023']."</li>\n";
	}
	echo "</ul>\n";
echo "</div>\n";
}

//Hotest threads
if (H_THREADS == 1) {
echo "<!-- Hottest Threads -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['global_022']."</h4><ul>\n";
	$timeframe = ($settings['popular_threads_timeframe'] != 0 ? "thread_lastpost >= ".(time()-$settings['popular_threads_timeframe']) : "");
	list($min_posts) = dbarraynum(dbquery("SELECT thread_postcount FROM ".DB_THREADS.($timeframe ? " WHERE ".$timeframe : "")." ORDER BY thread_postcount DESC LIMIT 4,1"));
	$timeframe = ($timeframe ? " AND tt.".$timeframe : "");
	$result = dbquery("
		SELECT tf.forum_id, tt.thread_id, tt.thread_subject, tt.thread_postcount
		FROM ".DB_FORUMS." tf
		INNER JOIN ".DB_THREADS." tt USING(forum_id)
		WHERE ".groupaccess('tf.forum_access')." AND tt.thread_postcount >= '".$min_posts."'".$timeframe." AND tt.thread_hidden='0'
		ORDER BY thread_postcount DESC, thread_lastpost DESC LIMIT 5");
	if (dbrows($result) != 0) {
		while($data = dbarray($result)) {
			$itemsubject = trimlink($data['thread_subject'], 25);
			echo "<li class='ht-link'><a href='".FORUM."viewthread.php?thread_id=".$data['thread_id']."' title='".$data['thread_subject']."'>".$itemsubject."</a><span class='ht-reply side-small'>[".($data['thread_postcount'] - 1)."]</span></li>\n";
		}
	} else {
		echo "<li>".$locale['global_023']."</li>\n";
	}
	echo "</ul>\n";
echo "</div>\n";
}

//Latest weblinks
if (L_LINKS == 1) {
echo "<!-- Latest Weblinks -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['latest_weblinks']."</h4><ul>\n";
	$result = dbquery("SELECT * FROM ".DB_WEBLINKS." ORDER BY weblink_datestamp DESC LIMIT 5");
	if (dbrows($result) != 0) {
		while($data = dbarray($result)) {
			$itemsubjectlink = trimlink($data['weblink_name'], 25);
			$itemdescriptionlink = trimlink($data['weblink_description'], 50);
			echo "<li><a href='".$data['weblink_url']."' title='".$itemdescriptionlink."' target='_blank'>".$itemsubjectlink."</a></li>\n";
		}
	} else {
		echo "<li>".$locale['no_links']."</li>\n";
	}
	echo "</ul>\n";
echo "</div>\n";
}

//Custom weblinks
if (C_LINKS == 1) {
echo "<!-- Custom Weblinks -->";
echo "<div class='links-section flleft'>";
	echo "<h4>".$locale['custom_links']."</h4><ul>\n";
		$wlids = explode(".", C_LINKS_LIST); $i = 1;
		foreach($wlids as $key => $value) {
			if(empty($value)) {
				unset($wlids[$key]);
			}
		}
		if (!empty($wlids)) {
			foreach ($wlids as $wlid) {
				$result = dbquery("SELECT * FROM ".DB_WEBLINKS." WHERE weblink_id='".$wlid."' LIMIT 1");
				while ($data = dbarray($result)) {
					if ($i < 6) {
						$itemsubjectlink = trimlink($data['weblink_name'], 25);
						$itemdescriptionlink = trimlink($data['weblink_description'], 50);
						echo "<li><a href='".$data['weblink_url']."' title='".$itemdescriptionlink."' target='_blank'>".$itemsubjectlink."</a></li>\n";
					$i++;
					}
				}
			}
		} else {
			echo "<li>".$locale['no_links']."</li>\n";
		}
	echo "</ul>\n";
echo "</div>\n";
}