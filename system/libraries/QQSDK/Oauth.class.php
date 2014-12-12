<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright © 2013, Tencent Corporation. All rights reserved.
 */

require_once(dirname(__FILE__)."/Recorder.class.php");
require_once(dirname(__FILE__)."/URL.class.php");
require_once(dirname(__FILE__)."/ErrorCase.class.php");

class Oauth{

    const VERSION = "2.0";
    const GET_AUTH_CODE_URL = "https://graph.qq.com/oauth2.0/authorize";
    const GET_ACCESS_TOKEN_URL = "https://graph.qq.com/oauth2.0/token";
    const GET_OPENID_URL = "https://graph.qq.com/oauth2.0/me";

    protected $recorder;
    public $urlUtils;
    protected $error;
    

    function __construct(){
        $this->urlUtils = new URL();
        $this->error = new ErrorCase();
    }

    public function qq_login($appid,$callback,$scope){

        //-------生成唯一随机串防CSRF攻击
        //$state = md5(uniqid(rand(), TRUE));
		$state = 0;

        //-------构造请求参数列表
        $keysArr = array(
            "response_type" => "code",
            "client_id" => $appid,
            "redirect_uri" => $callback,
            "state" => $state,
            "scope" => $scope
        );

        $login_url =  $this->urlUtils->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
		return array(
			'code'=>0,
			'msg'=>1,
			'data'=>$login_url
		);
    }

    public function get_accesstoken($appId,$appKey,$callback){
		/*
        $state = $this->recorder->read("state");

        //--------验证state防止CSRF攻击
		
        if($_GET['state'] != $state){
            $this->error->showError("30001");
        }
		*/

        //-------请求参数列表
        $keysArr = array(
            "grant_type" => "authorization_code",
            "client_id" => $appId,
            "redirect_uri" => $callback,
            "client_secret" => $appKey,
            "code" => $_GET['code']
        );

        //------构造请求access_token的url
        $token_url = $this->urlUtils->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->urlUtils->get_contents($token_url);
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response  = substr($response, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($response);

            if(isset($msg->error)){
                return array(
					'code'=>$msg->error,
					'msg'=>$msg->error_description,
					'data'=>''
				);
            }
        }

        $params = array();
        parse_str($response, $params);

		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$params["access_token"]
		);

    }

    public function get_openid($access_token){

        //-------请求参数列表
        $keysArr = array(
            "access_token" => $access_token
        );

        $graph_url = $this->urlUtils->combineURL(self::GET_OPENID_URL, $keysArr);
        $response = $this->urlUtils->get_contents($graph_url);
        //--------检测错误是否发生
        if(strpos($response, "callback") !== false){

            $lpos = strpos($response, "(");
            $rpos = strrpos($response, ")");
            $response = substr($response, $lpos + 1, $rpos - $lpos -1);
        }

        $user = json_decode($response);
        if(isset($user->error)){
			return array(
					'code'=>$msg->error,
					'msg'=>$user->error_description,
					'data'=>''
				);
        }

        //------记录openid
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$user->openid
		);
    }
}
