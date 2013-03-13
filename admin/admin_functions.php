<?php
$_ADMINCONFIG['admin_url'] = 'http://yournexthome.dk/admin';

function get_head($title, $includes = ''){
	global $_ADMINCONFIG;
	$head = '<head>

		<title>'.$title.'</title>

		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="content-language" content="en">

		<link rel="stylesheet" type="text/css" href="/includes/stylesheet.css">
		<link href="/includes/bootstrap/bootstrap.min.css" rel="stylesheet">
		<link href="/includes/bootstrap/bootstrap-responsive.min.css" rel="stylesheet">
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />    

		<style type="text/css">
			body {padding-top: 40px;padding-bottom: 40px;background-color: #f5f5f5;}
		</style>

		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
		<script src="/includes/bootstrap/bootstrap.min.js"></script>

		'.$includes.'

		</head>';
	return $head;
}

function get_menu(){
	$menu = '<div class="navbar navbar-inverse navbar-fixed-top">
	      <div class="navbar-inner">
		<div class="container">
		  <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		    <span class="icon-bar"></span>
		  </a>
		  <a class="brand" href="index.php">YourNextHome</a>
		  <div class="nav-collapse collapse">
		    <ul class="nav">
		      <li><a href="admin_shortlisted.php">Shortlisted</a></li>';
	if(isset($_SESSION['admin']) && $_SESSION['admin'] == 3){
		$menu .= '<li><a href="/admin/make_logout.php">Log Out</a></li>';
	}
	$menu .= '  </ul>
		  </div><!--/.nav-collapse -->
		</div>
	      </div>
	    </div>';
	return $menu;
}
?>
