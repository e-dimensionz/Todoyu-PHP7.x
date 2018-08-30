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
 * Register hooks and call them
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHookManager {

	/**
	 * Registered callbacks for hooks grouped by extension and hook name
	 *
	 * @var	Array
	 */
	private static $hooks = array();



	/**
	 * Get registered hooks
	 *
	 * @param	String		$extKey
	 * @param	String		$name		If empty: get all hooks+names
	 * @return	Array
	 */
	public static function getHooks($extKey, $name = '') {
		$extKey	= strtolower($extKey);
		$name	= trim(strtolower($name));

		$hooks	= TodoyuArray::assure(self::$hooks[$extKey][$name]);
		$hooks	= TodoyuArray::sortByLabel($hooks, 'position');

		return TodoyuArray::getColumn($hooks, 'function');
	}



	/**
	 * Get all hooks of given extension
	 *
	 * @param	String
	 * @return	Array
	 */
	public static function getAllHooksOfExtension($extKey) {
		return TodoyuArray::assure(self::$hooks[$extKey]);
	}



	/**
	 * Call all registered hooks for an event
	 *
	 * @param	String		$extKey
	 * @param	String		$name			Hook name
	 * @param	Array		$params			Parameters for the hook function
	 * @return	Array		The return values of all hook functions
	 */
	public static function callHook($extKey, $name, array $params = array()) {
		$hookFuncRefs	= self::getHooks($extKey, $name);
		$returnValues	= array();

		TodoyuLogger::logCore('Hook: ' . $extKey . '/' . $name);

		foreach($hookFuncRefs as $hookFuncRef) {
			TodoyuLogger::logCore('Call: ' . $hookFuncRef);
			$returnValues[] = TodoyuFunction::callUserFunctionArray($hookFuncRef, $params);
		}

		return $returnValues;
	}



	/**
	 * Call hooks which modify a data variable (ex: an array)
	 *
	 * @param	String		$extKey
	 * @param	String		$name				Hook name
	 * @param	Mixed		$dataVar			Data variable which will be passed to each hook
	 * @param	Array		$additionalParams	Additional parameters which will be placed after the $dataVar
	 * @return	Mixed
	 */
	public static function callHookDataModifier($extKey, $name, $dataVar, array $additionalParams = array()) {
		$hookFuncRefs	= self::getHooks($extKey, $name);
		$hookParams		= $additionalParams;
			// Prepend data var
		array_unshift($hookParams, $dataVar);

		TodoyuLogger::logCore('Hook: ' . $extKey . '.' . $name);

		foreach($hookFuncRefs as $hookFuncRef) {
			TodoyuLogger::logCore('Call: ' . $hookFuncRef);
			$hookParams[0] = TodoyuFunction::callUserFunctionArray($hookFuncRef, $hookParams);
		}

		return $hookParams[0];
	}



	/**
	 * Call voting hook
	 * This is a normal hook call, but the results of the hooks have to be one of the HOOK_VOTING_* constants
	 * The default for voting is TRUE - you can set an alternative default if no votes are available
	 * ALWAYS will always ignore other votes and return true
	 * NEVER will ignore other votes (except ALWAYS) and return false
	 * One no voting will result in FALSE
	 *
	 * @param	String		$extKey
	 * @param	String		$name
	 * @param	Array		$params
	 * @param	Boolean		$noVotingDefault
	 * @return	Boolean
	 */
	public static function callHookVoting($extKey, $name, array $params = array(), $noVotingDefault = true) {
		$hookResults= self::callHook($extKey, $name, $params);

			// No voting,
		if( sizeof($hookResults) ) {
				// Reverse order (later results have higher priority)
			$reversedResults	= array_reverse($hookResults);

				// Change for special results (always and never) which override all others
			if( in_array(HOOK_VOTING_ALWAYS, $reversedResults) ) {
				return true;
			}
			if( in_array(HOOK_VOTING_NEVER, $reversedResults) ) {
				return false;
			}

				// Check for NO votings (one no means no)
			foreach($hookResults as $hookResult) {
				if( $hookResult === HOOK_VOTING_NO ) {
					return false;
				}
			}

				// Default (all votes were YES)
			return true;
		}

			// Default (no votes at all)
		return $noVotingDefault;
	}



	/**
	 * Add a new hook functions for a hook event
	 *
	 * @param	String		$extKey			Extension key (of Ext to be extended)
	 * @param	String		$name			Hook name
	 * @param	String		$function		Function reference (e.g: 'Classname::method')
	 * @param	Integer		$position		Position of the hook (order of calling)
	 */
	public static function registerHook($extKey, $name, $function, $position = 100) {
		$extKey	= strtolower($extKey);
		$name	= strtolower($name);
		
		self::$hooks[$extKey][$name][] = array(
			'function'	=> $function,
			'position'	=> (int) $position
		);
	}

}

?>