<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/WxSdk/OAuth.php');
require_once(dirname(__FILE__).'/WxSdk/Api.php');
require_once(dirname(__FILE__).'/WxSdk/Pay.php');
class CI_WxSdk{
	var $option;

	public function __construct($option)
    {
		$this->CI = & get_instance();
		$this->option = $option;
	}
	
	public function getLoginUrl($callback,$callbackInfo,$scope)
	{
		$qc = new WXSdk_OAuth();
		return $qc->getRedirectUrl(
			$this->option['appId'],
			$callback,
			$callbackInfo,
			$scope
		);
	}
	
	public function getLoginCallBackInfo()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getState();
	}

	public function checkServerValid($token)
	{
		$qc = new WXSdk_OAuth();
		return $qc->checkServerValid($token);
	}
	
	public function getAccessTokenAndOpenId()
	{
		$qc = new WXSdk_OAuth();
		return $qc->getAccessTokenAndOpenId(
			$this->option['appId'],
			$this->option['appKey']
		);
	}
	
	public function getUserInfo($accessToken,$openId)
	{
		$qc = new WXSdk_Api(
			$this->option['appId'],
			$accessToken,
			$openId
		);
		return $qc->getUserInfo();
	}

	public function getUserInfoWithUnionId($accessToken,$openId)
	{
		$qc = new WXSdk_Api(
			$this->option['appId'],
			$accessToken,
			$openId
		);
		return $qc->getUserInfoWithUnionId();
	}

	public function getOrderPayInfo($openId,$dealId,$dealDesc,$dealFee,$dealNotify){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->unifiedOrder(array(
			'openid'=>$openId,
			'out_trade_no'=>$dealId,
			'body'=>$dealDesc,
			'total_fee'=>$dealFee,
			'notify_url'=>$dealNotify,
			'trade_type'=>'JSAPI'
		));
	}

	public function getJsPayInfo($prepay_id){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->jsPayOrder(
			$prepay_id
		);
	}

	public function getPayCallBackInfo(){
		$qc = new WXSdk_Pay(
			$this->option['appId'],
			$this->option['appKey'],
			$this->option['mchId'],
			$this->option['mchKey'],
			$this->option['mchSslCert'],
			$this->option['mchSslKey']
		);

		return $qc->notifyOrder();
	}

}