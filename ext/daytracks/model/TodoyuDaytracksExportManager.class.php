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
 * Manages the daytracks export
 *
 * @package		Todoyu
 * @subpackage	Daytracks
 */
class TodoyuDaytracksExportManager {

	/**
	 * Returns the configured export-filter-form
	 *
	 * @return	TodoyuForm	$form
	 */
	public static function getExportForm() {
		$xmlPath	= 'ext/daytracks/config/form/export.xml';
		$form		= TodoyuFormManager::getForm($xmlPath);

		$form->setUseRecordID(false);

			// User is only allowed to export his own trackings
		if( !Todoyu::allowed('daytracks', 'daytracks:timeExportAllPerson') ) {
			$form->removeField('employee', true);
			$form->addHiddenField('employee', Todoyu::personid());
			$form->getField('employees')->setAttribute('comment', TodoyuContactPersonManager::getPerson(Todoyu::personid())->getFullName());
			$form->removeField('employerSelect', true);
		}

		if( Todoyu::allowed('daytracks', 'daytracks:timeExportAllEmployer') ) {
			$form->removeField('employerSelect', true);
			$form->getField('employerAC')->setName('employer');
		} else {
			$form->removeField('employerAC', true);
			$form->addHiddenField('employer', '');
		}

		return $form;
	}



	/**
	 * Exports timetracks as CSV file
	 *
	 * @param	Array	$exportData
	 */
	public static function exportCSV(array $exportData) {
		$export	= self::getExportCsvFromExportData($exportData);

		$export->download('daytracks_export_' . date('YmdHis') . '.csv');
	}



