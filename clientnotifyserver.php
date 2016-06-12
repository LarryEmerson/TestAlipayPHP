<?php
	
require_once("alipay.config.php");
require_once("lib/mysql.php"); 

error_log('|||||||||||||||||||||||||||||'.json_encode($_POST),3,'logs/alipay.log');
// {memo = "";result = "partner=\"2088511678677741\"&seller_id=\"pay@360cbs.com\"&out_trade_no=\"ABCDEFG1234567\"&subject=\"\U652f\U4ed8\U5b9d\U6d4b\U8bd5\U5546\U54c1\U6807\U9898\"&body=\"\U652f\U4ed8\U5b9d\U6d4b\U8bd5\U5546\U54c1\U63cf\U8ff0\"&total_fee=\"0.01\"&notify_url=\"http://360cbs.hicp.net:8001/PHP-UTF-8/notify_url.php\"&service=\"mobile.securitypay.pay\"&payment_type=\"1\"&_input_charset=\"utf-8\"&it_b_pay=\"30m\"&show_url=\"m.alipay.com\"&success=\"true\"&sign_type=\"RSA\"&sign=\"brGpY0mocDJmiEb09uVgA4eGcdetpgPsMLpbnm0bQbCd2Q88L5AhxE08TQd9bjLuX1r2oLE7rdgMksUVFBFmgNtClwWKf0gJUkmU/B52hTVQqwt3Ao23FEwNgDfomlOO+kEaKzHFL8cxGaAtjUEmObwoFXOWiBsyhBW7WvM0wLo=\"";resultStatus = 9000;}

$trade_info				=$db->fetch('select * from tradelist where out_trade_no = '.$_POST['out_trade_no']);
if($trade_info){
	$trade_info_update	=$db->execute('update tradelist set '
						.' client_trade_status = \''.$_POST['success'].'\''
						);
}

?>