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
 * Render class for the contact module
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactRenderer {

	/**
	 * Extension key
	 *
	 * @var	String
	 */
	const EXTKEY = 'contact';



	/**
	 * Renders the contact page. The content is given from controller
	 *
	 * @param	String	$type
	 * @param	Integer	$idRecord
	 * @param	String	$searchWord
	 * @param	String	$content
	 * @return	String
	 */
	public static function renderContactPage($type, $idRecord, $searchWord, $content = '') {
			// Set active tab
		TodoyuFrontend::setActiveTab('contact');

		TodoyuPage::init('ext/contact/view/ext.tmpl');
		TodoyuPage::setTitle('contact.ext.page.title');

		$panelWidgets	= self::renderPanelWidgets();
		$tabs			= self::renderTabs($type);

		TodoyuPage::set('panelWidgets', $panelWidgets);
		TodoyuPage::set('tabs', $tabs);
		TodoyuPage::set('content', $content);

			// Display output
		return TodoyuPage::render();
	}



	/**
	 * Render the tab menu
	 *
	 * @param	String		$activeTab			e.g 'person' / 'company'
	 * @param	Boolean		$onlyActive
	 * @return	String
	 */
	public static function renderTabs($activeTab, $onlyActive = false) {
		$tabs	= TodoyuTabManager::getAllowedTabs(Todoyu::$CONFIG['EXT']['contact']['tabs']);

			// Render only the currenty active tab?
		if( $onlyActive ) {
			foreach($tabs as $tab) {
				if( $tab['id'] == $activeTab ) {
					$tabs = array($tab);
					break;
				}
			}
		}

		$name		= 'contact';
		$jsHandler	= 'Todoyu.Ext.contact.onTabSelect.bind(Todoyu.Ext.contact)';

		return TodoyuTabheadRenderer::renderTabs($name, $tabs, $jsHandler, $activeTab);
	}



	/**
	 * Render contacts list
	 *
	 * @param	String		$type
	 * @param	String		$searchWord
	 * @return	String
	 */
	public static function renderContactList($type, $searchWord = '') {
		$content	= '';

		switch($type) {
			case 'person':
				$content = TodoyuContactPersonRenderer::renderPersonList($searchWord);
				break;

			case 'company':
				$content = TodoyuContactCompanyRenderer::renderCompanyList($searchWord);
				break;
		}

		return $content;
	}



	/**
	 * Render edit form for given contact record of given type
	 *
	 * @param	String	$type
	 * @param	Integer	$idRecord
	 * @return	String
	 */
	public static function renderContactEdit($type, $idRecord = 0) {
		$idRecord	= intval($idRecord);

		$content	= '';
		switch($type) {
			case 'person':
				$content = TodoyuContactPersonRenderer::renderPersonEditForm($idRecord);
				break;

			case 'company':
				$content = TodoyuContactCompanyRenderer::renderCompanyEditForm($idRecord);
				break;
		}

		return $content;
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Render content of record info popup (e.g. person visiting card or company summary)
	 *
	 * @param	String		$type
	 * @param	Integer		$idRecord
	 * @return	String
	 */
	public static function renderDetails($type, $idRecord) {
		$content = '';

		switch($type) {
			case 'person':
				$content	= TodoyuContactPersonRenderer::renderPersonDetails($idRecord);
				break;

			case 'company':
				$content	= TodoyuContactCompanyRenderer::renderCompanyDetails($idRecord);
				break;
		}

		return $content;
	}



	/**
	* @param	Integer		$idRecord
	* @param	String		$recordType
	* @return	String
	*/
	public static function renderContactImageUploadForm($idRecord, $recordType) {
		$idRecord	= intval($idRecord);

			// Construct form object
		$xmlPath	= 'ext/contact/config/form/uploadcontactimage.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);

			// Set form data
		$formData	= array(
			'MAX_FILE_SIZE'	=> intval(Todoyu::$CONFIG['EXT']['contact']['contactimage']['max_file_size'])
		);

		$formData	= TodoyuFormHook::callLoadData($xmlPath, $formData);
		$formData['idContact']	= $idRecord;
		$formData['recordType']	= $recordType;

		$form->setFormData($formData);
		$form->setUseRecordID(false);


			// Render form
		$data	= array(
			'formhtml'	=> $form->render()
		);

			// Render form wrapped via dwoo template
		return Todoyu::render('ext/contact/view/contactimageuploadform.tmpl', $data);
	}



	/**
	 * Render upload iframe form after uploading finished
	 *
	 * @param	String		$recordType
	 * @param	Integer		$idContact
	 * @param	Integer		$idReplace
	 * @return	String
	 */
	public static function renderUploadFormFinished($recordType, $idContact, $idReplace) {
		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.contact.Upload.uploadFinished(\'' . $recordType . '\', ' . $idContact . ', \'' .$idReplace . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render content of upload frame after failed upload
	 *
	 * @param	Integer		$error
	 * @param	String		$filename
	 * @return	String
	 */
	public static function renderUploadframeContentFailed($error, $filename) {
		$error		= intval($error);
		$maxFileSize= TodoyuString::formatSize(TodoyuNumeric::intPositive(Todoyu::$CONFIG['EXT']['contact']['contactimage']['max_file_size']));

		$tmpl	= 'core/view/htmldoc.tmpl';
		$data	= array(
			'title'		=> 'Uploader IFrame',
			'content'	=> TodoyuString::wrapScript('window.parent.Todoyu.Ext.contact.Upload.uploadFailed(' . $error . ', \'' . $filename . '\', \'' . $maxFileSize . '\');')
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render list of found duplicated records (person/company)
	 *
	 * @param	Array		$records
	 * @return	String
	 */
	public static function renderDuplicatesList($records){
		$tmpl = 'ext/contact/view/form-warning-duplicates.tmpl';

		$data = array(
			'records' => $records
		);

		return Todoyu::render($tmpl, $data);
	}

}

?>