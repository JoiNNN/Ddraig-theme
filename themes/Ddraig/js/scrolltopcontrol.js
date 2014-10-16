/**
 *	Smooth scroll to top script with
 *	stop on mouse clicks or mousewheel
 *	Author: JoiNNN
 *
 *  Released as free software without warranties under GNU Affero GPL v3. 
 */

// On page load if hash found in url scroll to coresponding anchor
var hash = window.location.hash,
	target_offset = $(hash).offset(),
	clean = $("a[href=" + hash + "]").hasClass("clean");
if (target_offset && !clean) {
	var target_top = target_offset.top;
	$("html, body").animate({scrollTop:target_top}, 500);
}

// On click
$(".scroll").click(function(e){
	e.preventDefault();
	e.stopPropagation();

	var hash = $(this).prop("hash"),
		target_offset = $(hash).offset(),
		target_top = target_offset.top;

	$("html, body").animate({scrollTop:target_top}, 500);
	if(!$(this).hasClass("clean")) {
		window.location.hash = hash;
	}
});

// Stop scrolling if mousewheel or clicks are hit
$(document).bind("mousewheel DOMMouseScroll mousedown", function() {
	$("html, body").stop()
});