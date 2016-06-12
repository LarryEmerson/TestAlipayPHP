<?php
/* *
 * 功能：支付宝服务器异步通知页面
 * 版本：3.3
 * 日期：2012-07-23
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
 * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
 */

// {"discount":"0.00","payment_type":"1","subject":"\u652f\u4ed8\u5b9d\u6d4b\u8bd5\u5546\u54c1\u6807\u9898","trade_no":"2015110600001000930008973018","buyer_email":"larryemerson@163.com","gmt_create":"2015-11-06 10:13:20","notify_type":"trade_status_sync","quantity":"1","out_trade_no":"ABCDEFG1234567","seller_id":"2088511678677741","notify_time":"2015-11-06 10:13:21","body":"\u652f\u4ed8\u5b9d\u6d4b\u8bd5\u5546\u54c1\u63cf\u8ff0","trade_status":"WAIT_BUYER_PAY","is_total_fee_adjust":"Y","total_fee":"0.01","seller_email":"pay@360cbs.com","price":"0.01","buyer_id":"2088502145710939","notify_id":"e51039665d67954471a68ae8e348867776","use_coupon":"N","sign_type":"RSA","sign":"XZv39EpQYKnZTYH51QYAqm1\/SA\/Kv2yeHEKJLkQhh6dwoexiRT5IZ1IfyCfK7EoPOOfMWOb4yCQIPEZpNKjknb\/oTTvP2+0qhSMjrTxvFWFm1gzMY9QusuBsQ6Dd3ErOX0vt5wQJQHy74\/+ppv3XNStxMMLzqejyBv3JrU7ESFU="}{"discount":"0.00","payment_type":"1","subject":"\u652f\u4ed8\u5b9d\u6d4b\u8bd5\u5546\u54c1\u6807\u9898","trade_no":"2015110600001000930008973018","buyer_email":"larryemerson@163.com","gmt_create":"2015-11-06 10:13:20","notify_type":"trade_status_sync","quantity":"1","out_trade_no":"ABCDEFG1234567","seller_id":"2088511678677741","notify_time":"2015-11-06 10:13:21","body":"\u652f\u4ed8\u5b9d\u6d4b\u8bd5\u5546\u54c1\u63cf\u8ff0","trade_status":"TRADE_SUCCESS","is_total_fee_adjust":"N","total_fee":"0.01","gmt_payment":"2015-11-06 10:13:21","seller_email":"pay@360cbs.com","price":"0.01","buyer_id":"2088502145710939","notify_id":"f1e6f35bcbcb814e8029328093be839a76","use_coupon":"N","sign_type":"RSA","sign":"mcX2KEiQTJLkRcRER8KabeJdKTQ2DKdxbZ5Qm24Ij9qt+4o\/ZClhxtoN+H\/2mlGcq0czUOXYTCWgzCzPLvKqUEF+OX+Fr\/YrX9P8nGFYTtnaRdB8o\/3PVC2Uq6zoE5fpEAcFI5RK4PBD\/OZ1JB9n6J1KOlkDFND8DuYqPZ1R2KY=“}
	
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
require_once("lib/mysql.php"); 

//计算得出通知验证结果
$alipayNotify = new AlipayNotify($alipay_config);
$verify_result = $alipayNotify->verifyNotify();

error_log('||||||||||||||||||||||||tradelist_notify_url=>'.json_encode($_POST),3,'logs/alipay.log');

