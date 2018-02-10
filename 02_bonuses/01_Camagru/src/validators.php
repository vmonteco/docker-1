<?php

function login_validate()
{
	if (!array_key_exists('login', $_POST)
		|| !array_key_exists('password', $_POST)
		|| !($login = filter_input(INPUT_POST, 'login', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=> "User"::$LOGIN_REGEXP))))
		|| !($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)))
	{
        $GLOBALS['messages']['error'][] = "One of the fields is missing or invalid.";
		return false;
	}
	if (!($user = "User"::read($login)) || !($user->is_active)
        || !($user::hash_pwd($password) === $user->password))
	{
		sleep(2);
        $GLOBALS['messages']['error'][] = "Invalid login informations.";
		return (false);
	}
	return true;
}

function signup_validate()
{
    $GLOBALS['context']['error'] = '';
	if ((!array_key_exists('login', $_POST))
		|| (!array_key_exists('password', $_POST))
		|| (!array_key_exists('password-confirm', $_POST))
		|| (!array_key_exists('email', $_POST))
		|| !($login = filter_input(
            INPUT_POST,
            'login',
            FILTER_VALIDATE_REGEXP,
            array("options"=>array("regexp"=>User::$LOGIN_REGEXP))))
		|| !($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))
		|| !($password_confirm = filter_input(INPUT_POST, 'password-confirm', FILTER_SANITIZE_SPECIAL_CHARS))
		|| !($email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))
	)
	{
        $GLOBALS['messages']['error'][] = "One of the fields is missing.";
		return false;
	}
    if (!($email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)))
    {
        $GLOBALS['messages']['error'][] = "Invalid email input.";
        return false;
    }
	if (!(preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password)))
	{
		$GLOBALS['messages']['error'][] = "The password must contain letters and digits";
		return false;
	}
	if ($password !== $password_confirm)
	{
        $GLOBALS['messages']['error'][] = "Passwords don't match.";
		return false;
	}
    if (strlen($password) < 8)
    {
        $GLOBALS['messages']['error'][] = "Password too short (min : 8 chars).";
		return false;
    }
	if ("User"::read($login))
	{
        $GLOBALS['messages']['error'][] = "Username already used.";
		return false;
	}
	if (($user = "User"::read_by_email($email)))
	{
        $GLOBALS['messages']['error'][] = "Email already used.";
		return false;
	}
    return true;
}

function get_reset_link_validate()
{
    if (!array_key_exists('email', $_POST))
    {
        $GLOBALS['messages']['error'][] = "Missing field.";
        return false;
    }
    if (!($email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)))
    {
        $GLOBALS['messages']['error'][] = "Invalid email input.";
        return false;
    }
    return true;
}

