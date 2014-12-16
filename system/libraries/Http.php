<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
class CI_Http{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	public function async($option){
		//处理option
		$defaultOption = array(
			'host'=>'',
			'port'=>'',
			'url'=>'/',
			'cookie'=>$_COOKIE,
			'type'=>'post',
			'data'=>array(),
		);
		$allHost = $_SERVER['HTTP_HOST'];
		if( strpos($allHost,":") == false ){
			$defaultOption['host'] = $allHost;
			$defaultOption['port'] = 80;
		}else{
			$defaultOption['host'] = substr($allHost,0,strpos($allHost,":"));
			$defaultOption['port'] = substr($allHost,strpos($allHost,":")+1);
		}
		foreach( $option as $key=>$value )
			$defaultOption[$key] = $value;
		if( $defaultOption['type'] != 'post')
			return array(
				'code'=>1,
				'msg'=>'目前仅支持post请求',
				'data'=>'',
			);
		//连接服务器
		$errno = 0;
		$errstr = 1;
		$fp = @fsockopen(
			$defaultOption['host'], 
			$defaultOption['port'], 
			$errno,
			$errstr, 
			30
		);
		if( !$fp )
			return array(
				'code'=>1,
				'msg'=>'错误码：'.$errno.'错误描述：'.$errstr,
				'data'=>$defaultOption
			);
		//拼接数据
		$postData = '';
		foreach( $defaultOption['data'] as $key=>$value ){
			$postData .= $key.'='.urlencode($value).'&';
		}
		$postDataLen = strlen($postData);
		$cookieData = '';
		foreach( $defaultOption['cookie'] as $key=>$value ){
			$cookieData .= $key.'='.urlencode($value).';';
		}
		$url = $defaultOption['url'];
		$host = $defaultOption['host'];
		$port = $defaultOption['port'];
		$data =  "POST $url HTTP/1.0\r\n".
			"Host: $host:$port\r\n".
			"Cookie: $cookieData".
			"Connection: Close\r\n".
			"Content-Type: application/x-www-form-urlencoded\r\n".
			"Content-Length: $postDataLen\r\n\r\n".
			"$postData";
		//发送数据
		fputs($fp, $data);
		fclose($fp);
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$data,
		);
	}
};