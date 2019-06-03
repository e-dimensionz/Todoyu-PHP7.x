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
 * Panel widget for staff list
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelWidgetStaffList extends TodoyuPanelWidgetSearchList {

	/**
	 * Initialize staff list PanelWidget
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'contact',									// ext key
			'stafflist',								// panel widget ID
			'contact.panelwidget-stafflist.title',	// widget title text
			$config,									// widget config array
			$params										// widget parameters
		);

		$this->addHasIconClass();

		$this->setJsObject('Todoyu.Ext.contact.PanelWidget.StaffList');
	}



	/**
	 * Get list items (persons)
	 *
	 * @return	Array
	 */
	protected function getItems() {
		$persons	= $this->getListedPersons();
		$items		= array();

		foreach($persons as $person) {
			$items[] = array(
				'id'	=> $person['id'],
				'label'	=> $person['lastname'] . ' ' . $person['firstname'],
				'title'	=> $person['lastname'] . ' ' . $person['firstname'] . ' (ID: ' . $person['id'] . ')'
			);
		}

		return $items;
	}



	/**
	 * Get person IDs which match the filter
	 *
	 * @return	Integer[]
	 */
	protected function getPersonIDs() {
		$filters	= array(
			array(
				'filter'	=> 'fulltext',
				'value'		=> $this->getSearchText()
			),
			array(
				'filter'	=> 'isInternal',
				'value'		=> true
			)
		);
		$filter		= new TodoyuContactPersonFilter($filters);

		return $filter->getPersonIDs($this->getLimit());
	}



	/**
	 * Get list size limit
	 *
	 * @return	Integer
	 */
	protected function getLimit() {
		return intval($this->config['max']);
	}



	/**
	 * Get persons which match the filters
	 *
	 * @return	Array
	 */
	private function getListedPersons() {
		$personIDs	= $this->getPersonIDs();

		if( !empty($personIDs) ) {
			$fields	=	'p.id,
						 p.firstname,
						 p.lastname,
						 p.shortname,
						 p.salutation';
			$tables	= '	ext_contact_person p,
						ext_contact_company c,
						ext_contact_mm_company_person mm';
			$where	= '		p.id			= mm.id_person
						AND	mm.id_company	= c.id
						AND	c.is_internal	= 1
						AND	p.deleted		= 0
						AND ' . TodoyuSql::buildInListQueryPart($personIDs, 'p.id');
			$order	= '	p.lastname,
						p.firstname';

			$persons= Todoyu::db()->getArray($fields, $tables, $where, '', $order);
		} else {
			$persons	= array();
		}

		return $persons;
	}



	/**
	 * Check panelWidget access permission
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('contact', 'general:use');
	}

}

?>