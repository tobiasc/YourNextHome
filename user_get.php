<?php
require_once('functions.php');

// return object
$data = array();

if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] !== ''){

	// create default query
	$obj = array('_id' => new MongoId($_REQUEST['user_id']));

	// return keys
	$return = array('checkins' => 1, 'likes' => 1, 'name' => 1, 'link' => 1, 'image' => 1, '_id' => 0);

	// fire query
	$collection = get_db_collection('users');
	$user = $collection->findOne($obj, $return);

	// close any open db's
	close_db();

	$s = '<img src="'.$user['image'].'"><br><br>';

	// likes
	if(isset($user['likes'])){
		$s .= '<strong>#Likes:</strong><ul>';
		foreach($user['likes'] as $key => $like){
			if($key !== 'raw_likes'){
				$s .= '<li>'.$key.': '.$user['likes'][$key].'</li>';
			}
		}
		$s .= '</ul><br>';
	}

	// checkins
	if(isset($user['checkins'])){
		$s .= '<strong>#Berlin Checkins:</strong><ul>';
		foreach($user['checkins'] as $key => $checkin){
			$s .= '<li>'.$checkin['name'].': '.$checkin['count'].'</li>';
		}
		$s .= '</ul><br>';
	}

	$s .= '<a href="'.$user['link'].'" target="_blank">Facebook</a>';

	$s .= '<br><br><strong>Add Documents</strong>
		<div class="" rel="image">
			<input id="fileupload" type="file" name="files[]" data-url="/includes/jQuery-File-Upload/server/php/" multiple="">
			<ul style="list-style: none;">
			</ul>
		</div>';

	// LinkedIn verification button
	//$s .= '<script type="in/Login">Hello</script>';

} else {
	$s = '';
}

echo $s;
//echo json_encode($data);
?>
