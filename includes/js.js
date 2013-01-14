var search = function(){
	// if the map bounds cannot be found use default values (the entire Earth)
	var bounds = window.search_map.getBounds();
	if(typeof bounds !== 'undefined'){
		max_lat = bounds["Z"]["d"];
		min_lat = bounds["Z"]["b"];
		max_lng = bounds["ca"]["d"];
		min_lng = bounds["ca"]["b"];
	} else {
		max_lat = 90;
		min_lat = -90;
		max_lng = 180;
		min_lng = -180;
	}
	
	// do the search
	$.post("http://54.228.248.212/imapper/search.php", {
			"max_lat": max_lat, 
			"min_lat": min_lat, 
			"max_lng": max_lng, 
			"min_lng": min_lng
		}, function(data) {

			// only insert markers that are not already on the map
			for(var place in data["places"]){
				var place_id = data["places"][place].id;
				if(typeof window.search_map_places[place_id] === 'undefined'){
					window.search_map_places_new[place_id] = new google.maps.Marker({
						position: new google.maps.LatLng(data["places"][place].lat, data["places"][place].lng),
						map: window.search_map,
						title: data["places"][place].title,
						custom_data: data["places"][place],
						custom_id: place_id
					});
					google.maps.event.addListener(window.search_map_places_new[place_id], "click", function() {
						var content = '<img src="' + this.custom_data.img + '"><br><br>' + 
							'<strong>' + this.custom_data.title + '</strong><br><br>' +
							'Price: €' + this.custom_data.price + '<br>' +
							'Size: ' + this.custom_data.size + 'm2<br>' +
							'Rooms: ' + this.custom_data.rooms + '<br><br>' +
							'<a href="' + this.custom_data.url + '" target="_blank">ImmoScout24</a>';
						window.infowindow.setContent(content);
						window.infowindow.open(window.search_map, this);
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

function initializeSearchMap() {
	var yourStartLatLng = new google.maps.LatLng(window.lat, window.lng);
	var mapOptions = {
		zoom: 13,
		center: yourStartLatLng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	window.search_map = new google.maps.Map(document.getElementById("search_map_canvas"), mapOptions);
	centerMap(window.search_map);
	google.maps.event.addListener(window.search_map, "dragend", function() {
		window.map_changed = true;
		search();
	});
}		

$(document).ready(function() {
	window.lat = 50;
	window.lng = 0;
	window.search_map_places = [];
	window.search_map_places_new = [];
	window.infowindow = new google.maps.InfoWindow();
	initializeSearchMap();
});
