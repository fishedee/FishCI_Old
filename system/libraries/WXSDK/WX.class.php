<?php
/* PHP SDK
 * @version 2.0.0
 * @author connect@qq.com
 * @copyright ? 2013, Tencent Corporation. All rights reserved.
 */

class WX extends Oauth{
	const GET_AUTH_CODE_URL = "https://open.weixin.qq.com/connect/oauth2/authorize";
    const GET_ACCESS_TOKEN_URL = "https://api.weixin.qq.com/sns/oauth2/access_token";
	const GET_USER_INFO_URL = "https://api.weixin.qq.com/sns/userinfo";
	
	public function __construct(){
	}
	
	public function get_contents($url){
        if (ini_get("allow_url_fopen") == "1") {
            $response = file_get_contents($url);
        }else{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_URL, $url);
            $response =  curl_exec($ch);
            curl_close($ch);
        }

        //-------����Ϊ��
        if(empty($response)){
            $this->error->showError("50001");
        }

        return $response;
    }
	
	public function combineURL($baseURL,$keysArr){
        $combined = $baseURL."?";
        $valueArr = array();

        foreach($keysArr as $key => $val){
            $valueArr[] = "$key=".urlencode($val);
        }

        $keyStr = implode("&",$valueArr);
        $combined .= ($keyStr);
        
        return $combined;
    }
	
	public function getUserInfo($accessToken,$openId){
		//-------��������б�
        $keysArr = array(
            "access_token" => $accessToken,
            "openid" => $openId,
            "lang" => 'zh_CN'
        );

        //------��������access_token��url
        $token_url = $this->combineURL(self::GET_USER_INFO_URL, $keysArr);
        $response = $this->get_contents($token_url);
		$response = json_decode($response);
		if( isset($response->errcode)){
			return array(
				'code'=>$response->errcode,
				'msg'=>$response->errmsg,
				'data'=>''
			);
		}else{
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>array(
					'nickname'=>$response->nickname,
					'sex'=>$response->sex,
					'province'=>$response->province,
					'city'=>$response->city,
					'country'=>$response->country,
					'headimgurl'=>$response->headimgurl,
				)
			);
		}
	}
	
	public function wx_login($appid,$callback,$scope){
        //-------������������б�
        $keysArr = array(
			"appid" => $appid,
			"redirect_uri" => $callback,
            "response_type" => "code",
            "scope" => $scope,
            "state" => 0,
        );

        $login_url =  $this->combineURL(self::GET_AUTH_CODE_URL, $keysArr);
		$login_url  = $login_url.'#wechat_redirect';
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$login_url
		);
    }

    public function get_accesstoken_and_openid($appId,$appKey,$callback){
        //-------��������б�
        $keysArr = array(
            "grant_type" => "authorization_code",
            "appid" => $appId,
            "secret" => $appKey,
            "code" => $_GET['code']
        );

        //------��������access_token��url
        $token_url = $this->combineURL(self::GET_ACCESS_TOKEN_URL, $keysArr);
        $response = $this->get_contents($token_url);
		$response = json_decode($response);
		if( isset($response->errcode)){
			return array(
				'code'=>$response->errcode,
				'msg'=>$response->errmsg,
				'data'=>''
			);
		}else{
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>array(
					'accessToken'=>$response->access_token,
					'openId'=>$response->openid,
					'refreshToken'=>$response->refresh_token
				)
			);
		}
    }
};
?>