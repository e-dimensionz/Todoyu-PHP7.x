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
 * Contact panelwidget: export
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuContactPanelwidgetcontactexportActionController extends TodoyuActionController {

	/**
	 * Initialisation for action controller
	 */
	public function init() {
		TodoyuContactRights::restrictExport();
	}



	/**
	 * Export action for contact
	 *
	 * @param	Array		$params
	 */
	public function exportAction(array $params) {
		$searchWords= TodoyuArray::trimExplode(' ', $params['searchword'], true);
		$tab		= trim($params['tab']);

		if( $tab == 'person' ) {
			TodoyuContactPersonExportManager::exportCSV($searchWords);
		} elseif( $tab == 'company' ) {
			TodoyuContactCompanyExportManager::exportCSV($searchWords);
		} else {
			TodoyuLogger::logError('Unknown tab for contact export <' . $tab . '>');
		}
	}

}

?>