<?php
/*-------------------------------------------------------+
| PHP-Fusion Content Management System
| Copyright (C) 2002 - 2013 Nick Jones
| http://www.php-fusion.co.uk/
+--------------------------------------------------------+
| Filename: ddraig_tcpanel_admin.php
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
//Check rights
if (!checkrights("DDCP") || !defined("iAUTH") || $_GET['aid'] != iAUTH) { redirect("../../index.php"); }

require_once THEMES."templates/admin_header.php";
//Locales
if (file_exists(INFUSIONS."ddraig_theme_tcpanel/locale/".$settings['locale'].".php")) {
	include INFUSIONS."ddraig_theme_tcpanel/locale/".$settings['locale'].".php";
} else {
	include INFUSIONS."ddraig_theme_tcpanel/locale/English.php";
}

require_once INCLUDES."infusions_include.php"; //used for set_setting function

//Get settings from DB
if (!isset($setting)) {
	$setting = getsettings('ddraig_theme_tcpanel');
}

//Select options
$select_opt = array(
	1 => array(	"desc" => $locale['tcp_enabled'],
				"color" => "green"),
	0 => array(	"desc" => $locale['tcp_disabled'],
				"color" => "red")
	);

///////////////////////////
// Saving settings
///////////////////////////
if (isset($_POST['save_settings'])) {
	//Check and get all inputs values
	function check_input($name, $values="", $default="") {
		$res = "";
		//For inputs with predefined values
		if (isset($name) && $values != "" && $default != "") {
			if (isset($_POST[$name]) && isnum($_POST[$name]) && array_key_exists($_POST[$name], $values)) {
					$res = stripinput($_POST[$name]);
				} else {
					$res = $default;
				}
		} elseif (isset($name)) {
		//For inputs with no predefined values
		if (isset($_POST[$name]) && isnum($_POST[$name])) {
				$res = stripinput($_POST[$name]);
			} else {
				$res = "0";
			}
		}
		return $res;
	}
	$theme_maxwidth 		= check_input('theme_maxwidth');
	$theme_minwidth 		= check_input('theme_minwidth');
	$home_icon				= check_input('home_icon',		$select_opt, '1');
	$winter_mode 			= check_input('winter_mode',	$select_opt, '0');
	$thread_preview			= check_input('thread_preview',	$select_opt, '1');
	$search_in_header		= check_input('search_in_header',$select_opt, '1');
	$latest_news 			= check_input('latest_news');
	$newest_threads 		= check_input('newest_threads');
	$latest_articles 		= check_input('latest_articles');
	$hottest_threads 		= check_input('hottest_threads');
	$latest_links 			= check_input('latest_links');
	$custom_links			= "0";
	$custom_links_list		= $setting['custom_links_list'];
		//If the checkbox is checked get IDs from input field
		if (isset($_POST['cbox_custom_links']) && $_POST['cbox_custom_links'] == 1) {
			$custom_links = "1";
			$custom_links_list = preg_replace(array('#(\.\.+)#', '#^[\.]|[^\d|\.]|[\.]$#'), array('.', ''), stripinput($_POST['custom_links'])); //cleanup the input, no multiple dots ahead or trailing
		}
	$relative_time	= "0";
	$relative_time_elements	= $setting['relative_time_elements'];
		if (isset($_POST['cbox_relative_time']) && $_POST['cbox_relative_time'] == 1) {
			$relative_time = "1";
			$relative_time_elements = stripinput($_POST['relative_time']);
		}
	//theme_maxwidth_forum should not be lower than MinWidth
	if (isset($_POST['cbox_theme_maxwidth_forum']) && $theme_maxwidth_forum < $theme_minwidth) {$theme_maxwidth_forum = $theme_minwidth;}
	$theme_maxwidth_forum = "0";
		//If the checkbox is checked get width from input field
		if (isset($_POST['cbox_theme_maxwidth_forum']) && isnum($_POST['theme_maxwidth_forum'])) {
			$theme_maxwidth_forum = stripinput($_POST['theme_maxwidth_forum']);
		}
		//theme_maxwidth_forum should not be lower than MinWidth
		if (isset($_POST['cbox_theme_maxwidth_forum']) && $theme_maxwidth_forum < $theme_minwidth) {$theme_maxwidth_forum = $theme_minwidth;}
		
	$theme_maxwidth_admin = "0";
		//If the checkbox is checked get width from input field
		if (isset($_POST['cbox_theme_maxwidth_admin']) && isnum($_POST['theme_maxwidth_admin'])) {
			$theme_maxwidth_admin = stripinput($_POST['theme_maxwidth_admin']);
		}
		//theme_maxwidth_admin should not be lower than MinWidth
		if (isset($_POST['cbox_theme_maxwidth_admin']) && $theme_maxwidth_admin < $theme_minwidth) {$theme_maxwidth_admin = $theme_minwidth;}

	//Check if any width field is empty
	if (empty($theme_maxwidth) || empty($theme_minwidth)) {
		$error_msg = $locale['tcp_invalid'];
		//MaxWidth should not be lower than MinWidth
		} elseif ($theme_maxwidth < $theme_minwidth) {
		$error_msg = $locale['tcp_maxwidth_low'];
		//If all is good, update settings
		} else {
		//Update the settings
		$inf = "ddraig_theme_tcpanel";
		set_setting('theme_maxwidth',			$theme_maxwidth, 		$inf);
		set_setting('theme_minwidth',			$theme_minwidth,		$inf);
		set_setting('theme_maxwidth_forum', 	$theme_maxwidth_forum,	$inf);
		set_setting('theme_maxwidth_admin', 	$theme_maxwidth_admin,	$inf);
		set_setting('home_icon',				$home_icon,				$inf);
		set_setting('winter_mode',				$winter_mode,			$inf);
		set_setting('search_in_header',			$search_in_header,		$inf);
		set_setting('thread_preview',			$thread_preview,		$inf);
		set_setting('latest_news',				$latest_news,			$inf);
		set_setting('latest_articles',			$latest_articles,		$inf);
		set_setting('newest_threads',			$newest_threads,		$inf);
		set_setting('hottest_threads',			$hottest_threads,		$inf);
		set_setting('latest_links',				$latest_links,			$inf);
		set_setting('custom_links',				$custom_links,			$inf);
		set_setting('custom_links_list',		$custom_links_list,		$inf);
		set_setting('relative_time',			$relative_time,			$inf);
		set_setting('relative_time_elements',	$relative_time_elements,$inf);

		redirect(FUSION_SELF.$aidlink."&amp;status=su"); //Settings updated, redirect
	}
}
//Render input function
function render_input($val="", $type="", $values="", $maxlen="2", $default="") {
	global $setting, $locale; $res="";
	//Text inputs
	if ($type == "input") {
		$res = "<input name='$val' id='$val' value='".$setting[$val]."' size='10' type='text' maxlength='$maxlen' class='textbox input' />";
	//Text inputs with checkbox
	} elseif ($type == "cboxinput") {
		$checked = "checked='checked'";
		$disabled = "";
		if ($setting[$val] == 0) {
			$checked = "";
			$disabled = "disabled='disabled'";
			$setting[$val] = $default;
		}
		$res = "<input type='checkbox' name='cbox_".$val."' id='cbox_".$val."' ".$checked." value='0' />";
		$res .= "<input name='$val' id='$val' value='".$setting[$val]."' ".$disabled." size='10' type='text' maxlength='$maxlen' class='textbox cboxinput' />";
	//Checkbox inputs
	} elseif ($type == "cbox") {
		$checked = "checked='checked'";
		if ($setting[$val] == 0) {
			$checked = "";
		}
		$res = "<input type='checkbox' name='".$val."' id='".$val."' ".$checked." value='".$setting[$val]."' />";
	//Select inputs
	} elseif ($type == "select") {
		$res = "<select class='textbox select' name='$val' id='$val'>";
		foreach($values as $key => $value){
			$selected = "";
			if ($setting[$val] == $key) {
				$selected = "selected='selected'";
			}
			$res .= "<option style='color:".$value['color']."' value='$key' ".$selected.">".$value['desc']."</option>";							 
		}
		$res .= "</select>";
		if ($setting[$val] == 0) {
			$res .= " <img src='".IMAGES."no.png' width='16' height='16' alt='".$locale['tcp_disabled']."' />";
		} else {
			$res .= " <img src='".IMAGES."yes.png' width='16' height='16' alt='".$locale['tcp_enabled']."' />";
		}
	}
	return $res;
}
//////////////////////////
// Render settings inputs
//////////////////////////
opentable($locale['tcp_title']);
echo "<form name='save_settings' method='post' action='".FUSION_SELF.$aidlink."'>
			<table class='settings center' width='100%' cellspacing='0'> 
			<tbody>
			<tr><th class='tbl2 forum-caption' colspan='4'><h3>".$locale['tcp_g_sets']."</h3></th></tr>";
$spacer = "<tr><td colspan='2'><hr /></td></tr>";

//Theme Max Width
echo "<tr>
			<td class='desc'><h3><label for='theme_maxwidth'>".$locale['tcp_max_w']."</label></h3>
				<p class='small'>".$locale['tcp_max_w_des']."</p>
			</td>
			<td class='inputs'>
			".render_input('theme_maxwidth', 'input', '', '4')." px
			</td>
	  </tr>";
echo $spacer;

//Theme Min Width
echo "<tr>
			<td class='desc'><h3><label for='theme_minwidth'>".$locale['tcp_min_w']."</label></h3>
				<p class='small'>".$locale['tcp_min_w_des']."</p>
			</td>
			<td class='inputs'>
			".render_input('theme_minwidth', 'input', '', '4')." px
			</td>
	  </tr>";
echo $spacer;

//Theme Max Width in Forum
echo "<tr>
		<td class='desc'><h3><label for='theme_maxwidth_forum'>".$locale['tcp_max_wf']."</label></h3>
			<p class='small'>".$locale['tcp_max_wf_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('theme_maxwidth_forum', 'cboxinput', '', '4', $setting['theme_maxwidth'])." px
		</td>
	  </tr>";
echo $spacer;

//Theme Max Width in Administration
echo "<tr>
		<td class='desc'><h3><label for='theme_maxwidth_admin'>".$locale['tcp_max_wa']."</label></h3>
			<p class='small'>".$locale['tcp_max_wa_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('theme_maxwidth_admin', 'cboxinput', '', '4', $setting['theme_maxwidth'])." px
		</td>
	  </tr>";
echo $spacer;

//Search in header
echo "<tr>
		<td class='desc'><h3><label for='search_in_header'>".$locale['tcp_search_h']."</label></h3>
			<p class='small'>".$locale['tcp_search_h_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('search_in_header', 'select', $select_opt)."
		</td>				
	  </tr>";
echo $spacer;

//Home Icon
echo "<tr>
		<td class='desc'><h3><label for='home_icon'>".$locale['tcp_home_icon']."</label></h3>
			<p class='small'>".$locale['tcp_home_icon_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('home_icon', 'select', $select_opt)."
		</td>				
	  </tr>";
echo $spacer;

//Winter Mode
echo "<tr>
		<td class='desc'><h3><label for='winter_mode'>".$locale['tcp_winter']."</label></h3>
			<p class='small'>".$locale['tcp_winter_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('winter_mode', 'select', $select_opt)."
		</td>				
	  </tr>";
echo $spacer;

//Thread preview
echo "<tr>
		<td class='desc'><h3><label for='thread_preview'>".$locale['tcp_th_prev']."</label></h3>
			<p class='small'>".$locale['tcp_th_prev_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('thread_preview', 'select', $select_opt)."
		</td>				
	  </tr>";
echo $spacer;

//Pretty relative date and time script settings
echo "<tr>
		<td class='desc' valign='top'><h3><label for='relative_time'>".$locale['tcp_rel_date']."</label></h3>
			<p class='small'>".$locale['tcp_rel_date_des']."</p>
		</td>
		<td class='inputs'>
		<input type='checkbox' name='cbox_relative_time' id='cbox_relative_time' ".($setting['relative_time'] == 1 ? "checked='checked' value='1'" : "value='0'")." />
		<input name='relative_time' id='relative_time' value='".$setting['relative_time_elements']."' ".($setting['relative_time'] == 0 ? "disabled='disabled'" : "")." size='30' type='text' maxlength='100' class='textbox cboxinput' /> element/id/class
		</td>
	  </tr>";
echo $spacer;

//Footer links
echo "<tr>
		<td class='desc' valign='top'><h3>".$locale['tcp_ftr_links']."</h3>
			<p class='small'>".$locale['tcp_ftr_links_des']."</p>
		</td>
		<td class='inputs'>
		".render_input('latest_news', 		'cbox')." <label for='latest_news'>".$locale['tcp_latest_n']."</label><br />
		".render_input('latest_articles',	'cbox')." <label for='latest_articles'>".$locale['tcp_latest_a']."</label><br />
		".render_input('newest_threads',	'cbox')." <label for='newest_threads'>".$locale['tcp_newst_t']."</label><br />
		".render_input('hottest_threads',	'cbox')." <label for='hottest_threads'>".$locale['tcp_hotest_t']."</label><br />
		".render_input('latest_links',		'cbox')." <label for='latest_links'>".$locale['tcp_latest_w']."</label><br />
		<input type='checkbox' name='cbox_custom_links' id='cbox_custom_links' ".($setting['custom_links'] == 1 ? "checked='checked' value='1'" : "value='0'")." />".$locale['tcp_custom_w']."
		<input name='custom_links' id='custom_links' value='".$setting['custom_links_list']."' ".($setting['custom_links'] == 0 ? "disabled='disabled'" : "")." size='10' type='text' maxlength='100' class='textbox cboxinput' /> IDs (separated by a dot)
		</td>				
	  </tr>";
echo $spacer;

//Save Button
echo "<tr>";
echo "<td colspan='3' align='center'><br /><input type='submit' name='save_settings' value='".$locale['tcp_save_sets']."' class='button' /></td>";
echo "</tr>";
		
echo "</tbody>";
echo "</table>";
echo "</form>";
add_to_head("<style type='text/css'> 
.settings h3,
.settings p {
	margin: 0;
}
.desc {
	padding-left: 40px;
}
.desc h3 {
	font-size: 13px;
}
</style>");
add_to_footer("<script type='text/javascript'>
/* <![CDATA[ */
jQuery(document).ready(function() {
	$('.settings .inputs select').change(function () {
		var color = $('option:selected', this).attr('style');
		$(this).attr('style', color);
	});

	$('.settings .inputs select').each(function () {
		var color = $('option[selected=selected]', this).attr('style');
		$(this).attr('style', color);
	});

	$('.settings input[type=checkbox]:checked').each(function() { 
       $(this).val('1')
    });

	$('.settings input[type=checkbox]').click(function() {
	var id = $(this).attr('id').replace('cbox_', '#');
  	if (this.checked) {
		$(this).val('1');
   		$(id).removeAttr('disabled');
	  } else {
	  	$(this).val('0');
   		$(id).attr('disabled', 'disabled');
	  }
	});
});
/* ]]>*/
</script>");

//Status messages
if (isset($_GET['status']) && $_GET['status'] == "su") {
	$message = $locale['tcp_sets_up'];
	replace_in_output("<!--error_handler-->", "<!--error_handler--><div id=\'close-message\'><div class=\'admin-message\'>".$message."</div></div>");
}

//If any error message is set show it
if (isset($error_msg)) { replace_in_output("<!--error_handler-->", "<!--error_handler--><div class=\'admin-message\'>".$error_msg."</div>"); };

closeside();

require_once THEMES."templates/footer.php";
?>