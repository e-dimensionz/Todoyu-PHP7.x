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
 * Form manager
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuFormManager {

	/**
	 * Create a form object, set record ID and call buildForm hook
	 *
	 * @param	String		$xmlPath
	 * @param	Integer		$idRecord
	 * @param	Array		$params			Optional parameters for the form hooks
	 * @param	Array		$formData		Already available form data
	 * @return	TodoyuForm|Boolean
	 */
	public static function getForm($xmlPath, $idRecord = 0, array $params = array(), array $formData = array()) {
		if( !TodoyuFileManager::isFile($xmlPath) ) {
			TodoyuLogger::logFatal('Failed to create form. No form XML found at <' . $xmlPath . '>');
			return false;
		}

		$form	= new TodoyuForm($xmlPath, $idRecord, $params);

		if( !isset($params['formData']) ) {
			$params['formData'] = $formData;
		}

		$form	= TodoyuFormHook::callBuildForm($xmlPath, $form, $idRecord, $params);

		return $form;
	}



	/**
	 * Render a sub record of a database relation field
	 *
	 * @param	String		$xmlPath
	 * @param	String		$fieldName
	 * @param	String		$formName
	 * @param	Integer		$index
	 * @param	Integer		$idRecord
	 * @param	Array		$data
	 * @return	String
	 */
	public static function renderSubFormRecord($xmlPath, $fieldName, $formName, $index = 0, $idRecord = 0, array $data = array()) {
		$index		= (int) $index;
		$idRecord	= (int) $idRecord;

			// Make form object
		$form	= self::getForm($xmlPath, $idRecord);

			// Load (/preset) form data
		$data	= TodoyuFormHook::callLoadData($xmlPath, $data, $idRecord);

			// Set form data
		$form->setFormData($data);
		$form->setRecordID($idRecord);

		/**
		 * @var	TodoyuFormElement_DatabaseRelation	$field
		 */
		$field			= $form->getField($fieldName);
		$form['name']	= $formName;

		return $field->renderNewRecord($index);
	}



	/**
	 * Add a field type
	 *
	 * @param	String		$name
	 * @param	String		$className
	 * @param	String		$pathTemplate
	 */
	public static function addFieldType($name, $className, $pathTemplate) {
		Todoyu::$CONFIG['FORM']['TYPES'][$name] = array(
			'class'		=> $className,
			'template'	=> $pathTemplate
		);
	}



	/**
	 * Add field type records
	 *
	 * @param	String		$name
	 * @param	String		$className
	 */
	public static function addFieldTypeRecords($name, $className) {
		$pathTemplate	= 'core/view/form/FormElement_Records.tmpl';
		$name			= 'records' . ucfirst($name);

		self::addFieldType($name, $className, $pathTemplate);
	}



	/**
	 * Get class which represents an object of the requested type
	 * A new instance will be created with the NEW operator
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getTypeClass($type) {
		return Todoyu::$CONFIG['FORM']['TYPES'][$type]['class'];
	}



	/**
	 * Get the template for the input type
	 *
	 * @param	String		$type
	 * @return	String
	 */
	public static function getTypeTemplate($type) {
		$template	= Todoyu::$CONFIG['FORM']['TYPES'][$type]['template'];

		if( empty($template) ) {
			TodoyuLogger::logError('Form Manager Error: No form type template found for type: "' . $type . '"');
		}

		return $template;
	}

}

?>