<?php
require_once('functions.php');

// go through all websites
$websites = array('im24');
foreach($websites as $key => $val){
	require_once('fetch_'.$val.'.php');
	eval('fetch_'.$val.'();');
}

// create the list of distinct tags
create_tags_list();

// close any open db's
close_db();

// inserts a list of distinct tags
function create_tags_list(){
	$places = get_db_collection('places');
	
	// remove the old tags
	$tags = get_db_collection('tags');
	$tags->remove();

	// insert the list of distinct tags
	$tags_array = array();
	$obj = array();
	$return = array('tags' => 1, '_id' => 0);
	$cursor = $places->find($obj, $return);
	foreach($cursor as $place){
		if(is_array($place['tags']) && sizeof($place['tags'] > 0)){
			foreach($place['tags'] as $key => $tag){
				if(!isset($tags_array[$tag])){
					$tags->insert(array('tag' => $tag));
					$tags_array[$tag] = 1;
				}
				array_push($tags_array, $tag);
			}
		}
	}
}

// get lat/lng from address, this uses Google Maps API
function getLatLng($address){
	$collection = get_db_collection('addresses');

	// set default object
	$data = array('lat' => '', 'lng' => '');

	// get cached location
	$obj = array('address' => $address);
	$return = array('lat' => 1, 'lng' => 1, '_id' => 0);
	$loc = $collection->findOne($obj, $return);
	if($loc !== null){
		$data = $loc;

	} else {
		$location = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false'));
		if(isset($location->results[0]->geometry)){
			$first_location = $location->results[0]->geometry;
			$data = array('lat' => $first_location->location->lat, 'lng' => $first_location->location->lng);
			$insert = array('lat' => $data['lat'], 'lng' => $data['lng'], 'address' => $address);
			$collection->update($obj, $insert, array('upsert' => true));
		}
		sleep(1);
	}

	return $data;
}
?>
