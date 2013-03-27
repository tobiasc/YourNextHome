<?php 
session_start();
require_once('includes/config.php');
require_once('functions.php');

$location = $_CONFIG['url'];

if(strlen($_REQUEST['email']) > 0) {
	if($_REQUEST['action'] === 'login'){

		$users = get_db_collection('users');
		$key = array('email' => $_REQUEST['email'], 'password' => sha1($_REQUEST['password']));
		$user = $users->findOne($key);
		if($user !== null){
			$_SESSION['permission'] = 1;
			$_SESSION['name'] = $user['name'];
			$_SESSION['id'] = $user['_id']->{'$id'};

			// if this user has uploaded places, send them to the admin page after log in
			$places = get_db_collection('places');
			$key_place = array('owner' => $_SESSION['id']);
			$count = $places->count($key_place);
			if($count > 0){
				$location = $_CONFIG['url'].'?page=favorites';
			}
			
		} else {

			$location = $_CONFIG['url'].'?page=login&msg=not_found';
		}

	} else if($_REQUEST['action'] === 'user_create'){

		$location = $_CONFIG['url'].'?page=user_create&email='.$_REQUEST['email'];
	}
}

header('Location:'.$location);
exit;
?>
