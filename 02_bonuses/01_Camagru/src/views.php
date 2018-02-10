<?php

include "src/masks.php";

/*
**  Error views :
*/

class Error404 extends Exception {}
class Error403 extends Exception {}

function raise_404()
{
    http_response_code(404);
    throw new Error404();
}

function raise_403()
{
    http_response_code(403);
    throw new Error403();
}

function error_404()
{
	$GLOBALS["content_src"] = "src/templates/404.php";
    http_response_code(404);
    include "src/templates/base.php";
}

function error_403()
{
	$GLOBALS["content_src"] = "src/templates/403.php";
    http_response_code(403);
	include "src/templates/base.php";
}

/*
** Validators include :
*/

include "validators.php";


/*
**  Simple views :
*/

function home($matches)
{
	$GLOBALS["content_src"] = "src/templates/home.php";
	include "src/templates/base.php";
}

/*
**  User management views :
*/


function login($matches)
{
	$GLOBALS["content_src"] = "src/templates/login.php";
    if ($_SESSION['login'])
        raise_404();
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		if (login_validate())
		{
			$_SESSION['login'] = filter_input(
                INPUT_POST,
                'login',
                FILTER_VALIDATE_REGEXP,
                array("options"=>array("regexp"=>"User"::$LOGIN_REGEXP))
            );
			header("Location: /");
		}
		else
		{
			$error = "Invalid login informations.";
			include "src/templates/base.php";
		}
	}
	else
	{
		$error = "GET_REQ";
		include "src/templates/base.php";
	}
}

function signup($matches)
{
	$GLOBALS["content_src"] = "src/templates/signup.php";
    if ($_SESSION['login'])
        raise_404();
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
	{
		if (signup_validate())
		{

			$user = new User(
                0,
				filter_input(
                    INPUT_POST,
                    'login',
                    FILTER_VALIDATE_REGEXP,
                    array("options"=>array("regexp"=>User::$LOGIN_REGEXP))
                ),
				User::hash_pwd(
                    filter_input(
                        INPUT_POST,
                        'password',
                        FILTER_SANITIZE_SPECIAL_CHARS)
                ),
				filter_input(
                    INPUT_POST,'email',
                    FILTER_SANITIZE_EMAIL
                ),
				0,
                ''
				);
            //$user->set_token();
			if ($user->create())
			{
				$user = "User"::read($user->username);
				$user->send_activation_mail();
				header("Location: /");
			}
			else
			{
				$GLOBALS['messages']['error'][] = "An error occured while creating user";
				include "src/templates/base.php";
			}
		}
		else
		{
			include "src/templates/base.php";
		}
	}
	else
	{
		include "src/templates/base.php";
	}
}

function logout($matches)
{
    session_destroy();
    header('Location: /');
}

function get_reset_link($matches)
{
	$GLOBALS["content_src"] = "src/templates/get_reset_link.php";
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (get_reset_link_validate())
        {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            if ($user = "User"::read_by_email($email))
                $user->send_reset_link();
            header("Location: /");
        }
        else
        {
            include "src/templates/base.php";
        }
    }
    else if ($_SERVER['REQUEST_METHOD'] === 'GET')
    {
		include "src/templates/base.php";
    }
}

function activate_account($matches)
{
    $GLOBALS["content_src"] = "src/templates/activated.php";
    if ($_SESSION['login'])
        raise_404();
    if ($user = "User"::read_by_id($matches['pk']))
    {
        //$user->send_activation_mail();
        if (!$user->is_active
            && $user->token
            && $user->hash_token($matches['token']) === $user->token)
        {
            $user->is_active = true;
            $user->token = '';
            $user->update();
            include "src/templates/base.php";
        }
        else
            raise_404();
    }
    else        raise_404();
}

function reset_pwd($matches)
{
    if ($_SESSION['login'])
        raise_404();
    $GLOBALS["content_src"] = "src/templates/reset_pwd.php";
    if (($user = "User"::read_by_id($matches['pk']))
        && $user->token
        && ($user->token
            === $user->hash_token($matches['token'])))
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST')
        {
            
            if (reset_validate())
            {
                $user->is_active = true;
                $user->token = '';
                $user->password = $user->hash_pwd(
                    filter_input(INPUT_POST, 'password',
                                 FILTER_SANITIZE_SPECIAL_CHARS)
                );
                $user->update();
                header("Location: /");
                $GLOBALS['messages']['info'][] = "password successfully changed";
            }            
            else
                include "src/templates/base.php";
            
        }
        else if ($_SERVER['REQUEST_METHOD'] === 'GET')
            include "src/templates/base.php";
    }
    else
        raise_404();
}


/*
** Montage views :
*/


function montage($matches)
{
	$GLOBALS["content_src"] = "src/templates/montage.php";
    if (!$_SESSION['login'])
        raise_403();
	$GLOBALS['context']['pictures'] = "Picture"::read_by_user_id("User"::read($_SESSION['login'])->id);
    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
		if (validate_montage())
		{
            if (array_key_exists('image', $_FILES) && $_FILES["image"]["tmp_name"])
            {
				if ($fn = create_image_from_file())
				{
					if ($picture = Picture::read_by_filename($fn))
                    {
						header("Location: /picture/".$picture->id."/");
                    }
					else
						$GLOBALS['messages']['error'][] = "Reading image by filename failed.";
				}
				$GLOBALS['messages']['error'][] = "Image creation failed.";
            }
			else  if ($fn = create_png_from_hidden())
            {
				if ($picture = Picture::read_by_filename($fn))
					header("Location: /picture/".$picture->id."/");
				else
					$GLOBALS['messages']['error'][] = "Reading image by filename failed.";
            }
		}
    }
	include "src/templates/base.php";
}

