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
 * [Description]
 *
 * @package			Todoyu
 * @subpackage		Imap
 */
class TodoyuImapFormElement_RecordsMailAddress extends TodoyuFormElement_Records {

	/**
	 * Initialize
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('mailAddress', $name, $fieldset, $config);
	}



	/**
	 * Get record data
	 *
	 * @return	Array
	 */
	protected function getRecords() {
		$addressIDs	= $this->getValue();
		$records		= array();

		foreach($addressIDs as $idAddress) {
			$records[] = array(
				'id'		=> $idAddress,
				'label'		=> TodoyuImapAddressManager::getLabel($idAddress),
				'className'	=> 'mailAddress'
			);
		}

		return $records;
	}

}

?>