/** 
*  jQuery FlexMenu plugin
*
*  Author: JoiNNN
*  Description: If a list is too long for all items to fit on one line, display a popup menu instead
*/

(function ($) {

	$.fn.flexMenu = function (options) {
		// Plugin settings
		var defaults = {
			activeClass: '.active',
			hideAll: false,
			hideOnMouseOut: true
		};
		// Get custom settings if any
		settings = $.extend({}, defaults, options);
		var $s = settings;
		
		var resizeMenu;
		var el = $(this); // this object(menu)

		// Monitor for changes
		$( window ).on('resize orientationchange load', function() {
			clearTimeout(resizeMenu);
			resizeMenu = setTimeout(function () {
				adjustFlexMenu();
			}, 100);
		});

		// Animate menu dropdown
		el.find('.responsive-menu-button').live('click', function(e) {
			e.preventDefault();

			toggleMenu();
		});
		// Hide the menu on mouseout
		if ($s.hideOnMouseOut) {
			el.find('.responsive-menu').live('mouseleave', function() {
				if ($(this).hasClass('active')) toggleMenu();
			});
		}

		function toggleMenu() {
			var menu = el.find('.responsive-menu'),
				menu_ul = el.find('.responsive-menu ul');

			$(menu_ul).stop(true, true).slideToggle('fast');
			$(menu).toggleClass('active');
		}

		function adjustFlexMenu() {
			//console.info('*** Adjusting menu for ' + el.attr('class'));

			// Reset everything before adjusting the menu
			el.find('.responsive-menu').removeClass('active');
			el.find('.responsive-menu ul').hide();

			// Add a link to the responsive menu
			addToMenu();

			// Remove a link from the responive menu
			removeFromMenu();

			// Remove responsive menu if there are no links in it
			if (!el.find('.responsive-menu ul li').length) el.find('.responsive-menu').remove();
		}

		function isSpace($check_hidden) {
			var menuWidth = el.outerWidth();
			var linksWidth = 20;
			var li = el.children('li:visible').not('.responsive-menu');

			// Add up the width of each link
			li.each(function() {
				linksWidth += $(this).outerWidth(true);
			});

			// Add up the width of the 1st hidden link too?
			if ($check_hidden) {
				linksWidth += el.children('li:hidden').first().outerWidth(true);
			}
			
			if (linksWidth < menuWidth) {
				//console.info('- space NOT FILLED by visible links! ' + linksWidth + 'px over ' + menuWidth + 'px')
				return true;
			} else {
				//console.warn(($check_hidden ? '- but ' : '- ') + 'FILLED by visible links' + ($check_hidden ? ' + 1st invisible link! ' : '! ') + linksWidth + 'px over ' + menuWidth + 'px')
				return false;
			}
		}

		function addToMenu() {
			// Check if there is no space in the menu and IF there are links that can be hidden
			if (($s.hideAll || !isSpace(false)) && el.children('li:visible').not('.responsive-menu').not($s.activeClass).length) {
				// Current links
				var li = el.children('li:visible').not('.responsive-menu').not($s.activeClass).last();
				// The links overflow, do we need to add the menu?
				if (!el.find('.responsive-menu').length) el.append('<li class=\'responsive-menu\'><a href=\'#\' class=\'responsive-menu-button\'><span></span></a><ul></ul></li>');
				// Clone current link and insert it in the responsive menu
				li.clone().prependTo(el.find('.responsive-menu ul'));
				// Hide current link
				li.hide();

				//console.info('- one item was HIDDEN!');
			} else {
				return;
			}
			addToMenu();
		}

		function removeFromMenu() {
			// Check if there is space in the menu and IF there are links in the responsive menu
			if (!$s.hideAll && isSpace(true) && el.find('.responsive-menu ul li').length) {
				// Remove the 1st link from responsive menu
				el.find('.responsive-menu li').first().remove();
				// Show 1st hidden link in the menu
				el.children('li:hidden').first().show();

				//console.info('- one item was DISPLAYED!');
			} else {
				return;
			}
			removeFromMenu();
		}

	}

})(jQuery);