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
 * Logout headlet with logout button
 *
 * @package		Todoyu
 * @subpackage	Loginpage
 */
class TodoyuLoginpageHeadletLogout extends TodoyuHeadletTypeButton {

	/**
	 * Initialize headlet
	 */
	protected function init() {
			// Set JavaScript object which handles events
		$this->setJsHeadlet('Todoyu.Ext.loginpage.Headlet.Logout');
	}



	/**
	 * Get headlet label
	 *
	 * @return	String
	 */
	public function getLabel() {
		return Todoyu::Label('loginpage.ext.headlet.label');
	}

}

?>