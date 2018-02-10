<?php

include "src/models.php";
include "config/setup.php";
include "src/views.php";

$routes = array (
		"#^/?$#" => "gallery",
		"#^/login/?$#" => "login",
		"#^/signup/?$#" => "signup",
		"#^/logout/?$#" => "logout",
        "#^/get_reset_link/?$#" => "get_reset_link",
        "#^/activate_account/(?P<pk>[0-9]+)-(?P<token>[a-zA-Z0-9]+)/?$#" => "activate_account",
        "#^/reset_password/(?P<pk>[0-9]+)-(?P<token>[a-zA-Z0-9]+)/?$#" => "reset_pwd",
        "#^/montage/?$#" => "montage",
        "#^/profile/?$#" => "selfprofile",
        "#^/profile/(?P<pk>[0-9]+)/?$#" => "profile",
        "#^/profile/edit/?$#" => "editprofile",
        "#^/profile/edit_username_mail/?$#" => "edit_username_mail_profile",
        "#^/profile/edit_password/?$#" => "edit_password_profile",
        "#^/profile/edit_mail_notif/?$#" => "edit_mail_notif_profile",
        "#^/profile/delete/?$#" => "deleteprofile",
        "#^/picture/(?P<pk>[0-9]+)/?$#" => "picture_details",
        "#^/picture/(?P<pk>[0-9]+)/delete/?$#" => "picture_delete",
        "#^/picture/(?P<pk>[0-9]+)/like/?$#" => "picture_like",
        "#^/picture/(?P<pk>[0-9]+)/unlike/?$#" => "picture_unlike",
        //"#^/gallery/?$#" => "gallery",
        "#^/gallery/(?P<page>[0-9]+)/?$#" => "gallery",
		"#^/404/?$#" => "error_404",
		"#^/403/?$#" => "error_403",
);

if ($GLOBALS["DEBUG"])
{
    $routes["#^".$GLOBALS["STATIC_URL"]."(?P<path>([\w .]+/)*([\w .]+))$#"] = "serve_static";
    $routes["#^".$GLOBALS["MEDIA_URL"]."(?P<path>([\w .]+/)*([\w .]+))$#"] = "serve_media";
    $routes["#^/infos/?$#"] = "php_infos";
	$routes["#^/reset_db/?$#"] = "reset_db_view";
}

function dispatch($server, $request)
{
	session_start();
	if (!$_SESSION)
	   $_SESSION["login"] = "";
	if ($_SESSION["login"])
		$GLOBALS["context"]["user"] = User::read($_SESSION["login"]);
	else
		$GLOBALS["context"]["user"] = false;
    $_request_uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_URL);
	foreach($GLOBALS["routes"] as $key => $elem)
	{
        $groups = array ();
		if (preg_match($key, $_request_uri, $groups))
		{
			$elem($groups);
			return true;
		}
	}
    raise_404();
}

try
{
    dispatch($_SERVER, $_REQUEST);
}
catch (Error404 $e)
{
    header("Location: /404/");
}
catch (Error403 $e)
{
    header("Location: /403/");
}

?>
