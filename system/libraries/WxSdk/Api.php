<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class WXSdk_Api{
	var $CI;
	var $keysArr;
	var $appId;
	
	public function __construct($appId,$accessToken,$openId){
		$this->CI = & get_instance();
		$this->CI->load->library('http');
		$this->keysArr = array(
			"access_token" => $accessToken,
            "openid" => $openId,
            "lang" => 'zh_CN'
		);
		$this->appId = $appId;
	}

	/**
	 * 	作用：产生随机字符串，不长于32位
	 */
	private function createNoncestr( $length = 32 ) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		}  
		return $str;
	}

	/**
	 * 	作用：格式化参数，签名过程需要使用
	 */
	private function formatBizQueryParaMap($paraMap, $urlencode)
	{
		$buff = "";
		ksort($paraMap);
		foreach ($paraMap as $k => $v)
		{
			if( $v == '')
				continue;
		    if($urlencode)
		    {
			   $v = urlencode($v);
			}
			//$buff .= strtolower($k) . "=" . $v . "&";
			$buff .= $k . "=" . $v . "&";
		}
		$reqPar = "";
		if (strlen($buff) > 0) 
		{
			$reqPar = substr($buff, 0, strlen($buff)-1);
		}
		return $reqPar;
	}

	/**
	 * 	作用：生成签名
	 */
	private function getSign($Obj) 
	{
		$Parameters = array();
		foreach ($Obj as $k => $v)
		{
			$Parameters[$k] = $v;
		}
		//签名步骤一：按字典序排序参数
		ksort($Parameters);

		//签名步骤二：拼接参数
		$String = $this->formatBizQueryParaMap($Parameters, false);

		//签名步骤二：sha1哈希
		return sha1($String);
	}

	
	private function applyApi($url,$type,$keysArgv){
		 //-------请求参数列表
		$keysArgv = array_merge($this->keysArr,$keysArgv);

		//------构造请求access_token的url
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

    public function getUserInfoWithUnionId(){
        return $this->applyApi('https://api.weixin.qq.com/cgi-bin/user/info','get',array());
    }

    public function getJsConfig($jsApiTicket,$url){
    	if( strpos($url,'#') !== false )
    		$url = substr($url,0,strpos($url,'#'));
    	$result = array();
    	$result['appId'] = $this->appId;
    	$result['timestamp'] = time().'';
    	$result['nonceStr'] = $this->createNoncestr();
    	$result['signature'] = $this->getSign(array(
    		'jsapi_ticket'=>$jsApiTicket,
    		'noncestr'=>$result['nonceStr'],
    		'timestamp'=>$result['timestamp'],
    		'url'=>$url
    	));
    	return $result;
    }
}
?>