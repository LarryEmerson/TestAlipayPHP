<?php

require_once("alipay.config.php");
require_once("lib/mysql.php"); 

// $db = new datamanager;
// $db->charset = "utf8";
// $db->connect('115.29.238.177', 'root', 'CB1qaz2wsx', 'alipay', 0);

if(!array_key_exists('userid', $_POST)) {
	echoresponse('',1,'请指定用户ID');
	exit(); 
}else{
	$result=$db->query('select * from tradelist where userid = '.$_POST['userid']);
	echoresponse($result);
}
?>