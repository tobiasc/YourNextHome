<?php
session_start();
require_once('admin_functions.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<?php echo get_head('YourNextHome'); ?>
<body>

<div class="container">

<?php
if(isset($_SESSION['admin']) && $_SESSION['admin'] == 3){
	echo get_menu();
} else {
?>
<form class="form-signin" action="admin_make_login.php" method="post">
<h2 class="form-signin-heading">Please sign in</h2>
<input type="text" class="input-block-level" placeholder="Email address" name="email">
<input type="password" class="input-block-level" placeholder="Password" name="password">
<button class="btn btn-large btn-primary" type="submit">Sign in</button>
</form>
<?php
}
?>
</div> 
</body>
</html>
