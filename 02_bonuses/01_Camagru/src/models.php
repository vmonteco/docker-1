<?php

include "mails/mails.php";

/*
 *  This files contains the application models definition.
 *  The models also contain a init_DB method to init database tables.
 */

class User {
	
    /*
    ** Queries :
    */

    static public $CREATE_TABLE_QUERY = "CREATE TABLE IF NOT EXISTS user (id INT PRIMARY KEY AUTO_INCREMENT, username VARCHAR(40) UNIQUE, password VARCHAR(255), email VARCHAR(191) UNIQUE, is_active BOOLEAN DEFAULT FALSE, mail_notif BOOLEAN DEFAULT TRUE, token VARCHAR(40));";
    static public $DELETE_TABLE_QUERY = "DROP TABLE IF EXISTS user;";

    
	static public $CREATE_ENTRY_QUERY = "INSERT INTO user (username, password, email, is_active, token) VALUES (:username, :password, :email, :is_active, :token);";
	static public $READ_ENTRY_QUERY = "SELECT * FROM user WHERE username=:username;";
	static public $READ_ENTRY_BY_EMAIL_QUERY = "SELECT * FROM user WHERE email=:email;";
    static public $READ_ENTRY_BY_ID_QUERY = "SELECT * FROM user WHERE id=:id;";	
	static public $UPDATE_ENTRY_QUERY = "UPDATE user
SET username=:username, password=:password, email=:email, is_active=:is_active, mail_notif=:mail_notif, token=:token
WHERE id=:id;";
	static public $DELETE_ENTRY_QUERY = "DELETE FROM user WHERE id=:id;";
	static public $GET_ENTRIES_QUERY = "SELECT * FROM user;";
    static public $LOGIN_REGEXP = "#^[a-zA-Z0-9]+$#";


