<?php 
session_start();
require_once('admin_functions.php');

if(strlen($_REQUEST['email']) > 0 && strlen($_REQUEST['password']) > 0) {
	$_SESSION['admin'] = 3;
	$_SESSION['name'] = 'Admin';
	$_SESSION['id'] = 1;

	header('Location:'.$_ADMINCONFIG['admin_url']);
	exit;
}
?>
