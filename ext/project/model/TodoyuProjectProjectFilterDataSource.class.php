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
 * Handles the data source for filter widgets
 *
 * @package		Todoyu
 * @subpackage	project
 */
class TodoyuProjectProjectFilterDataSource {

	/**
	 * Search for projects by given search string from the auto-completion
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array				array (id => label)
	 */
	public static function autocompleteProjects($input, array $formData = array(), $name = '') {
		$data = array();

		$keywords		= TodoyuArray::trimExplode(' ', $input, true);
		$projectIDs		= TodoyuProjectProjectSearch::searchProjects($keywords, array(), 30);

		if( sizeof($projectIDs) > 0 ) {
			$projects	= self::getProjects($projectIDs);
			foreach($projects as $project) {
				if( TodoyuProjectProjectRights::isSeeAllowed($project['id']) ) {
					$companyShort	= (! empty($project['company']) ) ? $project['company'] : TodoyuString::crop($project['companyfulltitle'], 16, '..', false);
					$data[$project['id']] = $companyShort . ' - ' . $project['title'];
				}
			}
		}

		return $data;
	}



	/**
	 * Search for projects in which adding of tasks is allowed by given search string from the auto-completion
	 *
	 * @param	String	$input
	 * @param	Array	$formData
	 * @param	String	$name
	 * @return	Array				array (id => label)
	 */
	public static function autocompleteTaskAddableProjects($input, array $formData = array(), $name = '') {
		$data = array();

		$searchWords= TodoyuArray::trimExplode(' ', $input, true);
		$projectIDs	= TodoyuProjectProjectManager::searchProjects($searchWords, array(), array(), 30);

		if( sizeof($projectIDs) > 0 ) {
			$projects	= self::getProjects($projectIDs);
			foreach($projects as $project) {
				if( TodoyuProjectTaskRights::isAddInProjectAllowed($project['id']) ) {
					$companyShort	= (! empty($project['company']) ) ? $project['company'] : TodoyuString::crop($project['companyfulltitle'], 16, '..', false);
					$data[$project['id']] = $companyShort . ' - ' . $project['title'];
				}
			}
		}

		return $data;
	}



	/**
	 * Get project records with given IDs
	 *
	 * @param	Array	$projectIDs
	 * @return	Array
	 */
	private static function getProjects(array $projectIDs = array()) {
		$fields		= '	p.id,
						p.title,
						c.shortname as company,
						c.title as companyfulltitle';
		$tables		= ' ext_project_project p,
						ext_contact_company c';
		$where		= ' p.id_company = c.id AND
						p.id IN(' . implode(',', $projectIDs) . ') ';

		return Todoyu::db()->getArray($fields, $tables, $where, '', '', 30);
	}



	/**
	 * Gets the label for the current autocompletion value.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function getLabel($definitions) {
		$idProject	= intval($definitions['value']);

		if( $idProject !== 0 ) {
			$project = TodoyuProjectProjectManager::getProject($idProject);

			$definitions['value_label'] = $project->getFullTitle();
		} else {
			$definitions['value_label'] = '';
		}

		return $definitions;
	}



	/**
	 * Prepares the options of project-status for rendering in the widget.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getStatusOptions(array $definitions) {
		$options	= array();
		$statuses	= TodoyuProjectProjectStatusManager::getStatusInfos();

		foreach($statuses as $status) {
			$options[] = array(
				'label'		=> $status['label'],
				'value'		=> $status['index'],
			);
		}

		$definitions['options'] = $options;

		return $definitions;
	}



	/**
	 * Dynamic date options
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public static function getDynamicDateOptions($definitions) {
		$definitions['options'] =  TodoyuSearchFilterHelper::getDynamicDateOptions();

		return $definitions;
	}

}

?>