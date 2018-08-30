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
 * Company render class
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyRenderer {

	/**
	 * Render company quick creation form
	 *
	 * @return	String
	 */
	public static function renderCompanyQuickCreateForm() {
		$form	= TodoyuContactCompanyManager::getQuickCreateForm();

			// Preset (empty) form data
		$formData	= $form->getFormData();
		$formData	= TodoyuFormHook::callLoadData('ext/contact/config/form/company.xml', $formData, 0);
		$form->setFormData($formData);

		return $form->render();
	}



	/**
	 * Render company list
	 *
	 * @param	String		$searchWord
	 * @param	Integer		$offset
	 * @return	String
	 */
	public static function renderCompanyList($searchWord = '', $offset = 0) {
		Todoyu::restrict('contact', 'general:area');

		return TodoyuListingRenderer::render('contact', 'company', $offset, false, array('sword' => $searchWord));
	}



	/**
	 * @param	Integer[]	$companyIDs
	 * @return	String
	 */
	public static function renderCompanyListingSearch($companyIDs){
		return TodoyuListingRenderer::render('contact', 'companySearch', 0, true, array('companyIDs' => $companyIDs));
	}



	/**
	 * Render company edit form for popup (different save and cancel handling than conventional)
	 *
	 * @param	Integer	$idCompany
	 * @param	String	$idTarget		HTML Id of the input field
	 * @return	String
	 */
	public static function renderCompanyCreateWizard($idCompany, $idTarget) {
		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company	= TodoyuContactCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
			// Call hooked load data functions
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$form->getFieldset('buttons')->getField('cancel')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.cancelWizard(this.form);');
		$form->getFieldset('buttons')->getField('save')->setAttribute('onclick', 'Todoyu.Ext.contact.Company.saveWizard(this.form, \''.$idTarget.'\');');

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'	=> $company->getLabel(),
			'formhtml'		=> $form->render()
		);

		$content	= Todoyu::render($tmpl, $data);
		$content	.= TodoyuString::wrapScript('Todoyu.Ext.contact.Company.onEdit(' . $idCompany. ')');

		return $content;
	}



	/**
	 * Render company edit form
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyEditForm($idCompany) {
		$idCompany	= intval($idCompany);
		$xmlPath	= 'ext/contact/config/form/company.xml';

		$form	= TodoyuFormManager::getForm($xmlPath, $idCompany);

		$company= TodoyuContactCompanyManager::getCompany($idCompany);
		$data	= $company->getTemplateData(true);
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idCompany);

		$form->setFormData($data);
		$form->setRecordID($idCompany);

		$tmpl	= 'ext/contact/view/form.tmpl';
		$data	= array(
			'formheader'=> $company->getLabel(),
			'formhtml'	=> $form->render()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render company summary
	 *
	 * @param	Integer	$idCompany
	 * @return	String
	 */
	public static function renderCompanyDetails($idCompany) {
		$idCompany = intval($idCompany);

		$tmpl		= 'ext/contact/view/company-detail.tmpl';
		$company	= TodoyuContactCompanyManager::getCompany($idCompany);

		$data		= $company->getTemplateData(true);

		$data['hookedContent']	= implode('', TodoyuHookManager::callHook('contact', 'company.renderDetail', array($idCompany)));

		return Todoyu::render($tmpl, $data);
	}




	/**
	 * Render employee list of given company
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function renderEmployeeList($idCompany) {
		return TodoyuListingRenderer::render('contact', 'employee', 0, true, array('idCompany' => $idCompany));
	}



	/**
	 * Render action buttons for company record
	 *
	 * @param	Integer		$idCompany
	 * @return	String
	 */
	public static function renderCompanyActions($idCompany) {
		$tmpl	= 'ext/contact/view/company-actions.tmpl';
		$data	= array(
			'id'	=> intval($idCompany)
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>