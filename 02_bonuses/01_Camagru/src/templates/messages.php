<div id="messages-div">
    <?php
	$message_index = 0;
	if (!(empty($GLOBALS['messages']['info']))) {
		foreach($GLOBALS['messages']['info'] as $message) {
	?>
		<div id="box<?php print($message_index); $message_index++; ?>" class="message_box info_box">
			<p>
				<?php print("Info : ".$message); ?>
			</p>
			<button type="button" class="message_button">
				close
			</button>
		</div>
	<?php
	} 
	}
	if (!(empty($GLOBALS['messages']['error']))) {
		foreach($GLOBALS['messages']['error'] as $message) { ?>
		<div id="box<?php print($message_index); $message_index++; ?>" class="message_box error_box">
			<p>
				<?php print("Error : ".$message); ?>
			</p>
			<button type="button" class="message_button">
				close
			</button>
		</div>
		
	<?php }
	} ?>
	<script>

	 [].forEach.call(document.getElementsByClassName("message_button"), function(elem){
		 elem.addEventListener("click", function(e){
			 elem.parentNode.parentNode.removeChild(elem.parentNode);
		 });
	 });
	 
	</script>
</div>
