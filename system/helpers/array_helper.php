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
 * CodeIgniter Array Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/array_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Element
 *
 * Lets you determine whether an array index is set and whether it has a value.
 * If the element is empty it returns FALSE (or whatever you specify as the default value.)
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if ( ! function_exists('element'))
{
	function element($item, $array, $default = FALSE)
	{
		if ( ! isset($array[$item]) OR $array[$item] == "")
		{
			return $default;
		}

		return $array[$item];
	}
}

// ------------------------------------------------------------------------

/**
 * Random Element - Takes an array as input and returns a random element
 *
 * @access	public
 * @param	array
 * @return	mixed	depends on what the array contains
 */
if ( ! function_exists('random_element'))
{
	function random_element($array)
	{
		if ( ! is_array($array))
		{
			return $array;
		}

		return $array[array_rand($array)];
	}
}

// --------------------------------------------------------------------

/**
 * Elements
 *
 * Returns only the array items specified.  Will return a default value if
 * it is not set.
 *
 * @access	public
 * @param	array
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */
if ( ! function_exists('elements'))
{
	function elements($items, $array, $default = FALSE)
	{
		$return = array();
		
		if ( ! is_array($items))
		{
			$items = array($items);
		}
		
		foreach ($items as $item)
		{
			if (isset($array[$item]))
			{
				$return[$item] = $array[$item];
			}
			else
			{
				$return[$item] = $default;
			}
		}

		return $return;
	}
}

if ( ! function_exists('array_column_merge'))
{
	function array_column_merge($array1, $array2, $indexKey ,$indexOrder=null)
	{
		if( $indexOrder == null )
		{
			$indexOrder = array_merge(
				array_column($array1,$indexKey),
				array_column($array2,$indexKey)
			);
			$indexOrder = array_unique($indexOrder);
		}

		$result = array();
		$array1 = array_column($array1,null,$indexKey);
		$array2 = array_column($array2,null,$indexKey);
		foreach( $indexOrder as $singleIndexOrder)
		{
			if( isset($array1[$singleIndexOrder]) == false )
				continue;
			if( isset($array2[$singleIndexOrder]) == false )
				continue;
			$result[] = array_merge(
				$array1[$singleIndexOrder],
				$array2[$singleIndexOrder]
			);
		}
		
		return $result;
	}
}

/* End of file array_helper.php */
/* Location: ./system/helpers/array_helper.php */