if($verify_result) {//验证成功 
	//请在这里加上商户的业务逻辑程序代
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
	//商户订单号
	$out_trade_no 	= $_POST['out_trade_no'];
	//支付宝交易号
	$trade_no 		= $_POST['trade_no']; 
	//交易状态
	$trade_status 	= $_POST['trade_status']; 
	//==update table
// 	if($trade_status=='TRADE_CLOSED')
	$tradeinfo		=$db->fetch('select * from tradelist where '
						.' out_trade_no =\''	.$_POST['out_trade_no']		.'\'' 
						.' and total_fee='		.$_POST['total_fee']
						.' and quantity='		.$_POST['quantity']);
	error_log('|||||||||||||||||||||||||||$tradeinfo=>'.json_encode($tradeinfo).'\n',3,'logs/alipay.log');
	if($tradeinfo){
		
		if($tradeinfo['trade_status']=='TRADE_FINISHED'&&$trade_status=='TRADE_SUCCESS'){
			
		}else{
			$db->execute('update tradelist set 
			  discount=\''				.$_POST['discount']				.'\''.
			',trade_no=\''				.$_POST['trade_no']				.'\''.
			',buyer_email=\''			.$_POST['buyer_email']			.'\''.
			',notify_type=\''			.$_POST['notify_type']			.'\''.
			',seller_id=\''				.$_POST['seller_id']			.'\''.
			',notify_time=\''			.$_POST['notify_time']			.'\''.
			',trade_status=\''			.$_POST['trade_status']			.'\''.
			',is_total_fee_adjust=\''	.$_POST['is_total_fee_adjust']	.'\''.
			',seller_email=\''			.$_POST['seller_email']			.'\''.
			',price=\''					.$_POST['price']				.'\''.
			',buyer_id=\''				.$_POST['buyer_id']				.'\''.
			',notify_id=\''				.$_POST['notify_id']			.'\''.
			',use_coupon=\''			.$_POST['use_coupon']			.'\''.
			' where out_trade_no =\''	.$_POST['out_trade_no']			.'\''.
			' and partner=\''			.$_POST['seller_id']			.'\''.
			' and total_fee=\''			.$_POST['total_fee']			.'\''.
			' and seller=\''			.$_POST['seller_email']			.'\''.
			' and quantity=' 			.$_POST['quantity']);
		}
		
	
	    if($_POST['trade_status'] == 'TRADE_FINISHED') {
		    $db->execute('update tradelist set gmt_payment='.$_POST['gmt_payment']);
			//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序 
			//注意：
			//退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
			//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的 
	        //调试用，写文本函数记录程序运行情况是否正常
	        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
	    } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
			//判断该笔订单是否在商户网站中已经做过处理
			//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
			//如果有做过处理，不执行商户的业务程序 
			//注意：
			//付款完成后，支付宝系统发送该交易状态通知
			//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的 
	        //调试用，写文本函数记录程序运行情况是否正常
	        //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
	    } 
	    echo "success";
	    exit(0);
	    		//请不要修改或删除 
	}else{
		$insert_new=	$db->execute('insert into tradelist (
		discount,
		payment_type,
		subject,
		trade_no,
		buyer_email,
		gmt_create,
		notify_type,
		quantity,
		out_trade_no,
		seller_id,
		notify_time,
		body,
		trade_status,
		is_total_fee_adjust,
		total_fee,seller_email,
		price,
		buyer_id,
		notify_id,
		use_coupon) values ('
		.'\''		.$_POST['discount']				.'\','
					.$_POST['payment_type']			.'	,'
		.'\''		.$_POST['subject']				.'\','
		.'\''		.$_POST['trade_no']				.'\','
		.'\''		.$_POST['buyer_email']			.'\','
		.'\''		.$_POST['gmt_create']			.'\','
		.'\''		.$_POST['notify_type']			.'\','
					.$_POST['quantity']				.'	,'
		.'\''		.$_POST['out_trade_no']			.'\','
		.'\''		.$_POST['seller_id']			.'\','
		.'\''		.$_POST['notify_time']			.'\','
		.'\''		.$_POST['body']					.'\','
		.'\''		.$_POST['trade_status']			.'\','
		.'\''		.$_POST['is_total_fee_adjust']	.'\','
		.'\''		.$_POST['total_fee']			.'\','
		.'\''		.$_POST['seller_email']			.'\','
		.'\''		.$_POST['price']				.'\','
		.'\''		.$_POST['buyer_id']				.'\','
		.'\''		.$_POST['notify_id']			.'\','
		.'\''		.$_POST['use_coupon']			.'\')');  
		if($insert_new){
			echo "success";
			exit(0);
		}
	}  
}  
echo "fail";
?>