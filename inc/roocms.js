/**
*	jQuery
**/
$(document).ready(function(){
	$("a[rel='img']").colorbox({maxWidth:"98%", maxHeight:"98%"});
	
	$(".corner").corner("round 4px");
	$(".button").corner("round 3px");
	
	//$("#content").corner("bottom round 10px");
	//$("h1").corner("top round 10px");	
	
	$("#menu li").prepend("<span></span>"); //Throws an empty span tag right before the a tag
	
	$("#menu li").each(function() { //For each list item...
		var linkText = $(this).find("a").html(); //Find the text inside of the a tag
		$(this).find("span").show().html(linkText); //Add the text in the span tag
	}); 
	
	$("#menu li").hover(function() {	//On hover...
		$(this).find("span").stop().animate({ 
			marginTop: "-40" //Find the span tag and move it up 40 pixels
		}, 250);
	} , function() { //On hover out...
		$(this).find("span").stop().animate({
			marginTop: "0" //Move the span back to its original state (0px)
		}, 250);
	});
});


/**
*	Java Script
**/
function open_window(link,w,h)
{
	var win = "width="+w+",height="+h+",menubar=no,location=no,resizable=yes,scrollbars=auto";
	newWin = window.open(link,'newWin',win);
	newWin.focus();
}