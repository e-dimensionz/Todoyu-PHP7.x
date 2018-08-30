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
 * Sysmanager extconf action controller
 *
 * @package		Todoyu
 * @subpackage	Sysmanager
 */
class TodoyuSysmanagerExtconfActionController extends TodoyuActionController {

	/**
	 * Restrict access
	 *
	 * @param	Array		$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('sysmanager', 'extensions:config');
	}



	/**
	 * Save extension configuration
	 *
	 * @param	Array			$params
	 * @return	Void|String		Failure returns re-rendered form with error messages
	 */
	public function saveAction(array $params) {
		$data	= $params['config'];
		$extKey	= $data['extension'];
		$form	= TodoyuSysmanagerExtConfManager::getForm($extKey, false);

		$form->setFormData($data);

		if( $form->isValid() ) {
			$config	= $form->getStorageData();

			TodoyuSysmanagerExtConfManager::saveExtConf($extKey, $config);
		} else {
			TodoyuHeader::sendTodoyuErrorHeader();

			return $form->render();
		}
	}

}

?>