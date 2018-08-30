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
 * Manager for email receiver types
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuMailReceiverManager {

	/**
	 * @var	String[]		Mail receiver types
	 */
	private static $types = array();

	/**
	 * @var	String[]		Search callbacks for mail receiver records element
	 */
	private static $searchCallbacks = array();



	/**
	 * Register an email receiver type
	 *
	 * @param	String		$type
	 * @param	String		$object
	 */
	public static function addType($type, $object) {
		self::$types[$type] = $object;
	}



	/**
	 * Get email receiver type configuration
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getTypeClass($type) {
		return self::$types[$type];
	}



	/**
	 * Add a search callback as source for the mail receivers records selector form element
	 *
	 * @param	String		$callback
	 */
	public static function addSearchCallback($callback) {
		self::$searchCallbacks[$callback] = $callback;
	}



	/**
	 * Get mail receiver for tuple
	 *
	 * @param	String				$receiverTuple		Tuple: 'type:ID', e.g. 'contactperson:232' or just ID, which sets default type: 'contactperson'
	 * @return	TodoyuMailReceiverInterface
	 */
	public static function getMailReceiver($receiverTuple) {
		$receiverTuple	= trim($receiverTuple);

		if( is_numeric($receiverTuple) ) {
				// Default type: person
			$type		= 'contactperson';
			$idRecord	= $receiverTuple;
		} else {
				// ID is prefixed with registered key of receiver type
			list($type, $idRecord)	= explode(':', $receiverTuple, 2);
		}

		$class = self::getTypeClass($type);
		if( !class_exists($class)) {
			TodoyuLogger::logError('Unknown email receiver type key in tuple <' . $receiverTuple . '>');
			return false;
		}

		return new $class($idRecord);
	}



	/**
	 * Get mail receiver objects for tuples
	 * Tuples are the indexes
	 *
	 * @param	String[]	$receiverTuples
	 * @return	TodoyuMailReceiverInterface[]
	 */
	public static function getMailReceivers(array $receiverTuples) {
		$receivers	= array();

		foreach($receiverTuples as $receiverTuple) {
			$receiver	= self::getMailReceiver($receiverTuple);

			if( $receiver ) {
				$receivers[$receiverTuple] = $receiver;
			}
		}

		return $receivers;
	}



	/**
	 * Check whether the given type key is registered
	 *
	 * @param	String		$type
	 * @return	Boolean
	 */
	public static function isTypeRegistered($type) {
		return isset(self::$types[$type]);
	}



	/**
	 * Get matching email receivers as list items
	 *
	 * @param	String[]		$searchWords
	 * @param	String[]		$ignoreTuples
	 * @param	Array			$params
	 * @param	String			$type
	 * @return	Array[]
	 */
	public static function getMatchingMailReceivers(array $searchWords, array $ignoreTuples = array(), array $params = array(), $type) {
		$searchWords	= TodoyuArray::trim($searchWords);
		$ignoreTuples	= TodoyuArray::trim($ignoreTuples);
		$receiverTuples	= array();
		$listItems		= array();

		if( sizeof($searchWords) === 0 ) {
			return array();
		}

			// Search with all callbacks
		foreach(self::$searchCallbacks as $searchCallback) {
			$typeReceiverTuples	= TodoyuFunction::callUserFunction($searchCallback, $searchWords, $ignoreTuples, $params);

			if( is_array($typeReceiverTuples) ) {
				$receiverTuples	= array_merge($receiverTuples, $typeReceiverTuples);
			}
		}

		$receiverTuples = array_unique($receiverTuples);

			// Convert receiver tuples in records list items
		foreach($receiverTuples as $receiverTuple) {
			list($type, $idRecord) = explode(':', $receiverTuple);
			$mailReceiver	= TodoyuMailReceiverManager::getMailReceiver($receiverTuple);

			$listItems[] = array(
				'id'		=> $receiverTuple,
				'label'		=> $mailReceiver->getLabel(),
				'className'	=> 'type' . ucfirst($type)
			);
		}

		return $listItems;
	}

}

?>