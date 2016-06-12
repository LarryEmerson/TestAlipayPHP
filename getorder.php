<?php

require_once("alipay.config.php");
require_once("lib/mysql.php"); 
require_once("lib/alipay_core.function.php");
require_once("lib/alipay_rsa.function.php");
 

function generatetradeno($id){ 
 	$ran=rand(0,99);
	$code=date("YmdHis")
	.sprintf('%01d',$ran/10)
	.sprintf('%02d',intval($id))
	.sprintf('%01d',$ran%10)
	;
	return $code;
} 
error_log('|||||||||||||||||||||||||||||'.json_encode($_POST),3,'logs/alipay.log'); 
 
// $db = new datamanager;
// $db->charset = "utf8";
// $db->connect('115.29.238.177', 'root', 'CB1qaz2wsx', 'alipay', 0);

if(array_key_exists('id', $_POST)){
	$id						=$_POST['id'];
	$tradeinfo				=$db->fetch('select * from tradelist where id='.$id.' and trade_status=\'WAIT_BUYER_PAY\''); 
	$order['partner']		='2088511678677741';
	$order['seller_id']		='pay@360cbs.com';
	$order['out_trade_no']	=$tradeinfo['out_trade_no'];
	$order['subject']		=$tradeinfo['subject'];
	$order['body']			=$tradeinfo['body'];
	$order['total_fee']		=$tradeinfo['total_fee'];
	$order['notify_url']	='http://360cbs.hicp.net:8001/PHP-UTF-8/notify_url.php';
	$order['service']		='mobile.securitypay.pay';
	$order['payment_type']	='1';
	$order['_input_charset']='utf-8';
	$order['it_b_pay']		='5m';
	$order['show_url']		='m.alipay.com';
	 
	$order['partner']		='"'.$order['partner']			.'"';
	$order['seller_id']		='"'.$order['seller_id']		.'"';
	$order['out_trade_no']	='"'.$order['out_trade_no']		.'"';
	$order['subject']		='"'.$order['subject']			.'"';
	$order['body']			='"'.$order['body']				.'"';
	$order['total_fee']		='"'.$order['total_fee']		.'"';
	$order['notify_url']	='"'.$order['notify_url']		.'"';
	$order['service']		='"'.$order['service']			.'"';
	$order['payment_type']	='"'.$order['payment_type']		.'"';
	$order['_input_charset']='"'.$order['_input_charset']	.'"';
	$order['it_b_pay']		='"'.$order['it_b_pay']			.'"';
	$order['show_url']		='"'.$order['show_url']			.'"';
	//
	 	
	$order=paraFilter($order);	 
	$linkString=createLinkstring($order);  
	$sign=rsaSign($linkString,$alipay_config['private_key_path']);
	$sign=urlencode($sign);  
	// 
	$result['order']=$linkString; 
	$result['sign']=$sign;  
	echoresponse($result);
}else{ 
	if(!array_key_exists('userid', $_POST)) {
		echoresponse('',1,'请指定用户ID');
		exit(); 
	}else if(!array_key_exists('productid', $_POST)) {
		echoresponse('',1,'请指定购买商品的ID');
		exit(); 
	}else{
		$userid					=$_POST['userid'];
		$productid 				= $_POST['productid'];  
		$hasorder				=$db->fetch('select * from tradelist where'
								.' userid='.$userid
								.' and productid='.$productid
								.' and trade_status<>\'TRADE_FINISHED\' and trade_status<>\'TRADE_CLOSED\''); 
		if($hasorder){
			if($hasorder['trade_status']=='WAIT_BUYER_PAY'/**/){
				echoresponse($hasorder['id'],2,'订单已生成，等待买家付款');
			}else if($hasorder['trade_status']=='TRADE_SUCCESS'){
				echoresponse('',3,'订单已生成，卖家已发货');
			}
		}else{
			$product   				= $db->fetch('select * from product where id='.$productid); 
			$order['partner']		='2088511678677741';
			$order['seller_id']		='pay@360cbs.com';
			$order['out_trade_no']	=generatetradeno($productid);
			$order['subject']		=$product['subject'];
			$order['body']			=$product['body'];
			$order['total_fee']		=$product['price'];
			$order['notify_url']	='http://360cbs.hicp.net:8001/PHP-UTF-8/notify_url.php';
			$order['service']		='mobile.securitypay.pay';
			$order['payment_type']	='1';
			$order['_input_charset']='utf-8';
			$order['it_b_pay']		='5m';
			$order['show_url']		='m.alipay.com';
			//
			$db->execute(
				'insert into tradelist '
				.'(partner,seller,out_trade_no,`subject`,body,total_fee,payment_type,it_b_pay,userid,productid,quantity)'
				.' values '
				.'('
				.'\''	.$order['partner']			.'\','
				.'\''	.$order['seller_id']		.'\',' 
				.'\''	.$order['out_trade_no']		.'\',' 
				.'\''	.$order['subject']			.'\',' 
				.'\''	.$order['body']				.'\',' 
				.'\''	.$order['total_fee']		.'\',' 
						.$order['payment_type']		.'  ,' 
				.'\''	.$order['it_b_pay']			.'\',' 
						.$userid					.'  ,'
						.$productid 				.',1)'  
			);
			//
			$order['partner']		='"'.$order['partner']			.'"';
			$order['seller_id']		='"'.$order['seller_id']		.'"';
			$order['out_trade_no']	='"'.$order['out_trade_no']		.'"';
			$order['subject']		='"'.$order['subject']			.'"';
			$order['body']			='"'.$order['body']				.'"';
			$order['total_fee']		='"'.$order['total_fee']		.'"';
			$order['notify_url']	='"'.$order['notify_url']		.'"';
			$order['service']		='"'.$order['service']			.'"';
			$order['payment_type']	='"'.$order['payment_type']		.'"';
			$order['_input_charset']='"'.$order['_input_charset']	.'"';
			$order['it_b_pay']		='"'.$order['it_b_pay']			.'"';
			$order['show_url']		='"'.$order['show_url']			.'"';
			 
			//
			 	
			$order=paraFilter($order);	 
			$linkString=createLinkstring($order);  
			$sign=rsaSign($linkString,$alipay_config['private_key_path']);
			$sign=urlencode($sign);  
			// 
			$result['order']=$linkString; 
			$result['sign']=$sign;   
			echoresponse($result);
		} 
	}
}
?>