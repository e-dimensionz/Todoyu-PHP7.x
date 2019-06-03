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
 * Wizard step: company
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepCompany extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize
	 */
	protected function init() {
		$this->table	= 'ext_contact_company';
		$this->formXml	= 'ext/firststeps/config/form/company.xml';
	}



	/**
	 * Save company
	 *
	 * @param	Array	$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$form	= $this->getForm($data);

		if( $form->isValid() ) {
			$companyData	= $form->getStorageData();

			$this->saveCompany($companyData);

			return true;
		} else {
			$this->data = $data;

			return false;
		}
	}



	/**
	 * Render content
	 *
	 * @return	String
	 */
	public function getContent() {
		if( $this->data === null ) {
			$this->data = $this->getCompanyData();
		}

		return $this->getForm($this->data)->renderContent();
	}



	/**
	 * Get data for company incl address
	 *
	 * @return	Array
	 */
	private function getCompanyData() {
		$company	= $this->getCompany();
		$addresses	= $company->getAddresses();
		$data	= $company->getTemplateData();

		if( !empty($addresses)  ) {
			/** @var TodoyuContactAddress $mainAddress */
			$mainAddress= reset($addresses);

			$data['street']	= $mainAddress->getStreet();
			$data['zip']	= $mainAddress->getZip();
			$data['city']	= $mainAddress->getCity();
		}

		return $data;
	}



	/**
	 * Get first company
	 *
	 * @return	TodoyuContactCompany
	 */
	private function getCompany() {
		return TodoyuContactCompanyManager::getCompany(1);
	}



	/**
	 * Save/update company
	 *
	 * @param	Array	$submittedData
	 */
	private function saveCompany(array $submittedData) {
		$data	= array(
			'title'	=> $submittedData['title']
		);
		$idCompany	= 1;

		TodoyuContactCompanyManager::updateCompany($idCompany, $data);

		$data	= array(
			'street'	=> $submittedData['street'],
			'zip'		=> $submittedData['zip'],
			'city'		=> $submittedData['city']
		);

		$addresses	= $this->getCompany()->getAddresses();

		if( empty($addresses) ) {
			$idAddress	= TodoyuContactAddressManager::addAddress($data);
			TodoyuContactCompanyManager::linkAddresses($idCompany, array($idAddress));
		} else {
			/** @var TodoyuContactAddress $firstAddress */
			$firstAddress	= reset($addresses);
			$idAddress		= $firstAddress->getID();
			TodoyuContactAddressManager::updateAddress($idAddress, $data);
		}
	}

}

?>