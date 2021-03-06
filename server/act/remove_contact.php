<?php
/***
file: remove_contact.php
remove contact member

input: email, freind-email \ id
output:
0 - success
1 - no input \ no email \ no password \ no freind (email\id)
2 - bad email \ password
***/


//settings
include('../inc/settings.php');

//error - no email
if ( !$_POST || !isset($_POST['email']) || !isset($_POST['password']) || !(isset($_POST['femail']) || isset($_POST['fid'])) )
	exit('1');


//varify email and password
//-----------------------------------------------------------------
$email 	= strtolower($_POST['email']);
$pass 	= $_POST['password'];
$femail	= strtolower($_POST['femail']);


//connect to db
require('../inc/conn.php');

//del the contact
$sql = "DELETE c, p
	FROM `contacts` c
		INNER JOIN `users` u1
		ON u1.`email`='$email'
		INNER JOIN `users` u2
		ON u2.`email`='$femail'
		INNER JOIN `images` i
		ON i.`owner`=u1.`id`
		INNER JOIN `permissions` p
		ON p.`user`=u2.`id` AND p.`image`=i.`id`
	WHERE c.`user`=u1.`id` AND c.`friend`=u2.`id` AND BINARY u1.`pass`='$pass'
	";
mysql_query($sql) or die('2');


//if success
if (mysql_affected_rows() > 0)
	echo '0';

//bad email/pass
else
	echo '2';

	
//close db
mysql_close($link);


//-----------------------------------------------------------------

?>