    /*
    ** Constructor
    */

    
    function __construct($id, $username, $password, $email, $is_active, $mail_notif, $token='')
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->is_active = $is_active;
        $this->mail_notif = $mail_notif;
        $this->token = $token;
    }


    /*
     **  Table management methods.
    */
    
    
	static function create_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$CREATE_TABLE_QUERY);
        $query->execute();
	}

    
    static function delete_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$DELETE_TABLE_QUERY);
        $query->execute();
    }
    
    
    /*
    ** CRUD
    */


    function create()
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$CREATE_ENTRY_QUERY);
        if ($query->execute(
            array(
                ':username' => $this->username,
                ':password' => $this->password,
                ':email' => $this->email,
                ':is_active' => $this->is_active,
                //':mail_notif' => $this->mail_notif,
                ':token' => $this->token
            )
        ))
			return true;
		return false;
    }

    
    static function read($username)
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$READ_ENTRY_QUERY);
        $query->execute(
            array(
                ':username' => $username,
            )
        );
		if (!$query->rowCount())
			return false;
        $entry = $query->fetch();
        $user = new User(
            $entry["id"],
            $entry["username"],
            $entry["password"],
            $entry["email"],
            $entry["is_active"],
            $entry["mail_notif"],
            $entry["token"]
        );
        return ($user);
    }

	static function read_by_email($email)
	{
        $db = connect_to_DB();
        $query = $db->prepare(User::$READ_ENTRY_BY_EMAIL_QUERY);
        $query->execute(
            array(
                ':email' => $email,
            )
        );
		if (!$query->rowCount())
			return false;
        $entry = $query->fetch();
        $user = new User(
            $entry["id"],
            $entry["username"],
            $entry["password"],
            $entry["email"],
            $entry["is_active"],
            $entry["mail_notif"],
            $entry["token"]
        );
		return ($user);
	}

	static function read_by_id($id)
	{
        $db = connect_to_DB();
        $query = $db->prepare(User::$READ_ENTRY_BY_ID_QUERY);
        $query->execute(
            array(
                ':id' => $id,
            )
        );
        if (!($entry = $query->fetch()))
            return false;
        $user = new User(
            $entry["id"],
            $entry["username"],
            $entry["password"],
            $entry["email"],
            $entry["is_active"],
            $entry["mail_notif"],
            $entry["token"]
        );
        return ($user);
	}
    
    function update()
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$UPDATE_ENTRY_QUERY);
        return ($query->execute(
            array(
                ':id' => $this->id,
                ':username' => $this->username,
                ':password' => $this->password,
                ':email' => $this->email,
                ':is_active' => $this->is_active,
                ':mail_notif' => $this->mail_notif,
                ':token' => $this->token
            )
        ));
    }

    
    function delete()
    {
        $db = connect_to_DB();
        $query = $db->prepare(User::$DELETE_ENTRY_QUERY);
        $query->execute(
            array(
                ':id' => $this->id
            )
        );
    }

    
    /*
    ** Other methods :
    */
    
    
    function set_token()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < 40; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        $this->token = $this->hash_token($randomString);
		return $randomString;
    }

    function generate_reset_link()
    {
        $token = $this->set_token();
        $this->update();
        $http_host = $_SERVER['HTTP_HOST'];
		$link = $http_host."/reset_password/".$this->id."-".$token;
        if (isset($_SERVER['HTTPS']))
            $link = 'https://'.$link;
        else
            $link = 'http://'.$link;
        return $link;
    }
    
    function send_reset_link()
    {
        if ($link = $this->generate_reset_link())
        {
			$headers = "From: \"Camagru\"<contact@vmonteco.ninja>\n";
			$headers .= "Content-Type: text/html; charset=\"utf-8\"";
			return mail(
				$this->email,
				"Camagru : Reset link",
                sprintf($GLOBALS['reset_link'], $this->username, $link),
				$headers);
        }
    }

    function generate_activation_link()
    {
        if ($this->is_active)
            return false;
        $token = $this->set_token();
        $this->update();
        $http_host = $_SERVER['HTTP_HOST'];
        $link = $http_host."/activate_account/".$this->id."-".$token;
        if (isset($_SERVER['HTTPS']))
            $link = 'https://'.$link;
        else
            $link = 'http://'.$link;
        return $link;
    }
    
    function send_activation_mail()
    {
        if ($link = $this->generate_activation_link())
        {
			$headers = "From: \"Camagru\"<contact@vmonteco.ninja>\n";
			$headers .= "Content-Type: text/html; charset=\"utf-8\"";
            if ($GLOBALS['DEBUG'])
                error_log($link);
			return mail(
				$this->email,
				"Camagru : activation link",
				sprintf($GLOBALS['activation_link'], $this->username, $link),
				$headers);
        }
		return False;
    }
    

    /*
    ** Hash methods :
    */
    
    static function hash_pwd($pwd)
    {
        return (hash("sha256", $pwd));
    }


    static function hash_token($token)
    {
        return (hash("md5", $token));
    }
    
}


class Picture {

    /*
    ** Queries :
    */
    
    
    static public $CREATE_TABLE_QUERY = "CREATE TABLE IF NOT EXISTS picture (id INT PRIMARY KEY AUTO_INCREMENT, filename VARCHAR(191) UNIQUE, user INT NOT NULL, created DATETIME, FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE);";
    static public $DELETE_TABLE_QUERY = "DROP TABLE IF EXISTS picture;";

	static public $CREATE_ENTRY_QUERY = "INSERT INTO picture (filename, user, created) VALUES (:filename, :user_id, :created );";
	static public $READ_ENTRY_QUERY = "SELECT * FROM picture WHERE id=:id;";
    static public $READ_ENTRY_BY_FILENAME_QUERY = "SELECT * FROM picture WHERE filename=:filename;";
	static public $READ_ENTRY_BY_USER_ID_QUERY = "SELECT * FROM picture WHERE user=:user_id;";
    static public $GET_PAGE_QUERY = "SELECT * FROM picture ORDER BY created DESC LIMIT :index , :n;";
    static public $GET_ALL_PICTURES_QUERY = "SELECT * FROM picture;";
	static public $UPDATE_ENTRY_QUERY = "UPDATE picure SET filename=:filename, user=:user_id, created=:created  WHERE id=:id;";
	static public $DELETE_ENTRY_QUERY = "DELETE FROM picture WHERE id=:id;";
    static public $DELETE_ENTRIES_BY_USER_QUERY = "DELETE FROM picture WHERE user=:user_id;";

