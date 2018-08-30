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
 * Wizard steps: customers
 *
 * @package		Todoyu
 * @subpackage	Firststeps
 */
class TodoyuFirstStepsWizardStepCustomers extends TodoyuFirstStepsWizardStep {

	/**
	 * Initialize
	 */
	protected function init() {
		$this->table	= 'ext_contact_person';
		$this->formXml	= 'ext/firststeps/config/form/customer.xml';
	}



	/**
	 * Save customer
	 *
	 * @param	Array		$data
	 * @return	Boolean
	 */
	public function save(array $data) {
		$form	= $this->getForm($data);

		if( $form->isValid() ) {
			$customerData	= $form->getStorageData();

			$this->addCustomer($customerData);

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
		$tmpl	= 'ext/firststeps/view/form-with-list.tmpl';
		$data	= array(
			'items'	=> $this->getListItems(),
			'form'	=> $this->getForm($this->data)->renderContent()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get all employee records
	 *
	 * @return	Array
	 */
	private function getListItems() {
		$fields	= array(
			'id',
			'title'
		);
		$where	= 'is_internal = 0';

		$companies	= TodoyuContactCompanyManager::getAllCompanies($fields, $where);
		$items		= array();

		foreach($companies as $company) {
			$items[] = array(
				'id'	=> $company['id'],
				'label'	=> $company['title']
			);
		}

		return $items;
	}



	/**
	 * Add a customer
	 *
	 * @param	Array		$submittedData
	 * @return	Integer
	 */
	private function addCustomer(array $submittedData) {
		$data	= array(
			'title'	=> $submittedData['title']
		);

		$idCompany	= TodoyuContactCompanyManager::addCompany($data);

		$data	= array(
			'street'	=> $submittedData['street'],
			'zip'		=> $submittedData['zip'],
			'city'		=> $submittedData['city']
		);

		$idAddress	= TodoyuContactAddressManager::addAddress($data);

		TodoyuDbHelper::addMMLink('ext_contact_mm_company_address', 'id_company', 'id_address', $idCompany, $idAddress);

		return $idCompany;
	}

}

?>