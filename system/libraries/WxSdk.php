<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/WXSDK/OAuth.php');
require_once(dirname(__FILE__).'/WXSDK/Api.php');
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
	
	public function getLoginUrl($loginInfo)
	{
		$qc = new WXSdk_OAuth();
		return $qc->getRedirectUrl(
			$this->appId,
			$this->callback,
			$loginInfo,
			$this->scope
		);
	}
	
	public function getLoginInfo()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getState();
	}
	
	public function getAccessTokenAndOpenId()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getAccessTokenAndOpenId(
			$this->appId,
			$this->appKey,
			$this->callback
		);
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new WXSdk_Api(
			$this->appId,
			$accessToken,
			$openId
		);
		return $qc->getUserInfo();
	}
	
	
	
	
}