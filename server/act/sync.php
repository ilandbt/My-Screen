<?php
/***
file: sync.php
receive commands at once

input: json
output:
0 - success
1 - json error
2 - sql error
***/



require_once('server.php');


try
{
	$s = new server($json);
	$s->action();
	$s->sqlQuery();
	echo '0';
}
catch ( Exception $e)
{
	//die($e->getMessage());
	echo '1';	//json error
	//echo 'Caught exception: ',  $e->getMessage(), "\n";
}



?>
