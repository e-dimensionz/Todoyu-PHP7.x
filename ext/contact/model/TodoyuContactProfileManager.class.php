<?php
/****************************************************************************
 * todoyu is published under the BSD License:
 * http://www.opensource.org/licenses/bsd-license.php
 *
 * Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * Manage contact profile
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactProfileManager {


	/**
	 * Get modified form for profile
	 *
	 * @param	Integer		$idPerson
	 * @return	TodoyuForm
	 */
	public static function getProfileForm($idPerson) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$form->setRecordID($idPerson);

			// Adapt form action and buttons for profile
		$form->setAction('index.php?ext=contact&amp;controller=profile');

		$fieldsetButtons	= $form->getFieldset('buttons');
		$fieldsetButtons->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Profile.save(this.form)');
		$fieldsetButtons->getField('cancel')->remove();

		return $form;
	}

}

?>