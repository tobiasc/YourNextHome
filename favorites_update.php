<?php
require_once('functions.php');

// return object
$data = array();

if(isset($_REQUEST['fb_id']) && $_REQUEST['fb_id'] !== '' && 
	isset($_REQUEST['place_id']) && $_REQUEST['place_id'] !== '' && 
	isset($_REQUEST['action']) && $_REQUEST['action'] !== ''){

	// create default query
	$obj = array('fb_id' => $_REQUEST['fb_id'], 'place_id' => $_REQUEST['place_id']);

	$collection = get_db_collection('shortlists');

	if($_REQUEST['action'] === 'insert'){
		$collection->update($obj, $obj, array('upsert' => true));
	} else if($_REQUEST['action'] === 'remove'){
		$collection->remove($obj);
	}

	// close any open db's
	close_db();
}

echo json_encode($data);
?>
