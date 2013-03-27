<?php 
session_start();
require_once('includes/config.php');
require_once('functions.php');

$location = $_CONFIG['url'].'?page=user';

if(isset($_REQUEST['user_id']) && strlen($_REQUEST['user_id']) > 0 && isset($_SESSION['permission']) && $_SESSION['permission'] === 1) {
	$users = get_db_collection('users');

	$key = array('_id' => new MongoId($_REQUEST['user_id']));
	$user = $users->findOne($key);
	if($user !== null){
		$update_obj = $_REQUEST;

		foreach($_FILES as $key_files => $value_files){
			if ($_FILES[$key_files]['error'] === 0){
				$file_type = substr($_FILES[$key_files]['name'], strrpos($_FILES[$key_files]['name'], '.'));
				$file_name = 'files/'.$key_files.'_'.$_REQUEST['user_id'].'_'.time().$file_type;
				if(move_uploaded_file($_FILES[$key_files]['tmp_name'], $file_name)){
					$update_obj[$key_files] = $file_name;
				}
			}
		}
		
		$users->update($key, array('$set' => $update_obj), array('upsert' => 1));

		// update log in info
		$_SESSION['name'] = $_REQUEST['name'];
	}

	$location = $_CONFIG['url'].'?page=user&user_id='.$_REQUEST['user_id'];
}

header('Location:'.$location);
exit;
?>
