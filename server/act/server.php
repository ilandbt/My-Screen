<?php
class server
{
	//user extra info
	private $ip;
	private $now;

	//json input
	private $json;

	//user connection details
	private	$id;
	private	$email;
	private $pass;

	//action arrays
	private $sql;
	//private $mail;

	//possible actions
	private $act = array(
		"login" 		=> 0,	//zero is counter. for future use, maybe.
		"add_contacts" 		=> 0,
		"add_images" 		=> 0,
		"add_permissions" 	=> 0,
		"remove_contacts" 	=> 0,
		"remove_images" 	=> 0,
		"remove_permissions" 	=> 0
	);

//$schema = array(
//	"login" 		=> array("id","email","password"),
//	"add_contacts" 		=> 0,
//	"add_images" 		=> 0,
//	"add_permissions" 	=> array("user","images" => 0),
//	"remove_contacts" 	=> 0,
//	"remove_images" 	=> 0,
//	"remove_permissions" 	=> array("user","images" => 0)
//);


	public function __construct($arr)
	{
	//set input
        $this->json = json_decode($arr);
		if($this->json === null) {
			throw new Exception("Bad json input");
		}

		//save extra info
		$this->ip 	= $_SERVER['REMOTE_ADDR']; if ($ip=='') $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		$this->now 	= time(); //GMT 0

		//set arrays
		$this->sql 	= array();
		//$this->mail	= array();

		//set user connection details
		$this->user($this->json->login);
	}

	//getters
	public function getSQL(){ return $this->sql; }


	public function sqlQuery()
	{
		foreach ($s->getSQL() as $value)
			$sql .= "\n$value;";

		//connect to db
		require('../inc/conn.php');

		mysql_query($sql) or die('2');
		if (mysql_errno())
			echo mysql_errno() . ": " . mysql_error() . "\n";

		//close db
		mysql_close($link);
	}

	//call for all $act methods
	public function action($count=0)
	{
		foreach ($this->act as $key => &$value) {
			if ( array_key_exists($key, $this->json) ){
				$count++;
				$a = "\$this->$key(\$this->json->$key);";
				eval($a);
			}
		}

		return $count;
	}

	public function user($arr)
	{
		$this->id 	= $arr->id;
		$this->email 	= strtolower($arr->email);
		$this->pass 	= $arr->password;
		return 0;
	}

	public function login($arr)
	{
		$this->sql[] = "UPDATE `users` u
				SET u.`t_last`='$this->now', u.`ip`='$this->ip'
				WHERE u.`email`='$arr->email' AND BINARY u.`pass`='$arr->password'";
	}

	public function add_contacts($arr)
	{
		//start sql
		$temp_sql = 	"INSERT INTO `contacts` (`user`, `friend`)
				SELECT u1.`id`, u2.`id`
				FROM `users` as u1
				INNER JOIN `users` u2
				ON";

		//loop contacts
		foreach ($arr as &$value) {
			$temp_sql .= " u2.`email`='$value' OR";
		}
		$temp_sql = substr($temp_sql, 0, -3);  //cut the " OR" characters

		//end sql
		$temp_sql .= "\nWHERE u1.email = '$this->email' AND BINARY u1.`pass`='$this->pass'";

		//save it
		$this->sql[] = $temp_sql;
	}
	
	public function add_images($arr)
	{
		//start sql
		$temp_sql = 	"INSERT INTO `images` (`serial`, `rsa`, `owner`)
				VALUES";

		//loop images
		foreach ($arr as $key => &$value) {
			$temp_sql .= "\n('$key', '$value', '$this->id'),";
		}
		$temp_sql = substr($temp_sql, 0, -1);  //cut the "," character

		//save it
		$this->sql[] = $temp_sql;
	}
	
	public function add_permissions($arr)
	{
		//start sql
		$temp_sql = 	"INSERT INTO `permissions` (`image`, `user`)
				SELECT i.`id`, u2.`id`
				FROM `users` as u1
					INNER JOIN `users` u2
					ON u2.`email`='$arr->user'
					INNER JOIN `contacts` c
					ON c.`user`=u1.`id` AND c.`friend`=u2.`id`
					INNER JOIN `images` i
					ON i.`owner`=u1.`id` AND (";

		//loop images
		foreach ($arr->images as &$value) {
			$temp_sql .= "i.`serial`='$value' OR ";
		}
		$temp_sql = substr($temp_sql, 0, -4) . ')';  //cut the " OR" characters

		//end sql
		$temp_sql .= "\nWHERE u1.email = '$this->email' AND BINARY u1.`pass`='$this->pass'";

		//save it
		$this->sql[] = $temp_sql;
	}
	
	//Reminder:
	//when removing contact, remove all of it's images permissions as well
	public function remove_contacts($arr)
	{
		return 0;
	}

	//Reminder:
	//When removing image, remove all of it's permissions as well
	public function remove_images($arr)
	{
		return 0;
	}
	
	public function remove_permissions($arr)
	{
		return 0;
	}
}
?>
