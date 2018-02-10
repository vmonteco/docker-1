<div class="row">
<?php include "src/templates/paginator.php" ?>
</div>

<div class="content row">
  <div class="row col-12 hidden-xs section-title">
	<h2>gallery page</h2>
  </div>
  <ul class="row col-sm-10 col-sm-offset-1">
	<?php
	  foreach($GLOBALS['context']['pictures'] as $picture)
	  {
	  ?>
	<li class="col-sm-6 col-md-4 col-lg-3 gallery-item row">
	  <a class="col-12" href="/picture/<?php print($picture->id); ?>">
		<img src="<?php print($GLOBALS["MEDIA_URL"].$picture->filename); ?>" onerror="if (this.src != 'error.jpg') this.src = '<?php print($GLOBALS['STATIC_URL'].'img/notfound.png'); ?>';" />
	  </a>
	  <span><?php print($picture->likes); ?> likes</span>
	</li>
	<?php
	  }
	  ?>
  </ul>
</div>

<div class="row">
<?php include "src/templates/paginator.php" ?>
</div>
