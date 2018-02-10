<?php

function getRandomString($len)
{
    $charset = "ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $res = '';
    $l = strlen($charset);
    while ($len--)
    {
        $res .= $charset[mt_rand(0, $l - 1)];
    }
    return $res;
}

function create_png_from_hidden()
{
    $filename = "img/".getRandomString(10).".png";
    while (file_exists($GLOBALS['MEDIA_ROOT'].$filename))
        $filename = "img/".getRandomString(10).".png";
	set_error_handler(function ($no, $msg, $file, $line) {
			throw new ErrorException($msg, 0, $no, $file, $line);
		});
	try {
		if (!$image = imagecreatefromstring(base64_decode($_POST['image-hidden'])))
		{
			$GLOBALS['messages']['error'][] = 'Can\'t convert uploaded image into gd image.';
			return false;
		}
	}
	catch (Exception $e) {
			$GLOBALS['messages']['error'][] = 'This image was detected as invalid.';
			return false;
	}
    if ($_POST['mask'] !== "0")
    {
        if (!$filter = imagecreatefrompng($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']))
        {
            $GLOBALS['messages']['error'][] = 'Can\'t load filter.';
            return false;
        }
		list($width, $height) = getimagesize($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']);
		$success = imagecopyresampled($image, $filter, 0, 0, 0, 0, 640, 480, $width, $height);
        if (!$success)
        {
            $GLOBALS['messages']['error'][] = 'Failed to apply filter.';
            return false;            
        }   
    }
    if (!imagepng($image, $GLOBALS['MEDIA_ROOT'].$filename))
    {
            $GLOBALS['messages']['error'][] = 'Failed to write to file.';
            return false;        
    }
	$GLOBALS['messages']['info'][] = 'foo';
	$GLOBALS['messages']['info'][] = $filename;
	$user = User::read($_SESSION['login']);
	$picture = new Picture(0, $user->id, $filename, date("Y-m-d H:i:s"));
	$picture->create();
    return ($filename);
}

function create_image_from_png()
{
    $filename = "img/".getRandomString(10).".png";
    while (file_exists($GLOBALS['MEDIA_ROOT'].$filename))
        $filename = "img/".getRandomString(10).".png";
    $image = imagecreatefrompng($_FILES['image']['tmp_name']);
    if ($_POST['mask'] !== "0")
    {
        if (!$filter = imagecreatefrompng($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']))
        {
            $GLOBALS['messages']['error'][] = 'Can\'t load filter.';
            return false;
        }
		list($width, $height) = getimagesize($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']);
		list($dst_width, $dst_height) = getimagesize($_FILES["image"]["tmp_name"]);
        $success = imagecopyresampled($image, $filter, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
        if (!$success)
        {
            $GLOBALS['messages']['error'][] = 'Failed to apply filter.';
            return false;            
        }   
    }
    if (!imagepng($image, $GLOBALS['MEDIA_ROOT'].$filename))
    {
            $GLOBALS['messages']['error'][] = 'Failed to write to file.';
            return false;        
    }
	$user = User::read($_SESSION['login']);
	$picture = new Picture(0, $user->id, $filename, date("Y-m-d H:i:s"));
	$picture->create();
    return ($filename);
}

function create_image_from_jpeg()
{
    $filename = "img/".getRandomString(10).".jpeg";
    while (file_exists($GLOBALS['MEDIA_ROOT'].$filename))
        $filename = "img/".getRandomString(10).".jpeg";
    $image = imagecreatefromjpeg($_FILES['image']['tmp_name']);
    if ($_POST['mask'] !== "0")
    {
        if (!$filter = imagecreatefrompng($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']))
        {
            $GLOBALS['messages']['error'][] = 'Can\'t load filter.';
            return false;
        }
		list($width, $height) = getimagesize($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']);
		list($dst_width, $dst_height) = getimagesize($_FILES["image"]["tmp_name"]);
        $success = imagecopyresampled($image, $filter, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
        if (!$success)
        {
            $GLOBALS['messages']['error'][] = 'Failed to apply filter.';
            return false;            
        }   
    }
    if (!imagejpeg($image, $GLOBALS['MEDIA_ROOT'].$filename))
    {
            $GLOBALS['messages']['error'][] = 'Failed to write to file.';
            return false;        
    }
	$user = User::read($_SESSION['login']);
	$picture = new Picture(0, $user->id, $filename, date("Y-m-d H:i:s"));
	$picture->create();
    return ($filename);
}

function create_image_from_gif()
{
    $filename = "img/".getRandomString(10).".gif";
    while (file_exists($GLOBALS['MEDIA_ROOT'].$filename))
        $filename = "img/".getRandomString(10).".gif";
    $image = imagecreatefromgif($_FILES['image']['tmp_name']);
    if ($_POST['mask'] !== "0")
    {
        if (!$filter = imagecreatefrompng($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']))
        {
            $GLOBALS['messages']['error'][] = 'Can\'t load filter.';
            return false;
        }
		list($width, $height) = getimagesize($GLOBALS['STATIC_ROOT'].$GLOBALS['masks'][$_POST['mask'] - 1]['path']);
		list($dst_width, $dst_height) = getimagesize($_FILES["image"]["tmp_name"]);
		 $success = imagecopyresampled($image, $filter, 0, 0, 0, 0, $dst_width, $dst_height, $width, $height);
        if (!$success)
        {
            $GLOBALS['messages']['error'][] = 'Failed to apply filter.';
            return false;            
        }   
    }
    if (!imagegif($image, $GLOBALS['MEDIA_ROOT'].$filename))
    {
            $GLOBALS['messages']['error'][] = 'Failed to write to file.';
            return false;        
    }
	$user = User::read($_SESSION['login']);
	$picture = new Picture(0, $user->id, $filename, date("Y-m-d H:i:s"));
	$picture->create();
    return ($filename);
}


function create_image_from_file()
{
    $file_extension = strrchr($_FILES['image']['name'], ".");
    if (in_array($file_extension, array(".png", ".PNG")))
    {
        return create_image_from_png();
    }
    else if (in_array($file_extension, array(".jpeg", ".jpg", ".jpeg", ".JPEG")))
    {
		return create_image_from_jpeg();
    }
    else if (in_array($file_extension, array(".GIF", ".gif")))
    {
		return create_image_from_gif();
    }
    else
    {
        $_GLOBALS['messages']['error'][] = "Can't use uploaded file.";
        return false;
    }
}

?>
