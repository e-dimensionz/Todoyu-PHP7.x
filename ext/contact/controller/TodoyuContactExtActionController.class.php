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
 *  Default action controller for contact extension
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactExtActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init(array $dt = array()) {
		Todoyu::restrict('contact', 'general:area');
	}



	/**
	 * Default action: setup and render contact page view
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function defaultAction(array $params) {
			// Get record ID from param
		$idRecord	= intval($params['id']);
		$type		= isset($params['tab']) ? $params['tab'] : $params['type'];

			// Save search word if provided
		if( is_null($params['sword']) ) {
			$searchWord	= TodoyuContactPreferences::getSearchWord();
		} else {
			$searchWord	= trim($params['sword']);
			TodoyuContactPreferences::saveSearchWord($searchWord);
		}

			// Get type from parameter or preferences
		if( empty($type) ) {
			$type	= TodoyuContactPreferences::getActiveTab();
		} else {
			TodoyuContactPreferences::saveActiveTab($type);
		}

		if( $idRecord !== 0 ) {
			$content	= TodoyuContactRenderer::renderDetails($type, $idRecord);
		} else {
			$content	= TodoyuContactRenderer::renderContactList($type, $searchWord);
		}

		TodoyuPage::addJsInit('Todoyu.Ext.contact.Company.initCompanyList()', 100);
		TodoyuPage::addJsInit('Todoyu.Ext.contact.Person.initPersonList()', 100);

		return TodoyuContactRenderer::renderContactPage($type, $idRecord, $searchWord, $content);
	}



	/**
	 * Check for duplicated contactinfo entries
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function checkforduplicatedcontactinformationAction(array $params) {
		$contactInfo		= $params['value'];

		$records = TodoyuContactContactInfoManager::getContactInfoDuplicates($contactInfo);

		if( sizeof($records) > 0 ) {
			TodoyuHeader::sendTodoyuHeader('duplicates', true);
		}

		return TodoyuContactRenderer::renderDuplicatesList($records);
	}



	/**
	 * Check for duplicated address records
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function checkforduplicatedaddressAction(array $params) {
		$addressInformations	= array($params['city'], $params['street'], $params['zip'], $params['postbox']);

		$records = TodoyuContactAddressManager::getDuplicatedAddresses($addressInformations);

		if( sizeof($records) > 0 ) {
			TodoyuHeader::sendTodoyuHeader('duplicates', true);
		}

		return TodoyuContactRenderer::renderDuplicatesList($records);
	}
}

?>