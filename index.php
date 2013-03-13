<?php
session_start();

$appid = '217074015100341';
$secret = '5c421ecc737132f49a2acb497124b6a5';
$redirect_uri = 'http://yournexthome.dk/';

if(!isset($_SESSION['fb_id']) || !isset($_SESSION['name'])){
	if((isset($_REQUEST['login']) && $_REQUEST['login'] == 1) || isset($_REQUEST['state'])){
		if(isset($_REQUEST['state']) && isset($_SESSION['state'])) {
			unset($_SESSION['state']);
			if(isset($_REQUEST['code'])) {
				require_once('functions.php');

				$token_url = 'https://graph.facebook.com/oauth/access_token?client_id='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&client_secret='.$secret.'&code='.$_REQUEST['code'];
				$response = file_get_contents($token_url);
				$params = null;
				parse_str($response, $params);

				// get basic user info
				$graph_url = 'https://graph.facebook.com/me?access_token='.$params['access_token'];
				$user = json_decode(file_get_contents($graph_url));
				$_SESSION['name'] = $user->name;
				$_SESSION['fb_id'] = $user->id;
				$_SESSION['email'] = $user->email;

				// get likes
				$likes_url = 'https://graph.facebook.com/'.$user->id.'/likes?access_token='.$params['access_token'];
				$likes_obj = json_decode(file_get_contents($likes_url));
				$likes = array();
				foreach($likes_obj->data as $i => $like){
					if(!isset($likes[$like->category])){
						$likes[$like->category] = 0;
					}
					$likes[$like->category]++;
				}
				arsort($likes);
				$likes['raw_likes'] = $likes_obj->data;

				// get checkins
				$checkins_url = 'https://graph.facebook.com/'.$user->id.'/checkins?access_token='.$params['access_token'];
				$checkins_obj = json_decode(file_get_contents($checkins_url));
				$checkins = array();
				foreach($checkins_obj->data as $i => $checkin){
					if($checkin->place->location->city === 'Berlin'){
						if(!isset($checkins[$checkin->place->id])){
							$checkins[$checkin->place->id] = array('count' => 0);
						}
						$count = $checkins[$checkin->place->id]['count'] + 1;
						$checkins[$checkin->place->id] = $checkin->place;
						$checkins[$checkin->place->id]->count = $count;
						$checkins[$checkin->place->id]->lat = $checkin->place->location->latitude;
						$checkins[$checkin->place->id]->lng = $checkin->place->location->longitude;						
					}
				}

				$collection = get_db_collection('users');
				$key = array('fb_id' => $user->id);
				$obj = array(
					'fb_id' => $user->id, 
					'name' => $user->name, 
					'first_name' => $user->first_name, 
					'last_name' => $user->last_name, 
					'link' => $user->link, 
					'email' => $user->email, 
					'gender' => $user->gender, 
					'timezone' => $user->timezone, 
					'locale' => $user->locale, 
					'image' => 'https://graph.facebook.com/'.$user->username.'/picture?type=large', 
					'username' => $user->username, 
					'languages' => $user->languages, 
					'likes' => $likes, 
					'checkins' => $checkins, 
					'access_token' => $params['access_token']
				);
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
			$url = 'https://www.facebook.com/dialog/oauth?client_id='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&state='.$_SESSION['state'].'&scope=user_likes,publish_stream,user_checkins,email,user_photos,user_status';
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
<script src="includes/yournexthome.js"></script>

</head>
<body>

<div id="search_box">
	<a href="/"><h1>YourNextHome</h1></a>
	<p>Find your next home here</p><br>

	Price: €<span id="min_price">0</span> - €<span id="max_price">4000</span><br>
	<div class="slider" id="price_slider"></div>	

	Rooms: <span id="min_rooms">0</span> - <span id="max_rooms">10</span><br>
	<div class="slider" id="rooms_slider"></div>	

	Size: <span id="min_size">0</span>m2 - <span id="max_size">500</span>m2<br>
	<div class="slider" id="size_slider"></div>	

	<div id="accordion">
		<h3>Interests</h3>
		<div id="interests"></div>

		<h3>Extras</h3>
		<div id="extras"></div>
	</div>

	<br><br><br>
	Legend:<br>
	<img src="includes/accuracy_1.png" class="legend"> Street, number, & area known<br>
	<img src="includes/accuracy_2.png" class="legend"> Street, & area known<br>
	<img src="includes/accuracy_3.png" class="legend"> Area known<br>

	<br><br>
	<?php
	if(!isset($_SESSION['fb_id']) || !isset($_SESSION['name'])){
		?>
		<a href="?login=1">Log In</a>
		<?php
	} else {
		?>
		<a data-target="#modal" href="get_user.php?fb_id=<?php echo $_SESSION['fb_id']; ?>" data-toggle="modal"><?php echo $_SESSION['name']; ?></a> - <a href="make_logout.php">Log Out</a>
		<div id="modal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3 id="myModalLabel">User Profile</h3>
			</div>
			<div class="modal-body">
				
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary">Save changes</button>
			</div>
		</div>

		<br><br>
		<strong>Shortlist:</strong>
		<div id="shortlist"></div>

		<script>
			var fb_id = <?php echo $_SESSION['fb_id']; ?>;
		</script>
		<?php
	}
	?>
</div>

<div id="search_map_canvas"></div>

</body>
</html>

