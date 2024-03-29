var search = function(){
	// if the map bounds cannot be found use default values (the entire Earth)
	var bounds = window.search_map.getBounds();
	if(typeof bounds !== 'undefined'){
		max_lat = bounds["Z"]["d"];
		min_lat = bounds["Z"]["b"];
		max_lng = bounds["fa"]["d"];
		min_lng = bounds["fa"]["b"];
	} else {
		max_lat = 90;
		min_lat = -90;
		max_lng = 180;
		min_lng = -180;
	}

	// create the tags query parameter
	var tags = '';
	$('.tag_class').each(function(index) {
		if($(this).attr('checked')) {
			tags += $(this).val() + ',';
		}
	});

	// create the tags query parameter
	var interests = '';
	$('.interest_class').each(function(index) {
		if($(this).attr('checked')) {
			interests += $(this).val() + ',';
		}
	});
	
	// do the search
	$.post(yournexthome_server + "place_get.php", {
			"max_lat": max_lat, 
			"min_lat": min_lat, 
			"max_lng": max_lng, 
			"min_lng": min_lng,
			"max_size": $("#max_size").html(),
			"min_size": $("#min_size").html(),
			"max_price": $("#max_price").html(),
			"min_price": $("#min_price").html(),
			"max_rooms": $("#max_rooms").html(),
			"min_rooms": $("#min_rooms").html(),
			"tags": tags,
			"interests": interests
		}, function(data) {

			// only insert markers that are not already on the map
			for(var place in data["places"]){
				var place_id = data["places"][place].id;
				if(typeof window.search_map_places[place_id] === 'undefined'){
					var icon = 'includes/accuracy_3.png';
					if(data['places'][place].address_accuracy === 1){
						icon = 'includes/accuracy_1.png';
					} else if(data['places'][place].address_accuracy === 2){
						icon = 'includes/accuracy_2.png';
					} else {
						icon = 'includes/accuracy_3.png';
					}
					window.search_map_places_new[place_id] = new google.maps.Marker({
						'position': new google.maps.LatLng(data["places"][place].lat, data["places"][place].lng),
						'map': window.search_map,
						'title': data["places"][place].title,
						'custom_data': data["places"][place],
						'icon': icon,
						'custom_id': place_id
					});
					google.maps.event.addListener(window.search_map_places_new[place_id], "click", function() {
						var content = '<img src="' + this.custom_data.img + '"><br><br>' + 
							'<strong>' + this.custom_data.title + '</strong><br><br>' +
							'Price: €' + this.custom_data.price + '<br>' +
							'Size: ' + this.custom_data.size + 'm2<br>' +
							'Rooms: ' + this.custom_data.rooms + '<br><br>' +
							'<a href="' + this.custom_data.url + '" target="_blank">ImmoScout24</a>';
						if(typeof user_id != 'undefined'){
							content += '<div style="float:right;"><button type="button" place_id="' + this.custom_data.id + '" user_id="' + user_id + '" class="btn btn-success add_to_shortlist">Add To Shortlist</button></div>';
						}
						window.infowindow.setContent(content);
						window.infowindow.open(window.search_map, this);

						// set shortlist action listener
						addShortlistButtonClick();
					});
				} else {
					window.search_map_places_new[place_id] = window.search_map_places[place_id];
				}
			}
			// remove markers that are not in the new search result
			for(var place in window.search_map_places){
				if(typeof window.search_map_places_new[place] === 'undefined'){
					window.search_map_places[place].setMap(null);
					delete window.search_map_places[place];
				}
			}
			window.search_map_places = window.search_map_places_new;
			window.search_map_places_new = [];
			
		}, "json");
}

var centerMap = function(map){
	if (navigator.geolocation) {
	    	navigator.geolocation.getCurrentPosition(function(position){
				window.lat = position.coords.latitude;
				window.lng = position.coords.longitude;
				map.panTo(new google.maps.LatLng(window.lat, window.lng));
				search();
			}, function(){
				console.log('browser location failed');
			});
	}
};

