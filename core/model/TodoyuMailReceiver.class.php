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
 * Mail Receiver Object
 *
 * @package		Todoyu
 * @subpackage	Core
 */
abstract class TodoyuMailReceiver implements TodoyuMailReceiverInterface {

	/**
	 * Type key
	 *
	 * @var	String		Type
	 */
	protected $type;

	/**
	 * @var	Integer		Record ID
	 */
	protected $idRecord = 0;

	/**
	 * @var	Boolean		Receiver is enabled
	 */
	protected $enabled = true;



	/**
	 * Initialize
	 *
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 */
	protected function __construct($type, $idRecord) {
		$this->type		= trim($type);
		$this->idRecord	= intval($idRecord);
	}



	/**
	 * Get person ID if available
	 *
	 * @return	Integer
	 */
	public function getPersonID() {
		return 0;
	}



	/**
	 * Get person
	 * This functions needs to be overridden if the derived object supports it
	 *
	 * @return	TodoyuContactPerson|Boolean
	 */
	public function getPerson() {
		TodoyuLogger::logFatal('Called getPerson() on a MailReceiver Object which doesn\'t implement this feature');

		return false;
	}



	/**
	 * Check whether person is available
	 * Not available by default
	 *
	 * @return	Boolean
	 */
	public function hasPerson() {
		return false;
	}



	/**
	 * Get key of registered receiver type, e.g. 'contactperson'
	 *
	 * @return	String
	 */
	public function getType() {
		return $this->type;
	}



	/**
	 * Get record ID
	 *
	 * @return	Integer
	 */
	public function getRecordID() {
		return $this->idRecord;
	}



	/**
	 * Get receiver tuple ('type:ID')
	 *
	 * @return	String
	 */
	public function getTuple() {
		return $this->getType() . ':' . $this->getRecordID();
	}



	/**
	 * Get label
	 *
	 * @param	Boolean		$withAddress
	 * @return	String
	 */
	public function getLabel($withAddress = true) {
		$name	= $this->getName();
		$address= $this->getAddress();

		if( empty($name) ) {
			return $address;
		} elseif( $withAddress ) {
			return $name . ' (' . $address . ')';
		} else {
			return $name;
		}
	}



	/**
	 * Get address as mail format
	 *
	 * @return	String
	 */
	public function getMailFormat() {
		$name	= $this->getName();
		$address= $this->getAddress();

		if( empty($name) ) {
			return $address;
		} else {
			return $name . ' <' . $address . '>';
		}
	}





	/**
	 * Check whether receiver is enabled
	 *
	 * @return	Boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}



	/**
	 * Check whether receiver is disabled
	 *
	 * @return	Boolean
	 */
	public function isDisabled() {
		return !$this->enabled;
	}



	/**
	 * Enable receiver
	 *
	 */
	public function enable() {
		$this->enabled = true;
	}



	/**
	 * Disable receiver
	 *
	 */
	public function disable() {
		$this->enabled = false;
	}



	/**
	 * Get data
	 *
	 * @return	Array
	 */
	public function getData() {
		return array(
			'name'		=> $this->getName(),
			'address'	=> $this->getAddress()
		);
	}

}

?>