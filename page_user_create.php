<form class="form-new" action="user_create.php" method="post">
	<h2 class="form-new-heading">New Account</h2>

	<label>Name</label>
	<input type="text" class="input-block-level" placeholder="Name" name="name">

	<label>Email</label>
	<input type="text" class="input-block-level" placeholder="Email" name="email" value="<?php echo $_REQUEST['email']; ?>">

	<label>Password</label>
	<input type="password" class="input-block-level" placeholder="Password" name="password">

	<button class="btn btn-large btn-primary" type="submit">Create</button>
</form>

