<?php
session_start();
require_once('includes/config.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

<title>YourNextHome</title>

<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="content-language" content="en">

<link rel="stylesheet" type="text/css" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />    
<link rel="stylesheet" type="text/css" href="includes/bootstrap/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="includes/bootstrap/bootstrap-responsive.min.css">
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">

<!--
<script type="text/javascript" src="http://platform.linkedin.com/in.js">
  api_key: l86mo5o64uv9
</script>
-->

</head>
<body>

<div id=ynh_container>
	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div id="ynh_menu_container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="<?php echo $_CONFIG['url']; ?>">YourNextHome</a>
				<div class="nav-collapse collapse">
					<ul class="nav">
						<?php
						if(isset($_SESSION['permission']) && $_SESSION['permission'] === 1){
							?>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $_SESSION['name']; ?> <b class="caret"></b></a>
								<ul class="dropdown-menu">
									<li><a href="?page=user&user_id=<?php echo $_SESSION['id']; ?>">Profile</a></li>
									<li><a href="?page=favorites&id=<?php echo $_SESSION['id']; ?>">Favorites</a></li>
									<li><a href="?page=place_create&id=<?php echo $_SESSION['id']; ?>">Add Apartment</a></li>
									<li><a href="?page=place_create&id=<?php echo $_SESSION['id']; ?>">Edit Apartments</a></li>
								</ul>
							</li>
							<li><a href="make_logout.php">Log Out</a></li>
							<script>
								var user_id = '<?php echo $_SESSION['id']; ?>';
							</script>
							<?php
						} else {
							?>
							<li><a href="?page=login">Log In</a></li>
							<?php
						}
						?>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
	<script type="text/javascript" src="includes/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
	<script type="text/javascript" src="includes/jQuery-File-Upload/js/jquery.fileupload.js"></script>
	<script type="text/javascript" src="includes/bootstrap/bootstrap.min.js"></script>
	<script type="text/javascript" src="includes/config.js"></script>

	<?php
	// make sure page is set
	$_REQUEST['page'] = (isset($_REQUEST['page'])) ? $_REQUEST['page'] : '';

	if($_REQUEST['page'] === 'user'){
		require_once('page_user.php');
	} else if($_REQUEST['page'] === 'favorites'){
		require_once('page_favorites.php');
	} else if($_REQUEST['page'] === 'login'){
		require_once('page_login.php');
	} else if($_REQUEST['page'] === 'user_create'){
		require_once('page_user_create.php');
	} else if($_REQUEST['page'] === 'place_create'){
		require_once('page_place_create.php');
	} else {
		require_once('page_map.php');
	}
	?>

</div>

</body>
</html>

