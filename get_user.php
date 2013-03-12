<?php
require_once('functions.php');

// return object
$data = array();

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== ''){

	// create default query
	$obj = array('fb_id' => $_REQUEST['user_id']);

	// return keys
	$return = array('likes' => 1, 'name' => 1, 'link' => 1, 'image' => 1, '_id' => 0);

	// fire query
	$collection = get_db_collection('users');
	$user = $collection->findOne($obj, $return);

	// close any open db's
	close_db();

	$s = '<h2>'.$user['name'].'</h2><img src="'.$user['image'].'"><br><br><strong>#Likes:</strong><ul>';
	foreach($user['likes'] as $key => $like){
		if($key !== 'raw_likes'){
			$s .= '<li>'.$key.': '.$user['likes'][$key].'</li>';
		}
	}
	$s .= '</ul><br><a href="'.$user['link'].'" target="_blank">Facebook</a>';
} else {
	$s = '';
}

echo $s;
//echo json_encode($data);
?>
