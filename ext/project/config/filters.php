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
 * Filter configurations for project extension
 *
 * @package		Todoyu
 * @subpackage	Project
 */

/**
 * Task filters
 */
Todoyu::$CONFIG['FILTERS']['TASK'] = array(
	'key'		=> 'task',
	'config'	=> array(
		'label'				=> 'project.task.search.label',
		'position'			=> 10,
		'resultsRenderer'	=> 'TodoyuProjectTaskRenderer::renderTaskListing',
		'class'				=> 'TodoyuProjectTaskFilter',
		'defaultSorting'	=> 'ext_project_task.date_deadline',
		'require'			=> 'project.general:use'
	),
	'widgets' => array(

		/**
		 * OptGroup filter
		 */
		'filterSet' => array(
			'label'		=> 'search.ext.filterlabel.filterset',
			'optgroup'	=> 'core.global.filter',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuSearchFiltersetManager::getTaskFilterSetSelectionOptions'
			)
		),

		/**
		 * OptGroup task
		 */
		'type' => array(
			'label'		=> 'project.filter.task.type',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'wConf' => array(
				'multiple'	=> false,
				'size'		=> 2,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getTypeOptions',
				'negation'	=> 'default'
			)
		),
		'status' => array(
			'label'		=> 'core.global.status',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 9,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getStatusOptions',
				'negation'	=> 'default'
			)
		),
		'creatorPerson' => array(
			'label'		=> 'project.filter.task.creatorPerson',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
				'negation'	=> 'default'
			)
		),
		'creatorRoles' => array(
			'label'		=> 'project.filter.task.creatorRoles',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'internal'	=> true,
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getProjectroleOptionDefinitions',
				'negation'	=> 'default'
			)
		),
		'assignedPerson' => array(
			'label'		=> 'project.filter.task.assignedPerson',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'internal'	=> true,
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
				'negation'	=> 'default'
			)
		),
		'assignedRoles' => array(
			'label'		=> 'project.filter.task.assignedRoles',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'internal'	=> true,
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getProjectroleOptionDefinitions',
				'negation'	=> 'default'
			)
		),
		'ownerPerson' => array(
			'label'		=> 'project.filter.task.ownerPerson',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
				'negation'	=> 'default'
			)
		),
		'ownerRoles' => array(
			'label'		=> 'project.filter.task.ownerRoles',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'internal'	=> true,
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getProjectroleOptionDefinitions',
				'negation'	=> 'default'
			)
		),
		'acknowledged' => array(
			'label'		=> 'project.filter.task.acknowledged',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'internal'	=> true,
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactPersonFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		),
		'isPublic'	=> array(
			'label'		=> 'project.filter.task.isPublic',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'checkbox',
			'internal'	=> true,
			'wConf'		=> array(
				'checked'	=> true
			)
		),
		'title' => array(
			'label'		=> 'project.filter.task.title',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'wConf'		=> array(
				'negation'		=> 'default'
			)
		),
		'fulltext' => array(
			'label'		=> 'project.filter.task.fulltext',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		),
		'activity' => array(
			'label'		=> 'project.filter.task.activity',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'select',
			'wConf' => array(
				'multiple'	=> true,
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getActivityOptions',
				'negation'	=> 'default'
			)
		),
		'parentTask' => array(
			'label'		=> 'project.task.attr.id_parenttask',
			'optgroup'	=> 'project.task.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuProjectTaskFilterDataSource::autocompleteTasks',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		),






		/**
		 * OptGroup project
		 */
		'project' => array(
			'label'		=> 'project.filter.project',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuProjectProjectFilterDataSource::autocompleteProjects',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
				'negation'		=> 'default'
			)
		),
		'projecttitle' => array(
			'label'		=> 'project.filter.project.title',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'negation'		=> 'default'
			)
		),
		'projectstatus' => array(
			'label'			=> 'project.filter.project.status',
			'optgroup'		=> 'project.ext.search.label',
				'widget'	=> 'select',
			'wConf'			=> array(
				'multiple'		=> true,
				'size'			=> 5,
				'FuncRef'		=> 'TodoyuProjectProjectFilterDataSource::getStatusOptions',
				'negation'		=> 'default'
			)
		),
		'projectrole' => array(
			'label'		=> 'project.filter.project.projectrole',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'projectrole',
			'internal'	=> true,
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'multiple'		=> true,
				'size'			=> 5,
				'negation'		=> 'default'
			)
		),
		'customer'  => array(
			'label'		=> 'project.filter.project.company',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactCompanyFilterDataSource::getCompanyLabel',
				'negation'		=> 'default'
			)
		),



		/**
		 * OptGroup time management
		 */
		'deadlinedate'		=> array(
			'label'		=> 'project.filter.task.deadlinedate',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'deadlinedateDyn'	=> array(
			'label'		=> 'project.filter.task.deadlinedateDyn',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getDynamicDateOptions',
				'negation'	=> 'datetimeDyn'
			)
		),
		'startdate'		=> array(
			'label'		=> 'project.filter.task.startdate',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'date',
			'internal'	=> true,
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'startdateDyn'	=> array(
			'label'		=> 'project.filter.task.startdateDyn',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'select',
			'internal'	=> true,
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getDynamicDateOptions',
				'negation'	=> 'datetimeDyn'
			)
		),
		'enddate'		=> array(
			'label'		=> 'project.filter.task.enddate',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'date',
			'internal'	=> true,
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'enddateDyn'	=> array(
			'label'		=> 'project.filter.task.enddateDyn',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'select',
			'internal'	=> true,
			'wConf'		=> array(
				'FuncRef'		=> 'TodoyuProjectTaskFilterDataSource::getDynamicDateOptions',
				'negation'		=> 'datetimeDyn'
			)
		),
		'createdate' => array(
			'label'		=> 'project.filter.task.createdate',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'createdateDyn' => array(
			'label'		=> 'project.filter.task.createdateDyn',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getDynamicDateOptions',
				'negation'	=> 'datetimeDyn'
			)
		),
		'editdate' => array(
			'label'		=> 'project.filter.task.editdate',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'editdateDyn' => array(
			'label'		=> 'project.filter.task.editdateDyn',
			'optgroup'	=> 'project.filter.task.timemanagement.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectTaskFilterDataSource::getDynamicDateOptions',
				'negation'	=> 'datetimeDyn'
			)
		)

	),

	/**
	 * Filters without a widget in the search area
	 */
	'filters' => array(

	),

	/**
	 * Sorting of the results
	 */
	'sorting' => array(
		'dateCreate' => array(
			'label'				=> 'core.date.date_create',
			'optgroup'			=> 'project.task.task'
		),
		'dateUpdate' => array(
			'label'				=> 'core.date.date_update',
			'optgroup'			=> 'project.task.task',
			'restrictInternal'	=> true
		),
		'dateStart' => array(
			'label'				=> 'project.task.attr.date_start',
			'optgroup'			=> 'project.task.task',
			'restrictInternal'	=> true
		),
		'dateEnd' => array(
			'label'				=> 'project.task.attr.date_end',
			'optgroup'			=> 'project.task.task',
			'restrictInternal'	=> true
		),
		'dateDeadline' => array(
			'label'				=> 'project.task.attr.date_deadline',
			'optgroup'			=> 'project.task.task'
		),
		'projectID'		=> array(
			'label'				=> 'project.filter.project.id',
			'optgroup'			=> 'project.task.task'
		),
		'title' => array(
			'label'				=> 'core.global.title',
			'optgroup'			=> 'project.task.task'
		),
		'personAssigned' => array(
			'label'				=> 'project.task.person_assigned',
			'optgroup'			=> 'project.task.task',
			'require'			=> 'contact.person:seeAllPersons'
		),
		'personOwner' => array(
			'label'				=> 'project.task.person_owner',
			'optgroup'			=> 'project.task.task',
			'require'			=> 'contact.person:seeAllPersons'
		),
		'taskNumber' => array(
			'label'				=> 'project.task.taskno',
			'optgroup'			=> 'project.task.task'
		),
		'status' => array(
			'label'				=> 'project.task.attr.status',
			'optgroup'			=> 'project.task.task'
		),
		'activity' => array(
			'label'				=> 'project.task.attr.activity',
			'optgroup'			=> 'project.task.task'
		),
		'estimatedWorkload' => array(
			'label'				=> 'project.task.estimated_workload',
			'optgroup'			=> 'project.task.task',
			'restrictInternal'	=> true
		),
		'acknowledged' => array(
			'label'				=> 'project.task.attr.is_acknowledged',
			'optgroup'			=> 'project.task.task'
		),
		'public' => array(
			'label'				=> 'project.task.attr.is_public',
			'optgroup'			=> 'project.task.task',
			'restrictInternal'	=> true
		)
	)

);