var initializeSearchMap = function() {
	var yourStartLatLng = new google.maps.LatLng(window.lat, window.lng);
	var mapOptions = {
		zoom: 13,
		center: yourStartLatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	window.search_map = new google.maps.Map(document.getElementById("search_map_canvas"), mapOptions);
	//centerMap(window.search_map);
	google.maps.event.addListener(window.search_map, "dragend", function() {
		search();
	});
	google.maps.event.addListener(window.search_map, "zoom_changed", function() {
		search();
	});
	search();
};

// enable sliders on page load
var enableSliders = function() {
	// price
	$("#price_slider").slider({
		range: true,
		min: 0,
		max: 4000,
		step: 50,
		values: [0, 4000],
		slide: function( event, ui ) {
			$("#min_price").html(ui.values[0]);
			$("#max_price").html(ui.values[1]);
		},
		change: function(event, ui) {
			$("#min_price").html(ui.values[0]);
			$("#max_price").html(ui.values[1]);
			search();
		}
	});
	$("#min_price").html($("#price_slider").slider("values", 0));
	$("#max_price").html($("#price_slider").slider("values", 1));

	// size
	$("#size_slider").slider({
		range: true,
		min: 0,
		max: 300,
		step: 5,
		values: [0, 300],
		slide: function( event, ui ) {
			$("#min_size").html(ui.values[0]);
			$("#max_size").html(ui.values[1]);
		},
		change: function(event, ui) {
			$("#min_size").html(ui.values[0]);
			$("#max_size").html(ui.values[1]);
			search();
		}
	});
	$("#min_size").html($("#size_slider").slider("values", 0));
	$("#max_size").html($("#size_slider").slider("values", 1));

	// rooms
	$("#rooms_slider").slider({
		range: true,
		min: 0,
		max: 10,
		step: 1,
		values: [0, 10],
		slide: function( event, ui ) {
			$("#min_rooms").html(ui.values[0]);
			$("#max_rooms").html(ui.values[1]);
		},
		change: function(event, ui) {
			$("#min_rooms").html(ui.values[0]);
			$("#max_rooms").html(ui.values[1]);
			search();
		}
	});
	$("#min_rooms").html($("#rooms_slider").slider("values", 0));
	$("#max_rooms").html($("#rooms_slider").slider("values", 1));
};

var populateInterests = function(){
	// do the search
	$.post(yournexthome_server + "interests_get.php", {}, function(data) {
		var tagContent = '';
		for(var interest in data){
			tagContent += '<input type="checkbox" class="interest_class" value="'+ data[interest]['id'] + '"> ' + data[interest]['name'] + '<br>';
		}
		$('#interests').html(tagContent);
		$(".interest_class").click(function(){
			search();
		});
	}, "json");
};

var populateTags = function(){
	// do the search
	$.post(yournexthome_server + "tags_get.php", {}, function(data) {
		var tagContent = '';
		for(var tag in data){
			tagContent += '<input type="checkbox" class="tag_class" value="'+ data[tag] + '"> ' + data[tag] + '<br>';
		}
		$('#extras').html(tagContent);
		$(".tag_class").click(function(){
			search();
		});
	}, "json");
};

var populateShortlist = function(){
	$('#shortlist').html('');
	if(typeof fb_id != 'undefined'){
		$.post(yournexthome_server + "favorites_get.php", {
			'user_id': user_id
			}, function(data) {
				var shortlistContent = '<ul>';
				for(var i in data){
					shortlistContent += '<li><a href="' + data[i]['url'] + '" target="_blank">' + data[i]['title'] + '</a>' + 
						' <button type="button" place_id="' + data[i]['id'] + '" fb_id="' + fb_id + '" class="btn-mini btn btn-danger remove_from_shortlist">X</button></li>';
				}
				shortlistContent += '</ul>';
				$('#shortlist').html(shortlistContent);
				removeShortlistButtonClick();
			}, "json"
		);
	}
};

var addShortlistButtonClick = function(){
	$(".add_to_shortlist").click(function(){
		$(this).attr('disabled', 'disabled');
		$.post(yournexthome_server + "favorites_update.php", {
			'user_id': $(this).attr('user_id'),
			'place_id': $(this).attr('place_id'),
			'action': 'insert'
		}, function(){
			populateShortlist();
		});
	});
};

var removeShortlistButtonClick = function(){
	$(".remove_from_shortlist").click(function(){
		$.post(yournexthome_server + "favorites_update.php", {
			'user_id': $(this).attr('user_id'),
			'place_id': $(this).attr('place_id'),
			'action': 'remove'
		}, function(){
			populateShortlist();
		});
	});
};

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

