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
 * Project export manager
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectExportManager {

	/**
	 * Exports Project as CSV
	 *
	 * @param	Array	$projectIDs
	 */
	public static function exportCSV(array $projectIDs) {
		$projectIDs	= TodoyuArray::intval($projectIDs);

		$projectsToExport = self::prepareDataForExport($projectIDs);

		$export		= new TodoyuExportCSV($projectsToExport);

		$export->setFilename('todoyu_project_export_' . date('YmdHis') . '.csv');

		$export->download();
	}



	/**
	 * Prepares projects data for export
	 *
	 * @param	Array	$projectIDs
	 * @return	Array
	 */
	protected static function prepareDataForExport(array $projectIDs) {
		$projectIDs	= TodoyuArray::intval($projectIDs);

		$exportData = array();

		foreach($projectIDs as $idProject)	 {
			$project	= TodoyuProjectProjectManager::getProject($idProject);

			$project->loadForeignData();

			$exportData[] = self::parseDataForExport($project);
		}

		return $exportData;
	}



	/**
	 * Parses Project data for CSV export
	 *
	 * @param	TodoyuProjectProject	$project
	 * @return	Array
	 */
	protected static function parseDataForExport(TodoyuProjectProject $project) {
		$exportData = array(
			Todoyu::Label('project.ext.attr.id')			=> $project->getID(),
			Todoyu::Label('project.task.attr.date_create')	=> TodoyuTime::format($project->getDateCreate(), 'date'),
			Todoyu::Label('core.global.date_update')		=> TodoyuTime::format($project->getDateUpdate(), 'date'),
			Todoyu::Label('core.global.id_person_create')	=> $project->getPersonCreate()->getFullName(),
			Todoyu::Label('project.ext.attr.date_start')	=> TodoyuTime::format($project->getDateStart()),
			Todoyu::Label('project.ext.attr.date_end')		=> TodoyuTime::format($project->getDateEnd()),
			Todoyu::Label('project.ext.attr.date_deadline')	=> TodoyuTime::format($project->getDateDeadline()),
			Todoyu::Label('project.ext.attr.title')			=> $project->getTitle(),
			Todoyu::Label('core.global.description')		=> TodoyuString::html2text($project->getDescription(), true),
			Todoyu::Label('project.ext.attr.status')		=> $project->getStatusLabel(),
			Todoyu::Label('project.ext.attr.company')		=> $project->getCompany()->getLabel(),
		);

		foreach($project['persons'] as $index => $personData) {
			$exportData[Todoyu::Label('contact.ext.person') . '_' . ($index + 1)]			= $personData['firstname'] . ' ' . $personData['lastname'];
			$exportData[Todoyu::Label('project.ext.attr.persons.role') . '_' . ($index + 1)]= $personData['rolelabel'];
		}

		$exportData = TodoyuHookManager::callHookDataModifier('project', 'projectCSVExportParseData', $exportData, array($project));

		return $exportData;
	}
}

?>