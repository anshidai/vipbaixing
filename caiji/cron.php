<?php 

header("Content-type:text/html; charset=utf-8");

ini_set('display_errors', 1);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
//error_reporting(E_ALL);

require 'simple_html_dom.php';
require 'cj.class.php'; //引入采集扩展文件
require 'mysql.class.php';


define('ROOTPATH', substr(__FILE__, 0 , -8));

//数据库配置
$config['db'][1]['dbhost'] = 'localhost';        
$config['db'][1]['dbuser'] = 'root';        
$config['db'][1]['dbpw'] = '';        
$config['db'][1]['dbcharset'] = 'utf8';        
$config['db'][1]['pconnect'] = 0;            
$config['db'][1]['dbname'] = 'vipbaixing';         
$config['db'][1]['tablepre'] = 'wp_';
$config['db']['slave'] = array();

$db = & DB::object('db_mysql');
$db->set_config($config['db']);
$db->connect();


/*
require 'vipbuluo.php';
$Vipbuluo = new Vipbuluo();

//$Vipbuluo->cjXml('http://www.vipbuluo.com/sitemap.xml');

$Vipbuluo->cjSiteUrl();
$Vipbuluo->insertSiteUrl();
echo "url complete\r\n";

$Vipbuluo->cjContent();
echo "content complete\n";

$Vipbuluo->cjInsertDB();
echo "insert db complete\n";
*/

require 'vipzhanghao.php';
$Vipzhanghao = new Vipzhanghao();

$Vipzhanghao->cjUrl();
echo "cj url complete\r\n";

$Vipzhanghao->insertSiteUrl();
echo "add url complete\r\n";

$Vipzhanghao->cjContent();
echo "cj content complete\r\n";

$Vipzhanghao->cjInsertDB();
echo "insert db complete\r\n";

echo "all complete\r\n";

echo "all complete\n";


function pos_html($start_tag, $end_tag, $html = '')
{
	//$start_tag = str_replace('"', '\"', $start_tag);
	//$end_tag = str_replace('"', '\"', $end_tag);
	
	$start_pos = strpos($html, $start_tag);
	$end_pos = strpos($html, $end_tag);

	return substr($html, $start_pos, $end_pos - $start_pos);
	
}


//curl 提交
function curl_http($url, $header = array())
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	
	if(!empty($header)) {
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	}

	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	//释放curl句柄
	curl_close($ch);
	
	return $output;
}


/*
'header'=>"Host: xxx.com\r\n" . 
		"Accept-language: zh-cn\r\n" . 
		"User-Agent: Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; 4399Box.560; .NET4.0C; .NET4.0E)" .
		"Accept: *//*"
*/ 
function dfile_get_contents($url, $header = '', $timeout = 60)
{
	$opts = array(
		'http' => array(
			'method' => "GET",
			'timeout' => $timeout,
		)
	); 
	if(!empty($header)) {
		$opts['http']['header'] = $header;
	}
	
	$context = stream_context_create($opts);
	$content = @file_get_contents($url, false, $context);
	return trim($content);
}

//生成http header头信息
function http_header($referer = '')
{
	$header = array ();
	$header [] = 'User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; zh-CN; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8';
	$header [] = 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
	$header [] = 'Accept-Encoding: gzip, deflate';
	$header [] = 'Accept-Language: zh-cn,zh;q=0.5';
	$header [] = 'Connection: Keep-Alive';
	if($referer) $header [] = 'Referer: '.$referer;
	return $header;
} 

//生成指定位数时间戳
function getTimestamp($digits = false) 
{  
	$digits = $digits > 10 ? $digits : 10;  
	$digits = $digits - 10;  
	if ((!$digits) || ($digits == 10)) {  
		return time();  
	}  
	else {  
		return number_format(microtime(true), $digits, '', '');  
	}  
} 

function sendEmail($to, $title = '', $content = '')
{
	
	require 'PHPMailer/class.phpmailer.php';
	require 'PHPMailer/class.smtp.php';

	$mail = new PHPMailer(true); 
	$mail->IsSMTP(); //启用SMTP
	$mail->CharSet = 'UTF-8'; //设置邮件的字符编码，这很重要，不然中文乱码 
	$mail->SMTPAuth = true; //启用smtp认证
	$mail->Port = 25; 
	$mail->Host = "smtp.163.com";  //smtp服务器的名称
	$mail->Username = "lba8610@163.com"; //发件人邮箱名
	$mail->Password = "153421423163"; //发件人邮箱密码
	//$mail->IsSendmail(); //如果没有sendmail组件就注释掉，否则出现“Could not execute: /var/qmail/bin/sendmail ”的错误提示 
	//$mail->AddReplyTo("phpddt1990@163.com", "mckee"); //回复地址 
	$mail->From = "lba8610@163.com";  //址发件人地
	$mail->FromName = "www.vipbaixing.com";  //发件人姓名
	$mail->AddAddress($to); 
	$mail->Subject = $title? $title: "采集网址内容出现错误"; //邮件主题
	$mail->Body = $content? $content: "采集网址内容出现错误，请及时查看"; //邮件内容 
	$mail->AltBody = "采集网址内容出现错误，请及时查看"; //当邮件不支持html时备用显示，可以省略 
	$mail->WordWrap = 80; // 设置每行字符串的长度 
	//$mail->AddAttachment("f:/test.png"); //可以添加附件 
	$mail->IsHTML(true); 
	$mail->Send(); 
	echo "邮件已发送\r\n"; 
}


function dump($str)
{
	echo '<pre>';
	var_dump($str);
}