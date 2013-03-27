<?php 
session_start();
require_once('includes/config.php');
require_once('functions.php');

$location = $_CONFIG['url'].'?page=admin_new_account&email='.$_REQUEST['email'].'&password='.$_REQUEST['password'];

if(strlen($_REQUEST['name']) > 0 && strlen($_REQUEST['email']) > 0 && strlen($_REQUEST['password']) > 0) {
	$admins = get_db_collection('admins');

	$key = array('email' => $_REQUEST['email']);
	$admin = $admins->findOne($key);
	if($admin === null){
		// insert admin
		$obj = array(
			'email' => $_REQUEST['email'], 
			'password' => sha1($_REQUEST['password']),
			'name' => $_REQUEST['name'],
			'website' => $_REQUEST['website'],
			'phone' => $_REQUEST['phone']
		);
		$admins->insert($obj);

		// login new admin
		$_SESSION['permission'] = 2;
		$_SESSION['name'] = $_REQUEST['name'];
		$_SESSION['id'] = $obj['_id']->{'$id'};

		$location = $_CONFIG['url'].'?page=admin';
	} else {

		$location = $_CONFIG['url'].'?page=admin_login&msg=email_exists';
	}
}

header('Location:'.$location);
exit;
?>
