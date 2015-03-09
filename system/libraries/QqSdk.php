<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/QqSdk/OAuth.php');
require_once(dirname(__FILE__).'/QqSdk/Api.php');
class CI_QqSdk{
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
		$qc = new QQSdk_OAuth();
		return $qc->getRedirectUrl(
			$this->appId,
			$this->callback,
			$loginInfo,
			$this->scope
		);
	}
	
	public function getLoginInfo()
	{
		$qc = new QQSdk_OAuth();
		return $qc->getState();
	}
	
	public function getAccessToken()
	{
		$qc = new QQSdk_OAuth();
		return $qc->getAccessToken(
			$this->appId,
			$this->appKey,
			$this->callback
		);
	}
	
	public function getOpenId($accessToken)
	{
		$qc = new QQSdk_OAuth();
		return $qc->getOpenId(
			$accessToken
		);
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new QQSdk_Api(
			$this->appId,
			$accessToken,
			$openId
		);
		return $qc->getUserInfo();
	}
	
	
	
	
}