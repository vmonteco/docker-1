<?php

function connect_to_DB()
{
	$DB_DNS = $GLOBALS['DB_DNS'];
	$DB_USER = $GLOBALS['DB_USER'];
	$DB_PASSWORD = $GLOBALS['DB_PASSWORD'];
    try
    {
        $dbh = new PDO($DB_DNS, $DB_USER, $DB_PASSWORD, array(PDO::ATTR_PERSISTENT => true));
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }
    $GLOBALS['dbh'] = $dbh;
	return $dbh;
}

function init_DB() {
	User::create_table();
	Picture::create_table();
    Like::create_table();
	Comment::init_DB();
	
}

function reset_db()
{
	Like::delete_table();
	Comment::delete_table();
	Picture::delete_table();
	User::delete_table();
}

init_DB();

?>
