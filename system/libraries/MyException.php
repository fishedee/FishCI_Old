<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class CI_MyException extends Exception{ 

	var $code;
	var $message;
	var $data;
	
    public function __construct($code = 1,$message = '',$data = '') {  
        parent::__construct($message,$code);
		$this->data = $data;
    }
	
	public function getData() {  
        return $this->data;
    }
}  
?>