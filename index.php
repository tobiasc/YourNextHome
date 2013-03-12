<?php
$appid = '217074015100341';
$secret = '5c421ecc737132f49a2acb497124b6a5';
$redirect_uri = 'http://yournexthome.dk/';

if(!isset($_SESSION['id']) || !isset($_SESSION['name'])){
	if((isset($_REQUEST['login']) && $_REQUEST['login'] == 1) || isset($_REQUEST['state'])){
		if(isset($_REQUEST['state']) && isset($_SESSION['state']) && ($_SESSION['state'] === $_REQUEST['state'])) {
			unset($_SESSION['state']);
			if(isset($_REQUEST['code'])) {
				require_once('functions.php');

				$token_url = 'https://graph.facebook.com/oauth/access_token?client_id='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&client_secret='.$secret.'&code='.$_REQUEST['code'];
				$response = file_get_contents($token_url);
				$params = null;
				parse_str($response, $params);

				$graph_url = "https://graph.facebook.com/me?access_token=".$params['access_token'];
				$user = json_decode(file_get_contents($graph_url));
				$_SESSION['name'] = $user->name;
				$_SESSION['id'] = $user->id;
				$_SESSION['email'] = $user->email;

				$collection = get_db_collection('users');
				$key = array('id' => $user->id);
				$obj = array('id' => $user->id, 'name' => $user->name, 'first_name' => $user->first_name, 
					'last_name' => $user->last_name, 'link' => $user->link, 'email' => $user->email, 
					'gender' => $user->gender, 'timezone' => $user->timezone, 'locale' => $user->locale, 
					'image' => 'https://graph.facebook.com/'.$user->username.'/picture?type=large', 
					'username' => $user->username, 'access_token' => $params['access_token']);
				$collection->update($key, $obj, array('upsert' => true)); // upsert... sweet!

				// close any open db's
				close_db();

				header('Location:'.$redirect_uri);
				exit;	

			} else {
				echo 'You declined the login';
			}
		} else {
			$_SESSION['state'] = sha1(time().'yournexthome'.'SOME_ARBITRARY_BUT_UNIQUE_STRING');
			//$url = 'https://www.facebook.com/dialog/oauth?client_id='.$appid.'&redirect_uri='.$redirect_uri.'&scope=publish_stream,user_checkins,friends_checkins&state='.$_SESSION['state'];
			$url = 'https://www.facebook.com/dialog/oauth?client_id='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&state='.$_SESSION['state'];
			echo "<script type='text/javascript'>top.location.href = '$url';</script>";
		}
	}
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>

<title>YourNextHome</title>

<meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="content-language" content="en">

<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link href="includes/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="includes/bootstrap/bootstrap-responsive.min.css" rel="stylesheet">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.9.1/themes/base/jquery-ui.css" />    

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=true"></script>
<script src="includes/bootstrap/bootstrap.min.js"></script>
<script src="includes/js.js"></script>

</head>
<body>

<div id="search_box">
	<a href="/"><h1>YourNextHome</h1></a>

	Price: €<span id="min_price">0</span> - €<span id="max_price">4000</span><br>
	<div class="slider" id="price_slider"></div>	

	Rooms: <span id="min_rooms">0</span> - <span id="max_rooms">10</span><br>
	<div class="slider" id="rooms_slider"></div>	

	Size: <span id="min_size">0</span>m2 - <span id="max_size">500</span>m2<br>
	<div class="slider" id="size_slider"></div>	

	Extras:<br>
	<div id="extras"></div>

	<br><br><br>
	Legend:<br>
	<img src="includes/accuracy_1.png" class="legend"> Street, number, & area known<br>
	<img src="includes/accuracy_2.png" class="legend"> Street, & area known<br>
	<img src="includes/accuracy_3.png" class="legend"> Area known<br>

	<br><br>
	<?php
	if(!isset($_SESSION['id']) || !isset($_SESSION['name'])){
		echo '<a href="?login=1">Log In</a>';
	} else {
		echo '<a id="user_link" uid="'.$_SESSION['id'].'">'.$_SESSION['name'].'</a> - <a href="make_logout.php">Log Out</a>';
	}
	?>
</div>

<div id="search_map_canvas"></div>

</body>
</html>

