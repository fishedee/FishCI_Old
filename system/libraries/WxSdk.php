<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/WXSDK/WX.class.php');
class CI_WxSdk{
	var $appId;
	var $appKey;
	var $callback;
	var $scope;
	public function __construct($option)
    {
		$this->CI = & get_instance();
		$this->appId = $option['appId'];
		$this->appKey = $option['appKey'];
		$this->callback = $option['callback'];
		$this->scope = $option['scope'];
	}
	
	public function login($loginInfo)
	{
		$qc = new WX();
		$result = $qc->wx_login(
			$this->appId,
			($this->callback)."?loginInfo=".$loginInfo,
			$this->scope
		);
		return $result;
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new WX();
		$result = $qc->getUserInfo($accessToken,$openId);
		return $result;
	}
	
	public function getAccessTokenAndOpenId()
	{
		$qc = new WX();
		$result = $qc->get_accesstoken_and_openid(
			$this->appId,
			$this->appKey,
			$this->callback
		);
		return $result;
	}
	
	public function getLoginInfo(){
		if( isset($_GET['loginInfo']) == false )
			return array(
				'code'=>1,
				'msg'=>'¶ªÊ§µÇÂ¼ÐÅÏ¢',
				'data'=>'',
			);
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$_GET['loginInfo']
		);
	}
}