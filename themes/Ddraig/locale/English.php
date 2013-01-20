<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: English.php
| Author: JoiNNN
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------+
| This file is part of the PHP-Fusion localization
| standard.
+--------------------------------------------------------+
| Locale: English
+--------------------------------------------------------*/

//Footer
$locale['latest_news']		= "Latest News";
$locale['no_news']			= "No news";
$locale['latest_articles']	= "Latest Articles";
$locale['no_articles']		= "No articles";
$locale['latest_weblinks']	= "Latest Weblinks";
$locale['no_links']			= "No links";
$locale['custom_links']		= "Useful Links";
$locale['scroll_top']		= "Scroll to top";

$locale['tcp_warning']		= "<div class=\'admin-message tcp-warn\'>\n
<span id=\'tcp-warn\'></span><strong>Warning:</strong> the Theme Control Panel has not been infused yet.\n
<br />To infuse it go to infusions by <a href=\'".ADMIN."infusions.php".(isset($aidlink) ? $aidlink : "")."\'>clicking here</a>.\n
</div>";

//Forums
$locale['sticky']		= "Pinned";
$locale['locked']		= "Locked";
$locale['deleted_user']	= "Deleted user";
//thread preview
$locale['prev_thread']	= "Preview this thread";
$locale['close_prev']	= "Close preview";
$locale['made']			= "made the";
$locale['first']		= "first post";
$locale['last']			= "last post";
$locale['view_thread']	= "View this thread";

//Relative time script. Since the default language of the 'Relative time script' is English lines below are not used, they serve as translation guidelines for other locales
$locale['seconds']			= "A moment ago";
$locale['minute']			= "1 minute ago";
$locale['minutes']			= "%minutes% minutes ago";
$locale['today']			= "Today at %time%";
$locale['yesterday']		= "Yesterday at %time%";
$locale['thisWeek']			= "%day% at %time%";
$locale['other']			= "%month% %day%, %year%";
$locale['monthNames']		= "['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']";
$locale['monthNamesShort']	= "['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']";
$locale['dayNames']			= "['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']";
$locale['dayNamesShort']	= "['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']";
?>