/**
 * Project filters
 */
Todoyu::$CONFIG['FILTERS']['PROJECT'] = array(
	'key'		=> 'project',
	'config'	=> array(
		'label'				=> 'project.ext.search.label',
		'position'			=> 20,
		'resultsRenderer'	=> 'TodoyuProjectProjectRenderer::renderProjectListing',
		'class'				=> 'TodoyuProjectProjectFilter',
		'require'			=> 'project.general:use'
	),
	'widgets' => array(

		/**
		 * OptGroup filter
		 */
		'filterSet' => array(
			'label'		=> 'search.ext.filterlabel.filterset',
			'optgroup'	=> 'core.global.filter',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuSearchFiltersetManager::getProjectFilterSetSelectionOptions'
			)
		),
//		'taskFilter'	=> array(
//			'label'		=> 'project.filter.project.taskfilter',
//			'optgroup'	=> 'core.global.filter',
//			'widget'	=> 'select',
//			'wConf'		=> array(
//				'multiple'	=> true,
//				'size'		=> 5,
//				'FuncRef'	=> 'TodoyuSearchFiltersetManager::getTaskFilterSetSelectionOptions'
//			)
//		),



		/**
		 * OptGroup project
		 */
		'projectID' => array(
			'label'		=> 'project.filter.project.id',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf'		=> array(
				'negation'	=> 'default'
			)
		),
		'title' => array(
			'label'		=> 'core.global.title',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf'		=> array(
				'negation'	=> 'default'
			)
		),
		'fulltext' => array(
			'label'		=> 'project.filter.project.fulltext',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
		),
		'status' => array(
			'label'		=> 'core.global.status',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'multiple'	=> true,
				'size'		=> 5,
				'FuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getStatusOptions',
				'negation'	=> 'default'
			)
		),
		'company' => array(
			'label'		=> 'project.filter.project.company',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'text',
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies',
				'FuncParams'	=> array(),
				'LabelFuncRef'	=> 'TodoyuContactCompanyFilterDataSource::getCompanyLabel',
				'negation'		=> 'default'
			)
		),
		'projectrole' => array(
			'label'		=> 'project.filter.project.projectrole',
			'optgroup'	=> 'project.ext.search.label',
			'widget'	=> 'projectrole',
			'internal'	=> true,
			'wConf' => array(
				'autocomplete'	=> true,
				'FuncRef'		=> 'TodoyuContactPersonFilterDataSource::autocompletePersons',
				'multiple'		=> true,
				'size'			=> 5,
				'negation'		=> 'default'
			)
		),
