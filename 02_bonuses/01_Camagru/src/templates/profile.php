<div class="content row">

  <div class="row col-12 section-title">
	<h2>
	  <?php print($GLOBALS["context"]["profile"]->username); ?>
	</h2>
  </div>

  <div class="row">
	<form method="POST" class="row col-sm-6 col-sm-offset-3 jumbotron-form" action="#">
	  <p class="row">
		<label for="username" class="col-6">Username :</label>
		<input type="text" name="username" maxlength="40" class="col-6" value="<?php print($GLOBALS["context"]["profile"]->username); ?>" >
	  </p>
	  <p class="row">
		<label for="email" class="col-6">Email :</label>
		<input type="email" name="email" class="col-6" maxlength="191" value="<?php print($GLOBALS["context"]["profile"]->email); ?>" >
	  </p>
	  <p class="row">
		<label for="password" class="col-6">Password :</label>
		<input type="password" class="col-6" name="password" value="" minlength="8">
	  </p>
	  <input type="hidden" name="part" value="username_email" />
	  <input type="submit" name="save" class="col-12" value="Save">
	</form>
  </div>

  <hr class="col-sm-6 col-sm-offset-3"/>

  <div class="row">
	<form method="POST" class="row col-sm-6 col-sm-offset-3 jumbotron-form" action="#">
	  <p class="row">
		<label for="mail_notif" class="col-6">Mail notif :</label>
		<input type="checkbox" class="col-6" name="mail_notif" value="True" <?PHP if ($GLOBALS["context"]["profile"]->mail_notif){ ?>checked <?PHP } ?>>
	  </p>
	  <input type="hidden" name="part" value="mail_notif" />
	  <input type="submit" name="button" value="submit" class="col-12" />
	</form>
  </div>

  <hr class="col-sm-6 col-sm-offset-3"/>

  <div class="row ">
	<form method="POST" class="row col-sm-6 col-sm-offset-3 jumbotron-form" action="#">
	  <p class="row">
		<label for="old_password" class="col-6">Old password :</label>
		<input type="password" name="old_password" minlength="8"/>
	  </p>
	  <p class="row">
		<label for="new_password" class="col-6">New password :</label>
		<input type="password" name="new_password" minlength="8"/>
	  </p>
	  <p class="row">
		<label for="new_password_confirm" class="col-6">Confirm new password :</label>
		<input type="password" name="new_password_confirm" minlength="8"/>
	  </p>
	  <input type="hidden" name="part" value="password" />
	  <input type="submit" name="submit" value="save" class="col-12"/>
	</form>
  </div>

  <hr class="col-sm-6 col-sm-offset-3"/>

  <div class="row col-sm-6 col-sm-offset-3">
	<a class="col-xs-offset-10 col-xs-2" href="/profile/delete">Delete user</a>
  </div>

</div>
