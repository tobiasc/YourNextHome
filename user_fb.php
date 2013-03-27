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
				$_SESSION['permission'] = 1;

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
