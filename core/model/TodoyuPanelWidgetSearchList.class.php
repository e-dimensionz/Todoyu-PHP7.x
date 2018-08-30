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
 * [Enter Class Description]
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuPanelWidgetSearchList extends TodoyuPanelWidget {

	/**
	 * JavaScript object for initialization
	 *
	 * @var	String
	 */
	protected $jsObject;



	/**
	 * Render content of the search list
	 *
	 * @param	Boolean		$listOnly
	 * @return	String
	 */
	public function renderContent($listOnly = false) {
		$this->addClass('searchList');

		$tmpl	= $this->getTemplate();
		$data	= $this->getTemplateData();

		$data['listOnly']	= $listOnly;

		if( ! TodoyuRequest::isAjaxRequest() ) {
			$searchWord	= str_replace('\\', '', trim($this->getSearchText()) );
			$searchWord	= htmlentities($searchWord, ENT_QUOTES, 'UTF-8', false);

			TodoyuPage::addJsInit('Todoyu.R[\'' . $this->getID() . '\'] = new ' . $this->jsObject . '(\'' . $searchWord . '\')');
		}

		return trim(Todoyu::render($tmpl, $data));
	}



	/**
	 * Render search list
	 *
	 * @return String
	 */
	public function renderList() {
		return $this->renderContent(true);
	}



	/**
	 * Get template to render the search list
	 *
	 * @return	String
	 */
	protected function getTemplate() {
		return 'core/view/panelwidget-searchlist.tmpl';
	}



	/**
	 * Get template data
	 *
	 * @return	Array
	 */
	protected function getTemplateData() {
		$data = array(
			'searchForm'=> $this->renderSearchForm(),
			'items'		=> $this->getItems(),
			'id'		=> $this->getID()
		);

		return $data;
	}



	/**
	 * Render filter form
	 *
	 * @return	String
	 */
	protected function renderSearchForm() {
		$xmlPath= 'core/config/form/panelwidget-searchlist.xml';
		$form	= TodoyuFormManager::getForm($xmlPath);
		$data	= array(
			'search'	=> $this->getSearchText()
		);

		$form->setName($this->getID());
		$form->setFormData($data);
		$form->setUseRecordID(false);
		$form->setAttribute('class', 'searchList');

		return $form->render();
	}



	/**
	 * Set javascript object which handles the selector
	 *
	 * @param	String		$jsObject
	 */
	protected function setJsObject($jsObject) {
		$this->jsObject = $jsObject;
	}



	/**
	 * Get extension ID of the panel widget
	 *
	 * @return	Integer
	 */
	protected function getExtID() {
		return TodoyuExtensions::getExtID($this->get('ext'));
	}



	/**
	 * Save the entered search text as user preference
	 *
	 * @param	String		$search
	 */
	public function saveSearchText($search) {
		$pref	= 'panelwidgetsearchlist-' . $this->getID() . '-search';
		$search	= trim($search);

		TodoyuPreferenceManager::savePreference($this->getExtID(), $pref, $search, 0, true, AREA);
	}



	/**
	 * Get search text from prefs
	 *
	 * @return	String
	 */
	public function getSearchText() {
		$pref		= 'panelwidgetsearchlist-' . $this->getID() . '-search';
		$searchWord	= TodoyuPreferenceManager::getPreference($this->getExtID(), $pref, 0, AREA);

		return trim($searchWord);
	}


	/**
	 * Get items to render the list
	 *
	 * @abstract
	 * @return	Array
	 */
	protected abstract function getItems();

}

?>