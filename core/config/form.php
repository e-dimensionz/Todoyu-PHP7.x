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
 * Core config for forms: locales, templates
 *
 * @package		Todoyu
 * @subpackage	Core
 */



	/**
	 * Add basic form type configuration
	 * - template
	 * - class
	 */

Todoyu::$CONFIG['FORM']['templates'] = array(
	'form'		=> 'core/view/form/Form.tmpl',
	'fieldset'	=> 'core/view/form/Fieldset.tmpl',
	'formelement'=>'core/view/form/FormElement.tmpl',
	'hidden'	=> 'core/view/form/HiddenField.tmpl'
);

	// Text
TodoyuFormManager::addFieldType('text', 'TodoyuFormElement_Text', 'core/view/form/FormElement_Text.tmpl');
	// Select
TodoyuFormManager::addFieldType('select', 'TodoyuFormElement_Select', 'core/view/form/FormElement_Select.tmpl');
	// Select grouped
TodoyuFormManager::addFieldType('selectgrouped', 'TodoyuFormElement_SelectGrouped', 'core/view/form/FormElement_SelectGrouped.tmpl');
	// Radio
TodoyuFormManager::addFieldType('radio', 'TodoyuFormElement_Radio', 'core/view/form/FormElement_Radio.tmpl');
	// Text area
TodoyuFormManager::addFieldType('textarea', 'TodoyuFormElement_Textarea', 'core/view/form/FormElement_Textarea.tmpl');
	// Checkbox
TodoyuFormManager::addFieldType('checkbox', 'TodoyuFormElement_Checkbox', 'core/view/form/FormElement_Checkbox.tmpl');
	// Date
TodoyuFormManager::addFieldType('date', 'TodoyuFormElement_Date', 'core/view/form/FormElement_Date.tmpl');
	// Date and time
TodoyuFormManager::addFieldType('datetime', 'TodoyuFormElement_DateTime', 'core/view/form/FormElement_Date.tmpl');
	// Button
TodoyuFormManager::addFieldType('button', 'TodoyuFormElement_Button', 'core/view/form/FormElement_Button.tmpl');
	// Save button
TodoyuFormManager::addFieldType('saveButton', 'TodoyuFormElement_SaveButton', 'core/view/form/FormElement_Button.tmpl');
	// Cancel button
TodoyuFormManager::addFieldType('cancelButton', 'TodoyuFormElement_CancelButton', 'core/view/form/FormElement_Button.tmpl');
	// Expand all sub records button
TodoyuFormManager::addFieldType('expandAllButton', 'TodoyuFormElement_ExpandAllButton', 'core/view/form/FormElement_Button.tmpl');
	// duration
TodoyuFormManager::addFieldType('duration', 'TodoyuFormElement_Duration', 'core/view/form/FormElement_Duration.tmpl');
	// time
TodoyuFormManager::addFieldType('time', 'TodoyuFormElement_Time', 'core/view/form/FormElement_Duration.tmpl');
	// Rich text editor
TodoyuFormManager::addFieldType('RTE', 'TodoyuFormElement_RTE', 'core/view/form/FormElement_RTE.tmpl');
	// File upload
TodoyuFormManager::addFieldType('upload', 'TodoyuFormElement_Upload', 'core/view/form/FormElement_Upload.tmpl');
		// Text autocompleter
TodoyuFormManager::addFieldType('textAC', 'TodoyuFormElement_TextAC', 'core/view/form/FormElement_TextAC.tmpl');
	// Database relation (sub records)
TodoyuFormManager::addFieldType('databaseRelation', 'TodoyuFormElement_DatabaseRelation', 'core/view/form/FormElement_DatabaseRelation.tmpl');
	// Comment (text only element)
TodoyuFormManager::addFieldType('comment', 'TodoyuFormElement_Comment', 'core/view/form/FormElement_Comment.tmpl');
	// Icon selector
TodoyuFormManager::addFieldType('selectIcon', 'TodoyuFormElement_SelectIcon', 'core/view/form/FormElement_SelectIcon.tmpl');
	// General mail receivers records selector
TodoyuFormRecordsManager::addType('mailReceivers', 'TodoyuFormElement_RecordsMailReceivers', 'TodoyuMailReceiverManager::getMatchingMailReceivers');

?>