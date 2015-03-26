<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Libraries
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

	var $class;
	var $attachVar;
	/**
	 * Constructor
	 *
	 * @access public
	 */
	function __construct()
	{
		$this->class = new ReflectionClass($this);
		log_message('debug', "Model Class Initialized ".$this->class->getName() );
	}

	/**
	 * __get
	 *
	 * Allows models to access CI's loaded classes using the same
	 * syntax as controllers.
	 *
	 * @param	string
	 * @access private
	 */
	function __get($key)
	{
		if( isset($this->attachVar[$key]) )
			return $this->attachVar[$key];
		
		$CI =& get_instance();
		if(property_exists($CI,$key) === false ){
			log_message('error', "Has not this proerty ".$this->class->getName().'::'.$key );
		}
		return $CI->$key;
	}

	/**
	 *attach name mock
	 */
	function attach($name,$value){
		$this->attachVar[$name] = $value;
	}

	/**
	 *detach name mock
	 */
	function detach($name){
		unset($this->attachVar[$name]);
	}

}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */