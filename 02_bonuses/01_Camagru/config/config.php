<?php

/*
 *  Main configuration file, includes other configuration files.
 */

$GLOBALS['DEBUG'] = false;

include "config/database.php";
include "config/media.php";
include "config/gallery.php";

?>
