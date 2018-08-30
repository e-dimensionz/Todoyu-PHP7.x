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
 * Class for the contact search input panelWidget
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelWidgetSearch extends TodoyuPanelWidgetSearchBox {

	/**
	 * Constructor of the class
	 *
	 * @param	Array	$config
	 * @param	Array	$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'contact',											// ext key
			'contactsearch',									// panel widget ID
			'contact.panelwidget-contactsearchinput.title',	// widget title text
			$config,											// widget config array
			$params												// widget parameters
		);

		$this->setJsObject('Todoyu.Ext.contact.PanelWidget.ContactSearch');
	}



	/**
	 * Get stored search word from contact preferences
	 *
	 * @return	String
	 */
	protected function getSearchWord() {
		return TodoyuContactPreferences::getSearchWord();
	}

}
?>