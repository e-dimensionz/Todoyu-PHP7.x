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
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHeadletQuickCreate extends TodoyuHeadletTypeMenu {

	/**
	 * Initialize quick create headlet (set template, set initial data)
	 */
	protected function init() {
		$this->setJsHeadlet('Todoyu.CoreHeadlets.QuickCreate');
	}



	/**
	 * Get menu items for headlet based on registered engines
	 *
	 * @return	Array
	 */
	protected function getMenuItems() {
		$engines= TodoyuQuickCreateManager::getEngines();

		$items	= array();
		if( is_array($engines['primary']) ) {
			array_unshift($engines['all'], $engines['primary']);
		}

		foreach($engines['all'] as $engine) {
			$item	= array(
				'id'	=> $engine['ext'] . '-' . $engine['type'],
				'class'	=> 'item' . ucfirst($engine['ext']) . ucfirst($engine['type']),
				'label'	=> $engine['label']
			);

			if( $engine['isPrimary'] ) {
				$item['class']	.= ' primary';
				$item['id']		.= '-primary';
			}

			$items[] = $item;
		}

		return $items;
	}



	/**
	 * Get headlet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('core.global.quickcreate.title');
	}



	/**
	 * Check if no items are available in the create menu
	 *
	 * @return	Boolean
	 */
	public function isEmpty() {
		return sizeof($this->getMenuItems()) === 0;
	}

}

?>