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
 * Timetracking related person filters
 *
 * @package		Todoyu
 * @subpackage	Timetracking
 * @see			TodoyuTimetrackingManager::hookLoadContactFilterConfig
 */


	// Persons tracked time in project
Todoyu::$CONFIG['FILTERS']['PERSON']['widgets']['trackedinproject'] = array(
	'funcRef'	=> 'TodoyuTimetrackingPersonFilter::Filter_trackedinproject',
	'label'		=> 'timetracking.filter.person.trackedinproject',
	'optgroup'	=> 'timetracking.filter.optgroup',
	'widget'	=> 'text',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuProjectProjectFilterDataSource::autocompleteProjects',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
		'negation'		=> false
	)
);

	// Persons tracked time in project of customer company
Todoyu::$CONFIG['FILTERS']['PERSON']['widgets']['trackedforcustomer'] = array(
	'funcRef'	=> 'TodoyuTimetrackingPersonFilter::Filter_trackedforcustomer',
	'label'		=> 'timetracking.filter.person.trackedforcustomer',
	'optgroup'	=> 'timetracking.filter.optgroup',
	'widget'	=> 'text',
	'wConf' => array(
		'autocomplete'	=> true,
		'FuncRef'		=> 'TodoyuContactCompanyFilterDataSource::autocompleteCompanies',
		'FuncParams'	=> array(),
		'LabelFuncRef'	=> 'TodoyuProjectProjectFilterDataSource::getLabel',
		'negation'		=> false
	)
);

?>