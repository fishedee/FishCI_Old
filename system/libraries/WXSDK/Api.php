<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_Api{
	var $CI;
	var $keysArr;
	
	public function __construct($appId,$accessToken,$openId){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
		$this->keysArr = array(
			"access_token" => $accessToken,
            "openid" => $openId,
            "lang" => 'zh_CN'
		);
	}
	
	private function applyApi($url,$type,$keysArgv){
		 //-------��������б�
		$keysArgv = array_merge($this->keysArr,$keysArgv);
		
		//------��������access_token��url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$url,
			'type'=>$type,
			'data'=>$keysArgv,
			'responseType'=>'json'
		));
		
		if( isset($httpResponse['body']['errcode']) )
			throw new CI_MyException(1,$httpResponse['body']['errmsg']);
		
		return $httpResponse['body'];
	}
	
	public function getUserInfo(){
        return $this->applyApi('https://api.weixin.qq.com/sns/userinfo','get',array());
    }
	
}
?>