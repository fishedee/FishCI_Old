<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
class CI_Http{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	public function localAjax( $option ){
		//处理option
		$defaultOption = array(
			'url'=>'',
			'header'=>array(),
			'type'=>'',
			'data'=>'',
			'dataType'=>'text',
			'timeout'=>5,
			'async'=>false,
		);
		foreach( $option as $key=>$value )
			$defaultOption[$key] = $value;
		$defaultOption['url'] = 'http://localhost'.$option['url'];
		$defaultOption['header'][] = 'Host: '.$_SERVER['HTTP_HOST'];
		//业务逻辑
		return $this->ajax($defaultOption);
	}
	
	public function ajax($option){
		//处理option
		$defaultOption = array(
			'url'=>'',
			'header'=>array(),
			'type'=>'',
			'data'=>'',
			'dataType'=>'text',
			'timeout'=>5,
			'async'=>false,
		);
		foreach( $option as $key=>$value )
			$defaultOption[$key] = $value;
		//处理参数
		$url = trim($defaultOption['url']);
		$data = $defaultOption['data'];
		$dataType = $defaultOption['dataType'];
		$header = $defaultOption['header'];
		$type = strtolower($defaultOption['type']);
		$isAsync = $defaultOption['async'];
		if( $isAsync == false )
			$timeout = $defaultOption['timeout']*1000;
		else
			$timeout = 50;
		if( $dataType == 'text' ){
			if( is_array($data))
				$data = http_build_query($data);
		}else if( $dataType == 'json'){
			if( is_array($data))
				$data = json_encode($data);
		}else{
			return array(
				'code'=>1,
				'msg'=>'未确定的data type'.$dataType,
				'data'=>$data,
			);
		}
		//执行抓取
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt($curl, CURLOPT_TIMEOUT_MS , $timeout);
		curl_setopt($curl, CURLOPT_NOSIGNAL, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		if( strncmp($url,'https',5) == 0 ){
			curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,1);
			curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
		}
		if(count($header) != 0 )
			curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
		if( $type == 'get'){
			//get donothing
		}else if( $type == 'post'){
			//post
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
			curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		}else if( $type == 'delete' ){
			//delete
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'DELETE');
		}else if( $type == 'put' ){
			//put
			curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
		}else{
			return array(
				'code'=>1,
				'msg'=>'未确定的HTTP Type'.$type,
				'data'=>$data,
			);
		}
		
		$data = curl_exec($curl);
		$headerData = curl_getinfo($curl);
		curl_close($curl);
		if( $isAsync == false && $data == false ){
			return array(
				'code'=>1,
				'msg'=>'连接服务器失败 '.$url,
				'data'=>''
			);
		}
		//返回结果
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>array(
				'header'=>$headerData,
				'body'=>$data
			),
		);
	}
	/*
	*@deprecated 废弃，应该用ajax
	*/
	public function sync($option){
		//处理option
		$defaultOption = array(
			'host'=>'',
			'port'=>'',
			'isHttps'=>false,
			'url'=>'/',
		);
		foreach( $option as $key=>$value)
			$defaultOption[$key] = $value;
		//处理参数
		$url = '';
		if( $defaultOption['isHttps'] )
			$url .= 'https://';
		else
			$url .= 'http://';
		$url .= $defaultOption['host'];
		if( $defaultOption['port'] != '')
			$url .= ':'.$defaultOption['port'];
		$url .= $defaultOption['url'];
		$defaultOption['url'] = $url;
		return $this->ajax($defaultOption);
	}
	/*
	*@deprecated 废弃，应该用ajax
	*/
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