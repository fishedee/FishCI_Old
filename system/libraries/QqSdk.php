<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/QQSDK/qqConnectAPI.php');
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
	
	public function login($loginInfo)
	{
		$qc = new QC();
		$result = $qc->qq_login(
			$this->appId,
			($this->callback)."?loginInfo=".$loginInfo,
			$this->scope
		);
		return $result;
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new QC(
			$this->appId,
			$accessToken,
			$openId
		);
		$data = $qc->get_user_info();
		return array(
			'code'=>0,
			'msg'=>'',
			'data'=>$data
		);
	}
	
	public function getAccessToken()
	{
		$qc = new QC();
		$result = $qc->get_accesstoken(
			$this->appId,
			$this->appKey,
			$this->callback
		);
		return $result;
	}
	
	public function getOpenId($accessToken)
	{
		$qc = new QC();
		$result = $qc->get_openid(
			$accessToken
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