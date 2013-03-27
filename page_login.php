<form class="form-signin" action="make_login.php" method="post">
	<?php
	$msgs = array(
		'email_exists' => 'This email is already used', 
		'not_found' => 'The email & password does not match an existing user'
	);
	
	if(isset($_REQUEST['msg']) && isset($msgs[$_REQUEST['msg']])){
	?>
		<div class="alert alert-error">
			<button type="button" class="close" data-dismiss="alert">&times;</button>
			<?php 
			echo $msgs[$_REQUEST['msg']];
			?>
		</div>
	<?php 
	}
	?>

	<h2 class="form-signin-heading">Log In</h2>
	<input type="text" class="input-block-level" placeholder="Email address" name="email">
	<input type="password" class="input-block-level" placeholder="Password" name="password">
	<button class="btn btn-large btn-primary" type="submit" name="action" value="login">Log In</button>
	<button class="btn btn-large btn-primary pull-right" type="submit" name="action" value="user_create">New User</button>
</form>

