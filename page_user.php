<?php
require_once('functions.php');

if(isset($_SESSION['permission']) && $_SESSION['permission'] === 1){

	$user_id = (isset($_REQUEST['user_id'])) ? $_REQUEST['user_id'] : $_SESSION['id'];

	// create default query
	$obj = array('_id' => new MongoId($user_id));

	// return keys
	$return = array(
		'name' => 1, 
		'email' => 1, 
		'image' => 1, 
		'links' => 1, 
		'phone' => 1, 
		'country' => 1, 
		'city' => 1, 
		'street' => 1, 
		'street_number' => 1, 
		'facebook_id' => 1, 
		'facebook_link' => 1, 
		'facebook_image' => 1, 
		'facebook_checkins' => 1, 
		'facebook_likes' => 1, 
		'linkedin_id' => 1, 
		'linkedin_link' => 1, 
		'twitter_id' => 1, 
		'twitter_link' => 1, 
		'schufa' => 1, 
		'work_contract' => 1, 
		'payslips' => 1, 
		'bank_statement' => 1, 
		'landlord_notes' => 1, 
		'_id' => 0
	);

	// fire query
	$collection = get_db_collection('users');
	$user = $collection->findOne($obj, $return);
	$user = fill_array($return, $user);

	$user_score = calc_user_creation_score($user);

	// close any open db's
	close_db();

	?>
	<div class="container">
		<form action="user_update.php" method="post" enctype="multipart/form-data">
			<input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
			<div class="hero-unit">
				<?php
				if(isset($user['image'])){
					echo '<img src="'.$user['image'].'">';
				}
				?>
				<h1><?php echo $user['name']; ?></h1>
				<p>User Score: <?php echo $user_score; ?>%</p>
				<div class="progress">
					<div class="bar" style="width: <?php echo $user_score; ?>%;"></div>
				</div>
			</div>

			<div class="row">
				<div class="span4">
					<h2>Contact</h2>
					<table class="table">
						<?php
						if($user_id === $_SESSION['id']){
						?>
							<tr><td>Name:</td><td><input type="text" name="name" value="<?php echo $user['name']; ?>"></td></tr>
							<tr><td>Email:</td><td><input type="text" name="email" value="<?php echo $user['email']; ?>"></td></tr>
							<tr><td>Links:</td><td><input type="text" name="links" value="<?php echo $user['links']; ?>"></td></tr>
							<tr><td>Phone:</td><td><input type="text" name="phone" value="<?php echo $user['phone']; ?>"></td></tr>
							<tr><td>Country:</td><td><input type="text" name="country" value="<?php echo $user['country']; ?>"></td></tr>
							<tr><td>City:</td><td><input type="text" name="city" value="<?php echo $user['city']; ?>"></td></tr>
							<tr><td>Street:</td><td><input type="text" name="street" value="<?php echo $user['street']; ?>"></td></tr>
							<tr><td>Street Number:</td><td><input type="text" name="street_number" value="<?php echo $user['street_number']; ?>"></td></tr>
						<?php
						} else {
						?>
							<tr><td>Name:</td><td><?php echo $user['name']; ?></td></tr>
							<tr><td>Email:</td><td><?php echo $user['email']; ?></td></tr>
							<tr><td>Links:</td><td><?php echo $user['links']; ?></td></tr>
							<tr><td>Phone:</td><td><?php echo $user['phone']; ?></td></tr>
							<tr><td>Country:</td><td><?php echo $user['country']; ?></td></tr>
							<tr><td>City:</td><td><?php echo $user['city']; ?></td></tr>
							<tr><td>Street:</td><td><?php echo $user['street']; ?></td></tr>
							<tr><td>Street Number:</td><td><?php echo $user['street_number']; ?></td></tr>
						<?php
						}
						?>
					</table>
				</div>
				<div class="span4">
					<h2>Documents</h2>
					<table class="table">
						<?php
						if($user_id === $_SESSION['id']){
						?>
							<tr><td>Schufa:</td><td><?php if($user['schufa'] !== '') echo '<a href="'.$user['schufa'].'" target="_blank">Link</a> '; ?><input type="file" name="schufa"></td></tr>
							<tr><td>Work Contract:</td><td><?php if($user['work_contract'] !== '') echo '<a href="'.$user['work_contract'].'" target="_blank">Link</a> '; ?><input type="file" name="work_contract"></td></tr>
							<tr><td>Payslips:</td><td><?php if($user['payslips'] !== '') echo '<a href="'.$user['payslips'].'" target="_blank">Link</a> '; ?><input type="file" name="payslips"></td></tr>
							<tr><td>Bank Statement:</td><td><?php if($user['bank_statement'] !== '') echo '<a href="'.$user['bank_statement'].'" target="_blank">Link</a> '; ?><input type="file" name="bank_statement"></td></tr>
							<tr><td>Landlord Notes:</td><td><?php if($user['landlord_notes'] !== '') echo '<a href="'.$user['landlord_notes'].'" target="_blank">Link</a> '; ?><input type="file" name="landlord_notes"></td></tr>
						<?php
						} else {
						?>
							<tr><td>Schufa:</td><td><a href="<?php echo $user['schufa']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>Work Contract:</td><td><a href="<?php echo $user['work_contract']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>Payslips:</td><td><a href="<?php echo $user['payslips']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>Bank Statement:</td><td><a href="<?php echo $user['bank_statement']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>Landlord Notes:</td><td><a href="<?php echo $user['landlord_notes']; ?>" target="_blank">Link</a></td></tr>
						<?php
						}
						?>
					</table>
				</div>
				<div class="span4">
					<h2>Social Networks</h2>
					<table class="table">
						<?php
						if($user_id === $_SESSION['id']){
						?>
							<tr><td>Facebook:</td><td></td></tr>
							<tr><td>Twitter:</td><td></td></tr>
							<tr><td>LinkedIn:</td><td></td></tr>
						<?php
						} else {
						?>
							<tr><td>Facebook:</td><td><a href="<?php echo $user['facebook_link']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>Twitter:</td><td><a href="<?php echo $user['twitter_link']; ?>" target="_blank">Link</a></td></tr>
							<tr><td>LinkedIn:</td><td><a href="<?php echo $user['linkedin_link']; ?>" target="_blank">Link</a></td></tr>
						<?php
						}
						?>
					</table>
				</div>
			</div>

			<?php
			if($user_id === $_SESSION['id']){
			?>
			<button class="btn btn-large btn-primary" type="submit">Save</button>
			<?php
			}
			?>
		</form>

		<?php
		// LinkedIn verification button
		//$s .= '<script type="in/Login">Hello</script>';
		?>

	</div>

	<script type="text/javascript" src="includes/page_user.js"></script>
<?php
}
?>
