<?php
/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2012, snowflake productions GmbH, Switzerland
* All rights reserved.
*
* This script is part of the todoyu project.
* The todoyu project is free software; you can redistribute it and/or modify
* it under the terms of the BSD License.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the BSD License
* for more details.
*
* This copyright notice MUST APPEAR in all copies of the script.
*****************************************************************************/

/**
 * Debug helper. Print useful debug messages in different mime types
 * and limit the output to a list of defined users.
 * Also allows to send debug output with filePhp to FireBug
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDebug {

	/**
	 * Debugging active?
	 *
	 * @var	Boolean
	 */
	private static $active = false;


	
	/**
	 * Get information about the position debug was called
	 *
	 * @return	Array
	 */
	private static function getCaller() {
		$backtrace = debug_backtrace(false);

		$backtrace[1]['fileshort'] = str_replace(PATH, '', $backtrace[1]['file']);

		return $backtrace[1];
	}



	/**
	 * Check if the current user is listed in the username list
	 *
	 * @param	String		$usernames		Comma separated usernames
	 * @return	Boolean
	 */
	private static function isCurrentUser($usernames) {
		$currentUsername	= Todoyu::person()->getUsername();
		$checkUsernames		= explode(',', $usernames);

		return in_array($currentUsername, $checkUsernames);
	}



	/**
	 * Check whether debugging is active
	 *
	 * @return	Boolean
	 */
	public static function isActive() {
		return self::$active;
	}


	
	/**
	 * Enable / disable debugging
	 *
	 * @param	Boolean		$active
	 */
	public static function setActive($active = true) {
		self::$active = $active ? true : false;
	}



	/**
	 * Get PHP formatted information about given variable
	 *
	 * @todo	check and improve for different var types
	 *
	 * @param	Mixed		$var
	 * @param	String		$indent
	 * @param	Integer		$niv
	 * @return	String
	 */
	public static function phpFormat($var, $indent = '&nbsp;&nbsp;', $niv = 0) {
		$str = '';

		if( is_array($var) ) {
			$str .= 'array(<br />';

			foreach($var as $k=>$v) {
				for( $i = 0; $i < $niv; $i++) {
					$str .= $indent;
				}

				$str .= $indent . '\'' . $k . '\' => &nbsp;';
				$str .= self::phpFormat($v, $indent, $niv + 1);
			}
		} else if( is_object($var) ) {
			$str .= '[object]-class = [' . get_class($var) . ']-method=[';

			$arr = get_class_methods($var);

			foreach($arr as $method) {
				$str .= $method . '(), ';
			}

			$str .= ']-';
			$str .= self::phpFormat(get_object_vars($var), $indent, $niv + 1);
		} else {
			$str .= '\'' . $var . '\',<br />';
		}

		return($str);
	}



	/**
	 * Print debug message in plain text
	 *
	 * @param	Mixed		$item		Item to debug
	 * @param	String		$title		Title for debug output
	 * @param	Boolean		$return		Return or print result
	 * @return	Void|String
	 */
	public static function printPHP($item, $title = '', $return = false) {
		$tmpl	= 'core/view/debug_php.tmpl';
		$data	= array(
			'title'		=> $title,
			'debug'		=> self::phpFormat($item),
			'backtrace'	=> print_r( debug_backtrace(false), true ),
			'caller'	=> self::getCaller()
		);

		$debug	= Todoyu::render($tmpl, $data);

		if( $return ) {
			return $debug;
		} else {
			echo $debug;

			return '';
		}
	}



	/**
	 * Print debug message in plain text
	 *
	 * @param	Mixed		$item		Item to debug
	 * @param	String		$title		Title for debug output
	 */
	public static function printPlain($item, $title = '') {
		TodoyuHeader::sendTypeText();

		$caller = self::getCaller();

		$output	= "\n";
		if( $title != '' ) {
			$output .= 'DEBUG: ' . $title . "\n";
		}

		$output .= str_repeat('=', 70) . "\n";
		$output .= $caller['file'] . ' : ' . $caller['line'] . "\n";
		$output .= str_repeat('=', 70) . "\n";
		$output .= print_r($item, true);
		$output .= "\n\n";

		echo $output;
	}



	/**
	 * Print debug message as HTML
	 *
	 * @param	Mixed		$item		Item to debug
	 * @param	String		$title		Title for debug output
	 * @param	Boolean		$backtrace
	 */
	public static function printHtml($item, $title = '', $backtrace = false) {
		if( self::isActive() ) {
			if( $item === false || $item === true || $item === '' || $item === null ) {
				ob_start();
				var_dump($item);
				$debug = ob_get_flush();
			} else {
				$debug = print_r($item, true);
			}

			$tmpl	= 'core/view/debug_html.tmpl';
			$data	= array(
				'title'		=> $title,
				'debug'		=> $debug,
				'backtrace'	=> $backtrace ? print_r( debug_backtrace(false), true ) : '',
				'caller'	=> self::getCaller()
			);

			echo Todoyu::render($tmpl, $data);
		}
	}



	/**
	 * Print debug message in firebug
	 *
	 * @param	Mixed		$item
	 * @param	String		$title
	 */
	public static function printInFirebug($item, $title = '') {
		if( self::isActive() ) {
			self::firePhp()->log($item, $title);
		}
	}



	/**
	 * Print the last executed query in firebug
	 *
	 * @param	String		$ident			Special identifier
	 */
	public static function printLastQueryInFirebug($ident = null) {
		$title	= 'Last Query';

		if( ! is_null($ident) ) {
			$title .= ' (' . $ident . ')';
		}

		self::printInFirebug(Todoyu::db()->getLastQuery(), $title);
	}



	/**
	 * Get singleton instance of firePhp
	 *
	 * @return	FirePhp
	 */
	public static function firePhp() {
		return FirePHP::getInstance(true);
	}



	/**
	 * Print function backtrace debug
	 *
	 * @param	Integer		$limit
	 */
	public static function printBacktrace($limit = 0) {
		$backtrace	= self::getBacktrace($limit, 1);

		self::printHtml($backtrace, 'Backtrace');
	}



	/**
	 * Get backtrace info
	 *
	 * @param	Integer		$limit
	 * @param	Integer		$cut
	 * @return	Array[]
	 */
	public static function getBacktrace($limit = 0, $cut = 0) {
		$backtrace	= debug_backtrace(false);
		array_shift($backtrace);

		if( $limit > 0 ) {
			$backtrace = array_slice($backtrace, $cut, $limit);
		}

		return $backtrace;
	}



	/**
	 * Print error page for fatal (uncaught) exception
	 *
	 * @param	TodoyuException		$exception
	 */
	public static function printFatalExceptionPage(TodoyuException $exception) {
		$tmpl	= 'core/view/uncaught-exception.tmpl';
		$data	= array(
			'exception'	=> $exception
		);

		echo Todoyu::render($tmpl, $data);
		exit();
	}

}

?>