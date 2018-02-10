<?php

/*
 *  Config for media files.
 */

$BASE_DIR = dirname(__FILE__, 2);

//$MEDIA_ROOT = $BASE_DIR.'/media/';
$MEDIA_ROOT = '/var/www/media/';

if (!file_exists($MEDIA_ROOT)) {
    mkdir($MEDIA_ROOT, 0777, true);
}
if (!file_exists($MEDIA_ROOT.'img')) {
    mkdir($MEDIA_ROOT.'img', 0777, true);
}

$MEDIA_URL = '/media/';

$STATIC_ROOT = $BASE_DIR.'/static/';
$STATIC_URL = '/static/';

?>