	/**
	 * @param	Array	$exportData
	 * @return	String
	 */
	public static function renderView(array $exportData) {
		TodoyuCache::disable();
		$export	= self::getExportCsvFromExportData($exportData);
		TodoyuCache::enable();
		$csv	= $export->getContent();

		$csv		= explode("\n", $csv);
		$csvArray	= array();
		$columns	= array();

		foreach($csv as $index => $line) {
			$line	= explode(';', $line);

			if( $index === 0 ) {
				$columns	= $line;
			} else {
				$csvArray[]	= $line;
			}

			unset($csv[$index]);
		}

		$tmpl	= 'ext/daytracks/view/export-view.tmpl';
		$data	= array(
			'columnHeaders'	=> $columns,
			'dataRows'		=> $csvArray,
			'noData'		=> count($csvArray) === 1
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * @param	Array				$exportData
	 * @return	TodoyuExportCsv
	 */
	public static function getExportCsvFromExportData(array $exportData) {
		if( Todoyu::allowed('daytracks', 'daytracks:timeExportAllPerson') ) {
			$employeeIDs	= TodoyuArray::intExplode(',', $exportData['employee'], true, true);
		} else {
			$employeeIDs	= array(Todoyu::personid());
		}

		$employer	= TodoyuArray::intExplode(',', $exportData['employer']);
		$project	= TodoyuArray::intExplode(',', $exportData['project']);
		$company	= TodoyuArray::intExplode(',', $exportData['company']);
		$dateStart	= intval($exportData['date_start']);
		$dateEnd	= intval($exportData['date_end']);

		$trackingData	= self::getTrackingReport($employeeIDs, $employer, $project, $company, $dateStart, $dateEnd);
		$trackingData	= self::prepareDataForExport($trackingData);

		return new TodoyuExportCSV($trackingData);
	}



	/**
	 * Gets and prepares the data for the daytracks export
	 *
	 * @param	Array		$personIDs			Persons
	 * @param	Array		$employerIDs		Employeer companies
	 * @param	Array		$projectIDs			Projects
	 * @param	Array		$customerIDs		Customers
	 * @param	Integer		$dateStart
	 * @param	Integer		$dateEnd
	 * @return	Array
	 */
	public static function getTrackingReport(array $personIDs = array(), array $employerIDs = array(), array $projectIDs = array(), array $customerIDs = array(), $dateStart = 0, $dateEnd = 0) {
		$personIDs		= TodoyuArray::intval($personIDs, true, true);
		$employerIDs	= TodoyuArray::intval($employerIDs, true, true);
		$projectIDs		= TodoyuArray::intval($projectIDs, true, true);
		$customerIDs	= TodoyuArray::intval($customerIDs, true, true);
		$dateStart		= intval($dateStart);
		$dateEnd		= intval($dateEnd);

		if( !empty($projectIDs)  ) {
			$customerIDs = array();
		}

		$fields	= '	CONCAT_WS(\'.\', task.id_project, task.tasknumber) as tasknumber,
					task.title as task,
					DATE_FORMAT(FROM_UNIXTIME(track.date_track), \'%Y-%m-%d\') as date_tracked,
					SEC_TO_TIME(track.workload_tracked) as workload_tracked,
					SEC_TO_TIME(IF(track.workload_chargeable, track.workload_chargeable, track.workload_tracked)) as workload_chargeable,
					company.title as company,
					project.title as project,
					CONCAT_WS(\', \', person.lastname, person.firstname) as name,
					track.comment,
					activity.title as activity';
		$tables	= '	ext_project_task 		task,
					ext_project_project 	project,
					ext_project_activity 	activity,
					ext_contact_company 	company,
					ext_contact_person 		person,
					ext_timetracking_track 	track';
		$where	= '		task.id_project			= project.id'
				. ' AND task.id_activity		= activity.id'
				. '	AND project.id_company		= company.id'
				. ' AND track.id_task			= task.id'
				. ' AND track.id_person_create	= person.id';
		$order	= '	track.date_track ASC';

		if( !empty($personIDs) ) {
			$where .= ' AND person.id IN(' . implode(',', $personIDs) . ')';
		}

		if( !empty($employerIDs) ) {
			$tables	.= ',ext_contact_mm_company_person pcmm';
			$where .= ' AND person.id = pcmm.id_person
						AND pcmm.id_company IN(' . implode(',', $employerIDs) . ')';
		}

		if( !empty($customerIDs)  ) {
			$where .= ' AND company.id IN(' . implode(',', $customerIDs) . ')';
		}

		if( !empty($projectIDs)) {
			$where .= ' AND project.id IN(' . implode(',', $projectIDs) . ')';
		}

		if( $dateStart > 0 ) {
			$dateStart	= TodoyuTime::getDayStart($dateStart);
			$where .= ' AND track.date_track >= ' . $dateStart;
		}

		if( $dateEnd > 0 ) {
			$dateEnd	= TodoyuTime::getDayEnd($dateEnd);
			$where .= ' AND track.date_track < ' . $dateEnd;
		}

		return Todoyu::db()->getArray($fields, $tables, $where, '', $order);
	}




	/**
	 * Returns the options for employers of current person
	 *
	 * @param	TodoyuFormElement	$field
	 * @return	Array
	 */
	public static function getEmployersOptions(TodoyuFormElement $field) {
		$companies	= TodoyuContactPersonManager::getPersonCompanyRecords(Todoyu::personid());
		$reformConfig	= array(
			'id'	=> 'value',
			'title'	=> 'label'
		);

		return TodoyuArray::reform($companies, $reformConfig, true);
	}



	/**
	 * Prepare data for export - substitute locale labels by their parsed values
	 *
	 * @param	Array	$dataArray
	 * @return	Array
	 */
	protected static function prepareDataForExport(array $dataArray) {
		$reform			= array(
			'tasknumber'			=> Todoyu::Label('project.task.taskno'),
			'task'					=> Todoyu::Label('project.task.attr.title'),
			'date_tracked'			=> Todoyu::Label('timetracking.ext.attr.date_track'),
			'workload_tracked'		=> Todoyu::Label('timetracking.ext.attr.workload_tracked'),
			'workload_chargeable'	=> Todoyu::Label('timetracking.ext.attr.workload_chargeable'),
			'company'				=> Todoyu::Label('contact.ext.company'),
			'project'				=> Todoyu::Label('project.ext.project'),
			'name'					=> Todoyu::Label('contact.ext.person'),
			'comment'				=> Todoyu::Label('timetracking.ext.attr.comment'),
			'activity'				=> Todoyu::Label('project.ext.records.activity'),
		);

		return TodoyuArray::reform($dataArray, $reform);
	}

}
?>