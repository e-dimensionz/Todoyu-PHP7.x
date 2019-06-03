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
 * Panel widget for project tree
 *
 * @package		Todoyu
 * @subpackage	Project
 */
class TodoyuProjectPanelWidgetProjectList extends TodoyuPanelWidgetSearchList {

	/**
	 * Initialize project list PanelWidget
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 */
	public function __construct(array $config, array $params = array()) {
		parent::__construct(
			'project',								// ext key
			'projectlist',							// panel widget ID
			'project.panelwidget-projectlist.title',// widget title text
			$config,								// widget config array
			$params									// widget parameters
		);

		$this->addHasIconClass();

		$this->setJsObject('Todoyu.Ext.project.PanelWidget.ProjectList');
	}



	/**
	 * Get project list items
	 *
	 * @return	Array
	 */
	protected function getItems() {
		$projects	= $this->getListedProjectsData();
		$items		= array();

		foreach($projects as $project) {
			$companyShort	= $project['companyShort'] ? $project['companyShort'] : $project['company'];
			$items[] = array(
				'id'	=> $project['id'],
				'label'	=> $companyShort . ' - ' . $project['title'],
				'title'	=> $project['company'] . ' - ' . $project['title'] . ' (ID: ' . $project['id'] . ')',
				'class'	=> 'bcStatus' . $project['status']
			);
		}

		return $items;
	}



	/**
	 * Get project IDs which match to current filters
	 *
	 * @return	Array
	 */
	private function getProjectIDs() {
		$filters	= $this->getProjectFilters();
		$filter		= new TodoyuProjectProjectFilter($filters);
		$limit		= intval(Todoyu::$CONFIG['EXT']['project']['panelWidgetProjectList']['maxProjects']);

			// Get matching project IDs
		return $filter->getProjectIDs('', $limit);
	}



	/**
	 * Get configuration for status' and fulltext filter
	 *
	 * @return	Array
	 */
	private function getProjectFilters() {
		$statusWidget	= TodoyuPanelWidgetManager::getPanelWidget('project', 'StatusFilterProject');

		return array(
			array(
				'filter'	=> 'fulltext',
				'value'		=> $this->getSearchText()
			),
			array(
				'filter'	=> 'status',
				'value'		=> $statusWidget->getSelectedStatuses()
			)
		);
	}



	/**
	 * Get projects which match the filters
	 *
	 * @return	Array
	 */
	private function getListedProjectsData() {
		$projectIDs	= $this->getProjectIDs();

		if( !empty($projectIDs) ) {
			$fields	= '	ext_project_project.id,
						ext_project_project.title,
						ext_project_project.status,
						ext_contact_company.shortname as companyShort,
						ext_contact_company.title as company';
			$tables	= '	ext_project_project,
						ext_contact_company';
			$where	= '		ext_project_project.id_company	= ext_contact_company.id
						AND	ext_project_project.id IN(' . implode(',', $projectIDs) . ')';
			$order	= ' ext_contact_company.shortname,
						ext_project_project.title';

			$projects	= Todoyu::db()->getArray($fields, $tables, $where, '', $order);
		} else {
			$projects	= array();
		}

		return $projects;
	}



	/**
	 * Check panelWidget access permission
	 *
	 * @return	Boolean
	 */
	public static function isAllowed() {
		return Todoyu::allowed('project', 'general:use');
	}

}

?>