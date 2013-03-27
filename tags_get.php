<?php
require_once('functions.php');

// return object
$data = array();

// create default query
$obj = array();

// return keys
$return = array('tag' => 1, '_id' => 0);

// fire query
$collection = get_db_collection('tags');
$cursor = $collection->find($obj, $return);
foreach($cursor as $tag){
	array_push($data, $tag['tag']);
}

// close any open db's
close_db();

echo json_encode($data);
?>
