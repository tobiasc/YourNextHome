$(document).ready(function() {
	// Berlin centrum sat som default
	window.lat = 52.517474;
	window.lng = 13.405526;

	window.search_map_places = [];
	window.search_map_places_new = [];
	window.infowindow = new google.maps.InfoWindow();
	initializeSearchMap();
	populateInterests();
	populateTags();
	enableSliders();
	populateShortlist();

	$(function() {
		$("#accordion").accordion({
			collapsible: true,
			heightStyle: "content",
			active: false
		});
	});
});

