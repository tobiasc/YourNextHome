<?php 
session_start();
require_once('admin_functions.php');

setcookie('id', '', time()-3600);
setcookie('name', '', time()-3600);
setcookie('state', '', time()-3600);
$_SESSION = array();
session_unset();
session_destroy();

$_SESSION[logout] = true;
header('Location:'.$_ADMINCONFIG['admin_url']);
exit;
?>
