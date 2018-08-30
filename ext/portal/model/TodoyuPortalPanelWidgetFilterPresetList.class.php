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
 * Panel widget: filter presets
 *
 * @package		Todoyu
 * @subpackage	Portal
 */
class TodoyuPortalPanelWidgetFilterPresetList extends TodoyuPanelWidget {

	/**
	 * Initialize filter presetlist widget
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'portal',									// ext key
			'filterpresetlist',							// panel widget ID
			'portal.panelwidget-filterpresetlist.title',// widget title text
			$config,									// widget config array
			$params										// widget parameters
		);

		$this->addHasIconClass();
	}



	/**
	 * Get array of types of filtersets (record types with dedicated filtersets)
	 *
	 * @return	Array
	 */
	public function getFiltersetTypes() {
		$typeKeys	= TodoyuSearchFilterManager::getFilterTypes(true);
		$types		= array();

		foreach($typeKeys as $typeKey) {
			$typeFiltersets	= TodoyuSearchFiltersetManager::getTypeFiltersets($typeKey, 0, false, true);

			if( sizeof($typeFiltersets) > 0 ) {
				$types[$typeKey]['title'] = TodoyuSearchFilterManager::getFilterTypeLabel($typeKey);
			}

			foreach($typeFiltersets as $typeFilterset) {
				$isSeparator	= $typeFilterset['is_separator'] === '1';

				$label	= TodoyuString::crop($typeFilterset['title'], $isSeparator ? 50 : 46, '', false);

				if( ! $isSeparator ) {
					$resultCount	= TodoyuSearchFiltersetManager::getFiltersetCount($typeFilterset['id']);
					$label	.= ' (' . $resultCount . ')';
				}

				$types[$typeKey]['options'][] = array(
					'label'		=> $label,
					'value'		=> $typeFilterset['id'],
					'class'		=> $isSeparator ? 'separator' : '',
					'disabled'	=> $isSeparator ? '1' : '0'
				);
			}
		}

		return $types;
	}



	/**
	 * Get IDs of active filtersets in list
	 *
	 * @return	Array
	 */
	private static function getActiveFiltersetIDs() {
		return TodoyuPortalPreferences::getSelectionTabFiltersetIDs();
	}



	/**
	 * Render widget content
	 *
	 * @return	String
	 */
	public function renderContent() {
		$tmpl	= 'ext/portal/view/panelwidget-filterpresetlist.tmpl';

		$data	= array(
			'id'		=> $this->getID(),
			'types'		=> $this->getFiltersetTypes(),
			'selected'	=> array()
		);

		if( TodoyuPortalPreferences::getActiveTab() === 'selection' ) {
			$data['selected']	= self::getActiveFiltersetIDs();
		}

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render filter presets panel widget
	 *
	 * @return	String
	 */
	public function render() {
		TodoyuPage::addJsInit('Todoyu.Ext.portal.PanelWidget.FilterPresetList.init()', 100);

		return parent::render();
	}



	/**
	 * Check whether panel widget is allowed
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('portal', 'general:use');
	}

}

?>