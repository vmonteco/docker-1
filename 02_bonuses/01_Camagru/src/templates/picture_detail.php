<div class="content row">
  <div class="image-container">
	<div class="row image">
	  <img src="<?php print($GLOBALS['MEDIA_URL']); print($GLOBALS['context']['picture']->filename) ; ?>" class="col-10 col-offset-1" />
	  <p class="col-12 summary">
		Posted by : <cite><?php print($GLOBALS["context"]["picture"]->user->username); ?></cite>
	  </p>
	  <p class="col-12 summary">
		<?php
		  if ($_SESSION["login"] === $GLOBALS["context"]["picture"]->user->username){ ?>
		<a href="/picture/<?php print($GLOBALS["context"]["picture"]->id); ?>/delete">delete</a>
		<?php } ?>
		
		<?php print($GLOBALS["context"]["picture"]->likes); ?> likes.
		<?php if ($_SESSION['login'] && !$GLOBALS['context']['picture']->is_logged_as_author())
		{
		if ($GLOBALS["context"]["picture"]->is_liked)
		{
		print("<a href=\"/picture/".$GLOBALS["context"]["picture"]->id."/unlike/\">unlike</a>");
		}
		else
		{
		print("<a href=\"/picture/".$GLOBALS["context"]["picture"]->id."/like/\">like</a>");
		}
		}
		?>
	  </p>
	<?php if ($_SESSION['login']){ ?>
	<div class="comment-form-container row">
	  <form action="" method="POST" id="comment-form" class="row col-12 jumbotron-form">
		<textarea name="comment" form="comment-form" maxlength="254" class="col-sm-10"></textarea>
		<input type="submit" value="comment" name="submit" class="col-sm-2"/>
	  </form>
	</div>
	<?php } ?>
	</div>
	
	<ul class="comment-container row">
	  <?php
		foreach($GLOBALS["context"]["picture"]->comments as $comment){
	  $user = "User"::read_by_id($comment->author_id);
	  ?>
	  <li class="comment">
		<p><?php print($comment->content); ?></p>
		<small>by <cite><?php print($user->username);  ?></cite>, the <?php print($comment->created); ?>.</small>
	  </li>
	  <?php } ?>
	</ul>
  </div>
</div>
