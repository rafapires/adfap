$(document).ready(function() {

	// vertical align middle
	var totalHeight = 0;
	var totalVisibleHeight = 0;
	$('section').each(function() {
	    totalHeight += $(this).height();
	});
	$('section:visible').each(function() {
	    totalVisibleHeight += $(this).height();
	});
	
	var topPadding = ((totalHeight - totalVisibleHeight) / 2) - 100;
	$('#content').animate({'padding-top': topPadding+'px'}, 800);
				
});

