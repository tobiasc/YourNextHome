<?php
session_start();
require_once('admin_functions.php');
require_once('../functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<?php echo get_head('Shortlisted Apartments - YourNextHome');?>
<body>

<?php
if(isset($_SESSION['admin']) && $_SESSION['admin'] == 3){
	echo get_menu();

	$places_array = array();
	$users_array = array();

	// fire query
	$collection = get_db_collection('shortlists');
	$places = get_db_collection('places');
	$users = get_db_collection('users');

	$cursor = $collection->find();
	foreach($cursor as $item){
		// find places
		$places_key = array('_id' => new MongoId($item['place_id']));
		$return = array('url' => 1, 'title' => 1, '_id' => 0);
		$place = $places->findOne($places_key, $return);
		$place['id'] = $item['place_id'];

		// find users
		$users_key = array('fb_id' => $item['fb_id']);
		$return = array('name' => 1, '_id' => 0);
		$user = $users->findOne($users_key, $return);
		$users_array[$item['fb_id']] = $user['name'];
		
		if(!isset($place['users'])){
			$place['users'] = array();
		}
		array_push($place['users'], array('fb_id' => $item['fb_id'], 'name' => $user['name']));

		array_push($places_array, $place);
	}
	// close any open db's
	close_db();
	?>

	<div class="container">
		<h2>Shortlisted Apartments</h2>
		<br>

		<table class="table table-bordered table-striped">
		<tr><th>Place</th><th>Interested Users</th></tr>

		<?php
		foreach($places_array as $i => $place){
			$li = '';
			foreach($place['users'] as $j => $user){
				$li .= '<a data-target="#modal_'.$user['fb_id'].'" href="/get_user.php?fb_id='.$user['fb_id'].'" data-toggle="modal">'.$user['name'].'</a><br>';
			}
			echo '<tr><td><a href="'.$place['url'].'" target="_blank">'.$place['title'].'</a></td><td>'.$li.'</td></tr>';
		}
		?>

		</table>

		<?php
		foreach($users_array as $fb_id => $name){
			echo '<div id="modal_'.$fb_id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
						<h3 id="myModalLabel">User Profile</h3>
					</div>
					<div class="modal-body">
		
					</div>
					<div class="modal-footer">
						<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
					</div>
				</div>';
		}
		?>
	</div>

	<?php

} else {
	header('Location:'.$_ADMINCONFIG['admin_url']);
	exit;
}
?>
</body>
</html>
