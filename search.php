<?php
// MongoDB commands:
// db.places.insert({'loc': {'lat':52.510475, 'lng':13.419431}, 'title':'Ohmstr 10', 'price':600, 'rooms':1, 'size':45, 'img':'http://54.228.248.212/imapper/includes/room1.jpg', 'url':'http://www.immobilienscout24.de/expose/67980763'});
// db.places.insert({'loc': {'lat':52.493808, 'lng':13.382451}, 'title':'Hornstr 2', 'price':400, 'rooms':1, 'size':20, 'img':'http://54.228.248.212/imapper/includes/room2.jpg', 'url':'http://www.immobilienscout24.de/expose/67980763'});
// db.places.ensureIndex({'loc': '2d'});

require_once('functions.php');

$places = array();

$max_lat = (isset($_REQUEST['max_lat']))? floatval($_REQUEST['max_lat']): 90;
$min_lat = (isset($_REQUEST['min_lat']))? floatval($_REQUEST['min_lat']): -90;
$max_lng = (isset($_REQUEST['max_lng']))? floatval($_REQUEST['max_lng']): 180;
$min_lng = (isset($_REQUEST['min_lng']))? floatval($_REQUEST['min_lng']): -180;

// create default query
$obj = array();
array_push($obj['$and'], array('loc' => array('$within' => array('$box' => array(
	array($max_lat, $max_lng), array($min_lat, $min_lng))))));

// foreach request parameter
$params = array('min_size' => '$gte', 'max_size' => '$lte', 'min_price' => '$gte', 'max_price' => '$lte', 'min_rooms' => '$gte', 'max_rooms' => '$lte');
foreach($params as $key => $val){
	if(isset($_REQUEST[$key]) && $_REQUEST[$key] !== ''){
		array_push($query_obj['$and'], array(substr($key, 4) => array($val => (int)$_REQUEST[$key])));
	}
}

// return keys
$return = array('loc' => 1, 'vendor_name' => 1, 'vendor_link' => 1, 'url' => 1, 'title' => 1, 
	'img' => 1, 'price' => 1, 'size' => 1, 'rooms' => 1, 'tags' => 1, 'address' => 1);

// fire query
$collection = get_db_collection('places');
$cursor = $collection->find($obj, $return);
foreach($cursor as $place){
	array_push($places, array(
		'id' => $place['_id']->{'$id'}, 
		'title' => $place['title'], 
		'lat' => $place['loc']['lat'], 
		'lng' => $place['loc']['lng'],
		'rooms' => $place['rooms'], 
		'img' => $place['img'], 
		'price' => $place['price'], 
		'url' => $place['url'], 
		'size' => $place['size']));
}
$data['places'] = $places;

// close any open db's
close_db();

echo json_encode($data);
?>