/*
** Picture views :
*/

function picture_details($matches)
{
	$GLOBALS["content_src"] = "src/templates/picture_detail.php";

	if ($_SERVER['REQUEST_METHOD'] === "POST")
	{
		if (validate_comment())
		{
			$comment = new Comment(
				0,
				$GLOBALS['context']['user']->id,
				date("Y-m-d H:i:s"),
				$matches['pk'],
				$GLOBALS['validated_data']['comment']
			);
			$comment->create();
            $GLOBALS["messages"]["info"][] = "Your comment was successfully created.";
		}
	}
    if (!$picture = "Picture"::read($matches['pk']))
        raise_404();
    $GLOBALS["context"]["picture"] = $picture;
    include "src/templates/base.php";
}

function gallery($matches)
{
    $GLOBALS["content_src"] = "src/templates/gallery.php";
    if (!array_key_exists('page', $matches))
        $page = 1;
    else
        $page = $matches['page'];
    $GLOBALS["context"]["pictures"] = "Picture"::get_page($page);
    $pages = "Picture"::get_page_number();
    include "src/templates/base.php";
}

function picture_delete($matches)
{
    if (!$_SESSION['login'])
        raise_403();
    if (!$picture = "Picture"::read($matches['pk']))
        raise_404();
    if (!$owner = "User"::read_by_id($picture->user_id))
        raise_404();
    if ($owner->username !== $_SESSION['login'])
        raise_403();
    $picture->delete();
    header("Location: /");
}

function picture_like($matches)
{
    "Like"::like($matches['pk']);
    header("Location: /picture/".$matches['pk']);
    $GLOBALS["messages"]["info"][] = "exiting like view.";
}

function picture_unlike($matches)
{
    "Like"::unlike($matches['pk']);
    header("Location: /picture/".$matches['pk']);
}

/*
** Profile views :
*/

function profile($matches)
{
    $GLOBALS["content_src"] = "src/templates/public_profile.php";

    $GLOBALS["context"]["profile"] = "User"::read_by_id($matches['pk']);
    include "src/templates/base.php";
}

function selfprofile($matches)
{

    if (!$_SESSION['login'])
        raise_404();

    $GLOBALS["content_src"] = "src/templates/profile.php";
    $GLOBALS["context"]["profile"] = "User"::read($_SESSION['login']);

    if ($_SERVER['REQUEST_METHOD'] == "POST"
        && validate_button())
    {

        $part = filter_input(INPUT_POST, 'part', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($part === "mail_notif")
        {
            if (!array_key_exists('mail_notif', $_POST) || validate_profile_view())
            {
                if (array_key_exists('mail_notif', $_POST))
                {
                    if ($_POST['mail_notif'] == "True")
                        $GLOBALS["context"]["profile"]->mail_notif = true;
                    else
                        $GLOBALS["context"]["profile"]->mail_notif = false;
                }
                else
                {
                    $GLOBALS["context"]["profile"]->mail_notif = 0;
                }
                $GLOBALS["context"]["profile"]->update();
            }
        }

        else if ($part === "password")
        {
            if (validate_edit_password_profile())
            {
                $GLOBALS["context"]["profile"]->password = $GLOBALS["context"]["profile"]->hash_pwd(
                    filter_input(INPUT_POST, 'new_password',
                                 FILTER_SANITIZE_SPECIAL_CHARS)
                );
                $GLOBALS["context"]["profile"]->update(); 
            }

        }

        else if ($part === "username_email")
        {
            if (validate_edit_username_mail_profile())
            {
                $GLOBALS["context"]["profile"]->username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
                $GLOBALS["context"]["profile"]->email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
                $_SESSION["login"] = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS);
                // $_SESSION["login"] = $GLOBALS["context"]["profile"]->username;
                $GLOBALS["context"]["profile"]->update();
            }
        }
    }

    include "src/templates/base.php";
}

function deleteprofile($matches)
{
    $GLOBALS["content_src"] = "src/templates/delete_profile.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST')
    {
        if (validate_profile_deletion())
        {
            $user = "User"::read($_SESSION['login']);
            $user->delete();
            return logout($matches);
        }
    }
    include "src/templates/base.php";
}

/*
** Debug views :
*/

function php_infos($matches)
{
    phpinfo();
}

function serve_static($matches)
{
    $path = $GLOBALS["STATIC_ROOT"].$matches['path'];
    if (!file_exists($path) || !is_file($path) || !is_readable($path))
        raise_404();
    if (preg_match('#^img#', $matches['path']))
        $content_type = "image/png";
    else if (preg_match('#^css/#', $matches['path']))
        $content_type = "text/css";
    else if (preg_match('#^js/#', $matches['path']))
        $content_type = "text/javascript";
    else
        $content_type = "application/octet-stream";
    header("Content-type: ".$content_type);
    readfile($path);
}

function serve_media($matches)
{
    $path = $GLOBALS["MEDIA_ROOT"].$matches['path'];
    if (!file_exists($path) || !is_file($path) || !is_readable($path))
        raise_404();
    $content_type = "image/png";
    header("Content-type: ".$content_type);
    readfile($path);
}

function reset_db_view($matches)
{
	reset_db();
	header("Location: /");
}

?>
