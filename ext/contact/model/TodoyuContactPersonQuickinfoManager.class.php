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
 * Manage person quickinfo
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonQuickInfoManager {

	/**
	 * Add items to person quickinfo
	 *
	 * @param	TodoyuQuickinfo		$quickInfo
	 * @param	Integer				$idPerson
	 */
	public static function addPersonInfos(TodoyuQuickinfo $quickInfo, $idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

			// Name (with link)
		if( Todoyu::allowed('contact', 'general:area') ) {
			$link	= TodoyuContactPersonManager::getDetailLink($idPerson);
			$quickInfo->addInfo('name', $link, 10, false);
		} else {
			$quickInfo->addInfo('name', $person->getLabel(), 10, false);
		}

			// Restrict contact infos
		if( Todoyu::allowed('contact', 'relation:seeAllContactinfotypes') ) {
				// Email
			$email	= $person->getEmail(true);
			if( $email !== false ) {
				$quickInfo->addEmail('email', $email, $person->getFullName(), 100);
			}

				// Get preferred or only phone
			$phone = $person->getPhone();
			if( $phone !== false ) {
				$quickInfo->addInfo('phone', $phone, 150);
			}
		}
		
			// Restrict 
		if( Todoyu::allowed('contact', 'person:seeComment') ) {
				// Comment
			$comment	= $person->getComment();
			if( $comment !== '' ) {
				$quickInfo->addInfo('comment', TodoyuString::crop($comment, 100), 200);
			}
		}

		// Commented out. Is this really useful information?
//			// Add birthday information for internal persons
//		if( Todoyu::person()->isAdmin() || Todoyu::person()->isInternal() ) {
//			if( $data['birthday'] !== '0000-00-00' ) {
//				$birthday	= TodoyuTime::formatSqlDate($data['birthday']);
//				$quickinfo->addInfo('birthday', $birthday);
//			}
//		}
	}



	/**
	 * Add JS onload function to page (hooked into TodoyuPage::render())
	 */
	public static function addJSonloadFunction() {
		TodoyuPage::addJsInit('Todoyu.Ext.contact.QuickinfoPerson.init()', 100);
	}



	/**
	 * Add given company email-addresses to quickinfo
	 *
	 * @param    TodoyuQuickinfo $quickInfo
	 * @param    Integer $idPerson
	 */
	public static function addQuickInfoEmail(TodoyuQuickinfo $quickInfo, $idPerson) {
		$idPerson = intval($idPerson);
		$emailRecords = TodoyuContactContactInfoManagerPerson::getEmails($idPerson);

		foreach ($emailRecords as $key =>  $emailRecord) {
			if( TodoyuContactRights::isContactinfotypeOfPersonSeeAllowed($idPerson, $emailRecord['id_contactinfotype'])) {
				$emailType = TodoyuLabelManager::getLabel($emailRecord['title']);

				$emailTag = TodoyuSTring::buildMailtoATag($emailRecord['info'],
														  TodoyuString::wrapWithTag('strong', $emailType) . ': ' . $emailRecord['info']);

				$quickInfo->addHTML('email' . $key,
									$emailTag);
			}
		}
	}



	/**
	 * Add given company phone numbers to quickinfo
	 *
	 * @param    TodoyuQuickinfo $quickInfo
	 * @param    Integer $idCompany
	 */
	public static function addQuickInfoPhone(TodoyuQuickinfo $quickInfo, $idCompany) {
		$idCompany = intval($idCompany);
		$phoneRecords = TodoyuContactContactInfoManagerPerson::getPhones($idCompany);

		foreach ($phoneRecords as $key => $phoneRecord) {
			if( TodoyuContactRights::isContactinfotypeOfPersonSeeAllowed($idCompany, $phoneRecord['id_contactinfotype'])) {
				$title = TodoyuLabelManager::getLabel($phoneRecord['title']);
				$quickInfo->addHTML('phone' . $key, TodoyuString::wrapWithTag('strong', $title) . ': ' . $phoneRecord['info']);
			}
		}
	}

}

?>