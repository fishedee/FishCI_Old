<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_OAuth{
	var $CI;
	var $VERSION = "2.0";
	var $GET_JS_TICKET_URL = "https://api.weixin.qq.com/cgi-bin/ticket/getticket";
	var $GET_ACCESSTOKEN_URL = "https://api.weixin.qq.com/cgi-bin/token";
	var $GET_AUTH_CODE_URL_PC = "https://open.weixin.qq.com/connect/qrconnect";
    var $GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    var $GET_ACCESSTOKEN_AND_OPENID_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	
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

    public function getPcRedirectUrl($appid,$callback,$state,$scope){
    	$keysArr = array(
			"appid" => $appid,
			"redirect_uri" => $callback,
            "response_type" => "code",
            "scope" => $scope,
            "state" => $state,
        );
		
		return $this->GET_AUTH_CODE_URL_PC.'?'.http_build_query($keysArr);
    }

     public function getAccessToken($appId,$appKey){
    	//-------请求参数列表
        $keysArr = array(
            "grant_type" => "client_credential",
            "appid" => $appId,
            "secret" => $appKey
        );

        //------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_ACCESSTOKEN_URL,
			'data'=>$keysArr,
			'responseType'=>'json'
		));
		if( isset($httpResponse['body']['errcode']))
			throw new CI_MyException(1,$httpResponse['body']['errmsg']);

		return $httpResponse['body'];
    }

    public function getJsTicket($accessToken){
    	//-------请求参数列表
        $keysArr = array(
            "access_token" => $accessToken,
            "type" => 'jsapi'
        );

        //------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_JS_TICKET_URL,
			'data'=>$keysArr,
			'responseType'=>'json'
		));
		if( isset($httpResponse['body']['errcode']) && $httpResponse['body']['errcode'] != 0 )
			throw new CI_MyException(1,$httpResponse['body']['errmsg']);

		return $httpResponse['body'];
    }
	
	public function getAccessTokenAndOpenId($appId,$appKey){
		//-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "appid" => $appId,
            "secret" => $appKey,
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
		$httpResponse = $this->CI->http->ajax(array(
			'url'=>$this->GET_ACCESSTOKEN_AND_OPENID_URL,
			'data'=>$keysArr,
			'responseType'=>'json'
		));
		if( isset($httpResponse['body']['errcode']))
			throw new CI_MyException(1,$httpResponse['errmsg']['error_description']);
		
		return $httpResponse['body'];
    }

    public function checkServerValid($token){
		$signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $echostr = $_GET["echostr"];	
        		
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr != $signature ){
			throw new CI_MyException(1,'不合法的微信服务器');
		echo $echostr;
	}
}
	
}
?>