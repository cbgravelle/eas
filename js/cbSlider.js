$(function() {

	$slider = $('#slider').anythingSlider({ 
		buildStartStop: false,
		buildNavigation: false,
		forwardText: '>',
		backText: '<'
	});

	$(document).bind('keydown',function(e) {
		if (e.which == 32) {
			return false;
		}
	});



	$('a.cbthumb').bind('click', function() {
		$('.thumbnails').removeClass('active');
		window.scrollTo(0,0);
		$('#slider').anythingSlider($(this).attr('href').substring(1));
		return false;
	});

	$('a.showalllink').bind('click', function() {
		$('.thumbnails').addClass('active');
		return false;
	});

	theLocation = document.location.href.toString();

	fragment = theLocation.split('&')[1];
	panel = fragment.split('-')[1];

	console.log('panel: ' + panel);
	if (panel > 1) {
		$('#slider').anythingSlider(panel);
	}
	


});