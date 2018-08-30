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
 * Project related person filters - added via hook
 *
 * @package		Todoyu
 * @subpackage	Project
 * @see			TodoyuProjectManager::hookLoadContactFilterConfig
 */

	// Persons assigned in project
Todoyu::$CONFIG['FILTERS']['PERSON']['widgets']['assignedinproject'] = array(
	'funcRef'	=> 'TodoyuProjectPersonFilter::Filter_assignedinproject',
	'label'		=> 'project.filter.person.assignedinproject',
	'optgroup'	=> 'project.filter.optgroup.projects',
	'widget'	=> 'text',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuProjectProjectFilterDataSource::autocompleteProjects',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
		'negation'		=> false
	)
);

	// Company has project(s) with title fulltext
Todoyu::$CONFIG['FILTERS']['COMPANY']['widgets']['projecttitlefulltext']	= array(
	'funcRef'	=> 'TodoyuProjectCompanyFilter::Filter_projecttitlefulltext',
	'label'		=> 'project.filter.project.fulltext',
	'optgroup'	=> 'project.filter.optgroup.projects',
	'widget'	=> 'text',
	'wConf' => array(
		'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
		'negation'		=> false
	)
);

	// Company has project(s) with status...
Todoyu::$CONFIG['FILTERS']['COMPANY']['widgets']['projectstatus'] = array(
	'funcRef'	=> 'TodoyuProjectCompanyFilter::Filter_projectstatus',
	'label'		=> 'project.filter.company.projectstatus',
	'optgroup'	=> 'project.filter.optgroup.projects',
	'widget'	=> 'select',
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getStatusOptions',
		'negation'	=> 'default'
	)
);



	// Company has task(s) created before/after
//Todoyu::$CONFIG['FILTERS']['COMPANY']['widgets']['dateCreateTask'] = array(
//	'funcRef'	=> 'TodoyuProjectCompanyFilter::Filter_dateCreateTask',
//	'label'		=> 'project.filter.company.dateCreateTask',
//	'optgroup'	=> 'project.filter.optgroup.projects',
//	'widget'	=> 'date',
//	'wConf'		=> array(
//		'negation'	=> 'datetime'
//	)
//);
	// Company has task(s) created from/until dynamic
Todoyu::$CONFIG['FILTERS']['COMPANY']['widgets']['dateCreateTaskDynamic'] = array(
	'funcRef'	=> 'TodoyuProjectCompanyFilter::Filter_dateCreateTaskDynamic',
	'label'		=> 'project.filter.company.dateCreateTaskDynamic',
	'optgroup'	=> 'project.filter.optgroup.projects',
	'widget'	=> 'select',
	'wConf'		=> array(
		'FuncRef'	=> 'TodoyuProjectCompanyFilterDataSource::getDynamicDateOptions',
		'negation'	=> 'datetimeDyn'
	)
);

	// Company has / has not projects in project filter set
Todoyu::$CONFIG['FILTERS']['COMPANY']['widgets']['projectfilter'] = array(
	'funcRef'	=> 'TodoyuProjectCompanyFilter::Filter_projectFilter',
	'label'		=> 'project.filter.company.projectfilter',
	'optgroup'	=> 'project.filter.optgroup.projects',
	'widget'	=> 'select',
	'wConf'		=> array(
		'multiple'	=> true,
		'size'		=> 5,
		'FuncRef'	=> 'TodoyuProjectCompanyFilterDataSource::getProjectFilterSetSelectionOptions',
		'negation'	=> 'default'
	)
);

?>