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
 * Manage wizards and their steps
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuWizardManager {

	/**
	 * Wizard step configurations
	 *
	 * @var	Array
	 */
	private static $steps	= array();


	/**
	 * List of wizard classes
	 *
	 * @var	Array
	 */
	private static $wizards	= array();



	/**
	 * Add a wizard
	 *
	 * @param	String		$name
	 * @param	String		$class
	 */
	public static function addWizard($name, $class) {
		self::$wizards[$name] = $class;
	}



	/**
	 *
	 * @param	String			$name
	 * @return	TodoyuWizard
	 */
	public static function getWizard($name) {
		$class	= self::$wizards[$name];

		return new $class();
	}



	/**
	 * Add wizard step
	 *
	 * @param	String		$wizardName
	 * @param	Array		$stepConfig
	 */
	public static function addStep($wizardName, array $stepConfig) {
		self::$steps[$wizardName][$stepConfig['step']] = $stepConfig;
	}




	/**
	 * Get single wizard step
	 *
	 * @param	String		$wizardName
	 * @param	String		$stepName
	 * @return	Array
	 */
	public static function getStep($wizardName, $stepName) {
		return TodoyuArray::assure(self::$steps[$wizardName][$stepName]);
	}



	/**
	 * Get all steps of a wizard
	 *
	 * @param	String		$wizardName
	 * @return	Array
	 */
	public static function getSteps($wizardName) {
		return TodoyuArray::sortByLabel(TodoyuArray::assure(self::$steps[$wizardName], 'position'));
	}



	/**
	 * Save current step
	 *
	 * @param	String		$wizardName
	 * @param	String		$stepName
	 */
	public static function setCurrentStep($wizardName, $stepName) {
		TodoyuSession::set('wizard/' . $wizardName, $stepName);
	}



	/**
	 * Get current step
	 *
	 * @param	String		$wizardName
	 * @return	String
	 */
	public static function getCurrentStep($wizardName) {
		return TodoyuSession::get('wizard/' . $wizardName);
	}

}

?>