<div class="content row">

  <div class="row col-12 hidden-xs section-title">
    <h2>Montage page :</h2>
  </div>

  <div class="row">
	<div id="montage">
      <div id="montage_content" class="col-sm-12 col-md-9 content">
		
		<div id="canvas_container" class="row">
		  <div class="col-sm-6 col-sm-offset-3 row">
			<div id="photo-preview">
			  <canvas id="file-preview" width="640" height="480"></canvas>
			  <img class="filter-preview" src="" style="display:none;" />
			  <div id="camera-preview" style="display: none;">
				<video id="webcam-preview" width="640" height="480" autoplay></video>
				<img class="filter-preview" src="" style="display:none;"/>
			  </div>
			  <div id="shot" style="display: none; width: 100%; height: 100%; top: 0px; left: 0px; position: absolute; z-index: 10003;" content="">
				  <div class="row" style="position: absolute; top: 10px; left: 10px; width: calc(100% - 20px); height: 40px;">
					  <img class="col-xs-3" id="overlay-icon" src="/static/img/ic_filter_white_48dp.png" style="width: 40px; height: 40px; display: inline-block;" />
					  <p id="overlay-text" style="margin: 0; line-height: 40px; color: #555; background-color: rgba(150, 0, 0, 0.4);">Select a filter</p>
				  </div>
			  </div>
			</div>
		  </div>
		</div>

          <?php /* <div id="camera-button">
          <button id="shot-old" disabled>Take photo</button>
          </div> */ ?>
		
		<form action="" method="POST" enctype="multipart/form-data" class="row col-12">
		  <input id="file-hidden" type="hidden" name="image-hidden" />
		  <p class="row col-12">
			<label for="file" class="col-6">Upload a file from your computer:</label>
			<input id="file" class="col-6" type="file" name="image" disabled />
		  </p>
		  <ul class="filter-ul row col-12">
			<li class="filter-li col-xs-12">
			  <label for="filter0" >
				<input id="filter0" class="filter-radio null-filter-radio" type="radio" name="mask" value="0" />
				<div>
				  <span >No filter</span>
				</div>
			  </label>
			</li>
			<?php $i = 0; foreach($GLOBALS['masks'] as $mask) { ?>
			<li class="filter-li">
			  <label>
				<input type="radio" class="filter-radio non-null-filter-radio" name="mask" value="<?php $i++; print($i); ?>" />
				<img src="<?php print($GLOBALS['STATIC_URL'].$mask['path']); ?>" class="filter-mini" />
			  </label>
			</li>
			<?php } ?>
		  </ul>
		  <input type="submit" value="Submit" id="submit" name="submit" disabled />
		</form>
	  </div>

	  <div id="previous_shots" class="col-md-3 col-sm-12 col-xs-12 content">
		<?php if ($GLOBALS['context']['pictures']){ ?>
		<ul>
		  <?php
			foreach(array_reverse($GLOBALS['context']['pictures']) as $picture)
			{
			?>
		  <li class="gallery-item">
			  <?php /* <div>a</div> */ ?>
			  <a href="/picture/<?php print($picture->id); ?>">
				<img src="<?php print($GLOBALS["MEDIA_URL"].$picture->filename); ?>" onerror="if (this.src != 'error.jpg') this.src = '<?php print($GLOBALS['STATIC_URL'].'img/notfound.png'); ?>';" />
			  </a>
		  </li>
		  
		  <?php
			}
			?>
		</ul>
		<?php
		  }
		  else
		  {
		  ?>
		<p>No picture yet.</p>
		<?php } ?>
	  </div>
	</div>
	<script type="text/javascript" src="/static/js/montage.js"></script>
  </div>
</div>
