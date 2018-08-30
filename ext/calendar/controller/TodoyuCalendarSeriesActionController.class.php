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
 * Event series action controller
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
class TodoyuCalendarSeriesActionController extends TodoyuActionController {

	/**
	 * Initialize (restrict rights)
	 */
	public function init() {
		Todoyu::restrict('calendar', 'general:use');
		Todoyu::restrictInternal();
	}



	/**
	 * Delete all events of a series
	 *
	 * @param	Array	$params
	 */
	public function deleteAction(array $params) {
		$idSeries			= intval($params['series']);
		$deletedEventIDs	= TodoyuCalendarEventSeriesManager::deleteSeries($idSeries);
		$numDeletedEvents	= sizeof($deletedEventIDs);

		TodoyuHeader::sendTodoyuHeader('deleted', $numDeletedEvents);
	}



	/**
	 * Get series config sub form (includes possible overbooking warnings)
	 *
	 * @param	Array	$params
	 * @return	String
	 */
	public function configAction(array $params) {
			// Extract from data from request
		parse_str($params['data'], $urlData);
		$formData	= TodoyuArray::assure($urlData['event']);

			// Workaround: Prototype will serialize the persons as comma separated list...
		if( $formData['persons'] && $formData['persons'][0] ) {
			$formData['persons'] = TodoyuArray::intExplode(',', $formData['persons'][0]);
		}

			// Extract storage data
		$idEvent	= intval($formData['id']);
		$params	= array(
			'options'	=> array(
				'seriesEdit'	=> true
			)
		);
		$eventForm	= TodoyuCalendarEventStaticManager::getEventForm($idEvent, $formData, $params);
		$storageData= $eventForm->getStorageData();

			// Render the fields with the series object
		$idSeries	= intval($formData['id_series']);
		$series		= TodoyuCalendarEventSeriesManager::getSeries($idSeries);
		$series->setFormData($storageData);

		return $series->renderSeriesFields();
	}

}

?>