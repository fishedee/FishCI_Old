<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_OAuth{
	var $CI;
	var $VERSION = "2.0";
    var $GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    var $GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	
	public function __construct(){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
	}
	
	public function getState(){
		return $_GET['state'];
	}
	
	public function getRedirectUrl($appid,$callback,$state,$scope){
		//-------构造请求参数列表
		$keysArr = array(
			"appid" => $appid,
			"redirect_uri" => $callback,
            "response_type" => "code",
            "scope" => $scope,
            "state" => $state,
        );
		
		return $this->GET_AUTH_CODE_URL.'?'.http_build_query($keysArr);
    }
	
	public function getAccessTokenAndOpenId($appId,$appKey,$callback){
		//-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "appid" => $appId,
            "secret" => $appKey,
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_ACCESS_TOKEN_URL,
			'data'=>$keysArr,
			'responseType'=>'json'
		));
		if( isset($httpResponse['body']['errcode']))
			throw new CI_MyException(1,$httpResponse['errmsg']['error_description']);
		
		return $httpResponse['body'];
    }
	
}
?>