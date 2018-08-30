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
 * Steps for the first steps wizard
 */
Todoyu::$CONFIG['EXT']['firststeps']['wizardsteps'] = array(
	array(
		'step'		=> 'start',
		'position'	=> 10,
		'class'		=> 'TodoyuFirstStepsWizardStepStart',
		'title'		=> 'firststeps.ext.wizard.start.title',
		'info'		=> 'firststeps.ext.wizard.start.info',
		'help'		=> 'firststeps.ext.wizard.start.help'
	),
	array(
		'step'		=> 'jobtypes',
		'position'	=> 20,
		'class'		=> 'TodoyuFirstStepsWizardStepJobtypes',
		'title'		=> 'firststeps.ext.wizard.jobtypes.title',
		'info'		=> 'firststeps.ext.wizard.jobtypes.info',
		'help'		=> 'firststeps.ext.wizard.jobtypes.help'

	),
	array(
		'step'		=> 'projectroles',
		'position'	=> 30,
		'class'		=> 'TodoyuFirstStepsWizardStepProjectroles',
		'title'		=> 'firststeps.ext.wizard.projectroles.title',
		'info'		=> 'firststeps.ext.wizard.projectroles.info',
		'help'		=> 'firststeps.ext.wizard.projectroles.help'
	),
	array(
		'step'		=> 'activities',
		'position'	=> 40,
		'class'		=> 'TodoyuFirstStepsWizardStepActivities',
		'title'		=> 'firststeps.ext.wizard.activities.title',
		'info'		=> 'firststeps.ext.wizard.activities.info',
		'help'		=> 'firststeps.ext.wizard.activities.help'
	),
	array(
		'step'		=> 'userroles',
		'position'	=> 50,
		'class'		=> 'TodoyuFirstStepsWizardStepUserroles',
		'title'		=> 'firststeps.ext.wizard.userroles.title',
		'info'		=> 'firststeps.ext.wizard.userroles.info',
		'help'		=> 'firststeps.ext.wizard.userroles.help'
	),
	array(
		'step'		=> 'company',
		'position'	=> 60,
		'class'		=> 'TodoyuFirstStepsWizardStepUserroles',
		'title'		=> 'firststeps.ext.wizard.company.title',
		'info'		=> 'firststeps.ext.wizard.company.info',
		'help'		=> 'firststeps.ext.wizard.company.help'
	),
	array(
		'step'		=> 'employees',
		'position'	=> 70,
		'class'		=> 'TodoyuFirstStepsWizardStepEmployees',
		'title'		=> 'firststeps.ext.wizard.employees.title',
		'info'		=> 'firststeps.ext.wizard.employees.info',
		'help'		=> 'firststeps.ext.wizard.employees.help'
	),
	array(
		'step'		=> 'customers',
		'position'	=> 80,
		'class'		=> 'TodoyuFirstStepsWizardStepCustomers',
		'title'		=> 'firststeps.ext.wizard.customers.title',
		'info'		=> 'firststeps.ext.wizard.customers.info',
		'help'		=> 'firststeps.ext.wizard.customers.help'
	),
	array(
		'step'		=> 'project',
		'position'	=> 90,
		'class'		=> 'TodoyuFirstStepsWizardStepProject',
		'title'		=> 'firststeps.ext.wizard.project.title',
		'info'		=> 'firststeps.ext.wizard.project.info',
		'help'		=> 'firststeps.ext.wizard.project.help'
	),
	array(
		'step'		=> 'finish',
		'position'	=> 100,
		'class'		=> 'TodoyuFirstStepsWizardStepFinish',
		'title'		=> 'firststeps.ext.wizard.finish.title',
		'info'		=> 'firststeps.ext.wizard.finish.info',
		'help'		=> 'firststeps.ext.wizard.finish.help'
	)

);

?>