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
 * Company Quickinfo Manager
 *
 * @package        Todoyu
 * @subpackage    Contact
 */
class TodoyuContactCompanyQuickInfoManager {

	/**
	 * Add given company email-addresses to quickinfo
	 *
	 * @param    TodoyuQuickinfo $quickInfo
	 * @param    Integer $idCompany
	 */
	public static function addQuickInfoEmail(TodoyuQuickinfo $quickInfo, $idCompany) {
		$idCompany = intval($idCompany);
		$emailRecords = TodoyuContactContactInfoManagerCompany::getEmails($idCompany);

		foreach ($emailRecords as $key =>  $emailRecord) {
			if( TodoyuContactRights::isContactinfotypeOfCompanySeeAllowed($idCompany, $emailRecord['id_contactinfotype'])) {
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
		$phoneRecords = TodoyuContactContactInfoManagerCompany::getPhones($idCompany);

		foreach ($phoneRecords as $key => $phoneRecord) {
			if( TodoyuContactRights::isContactinfotypeOfCompanySeeAllowed($idCompany, $phoneRecord['id_contactinfotype'])) {
				$title = TodoyuLabelManager::getLabel($phoneRecord['title']);
				$quickInfo->addHTML('phone' . $key, TodoyuString::wrapWithTag('strong', $title) . ': ' . $phoneRecord['info']);
			}
		}
	}



	/**
	 * Add given company addresses to quickinfo
	 *
	 * @param    TodoyuQuickinfo $quickInfo
	 * @param    Integer $idCompany
	 */
	public static function addQuickInfoAddress(TodoyuQuickInfo $quickInfo, $idCompany) {
		$idCompany = intval($idCompany);
		$addressRecords = TodoyuContactCompanyManager::getCompanyAddressRecords($idCompany);
		$tmpl = 'ext/contact/view/quick-info-address.tmpl';

		foreach ($addressRecords as $key => $addressRecord) {
			if( TodoyuContactRights::isAddresstypeOfCompanySeeAllowed($idCompany, $addressRecord['id_addresstype']) ) {
				$content = Todoyu::render($tmpl, array('address' => $addressRecord));
				$content = str_replace(array("\n", "\r"), ' ', $content);
				$quickInfo->addHTML('address' . $key, $content);
			}
		}
	}
}

?>