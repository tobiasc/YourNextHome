<?php
require_once('functions.php');

// return object
$data = array();

// create default query
$obj = array();

// return keys
$return = array('name' => 1, '_id' => 1);

// fire query
$collection = get_db_collection('interests');
$cursor = $collection->find($obj, $return);
foreach($cursor as $interest){
	$interest['id'] = $interest['_id']->{'$id'};
	unset($interest['_id']);
	array_push($data, $interest);
}

// close any open db's
close_db();

echo json_encode($data);
?>
