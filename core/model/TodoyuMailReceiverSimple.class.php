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
 * Simple mail receiver object which takes name and address directly
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuMailReceiverSimple extends TodoyuMailReceiver implements TodoyuMailReceiverInterface {

	/**
	 * @var	String		Email address
	 */
	protected $address;

	/**
	 * @var	String		Name
	 */
	protected $name;



	/**
	 * Initialize with all data
	 *
	 * @param	String		$address
	 * @param	String		$name
	 * @param	Integer		$idRecord
	 */
	public function __construct($address, $name = '', $idRecord = 0) {
		parent::__construct('simple', $idRecord);

		$this->address	= trim($address);
		$this->name		= trim($name);
	}



	/**
	 * Get name
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->name;
	}



	/**
	 * Get address
	 *
	 * @return	String
	 */
	public function getAddress() {
		return $this->address;
	}

}

?>