function reset_validate()
{
    if (!array_key_exists('password', $_POST)
        || !array_key_exists('password-confirm', $_POST)
        || !($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS))
		|| !($password_confirm = filter_input(INPUT_POST, 'password-confirm', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS['messages']['error'][] = "Missing field.";
        return false;
    }
    if ($password !== $password_confirm)
    {
        $GLOBALS['messages']['error'][] = "Password don't match.";
        return false;        
    }
    if (strlen($password) < 8)
    {
        $GLOBALS['messages']['error'][] = "Password too short (min : 8 chars).";
		return false;
    }
	if (!(preg_match('/[A-Za-z]/', $password) && preg_match('/[0-9]/', $password)))
	{
		$GLOBALS['messages']['error'][] = "The password must contain letters and digits";
		return false;
	}
    return true;
}

function validate_montage()
{
    if (!array_key_exists('mask', $_POST))
    {
        $GLOBALS['messages']['error'][] = 'You have to submit a filter.';
        return false;
    }
    if (!is_numeric($_POST['mask']) || !($_POST['mask'] === '0' || array_key_exists(strval($_POST['mask']) - 1, $GLOBALS['masks'])))
    {
        $GLOBALS['messages']['error'][] = 'You have to submit a valid filter.';
        return false;
    }
    if (!array_key_exists('image', $_FILES)
        and !array_key_exist('image-hidden', $_POST))
    {
        $GLOBALS['messages']['error'][] = 'You have to upload an image.';
        return false;
    }
    if (array_key_exists('image', $_FILES) && $_FILES['image']['size'] > 0)
    {
        $file_extension = strrchr($_FILES['image']['name'], ".");
        if (!in_array($file_extension,
                     array (".jpg", ".jpeg", ".png",
                            ".JPG", ".JPEG", ".PNG")))
        {
            $GLOBALS['messages']['error'][] = "You have to upload a valid image.";
            return false;
        }
    }
	return true;
}

function validate_profile_deletion()
{
    if (!$_SESSION['login'])
        raise_403();
    if (!array_key_exists('password', $_POST)
        || !($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS['messages']['error'][] = "You have to enter your password!";
        return false;
    }
    $user = "User"::read($_SESSION["login"]);
    if ($user->hash_pwd($password) !== $user->password)
    {
        $GLOBALS['messages']['error'][] = "Incorrect password!";
        return false;
    }
    return true;
}

function validate_profile_view()
{
    if (!$_SESSION['login'])
        raise_403();
    if (!array_key_exists('mail_notif', $_POST)
        || !($password = filter_input(INPUT_POST, 'mail_notif', FILTER_VALIDATE_BOOLEAN)))
    {
        $GLOBALS['messages']['error'][] = "Invalid value.";
        return false;
    }
    return true;
}

function validate_comment()
{
    if (!$_SESSION['login'])
        raise_403();
	if (!array_key_exists('comment', $_POST)
		|| (!$GLOBALS['validated_data']['comment'] = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS['messages']['error'][] = "You have to enter a comment!";
        return false;
    }
	return true;
}

function validate_edit_username_mail_profile()
{
    if (!$_SESSION['login'])
        raise_403();
    $user = "User"::read($_SESSION['login']);
    if (!array_key_exists('email', $_POST)
        || !array_key_exists('username', $_POST)
        || !array_key_exists('password', $_POST)
        || !($email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS))
        || !($username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS))
        || !($password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS['messages']['error'][] = "Missing field.";
        return false;
    }
	if (!($username = filter_input(INPUT_POST, 'username', FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=> "User"::$LOGIN_REGEXP)))))
	{
		$GLOBALS['messages']['error'][] = "Invalid username (special characters).";
		return false;
	}
    if (strlen($username) > 40)
    {
        $GLOBALS['messages']['error'][] = "Username too long.";
        return false;        
    }
    if (!($email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)))
    {
        $GLOBALS['messages']['error'][] = "Invalid email input.";
        return false;
    }
    if (strlen($email) > 191)
    {
        $GLOBALS['messages']['error'][] = "Email too long.";
        return false;        
    }
    if ($email !== $user->email && "User"::read_by_email($email))
    {
        $GLOBALS['messages']['error'][] = "Email already used.";
        return false;
    }
    if ($username !== $user->username && "User"::read($username))
    {
        $GLOBALS['messages']['error'][] = "Username already used.";
        return false;
    }
    if ($user::hash_pwd($password) !== $user->password)
    {
        $GLOBALS['messages']['error'][] = "Invalid password.";
        return false;
    }    
    return true;
}

function validate_edit_password_profile()
{
    if (!$_SESSION['login'])
        raise_403();
    $user = "User"::read($_SESSION['login']);
    if (!array_key_exists('old_password', $_POST)
        || !array_key_exists('new_password', $_POST)
        || !array_key_exists('new_password_confirm', $_POST)
        || !($old_password = filter_input(INPUT_POST, 'old_password', FILTER_SANITIZE_SPECIAL_CHARS))
        || !($new_password = filter_input(INPUT_POST, 'new_password', FILTER_SANITIZE_SPECIAL_CHARS))
		|| !($new_password_confirm = filter_input(INPUT_POST, 'new_password_confirm', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS['messages']['error'][] = "Missing field.";
        return false;
    }
    if (!($user::hash_pwd($old_password) === $user->password))
    {
        $GLOBALS['messages']['error'][] = "Invalid password.";
        return false;
    }
	if (!(preg_match('/[A-Za-z]/', $new_password) && preg_match('/[0-9]/', $new_password)))
	{
		$GLOBALS['messages']['error'][] = "The password must contain letters and digits";
		return false;
	}
    if ($new_password !== $new_password_confirm)
    {
        $GLOBALS['messages']['error'][] = "Password don't match.";
        return false;        
    }
    if (strlen($new_password) < 8)
    {
        $GLOBALS['messages']['error'][] = "Password too short (min : 8 chars).";
		return false;
    }
    return true;
}

function validate_button()
{
    if (!array_key_exists('part', $_POST)
        || !($button = filter_input(INPUT_POST, 'part', FILTER_SANITIZE_SPECIAL_CHARS)))
    {
        $GLOBALS["messages"]["error"][] = "Invalid part value.";
        return false;
    }
    return true;
}

?>
