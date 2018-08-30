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
 * Person + IMAP address implementation for record selector
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapFormElement_RecordsPersonAndAddress extends TodoyuFormElement_Records {

	/**
	 * Initialize
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('personAndAddress', $name, $fieldset, $config);
	}



	/**
	 * Get record data
	 *
	 * @return	Array[]
	 */
	protected function getRecords() {
		$receiverTuples	= $this->getValue();
		$records		= array();

		foreach($receiverTuples as $tuple) {
			$receiver	= TodoyuMailReceiverManager::getMailReceiver($tuple);
			$records[] = array(
				'id'		=> $receiver->getTuple(),
				'label'		=> $receiver->getLabel(),
				'className'	=> $receiver->getType()
			);
		}

		return $records;
	}

}

?>