    static public $GET_LIKES_QUERY = "SELECT * FROM `like` WHERE picture=:picture_id;";
    
    /*
    ** Constructor :
    */
    
    
    function __construct($id, $user_id, $filename, $created)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->user = User::read_by_id($user_id);
        $this->filename = $filename;
        $this->created = $created;
        $this->likes = Picture::get_likes_number($this->id);
        $this->is_liked = Like::is_liked($this->id);
        $this->comments = Comment::read_by_picture($this->id);
    }

    
    /*
    ** Table management methods :
    */
	
	static function create_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Picture"::$CREATE_TABLE_QUERY);
        $query->execute();
	}


	static function delete_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Picture"::$DELETE_TABLE_QUERY);
        $query->execute();
	}

    
    /*
    ** CRUD :
    */
    
    function create()
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$CREATE_ENTRY_QUERY);
        if ($query->execute(
            array(
                ':filename' => $this->filename,
                ':user_id' => $this->user_id,
                ':created' => $this->created
            )
        ))
            return true;
        return false;
    }

    static function read($id)
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$READ_ENTRY_QUERY);
        $query->execute(
            array(
                ':id' => $id,
            )
        );
        if (!$query->rowCount())
            return false;
        $entry = $query->fetch();
        $picture = new Picture(
            $entry['id'],
            $entry['user'],
            $entry['filename'],
            $entry['created']
        );
        return ($picture);
    }

    static function read_by_user_id($user_id)
    {
        $db = connect_to_DB();
        $query = $db->prepare("Picture"::$READ_ENTRY_BY_USER_ID_QUERY);
        $query->execute(array(':user_id' => $user_id));
		$res = array_map(
			function ($entry){
				return new Picture($entry['id'],
								   $entry['user'],
								   $entry['filename'],
								   $entry['created']);			
			},
			$query->fetchAll()
		);
        return ($res);
    }

	
	
    static function read_by_filename($filename)
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$READ_ENTRY_BY_FILENAME_QUERY);
        $query->execute(
            array(
                ':filename' => $filename,
            )
        );
        if (!($entry = $query->fetch()))
			return (false);
        $picture = new Picture(
            $entry['id'],
            $entry['user'],
            $entry['filename'],
            $entry['created']
        );
        return ($picture);
    }
    
    static function get_page_number()
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$GET_ALL_PICTURES_QUERY);
        $query->execute(array());
        return (ceil($query->rowCount() / $GLOBALS['PAGINATE_BY']));
    }
    
    static function get_page($page)
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$GET_PAGE_QUERY);
        $query->bindValue(':index', (int) $GLOBALS['PAGINATE_BY'] * ($page - 1),  PDO::PARAM_INT);
        $query->bindValue(':n', (int) $GLOBALS['PAGINATE_BY'],  PDO::PARAM_INT);
        $query->execute();
        $res = $query->fetchAll();
        $res = array_map(function ($entry){
            return new Picture($entry['id'],
                               $entry['user'],
                               $entry['filename'],
                               $entry['created']);
        },
            $res);
        if (!count($res) && $page > 1)
            raise_404();
        return ($res);
    }

    static function get_all_pictures()
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$GET_ALL_PICTURES_QUERY);
        $query->execute(array ());
		$res = array_map(
			function ($entry){
				return new Picture($entry['id'],
								   $entry['user'],
								   $entry['filename'],
								   $entry['created']);			
			},
			$query->fetchAll()
		);
		return $res;
    }
    
    function update()
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$UPDATE_ENTRY_QUERY);
        $query->execute(
            array(
                ':id' => $this->id,
                ':filename' => $this->filename,
                ':user_id' => $this->user_id,
                ':created' => $this->created
            )
        );
    }

    function delete()
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$DELETE_ENTRY_QUERY);
        $res = $query->execute(array(":id" => $this->id));
        if ($res)
            unlink($GLOBALS['MEDIA_ROOT'].$this->filename);
    }

    static function delete_by_user($user_id)
    {
        $pictures = Picture::read_by_user_id($user_id);
        foreach($pictures as $picture){
            $picture->delete();
        }
    }
    
    static function get_likes_number($picture_id)
    {
        $db = connect_to_DB();
        $query = $db->prepare(Picture::$GET_LIKES_QUERY);
        $query->execute(array(":picture_id" => $picture_id));
        return $query->rowCount();
    }

	function is_logged_as_author()
	{
		$author = "User"::read_by_id($this->user_id);
		return ($author->username === $_SESSION['login']);
	}
    
}