//		'locked' => array(
//			  'funcRef'		=> 'TodoyuProjectProjectFilter::filter_locked',
//			  'label'		=> 'core.global.locked',
//			  'optgroup'	=> 'project.ext.search.label',
//			  'widget'		=> 'checkbox',
//		),



		/**
		 * OptGroup time management
		 */
		'startdate' => array(
			'label'		=> 'project.filter.project.date_start',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'enddate' => array(
			'label'		=> 'project.filter.project.date_end',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'deadline' => array(
			'label'		=> 'project.filter.project.deadline',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'date',
			'internal'	=> true,
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),

		'createdate' => array(
			'label'		=> 'project.filter.project.createdate',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'createdateDyn' => array(
			'label'		=> 'project.filter.project.createdateDyn',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getDynamicDateOptions',
				'negation'		=> 'datetime'
			)
		),
		'editdate' => array(
			'label'		=> 'project.filter.project.editdate',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'date',
			'wConf'		=> array(
				'negation'	=> 'datetime'
			)
		),
		'editdateDyn' => array(
			'label'		=> 'project.filter.project.editdateDyn',
			'optgroup'	=> 'project.filter.project.timemanagement.label',
			'widget'	=> 'select',
			'wConf'		=> array(
				'FuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getDynamicDateOptions',
				'negation'		=> 'datetime'
			)
		)

	),

	'sorting' => array(
		'dateCreate' => array(
			'label'		=> 'core.date.date_create',
			'optgroup'	=> 'project.ext.search.label'
		),
		'dateUpdate' => array(
			'label'		=> 'core.date.date_update',
			'optgroup'	=> 'project.ext.search.label'
		),
		'dateStart' => array(
			'label'		=> 'project.ext.attr.date_start',
			'optgroup'	=> 'project.ext.search.label'
		),
		'dateEnd' => array(
			'label'		=> 'project.ext.attr.date_end',
			'optgroup'	=> 'project.ext.search.label'
		),
		'projectID'		=> array(
			'label'		=> 'project.ext.attr.id',
			'optgroup'	=> 'project.ext.search.label'
		),
		'title' => array(
			'label'		=> 'core.global.title',
			'optgroup'	=> 'project.ext.search.label'
		),
		'status' => array(
			'label'		=> 'project.task.attr.status',
			'optgroup'	=> 'project.ext.search.label'
		),
		'company' => array(
			'label'		=> 'project.ext.attr.company',
			'optgroup'	=> 'project.ext.search.label'
		)
	)
);

?>