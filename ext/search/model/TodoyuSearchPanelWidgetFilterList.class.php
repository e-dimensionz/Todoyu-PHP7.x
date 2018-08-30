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
 * Panel widget: Search filter list
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchPanelWidgetFilterList extends TodoyuPanelWidget {

	/**
	 * Constructor of the class
	 * - initializes the filters
	 * - modifies the filters
	 *
	 * @param	Array	$config
	 * @param	Array	$params
	 */
	public function __construct(array $config, array $params = array()) {
		TodoyuExtensions::loadAllFilters();

		parent::__construct(
			'search',
			'searchfilterlist',
			'search.panelwidget-searchfilterlist.title',
			$config,
			$params
		);

		$this->addHasIconClass();

		TodoyuPage::addJsInit('Todoyu.Ext.search.PanelWidget.SearchFilterList.init()');
	}



	/**
	 * Render panel widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$filtersetTypes		= TodoyuSearchFiltersetManager::getFiltersetTypes();
		$filters			= TodoyuSearchFiltersetManager::getFiltersets(0, null, true);
		$groupedFiltersets	= $this->groupFiltersets($filters);
		$toggleStatus		= TodoyuSearchPreferences::getFiltersetListToggle();
		$activeFiltersets	= array();

		foreach($filtersetTypes as $filtersetType) {
			if( $filtersetType == TodoyuSearchPreferences::getActiveTab() ) {
				$activeFiltersets[] = TodoyuSearchPreferences::getActiveFilterset($filtersetType);
			}
		}

		$tmpl = 'ext/search/view/panelwidget-searchfilterlist.tmpl';
		$data = array(
			'id'				=> $this->getID(),
			'groupedFiltersets'	=> $groupedFiltersets,
			'activeFiltersets'	=> $activeFiltersets,
			'toggleStatus'		=> $toggleStatus
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Group filtersets by their type attribute
	 *
	 * @param	Array		$filtersets
	 * @return	Array
	 */
	private static function groupFiltersets(array $filtersets) {
		$groups = array();

		foreach($filtersets as $filterset) {
			$groups[ $filterset['type'] ]['label']			= Todoyu::Label(Todoyu::$CONFIG['FILTERS'][strtoupper($filterset['type'])]['config']['label']);
			$groups[ $filterset['type'] ]['filtersets'][]	= $filterset;
		}

		return $groups;
	}



	/**
	 * Shortcut for check whether search 'general:use' right is allowed
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('search', 'general:use');
	}

}

?>