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
 * Abstract headlet menu class
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuHeadletTypeMenu extends TodoyuHeadlet {

	/**
	 * Type
	 *
	 * @var	String
	 */
	protected $type = 'menu';



	/**
	 * Init type
	 */
	protected function initType() {
		$this->addButtonClass('headletTypeMenu');
	}



	abstract protected function getMenuItems();



	/**
	 * Render headlet type menu items
	 *
	 * @return	String
	 */
	private function renderMenuItems() {
		$items	= $this->getMenuItems();

		$tmpl	= 'core/view/headlet-menu.tmpl';
		$data	= array(
			'name'	=> $this->getName(),
			'items'	=> $items
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render items of headlet type menu
	 *
	 * @return	String
	 */
	public function render() {
		$this->data['content'] = $this->renderMenuItems();

		return parent::render();
	}
}

?>