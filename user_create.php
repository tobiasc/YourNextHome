<?php 
session_start();
require_once('includes/config.php');
require_once('functions.php');

$location = $_CONFIG['url'].'?page=user_create&email='.$_REQUEST['email'].'&password='.$_REQUEST['password'];

if(strlen($_REQUEST['name']) > 0 && strlen($_REQUEST['email']) > 0 && strlen($_REQUEST['password']) > 0) {
	$users = get_db_collection('users');

	$key = array('email' => $_REQUEST['email']);
	$user = $users->findOne($key);
	if($user === null){
		// insert admin
		$obj = array(
			'email' => $_REQUEST['email'], 
			'password' => sha1($_REQUEST['password']),
			'name' => $_REQUEST['name']
		);
		$users->insert($obj);

		// login new admin
		$_SESSION['permission'] = 1;
		$_SESSION['name'] = $_REQUEST['name'];
		$_SESSION['id'] = $obj['_id']->{'$id'};

		$location = $_CONFIG['url'].'?page=user';
	} else {

		$location = $_CONFIG['url'].'?page=login&msg=email_exists';
	}
}

header('Location:'.$location);
exit;
?>
