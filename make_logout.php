<?php 
session_start();

setcookie('id', '', time()-3600);
setcookie('name', '', time()-3600);
setcookie('state', '', time()-3600);
$_SESSION = array();
session_unset();
session_destroy();

$_SESSION[logout] = true;
header('Location:http://yournexthome.dk/');
exit;
?>
