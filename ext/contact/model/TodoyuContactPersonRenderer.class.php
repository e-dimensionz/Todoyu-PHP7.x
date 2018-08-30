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
 * Person render class
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonRenderer {

	/**
	 * Render person quick creation form
	 *
	 * @return	String
	 */
	public static function renderPersonQuickCreateForm() {
		$form	= TodoyuContactPersonManager::getQuickCreateForm();

			// Preset (empty) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData('ext/contact/config/form/person.xml', $formData, 0);
		$form->setFormData($formData);

		return $form->render();
	}



	/**
	 * Render person list
	 *
	 * @param	String		$searchWord
	 * @param	Integer		$offset
	 * @return	String
	 */
	public static function renderPersonList($searchWord = '', $offset = 0) {
		Todoyu::restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'person', $offset, false, array('sword' => $searchWord));
	}



	/**
	 * Render search result listing of persons
	 *
	 * @param	Array		$personIDs
	 * @return	String
	 */
	public static function renderPersonListingSearch(array $personIDs) {
		return TodoyuListingRenderer::render('contact', 'personSearch', 0, true, array('personIDs' => $personIDs));
	}



	/**
	 * Render person edit form
	 *
	 * @param	Integer	$idPerson
	 * @return	String
	 */
	public static function renderPersonEditForm($idPerson) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuContactPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'header'	=> $person->getLabel(),
			'formhtml'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render person edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idPerson
	 * @param	String	$fieldName		HTML Id of the input field
	 * @return	String
	 */
	public static function renderPersonCreateWizard($idPerson, $fieldName) {
		$idPerson	= intval($idPerson);
		$xmlPath	= 'ext/contact/config/form/person.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idPerson);

		$person	= TodoyuContactPersonManager::getPerson($idPerson);
		$data	= $person->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idPerson);

		$form->setFormData($data);
		$form->setRecordID($idPerson);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.cancelWizard(this.form);');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Person.saveWizard(this.form, \''.$fieldName.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $person->getLabel(),
			'formhtml'		=> $form->render()
		);

		$content	= Todoyu::render($tmpl, $data);
		$content	.= TodoyuString::wrapScript('Todoyu.Ext.contact.Person.onEdit(' . $idPerson. ')');

		return $content;
	}



	/**
	 * Render general person header
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonHeader($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$tmpl		= 'ext/contact/view/person-header.tmpl';

		$data	= $person->getTemplateData();
		$data	= TodoyuHookManager::callHookDataModifier('contact', 'person.renderHeader', $data);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render person details
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonDetails($idPerson) {
		$idPerson	= intval($idPerson);
		$person		= TodoyuContactPersonManager::getPerson($idPerson);

		$tmpl	= 'ext/contact/view/person-detail.tmpl';
		$data	= $person->getTemplateData(true);

		$companyIDs = $person->getCompanyIDs();
		foreach($companyIDs as $idCompany) {
			$company		= TodoyuContactCompanyManager::getCompany($idCompany);
			$companyData	= $company->getTemplateData(true);

			$data['companyData'][$idCompany] = $companyData['address'];
		}

		$data['email']			= $person->getEmail();
		$data['hookedContent']	= implode('', TodoyuHookManager::callHook('contact', 'person.renderDetail', array($idPerson)));

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render action buttons for person records
	 *
	 * @param	Integer		$idPerson
	 * @return	String
	 */
	public static function renderPersonActions($idPerson) {
		$tmpl	= 'ext/contact/view/person-actions.tmpl';
		$data	= array(
			'id'	=> intval($idPerson)
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>