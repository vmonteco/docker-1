<?php
$GLOBALS['reset_link'] = "<!doctype html>
<html>
  <head>
	<link rel=\"stylesheet\" type=\"text/css\" href=\"https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css\">
  </head>
  <body class=\"container\">
	<div class=\"row\">
	  <h1 class=\"text-center col-12\">Hello %s!</h1>
	</div>

	<div class=\"row\">
	  <p class=\"jumbotron col-12\">
		A request was made to reset your password, here is the <a href=\"%s\">link</a> you can use.
		If you didn't make this request, please ignore this message
	  </p>
	</div>

	<div class=\"row\">
	  <div class=\"col-10 text-right\">
		<p>
		  Regards,
		</p>
		<p>
		  Camagru
		</p>
	  </div>
	</div>
  </body>
</html>";
?>