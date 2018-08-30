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
 * Controller for daytracks export
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksExportActionController extends TodoyuActionController {

	/**
	 * Init controller: restrict to rights
	 *
	 * @param	Array	$params
	 */
	public function init(array $params = array()) {
		Todoyu::restrict('daytracks', 'general:use');
		Todoyu::restrict('daytracks', 'daytracks:timeExport');
	}



	/**
	 * Renders the download pop-up
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function renderpopupAction(array $params) {
		return TodoyuDaytracksExportRenderer::renderDaytracksExportForm($params);
	}



	/**
	 * Download Action for the CSV file
	 *
	 * @param	Array	$params
	 */
	public function downloadAction(array $params) {
		$form	= TodoyuDaytracksExportManager::getExportForm();
		$values	= TodoyuArray::assure($params['export']);
		$data	= $form->getStorageData($values);

		TodoyuDaytracksExportManager::exportCSV($data);
	}



	/**
	 * @param	Array	$params
	 * @return	String
	 */
	public function viewAction(array $params) {
		$data	= array(
			'employee'		=> $params['employee'],
			'employer'		=> $params['employer'],
			'project'		=> $params['project'],
			'company'		=> $params['company'],
			'date_start' 	=> $params['date_start'],
			'date_end'		=> $params['date_end'],
		);

		return TodoyuDaytracksExportManager::renderView($data);
	}
}

?>