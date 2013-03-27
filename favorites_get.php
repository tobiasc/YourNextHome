<?php
require_once('functions.php');

// return object
$data = array();

if(isset($_REQUEST['fb_id']) && $_REQUEST['fb_id'] !== ''){

	// create default query
	$key = array('fb_id' => $_REQUEST['fb_id']);

	// fire query
	$collection = get_db_collection('shortlists');
	$places = get_db_collection('places');

	$cursor = $collection->find($key);
	foreach($cursor as $item){
		// create default query
		$places_key = array('_id' => new MongoId($item['place_id']));
		// return keys
		$return = array('url' => 1, 'title' => 1, '_id' => 0);

		$place = $places->findOne($places_key, $return);

		$place['id'] = $item['place_id'];

		array_push($data, $place);
	}

	// close any open db's
	close_db();
}

echo json_encode($data);
?>