class Like {

    static public $CREATE_TABLE_QUERY = "CREATE TABLE IF NOT EXISTS `like` (user INT NOT NULL, picture INT NOT NULL, FOREIGN KEY (user) REFERENCES user(id) ON DELETE CASCADE, FOREIGN KEY (picture) REFERENCES picture(id) ON DELETE CASCADE, CONSTRAINT couple PRIMARY KEY (user, picture));";
    static public $DELETE_TABLE_QUERY = "DROP TABLE IF EXISTS `like`;";

    static public $READ_QUERY = "SELECT * FROM `like` WHERE user=:user_id AND picture=:picture_id;";
    static public $CREATE_QUERY = "INSERT INTO `like` (user, picture) VALUES (:user_id, :picture_id);";
    static public $DELETE_QUERY = "DELETE FROM `like` WHERE user=:user_id AND picture=:picture_id;";
    
    /*
    ** Constructor :
    */
    
    function __construct($user_id, $picture_id)
    {
        $this->user_id = $user_id;
        $this->picture_id = $picture_id;
    }
    
    /*
    ** Table management methods :
    */


	static function create_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Like"::$CREATE_TABLE_QUERY);
        $query->execute();
	}


	static function delete_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Like"::$DELETE_TABLE_QUERY);
        $query->execute();
	}

    /*
    **   CRUD :
    */

    function create()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Like"::$CREATE_QUERY);
        $query->execute(array(
            ':user_id' => $this->user_id,
            ':picture_id' => $this->picture_id
        ));
    }

    function delete()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Like"::$DELETE_QUERY);
        $query->execute(array(
            ':user_id' => $this->user_id,
            ':picture_id' => $this->picture_id
        ));
    }

    static function read($user_id, $picture_id)
    {
        $db = connect_to_DB();
        $query = $db->prepare("Like"::$READ_QUERY);
        $query->execute(array(
            ':user_id' => $user_id,
            ':picture_id' => $picture_id
        ));
        if (!$query->rowCount())
            return false;
        $like = $query->fetch();
        return (new Like($like['user'], $like['picture']));
    }

    /*
    **   Like/Unlike functions :
    */

    static function is_liked($picture_id)
    {
        if (!$_SESSION['login'])
            return false;
        if ("Like"::read($GLOBALS['context']['user']->id, $picture_id))
            return true;
        return false;
    }
    
    static function like($picture_id)
    {
        if (!$_SESSION['login'])
            raise_403();
        if (!($picture = "Picture"::read($picture_id)))
            raise_404();
        if ($picture->is_logged_as_author())
            raise_403();
        $user = "User"::read($_SESSION['login']);
        if (!Like::is_liked($picture_id))
        {
            $like = new Like($user->id, $picture_id);
            $like->create();
        }
    }

    static function unlike($picture_id)
    {
        if (!$_SESSION['login'])
            raise_403();
        if (!($picture = "Picture"::read($picture_id)))
            raise_404();
        if ($picture->is_logged_as_author())
            raise_403();
        $user = "User"::read($_SESSION['login']);
        if (Like::is_liked($picture_id))
        {
            $like = Like::read($user->id, $picture_id);
            $like->delete();
        }
    }
    
}

class Comment {

    static public $CREATE_TABLE_QUERY = "CREATE TABLE IF NOT EXISTS comment (id INT PRIMARY KEY AUTO_INCREMENT, author_id INT NOT NULL, picture_id INT NOT NULL, created DATETIME, content TINYTEXT, FOREIGN KEY (author_id) REFERENCES user(id) ON DELETE CASCADE, FOREIGN KEY (picture_id) REFERENCES picture(id) ON DELETE CASCADE);";
    static public $DELETE_TABLE_QUERY = "DROP TABLE comment;";

