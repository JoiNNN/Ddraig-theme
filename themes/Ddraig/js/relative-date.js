/** 
 *  Human readable date, jQuery plugin
 *  Developed to be used with Ddraig theme for PHP-Fusion
 *  Author: JoiNNN
 * 
 *  Released as free software without warranties under GNU Affero GPL v3. 
 *  Copyright (c) 2002 - 2012 by Nick Jones.
 */
(function ($) {
	'use strict';
	$.fn.extend({
		//plugin name
		toRelativeTime: function (options) {
			var settings = {
				live: true,			// update time and dates in real time
				ampm: false,		// convert time to 12-hour AM/PM format
				monthName: true,	// months name
				monthShort: true,	// short months name
				dayName: true,		// days name
				dayShort: false,	// short days name

				seconds: 'A moment ago',
				minute: '1 minute ago',
				minutes: '%minutes% minutes ago',
				today: 'Today at %time%',
				yesterday: 'Yesterday at %time%',
				thisWeek: '%day% at %time%',
				other: '%month% %day%, %year%',
				monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
				monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
				dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
				dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']

			};
			if (options) { $.extend(settings, options); } // check and get custom settings if any is set
			var $s = settings; // settings array

			// Build date and time text
			$.getRelativeTime = function (now, then, diff) {
				var nday		= now.getDay();			// today day number of the week
				var yday		= new Date(now.setDate(now.getDate()-1));	// yesterday

				var year		= then.getFullYear();	// year, four digits
				var monthNumber	= then.getMonth();		// month number 0-11
				var month		= ($s.monthName ? ($s.monthShort ? $s.monthNamesShort[monthNumber] : $s.monthNames[monthNumber]) : monthNumber);	// month, can be number or name, normal or short 
				var dayOfW		= then.getDay();		// day number of the week 0-6
				var dayOfM		= then.getDate();		// day number of the month 1-31
				var day			= ($s.dayName ? ($s.dayShort ? $s.dayNamesShort[dayOfW] : $s.dayNames[dayOfW]) : dayOfM);	// day, can be number or name, normal or short
				var hour		= then.getHours();		// the hour 0-23
				if ($s.ampm) {
					var ap = "AM";
					if (hour > 11) { ap = "PM"; }
					if (hour > 12) { hour = hour - 12; }
					if (hour === 0) { hour = 12; }
				}
				var mins		= (then.getMinutes() < 10 ? '0' + then.getMinutes() : then.getMinutes());	// add zero to minutes < 10 (12:5 => 12:05)
				var time		=  hour + ':' + mins + ($s.ampm ? ' ' + ap : '');	// the time

				// less than a minute ago
				if (diff < 60) {
					return $s.seconds;
				// less than 2 minutes
				} else if (diff > 60 && diff < 60 * 2) {
					return $s.minute;
				// less than 1 hour
				} else if (diff >= 60 * 2 && diff < 60 * 60) {
					return $s.minutes.
							replace(/%minutes%/i, Math.floor(diff / 60));
				// today, more than 1 hour, less than 24 hours, same day of week
				} else if (diff >= 60 * 60 && diff < 60 * 60 * 24 && nday === dayOfW) {
					return $s.today.
							replace(/%time%/i, time);
				// yesterday, less than 48 hours
				} else if (diff < 60 * 60 * 24 * 2 && dayOfM === yday.getDate()) {
					return $s.yesterday.
							replace(/%time%/i, time);
				// last 7 days
				} else if (diff > 60 * 60 * 24 && diff < 60 * 60 * 24 * 7) {
					return $s.thisWeek.
							replace(/%day%/i, day).
							replace(/%time%/i, time);
				// any other
				} else {
					return $s.other.
							replace(/%month%/i, month).
							replace(/%day%/i, dayOfM).
							replace(/%year%/i, year);
				}
			};

			return this.each(function () {
				var el = $(this),
					$t = new Date(el.attr('title') ? el.attr('title') : el.text());

					if ($t && !isNaN(Date.parse($t))) { // Make sure the element has a title or valid date as inner text
						var $n = new Date(timenow),
							$drel = el.attr('rel'),
							$d = ($drel ? $drel : (Math.round(Date.parse($n) - Date.parse($t)) / 1000));

						// Update intervals based on time difference
						var i = 60 * 60; // default update interval, each 60 mins
						if ($d < 60) { // interval is less than a minute, update each 10 sec
							var i = 10;	
							var $d = +$d + +i;
							if ($d > 60) {
								var i = 30;
							}
						} else if ($d >= 60 && $d < 60 * 60) { // interval is higher than 1 minute and less than 1 hour, update each 30 sec
							var i = 30;
							var $d = +$d + +i;
						}

						if ($drel) {	// if the element has 'rel' attribute a conversion was already done
							el.attr({'rel' : $d});
							$d = el.attr('rel');
						} else {		// no conversion was done, add the inner text as title and 'rel' attribute
							el.attr({'title' : el.text(), 'rel' : $d});
						}

						el.text($.getRelativeTime($n, $t, $d));	// convert and update the inner text

						if ($s.live) { // if refresh is enabled	
							setTimeout(function(){
								el.toRelativeTime(options);
							}, i * 1000);
						}
					}
			});
		}
	});
})(jQuery);