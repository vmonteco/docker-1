<div class="nav-container">
  <nav>
    
	<?php if ($_SESSION['login']) { ?>

    <div class="nav-elem col-sm-6 col-md-4">
	  <a href="/">Gallery</a>
    </div>			
	<div class="nav-elem col-sm-6 col-md-4">
      <a href="/montage/">Montage</a>
    </div>
    <div class="nav-right col-md-4 col-sm-12">
      <p>connected as : <a href="/profile/"><?php print($_SESSION['login']) ?></a> <a href="/logout">Logout</a></p>
    </div>
    
	<?php } else { ?>

	<div class="nav-elem col-sm-6">
      <a href="/">Gallery</a>
    </div>			
    <div class="col-sm-6 nav-right">
      <p>Not connected. <a href="/login/">login</a> - <a href="/signup">signup</a></p>
    </div>
    
	<?php } ?>

  </nav>
</div>
