<?php
// MongoDB commands:
// db.places.insert({'loc': {'lat':52.510475, 'lng':13.419431}, 'title':'Ohmstr 10', 'price':600, 'rooms':1, 'size':45, 'img':'http://54.228.248.212/imapper/includes/room1.jpg', 'url':'http://www.immobilienscout24.de/expose/67980763'});
// db.places.insert({'loc': {'lat':52.493808, 'lng':13.382451}, 'title':'Hornstr 2', 'price':400, 'rooms':1, 'size':20, 'img':'http://54.228.248.212/imapper/includes/room2.jpg', 'url':'http://www.immobilienscout24.de/expose/67980763'});
// db.places.ensureIndex({'loc': '2d'});

// view-source:http://54.228.248.212/imapper/search.php?max_lat=52.516855475741664&min_lat=52.45402969990515&max_lng=13.581579779052731&min_lng=13.26177463378906&max_size=280&min_size=0&max_price=4000&min_price=0&max_rooms=10&min_rooms=0
// view-source:http://54.228.248.212/imapper/search.php?tags=balkon

require_once('functions.php');

$places = array();

$_REQUEST['max_lat'] = (isset($_REQUEST['max_lat']))? floatval($_REQUEST['max_lat']): 90;
$_REQUEST['min_lat'] = (isset($_REQUEST['min_lat']))? floatval($_REQUEST['min_lat']): -90;
$_REQUEST['max_lng'] = (isset($_REQUEST['max_lng']))? floatval($_REQUEST['max_lng']): 180;
$_REQUEST['min_lng'] = (isset($_REQUEST['min_lng']))? floatval($_REQUEST['min_lng']): -180;

// create default query
$obj = array('$and' => array());

// foreach request parameter
$params = array('min_size' => '$gte', 'max_size' => '$lte', 
	'min_lat' => '$gte', 'max_lat' => '$lte', 
	'min_lng' => '$gte', 'max_lng' => '$lte', 
	'min_price' => '$gte', 'max_price' => '$lte', 
	'min_rooms' => '$gte', 'max_rooms' => '$lte');
foreach($params as $key => $val){
	if(isset($_REQUEST[$key]) && $_REQUEST[$key] !== ''){
		if(is_float($_REQUEST[$key])){
			$_REQUEST[$key] = (float)$_REQUEST[$key];
		} else {
			$_REQUEST[$key] = (int)$_REQUEST[$key];
		}
		array_push($obj['$and'], array(substr($key, 4) => array($val => $_REQUEST[$key])));
	}
}

// handle tags
$_REQUEST['tags'] = (isset($_REQUEST['tags']))?$_REQUEST['tags']:'';
$tags = explode(',', $_REQUEST['tags']);
foreach($tags as $key => $tag){
	if($tag !== ''){
		array_push($obj['$and'], array('tags' => $tag));
	}
}

// handle interests
// first check if any interests are selected, then pull out areas and match areas (min 1 area returned)
$_REQUEST['interests'] = (isset($_REQUEST['interests']))?$_REQUEST['interests']:'';
if($_REQUEST['interests'] !== ''){
	$interests = explode(',', $_REQUEST['interests']);
	$intersect_areas = array();
	$areas = array();
	$collection = get_db_collection('areas');
	$return = array('name' => 1, 'interests.id' => 1, '_id' => 0);
	$cursor = $collection->find(array(), $return);
	foreach($cursor as $area){
		$areas[$area['name']] = array();
		foreach($area['interests'] as $i => $int){
			array_push($areas[$area['name']], $int['id']);
		}
		$intersect_areas[$area['name']] = sizeof(array_intersect($interests, $areas[$area['name']]));
	}
	arsort($intersect_areas);
	$intersect_areas_new = array();
	$old_intersects = null;
	foreach($intersect_areas as $name => $intersects){
		if($old_intersects === null){
			$old_intersects = $intersects;
		}
		if($old_intersects !== $intersects){
			break;
		} else {
			array_push($intersect_areas_new, $name);
		}
	}
	array_push($obj['$and'], array('area.major_area' => array('$in' => $intersect_areas_new)));
}

// return keys
$return = array(
	'lat' => 1, 
	'lng' => 1, 
	'vendor_name' => 1, 
	'vendor_link' => 1, 
	'url' => 1, 
	'title' => 1, 
	'img' => 1, 
	'price' => 1, 
	'size' => 1, 
	'rooms' => 1, 
	'tags' => 1, 
	'address' => 1, 
	'area.accuracy' => 1
);

// fire query
$collection = get_db_collection('places');
$cursor = $collection->find($obj, $return);
foreach($cursor as $place){
	// add jitter if the address isn't accurate, to avoid places on top of each other
	if((int)$place['area']['accuracy'] !== 1){
		$place['lat'] = (round($place['lat'] * 10000,0) + rand(0,9))/10000;
		$place['lng'] = (round($place['lng'] * 10000,0) + rand(0,9))/10000;
	}
	array_push($places, array(
		'id' => $place['_id']->{'$id'}, 
		'address_accuracy' => $place['area']['accuracy'], 
		'title' => $place['title'], 
		'lat' => $place['lat'], 
		'lng' => $place['lng'],
		'rooms' => $place['rooms'], 
		'img' => $place['img'], 
		'price' => $place['price'], 
		'url' => $place['url'], 
		'tags' => $place['tags'], 
		'size' => $place['size']));

	// Google Maps doesn't like to many markers...	
	if(sizeof($places) > 500){
		break;
	}
}
$data['places'] = $places;

// close any open db's
close_db();

echo json_encode($data);
?>