    static public $CREATE_QUERY = "INSERT INTO comment (author_id, picture_id, created, content) VALUES (:author_id, :picture_id, :created, :content);";
	static public $READ_QUERY = "SELECT * FROM comment WHERE id=:id";
	static public $READ_BY_PICTURE_QUERY = "SELECT * FROM comment WHERE picture_id=:picture_id ORDER BY created DESC;";
	static public $UPDATE_ENTRY_QUERY = "UPDATE comment SET author_id=:author_id, picture_id=:picture_id, created=:created WHERE id=:id;";
	static public $UPDATE_DELETE_QUERY = "DELETE FROM comment WHERE id=:id;";
	
    /*
    ** Constructor
    */

    function __construct($id, $author_id, $created, $picture_id, $content)
    {
        $this->id = $id;
        $this->author_id = $author_id;
        $this->created = $created;
        $this->picture_id = $picture_id;
        $this->content = $content;
        $this->user = "User"::read_by_id($author_id);
    }
    
    /*
    ** Table management methods
    */
    
	static function init_DB() {
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$CREATE_TABLE_QUERY);
        $query->execute();
	}


	static function delete_table()
    {
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$DELETE_TABLE_QUERY);
        $query->execute();
	}

    /*
    ** CRUD :
    */

	function create()
	{
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$CREATE_QUERY);
        $query->execute(array(
            ':author_id' => $this->author_id,
            ':picture_id' => $this->picture_id,
			':created' => $this->created,
			':content' => $this->content
        ));
		$picture = "Picture"::read($this->picture_id);
		if (!$picture->is_logged_as_author())
		{
			if (!$this->send_notif())
				$GLOBALS['messages']['error'][] = "A problem occured while sending notification";
		}

	}
	
	function send_notif()
	{
		$picture = "Picture"::read($this->picture_id);
		$dest = "User"::read_by_id($picture->user_id);
        if ($dest->mail_notif)
        {
            $author = "User"::read_by_id($this->author_id);
            $headers = "From: \"Canagru\"<contact@vmonteco.ninja>\n";
            $headers .= "Content-Type: text/html; charset=\"utf-8\"";
            $http_host = $_SERVER['HTTP_HOST'];
            $link = $http_host."/picture/".$picture->id;
            if (isset($_SERVER['HTTPS']))
                $link = 'https://'.$link;
            else
                $link = 'http://'.$link;
			return mail(
				$dest->email,
                "Camagru: A comment was posted on your picture",
                sprintf($GLOBALS['notification_link'], $dest->username, $link),
				$headers);
        }
        else
            return true;
    }
	
	static function read($id)
	{
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$READ_QUERY);
		$query->execute(array(':id' => $id));
		if ($query->rowCount() === 0)
			raise_404();
		$entry = $query->fetch();
		$res = new Comment(
			$entry['id'],
			$entry['author_id'],
			$entry['created'],
			$entry['picture_id'],
			$entry['content']
		);
		return $res;
	}

	static function read_by_picture($picture_id)
	{
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$READ_BY_PICTURE_QUERY);
		$query->execute(array(
			':picture_id' => $picture_id
		));
		$entries = $query->fetchAll();
		$res = array_map(
			function($entry){
				return new Comment(
					$entry['id'],
					$entry['author_id'],
					$entry['created'],
					$entry['picture_id'],
					$entry['content']
				);
			},
			$entries
		);
		return $res;
	}

	function update()
	{
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$UPDATE_QUERY);
		$query->execute(array(
			':id' => $this->id,
			':author_id' => $this->author_id,
			':picture_id' => $this->picture_id,
			':content' => $this->content
		));
	}

	function delete()
	{
        $db = connect_to_DB();
        $query = $db->prepare("Comment"::$DELETE_QUERY);
		$query->execute(array(':id' => $this->id));
	}
	
}

?>
