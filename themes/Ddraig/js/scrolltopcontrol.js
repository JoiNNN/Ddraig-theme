/**
 *	Smooth scroll to top script with
 *	stop on mouse clicks or mousewheel
 *	Author: JoiNNN
 *
 *	Requires: jquery.mousewheel.js
 *
 *  Released as free software without warranties under GNU Affero GPL v3. 
 *  Copyright (c) 2002 - 2012 by Nick Jones.
 */

//on click
$(".scroll").click(function(e){
	//prevent the default action for the click event
	e.preventDefault();
	var el = this,
		full_url = this.href,
		parts = full_url.split("#"),
		trgt = parts[1],
		target_offset = $("#"+trgt).offset(),
		target_top = target_offset.top;
	//goto that anchor by setting the body scroll top to anchor top
	$("html, body").animate({scrollTop:target_top}, 500, function() {
		//add the hash in url if scrolling is complete
		if(!$(el).hasClass("clean")) {
			window.location.hash = trgt;
		}
	});
});
//on page load if hash found in url scroll to coresponding anchor
var hash = window.location.hash,
	target_offset = $(hash).offset(),
	clean = $("a[href=" + hash + "]").hasClass("clean");
if (target_offset && !clean) {
	var target_top = target_offset.top;
	//scroll
	$("html, body").animate({scrollTop:target_top}, 500);
}
//stop scrolling if mousewheel or clicks are hit
$(document).bind("mousewheel mousedown", function(ev) {
	$("html, body").stop()
});