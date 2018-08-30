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
 * Validates for a good password
 *
 * @package		Todoyu
 * @subpackage	User
 */
class TodoyuPasswordValidator {

	/**
	 * Enabled checks
	 *
	 * @var	Array
	 */
	private $checks	= array();

	/**
	 * Occured errors
	 *
	 * @var	Array
	 */
	private $errors = array();



	/**
	 * Initialize validator with active checks
	 */
	public function __construct() {
		$this->checks	= self::getChecks();
	}



	/**
	 * Validate $value with registered checks
	 *
	 * @param	Mixed	$value
	 * @return	Boolean
	 */
	public function validate($value) {
		$this->resetErrors();

		$functions	= array_keys($this->checks);

		foreach($functions as $function) {
				// Only check if config is not set to FALSE
			if( $this->checks[$function] !== false ) {
					// Check if validtor exists
				if( method_exists($this, $function) ) {
					call_user_func(array($this, $function), $value, $this->checks[$function]);
				} else {
					TodoyuLogger::logError('Invalid password validator function: ' . $function);
				}
			}
		}

		return !$this->hasErrors();
	}



	/**
	 * Reset error array (for second use of same object)
	 *
	 * @return	Array
	 */
	private function resetErrors() {
		$this->errors = array();
	}



	/**
	 * Add a new error
	 *
	 * @param	String		$errorMessage
	 */
	private function addError($errorMessage) {
		$this->errors[] = $errorMessage;
	}



	/**
	 * Check if errors are registered
	 *
	 * @return	Boolean
	 */
	public function hasErrors() {
		return sizeof($this->errors) > 0;
	}



	/**
	 * Get registered errors
	 *
	 * @return	Array
	 */
	public function getErrors() {
		return $this->errors;
	}



	/**
	 * Checks if password has a minimum length
	 *
	 * @param	String	$value
	 * @param	Array	$config
	 * @return	Boolean
	 */
	private function minLength($value, $config) {
		$value	= trim($value);
		$length	= (int) $config;

		if( strlen($value) < $length ) {
			$errorMessage = str_replace('%s', $length, Todoyu::Label('contact.ext.password.minLengthIfNotEmpty'));
			$this->addError($errorMessage);
		}
	}



	/**
	 * Checks password for numbers
	 *
	 * @param	String	$value
	 * @param	Array	$config
	 */
	private function hasNumbers($value, $config) {
		$pattern= '/[0-9]+/';
		$valid	= preg_match($pattern, $value);

		if( ! $valid ) {
			$this->addError(Todoyu::Label('contact.ext.password.numbers'));
		}
	}



	/**
	 * Checks password for lower case
	 *
	 * @param	String	$value
	 * @param	Array	$config
	 */
	private function hasLowerCase($value, $config) {
		$pattern= '/[a-z]+/';
		$valid	= preg_match($pattern, $value);

		if( ! $valid ) {
			$this->addError(Todoyu::Label('contact.ext.password.lower'));
		}
	}



	/**
	 * Checks password for upper case
	 *
	 * @param	String	$value
	 * @param	Array	$config
	 */
	private function hasUpperCase($value, $config) {
		$pattern= '/[A-Z]+/';
		$valid	= preg_match($pattern, $value);

		if( ! $valid ) {
			$this->addError(Todoyu::Label('contact.ext.password.upper'));
		}
	}



	/**
	 * Checks if password has special chars
	 *
	 * @param	String	$value
	 * @param	Array	$config
	 */
	private function hasSpecialChars($value, $config) {
		$pattern= '/[^a-zA-Z0-9]+/';
		$valid	= preg_match($pattern, $value);

		if( ! $valid ) {
			$this->addError(Todoyu::Label('contact.ext.password.special'));
		}
	}



	/**
	 * Get check config
	 *
	 * @return	Array
	 */
	public static function getChecks() {
		return TodoyuArray::assure(Todoyu::$CONFIG['SETTINGS']['passwordStrength']);
	}

}

?>