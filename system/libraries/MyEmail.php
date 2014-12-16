<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(dirname(__FILE__).'/PHPMailer/class.phpmailer.php');
class CI_MyEmail{
	var $CI;
	public function __construct(){
		$this->CI = & get_instance();
	}
	private function utf8Encode( $str ){
		return "=?utf-8?B?".base64_encode($str)."?=";
	}
	public function send( $toUser  , $toUserName, $title,$message ){
		$mail    = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->Host       = $this->CI->config->item('email_host');
		$mail->Port       = $this->CI->config->item('email_port');
		$mail->Username   = $this->CI->config->item('email_user');
		$mail->Password   = $this->CI->config->item('email_pass');
		$mail->Charset 	  = 'UTF-8';
		$mail->SMTPDebug  = 1; 
		$mail->IsHTML(true); 
		$mail->SetFrom(
			$this->CI->config->item('email_user'), 
			$this->utf8Encode($this->CI->config->item('email_user_name'))
		);
		$mail->AddReplyTo(
			$this->CI->config->item('email_user'), 
			$this->utf8Encode($this->CI->config->item('email_user_name'))
		);
		$mail->AddAddress(
			$toUser, 
			$this->utf8Encode($toUserName)
		);
		$mail->Subject    = $this->utf8Encode($title);
		$mail->MsgHTML($message);
		$result = $mail->Send();
		if( $result == false ){
			return array(
				'code'=>1,
				'msg'=>$mail->ErrorInfo,
				'data'=>array(
					'toUser'=>$toUser,
					'toUserName'=>$toUserName,
					'title'=>$title,
					'message'=>$message,
				)
			);
		}else{
			return array(
				'code'=>0,
				'msg'=>'',
				'data'=>''
			);
		}
	}
	public function asyncSend($toUser,$toUserName,$title,$message){
		$this->CI->load->library('http','','http');
		$result = $this->CI->http->async(array(
			'url'=>$this->CI->config->item('email_async_url'),
			'data'=>array(
				'user'=>$toUser,
				'name'=>$toUserName,
				'title'=>$title,
				'message'=>$message
			)
		));
		return $result;
	}
	public function asyncSendReceiver(){
		$this->CI->load->library('argv','','argv');
		$result = $this->CI->argv->postRequireInput(array('user','name','title','message'));
		if( $result["code"] != 0 ){
			return $result;
		}
		return $this->send(
			$result['data']['user'],
			$result['data']['name'],
			$result['data']['title'],
			$result['data']['message']
		);
	}
};