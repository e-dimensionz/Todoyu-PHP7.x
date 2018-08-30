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
 * Dynamic form based on XML definition
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuForm implements ArrayAccess {

	/**
	 * Path to the XML file
	 *
	 * @var	String
	 */
	protected $xmlFile;



	/**
	 * Fieldsets in the form which can contain fields
	 *
	 * @var	TodoyuFormFieldset[]
	 */
	protected $fieldsets = array();



	/**
	 * Hidden fields in the form
	 *
	 * @var	Array
	 */
	protected $hiddenFields = array();



	/**
	 * Attributes of the form
	 *
	 * @var	Array
	 */
	protected $attributes = array();



	/**
	 * References to the field elements
	 *
	 * @var	Array
	 */
	protected $fields = array();



	/**
	 * References to the fieldsets
	 *
	 * @var	Array
	 */
	protected $fieldsetRegister = array();



	/**
	 * Formdata
	 *
	 * @var	Array
	 */
	protected $formdata = array();



	/**
	 * Record ID
	 *
	 * @var	Integer
	 */
	protected $idRecord = null;


	/**
	 * Use record?
	 *
	 * @var	Boolean
	 */
	protected $useRecordID = true;


	/**
	 * array of fields with error messages
	 *
	 * @var array
	 */
	protected $invalidFields = array();


	/**
	 * Global vars
	 *
	 * @var	Array
	 */
	protected $vars	= array();


	/**
	 * Boolean for validation
	 *
	 * @var Boolean
	 */
	protected $validateSubforms = false;

	/**
	 * @var	Array		Form params
	 */
	protected $params = array();




	/**
	 * Initialize form by parsing the XML file to load elements
	 *
	 * @param	String			$xmlFile		Path to XML form definition
	 * @param	Integer|String	$idRecord
	 * @param	Array			$params			Form parameters
	 */
	public function __construct($xmlFile, $idRecord = 0, array $params = array()) {
		$this->xmlFile	= TodoyuFileManager::pathAbsolute($xmlFile);
		$this->idRecord	= $idRecord;
		$this->params	= $params;

			// Load all available form configuration
		TodoyuExtensions::loadAllForm();

			// Parse the XML file into this form
		TodoyuFormXmlParser::parse($this, $this->xmlFile);
	}



	/**
	 * Get a fieldset by name
	 *
	 * @param	String		$name
	 * @return	TodoyuFormFieldset
	 */
	public function __get($name) {
		return $this->getFieldset($name);
	}



	/**
	 * Set a new fieldset. You should use addFieldset()
	 *
	 * @see		addFieldset
	 * @param	String		$name
	 * @param	TodoyuFormFieldset	$fieldset
	 */
	public function __set($name, TodoyuFormFieldset $fieldset) {
		$this->fieldsets[$name] = $fieldset;
	}



	/**
	 * Check if a fieldset is set
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function __isset($name) {
		return $this->hasFieldset($name);
	}



	/**
	 * Remove a fieldset
	 *
	 * @param	String		$name
	 */
	public function __unset($name) {
		$this->removeFieldset($name);
	}



	/**
	 * Set form data with default values for form fields
	 *
	 * @param	Array		$formData
	 * @return	TodoyuForm
	 */
	public function setFormData(array $formData = array()) {
		$this->formdata = $formData;

		$this->updateFieldValues();

		return $this;
	}



	/**
	 * Get form params
	 *
	 * @return	Array
	 */
	public function getParams() {
		return $this->params;
	}



	/**
	 * Get param
	 *
	 * @param	String		$name
	 * @param	Boolean		$asInt
	 * @return	Mixed
	 */
	public function getParam($name, $asInt = false) {
		if( $asInt ) {
			return intval($this->params[$name]);
		} else {
			return $this->params[$name];
		}
	}



	/**
	 * Clear form values
	 *
	 */
	public function clear() {
		foreach($this->fields as $name => $field ) {
			$this->formdata[$name] = '';
		}

		$this->updateFieldValues();
	}



	/**
	 * Add extra form data without replacing the current one (overrides existing keys)
	 *
	 * @param	Array		$data
	 */
	public function addFormData(array $data) {
		$this->formdata	= array_merge($this->formdata, $data);

		$this->updateFieldValues();
	}



	/**
	 * Get stored form data
	 *
	 * @return	Array
	 */
	public function getFormData() {
		return $this->formdata;
	}



	/**
	 * Get form data of a field
	 *
	 * @param	String		$fieldName
	 * @return	Mixed
	 */
	public function getFieldData($fieldName) {
		return $this->formdata[$fieldName];
	}



	/**
	 * Update form data for a field
	 *
	 * @param	String		$fieldName
	 * @param	Mixed		$value
	 */
	public function setFieldFormData($fieldName, $value) {
		$this->formdata[$fieldName] = $value;
	}



	/**
	 * Set ID of current record (0 for new elements)
	 *
	 * @param	String		$idRecord
	 */
	public function setRecordID($idRecord) {
		$this->idRecord = $idRecord;
	}



	/**
	 * Get record ID. If record ID is available, use it, if not, try to find
	 * it in the data array under the id-key. Else return 0
	 * In special cases, the ID can also be a string (ex: 23-4)
	 *
	 * @return	String
	 */
	public function getRecordID() {
		if( ! is_null($this->idRecord) ) {
			return $this->idRecord;
		} elseif( isset($this->formdata['id']) ) {
			return $this->formdata['id'];
		} else {
			return 0;
		}
	}



	/**
	 * Get custom form vars which can be used for rendering
	 *
	 * @param	Array		$vars
	 */
	public function setVars(array $vars) {
		$this->vars = $vars;
	}



	/**
	 * Get custom set form var
	 *
	 * @param	String		$varName
	 * @return	Mixed
	 */
	public function getVar($varName) {
		return $this->vars[$varName];
	}



	/**
	 * Check whether a parent form exists
	 *
	 * @return	Boolean
	 */
	public function hasParentForm() {
		return $this->getVar('parentForm') instanceof TodoyuForm;
	}



	/**
	 * Get parent form
	 *
	 * @return	TodoyuForm
	 */
	public function getParentForm() {
		return $this->getVar('parentForm');
	}



	/**
	 * Get field from parent form
	 * 
	 * @param	String		$fieldName
	 * @return	TodoyuFormElement|Boolean
	 */
	public function getFieldFromParentForm($fieldName) {
		if( $this->hasParentForm() ) {
			return $this->getParentForm()->getField($fieldName);
		} else {
			return false;
		}
	}



	/**
	 * Update field values from form data
	 */
	protected function updateFieldValues() {
			// Update fields
		foreach($this->fields as $name => $field ) {
			/**
			 * @var	$field	TodoyuFormElement
			 */
			if( ! is_null($this->formdata[$name]) ) {
				$field->setValue($this->formdata[$name], false);
			}
		}

			// Update hidden fields
		$this->updateHiddenFieldValues();
	}



	/**
	 * Set values of hidden fields if set in form data
	 *
	 */
	protected function updateHiddenFieldValues() {
		foreach( $this->hiddenFields as $name => $value ) {
			if( !is_null($this->formdata[$name]) ) {
				$this->hiddenFields[$name]['value'] = $this->formdata[$name];
			}
		}
	}



	/**
	 * validates given fields
	 *
	 * @param	TodoyuFormElement	$field
	 */
	protected function validateField($field) {
		$this->checkRequiredFields($field);
	}



	/**
	 * checks if field is required
	 *	checks if field is empty
	 *
	 * @param	TodoyuFormElement	$field
	 */
	protected function checkRequiredFields($field) {
		if( $field->isRequired() ) {
			if( ! TodoyuValidator::isNotEmpty($this->formdata[$field->getName()]) ) {
				$this->invalidFields[$field->getName()] = true;
				$field->setAttribute('errorLabel', 'core.form.field.isrequired');
				$field->setAttribute('hasError', true);
			}
		}
	}



	/**
	 * Bubble error
	 * Report a field error to its parent
	 * Ignore, because form is already the most top element
	 *
	 * @param	TodoyuFormElement	$field
	 */
	public function bubbleError(TodoyuFormElement $field) {
//		TodoyuDebug::printInFirebug($field->getName(), 'Error bubbled');
	}



	/**
	 * Get a fieldset (reference) in the form by name
	 *
	 * @param	String		$name
	 * @return	TodoyuFormFieldset
	 */
	public function getFieldset($name) {
		return $this->fieldsetRegister[$name];
	}



	/**
	 * Add a new fieldset and return a reference to it
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Integer				$position
	 * @return	TodoyuFormFieldset
	 */
	public function addFieldset($name, TodoyuFormFieldset $fieldset = null, $position = null) {
		if( is_null($fieldset) ) {
			$fieldset	= new TodoyuFormFieldset($this, $name);
		} else {
			$fieldset	= clone $fieldset;
				// Set form parent to this form and register fields in the form
			$fieldset->setParent($this);
			$fieldset->setFieldsToForm($this);
		}

			// If no position given, append element
		if( is_null($position) ) {
			$this->fieldsets[$name] = $fieldset;
		} else {
				// If position available, insert element at given position
			list($insertMode, $insertRef) = explode(':', $position, 2);

			$this->fieldsets = TodoyuArray::insertElement($this->fieldsets, $name, $fieldset, $insertMode, $insertRef);
		}

			// Register fieldset
		$this->registerFieldset($name, $fieldset);

		return $fieldset;
	}



	/**
	 * Inject an existing fieldset into the form
	 *
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Integer			$position
	 * @return	TodoyuFormFieldset
	 */
	public function injectFieldset(TodoyuFormFieldset $fieldset, $position = null) {
		$fieldset->setFieldsToForm($this);

		$fieldset->setParent($this);

		return $this->addFieldset($fieldset->getName(), $fieldset, $position);
	}



	/**
	 * Add all elements of a form to this form
	 *
	 * @param	String		$xmlPath		Path to sub form XML file
	 * @param	Integer		$position		Insert position
	 */
	public function addElementsFromXML($xmlPath, $position = null) {
		$xmlPath	= TodoyuFileManager::pathAbsolute($xmlPath);
		$form		= TodoyuFormManager::getForm($xmlPath);

			// Get fieldSets of the other form
		$fieldSets	= $form->getFieldsets();

			// Add all fieldSets to this form
		foreach($fieldSets as $fieldSet) {
			$this->injectFieldset($fieldSet, $position);
				// Insert all following fieldSets after the current
			$position = 'after:' . $fieldSet->getName();
		}
	}



	/**
	 * Add elements from another XML into the form after the element named $name
	 *
	 * @see		$this->addElementsFromXML()
	 * @param	String		$xmlPath		Path to the xml file
	 * @param	String		$fieldName		Name of the field to insert the elements after
	 */
	public function addElementsFromXMLAfter($xmlPath, $fieldName) {
		$this->addElementsFromXML($xmlPath, 'after:' . $fieldName);
	}



	/**
	 * Add elements from another XML into the form before the element named $name
	 *
	 * @see		$this->addElementsFromXML()
	 * @param	String		$xmlPath		Path to the xml file
	 * @param	String		$fieldName		Name of the field to insert the elements before
	 */
	public function addElementsFromXMLBefore($xmlPath, $fieldName) {
		$this->addElementsFromXML($xmlPath, 'before:' . $fieldName);
	}



	/**
	 * Register fieldset
	 *
	 * @param	String			$name
	 * @param	TodoyuFormFieldset	$fieldset
	 */
	public function registerFieldset($name, $fieldset) {
		$this->fieldsetRegister[$name] = $fieldset;
	}



	/**
	 * Check whether a fieldset exists
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function hasFieldset($name) {
		return isset($this->fieldsets[$name]);
	}



	/**
	 * Get all names of the root fieldsets
	 *
	 * @return	Array
	 */
	public function getFieldsetNames() {
		return array_keys($this->fieldsets);
	}



	/**
	 * Get form fieldsets in root level
	 *
	 * @return	Array
	 */
	public function getFieldsets() {
		return $this->fieldsets;
	}



	/**
	 * Get validateForm property
	 *
	 * @todo	REMOVE
	 * @return	Boolean
	 */
	public function isSubformValidationActive() {
		return $this->validateSubforms;
	}



	/**
	 * Remove a fieldset
	 *
	 * @param	String		$name
	 */
	public function removeFieldset($name) {
		unset($this->fieldsets[$name]);
	}



	/**
	 * Check if an attribute is set
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function offsetExists($name) {
		return $this->hasAttribute($name);
	}



	/**
	 * Remove an attribute
	 *
	 * @param	String		$name
	 */
	public function offsetUnset($name) {
		$this->removeAttribute($name);
	}



	/**
	 * Set an attribute
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public function offsetSet($name, $value) {
		$this->setAttribute($name, $value);
	}



	/**
	 * Get an attribute
	 *
	 * @param	String		$name
	 * @return	String
	 */
	public function offsetGet($name) {
		return $this->getAttribute($name);
	}



	/**
	 * Set a hidden field
	 *
	 * @param	String		$name			Name of the field
	 * @param	String		$value			Value of the field
	 * @param	Boolean		$noStorage		Don't save file in database
	 * @param	Boolean		$noWrap			Don't wrap field name with form name
	 */
	public function addHiddenField($name, $value = '', $noStorage = false, $noWrap = false) {
		$this->hiddenFields[$name] = array(
			'value'		=> $value,
			'noStorage' => $noStorage || $noWrap,
			'noWrap'	=> $noWrap
		);
	}



	/**
	 * Get a hidden field value
	 *
	 * @param	String		$name
	 * @return	String
	 */
	public function getHiddenField($name) {
		return $this->hiddenFields[$name]['value'];
	}



	/**
	 * Get the array of hidden fields
	 *
	 * @param	Boolean	$onlyStorage		Only get storage fields
	 * @return	Array
	 */
	public function getHiddenFields($onlyStorage = false) {
		$data	= array();

		foreach($this->hiddenFields as $name => $config) {
			if( !$onlyStorage || !$config['noStorage'] ) {
				$data[$name] = $config['value'];
			}
		}

		return $data;
	}



	/**
	 * Check if a hidden field exists
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function hasHiddenField($name) {
		return isset($this->hiddenFields[$name]);
	}



	/**
	 * Remove a hidden field
	 *
	 * @param	String		$name
	 */
	public function removeHiddenField($name) {
		unset($this->hiddenFields[$name]);
	}



	/**
	 * Set an attribute
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	}



	/**
	 * Set attribute only of not already set
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function setAttributeIfNotSet($name, $value) {
		if( !$this->hasAttribute($name) ) {
			$this->setAttribute($name, $value);
		}
	}



	/**
	 * Get an attribute
	 *
	 * @param	String		$name
	 * @return	String
	 */
	public function getAttribute($name) {
		return $this->attributes[$name];
	}



	/**
	 * Check if an attribute exists
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function hasAttribute($name) {
		return isset($this->attributes[$name]);
	}



	/**
	 * Remove an attribute
	 *
	 * @param	String		$name
	 */
	public function removeAttribute($name) {
		unset($this->attributes[$name]);
	}



	/**
	 * Set action attribute
	 *
	 * @param	String		$action
	 */
	public function setAction($action) {
		$this->setAttribute('action', $action);
	}



	/**
	 * Set action url
	 *
	 * @param	String		$ext
	 * @param	String		$controller
	 * @param	String		$action
	 */
	public function setActionUrl($ext, $controller, $action = '') {
		$url = TodoyuString::buildUrl(array(
			'ext'		=> $ext,
			'controller'	=> $controller,
			'action'		=> $action
		));

		$this->setAction($url);
	}



	/**
	 * Set method attribute
	 *
	 * @param	String		$method
	 */
	public function setMethod($method) {
		$this->setAttribute('method', $method);
	}



	/**
	 * Set target attribute
	 *
	 * @param	String		$target
	 */
	public function setTarget($target) {
		$this->setAttribute('target', $target);
	}



	/**
	 * Set content encoding type
	 *
	 * @param	String		$encType
	 */
	public function setEnctype($encType) {
		$this->setAttribute('enctype', $encType);
	}



	/**
	 * Set the form name.
	 * Used by the XML-parser to set the form name
	 * You can change the form name here
	 *
	 * @param	String		$name
	 */
	public function setName($name) {
		$this->setAttribute('name', $name);
	}



	/**
	 * Get form name
	 *
	 * @return unknown
	 */
	public function getName() {
		return $this->getAttribute('name');
	}



	/**
	 * Set the flag if a record ID is used for IDs or not
	 *
	 * @param	Boolean		$use
	 */
	public function setUseRecordID($use = true) {
		$this->useRecordID = $use;
	}



	/**
	 * Dummy functions allow all child elements to access this form instance
	 *
	 * @return	TodoyuForm
	 */
	public function getForm() {
		return $this;
	}



	/**
	 * Check whether field with given name exist in the form
	 *
	 * @param	String	$fieldName
	 * @return	Boolean
	 */
	public function hasField($fieldName) {
		$fieldNames	= $this->getFieldnames();

		return in_array($fieldName, $fieldNames);
	}



	/**
	 * Get a field object by name. It doesn't matter where
	 * the field is located in the form
	 *
	 * @param	String			$name
	 * @return	TodoyuFormElement|Boolean
	 */
	public function getField($name) {
		if( $this->hasField($name) ) {
			return $this->fields[$name];
		} else {
			TodoyuLogger::logError('Tried to access a field which is no available in the form: ' . $name);
			return false;
		}
	}



	/**
	 * Remove a field from the form
	 *
	 * @param	String		$name			Field name
	 * @param	Boolean		$cleanup
	 */
	public function removeField($name, $cleanup = false) {
		if( $cleanup ) {
			if( $this->hasField($name) ) {
				$this->getField($name)->remove();
			}
		}

		unset($this->fields[$name]);
		unset($this->formdata[$name]);
	}



	/**
	 * Get the value of a field. This only works if the data has
	 * been set with setFormData() before!
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	public function getFieldValue($name) {
		if( $this->hasField($name) ) {
			return $this->getField($name)->getValue();
		} else {
			return false;
		}
	}



	/**
	 * Get all field names in the form
	 *
	 * @return	Array
	 */
	public function getFieldnames() {
		return array_keys($this->fields);
	}



	/**
	 * Register a field to be quickly accessable over getField()
	 *
	 * @param	TodoyuFormElement		$field
	 */
	public function registerField(TodoyuFormElement $field) {
		$this->fields[$field->getName()] = $field;
	}



	/**
	 * Make a HTML valid ID (without spaces and underscores)
	 *
	 * @param	String		$name		Fieldname to create an ID from
	 * @param	String		$type		Element type if not for a field
	 * @return	String
	 */
	public function makeID($name = '', $type = 'field') {
		$notAllowed	= array(' ', '_', '[', ']', '--');
		$replace	= '-';
		$elementName= trim(str_replace($notAllowed, $replace, $name));

		$id		= $this->getName();

		if( $this->useRecordID ) {
			$id .= '-' . $this->getRecordID();
		}

		$id .= '-' . $type;

		if( $elementName !== '' ) {
			$id .= '-' . $elementName;
		}

		$count = 1;

		while( $count != 0 ) {
			$id = str_replace($notAllowed, $replace, $id, $count);
		}

		return $id;
	}



	/**
	 * Make a HTML valid name. Prefixed by formname
	 *
	 * @param	String		$name
	 * @param	Boolean		$multiple
	 * @return	String
	 */
	public function makeName($name, $multiple = false) {
		return $this->getName() . '[' . $name . ']' . ($multiple?'[]':'');
	}



	/**
	 * Add js callback for form display
	 *
	 * @param	String		$jsCallback
	 */
	public function addOnDisplayJsCallback($jsCallback) {
		$this->setAttribute('onDisplayJsCallback', $jsCallback);
	}



	/**
	 * Check forms field values being valid
	 *
	 * @return	Boolean
	 */
	public function isValid() {
		$isValid	= true;

		$this->validateSubforms = true;

		$fieldNames	= $this->getFieldnames();

		foreach($fieldNames as $fieldName) {
			if( ! $this->getField($fieldName)->isValid() ) {
				$isValid = false;
				TodoyuDebug::printInFirebug($fieldName, 'Invalid form field');
			}
		}

		return $isValid;
	}



	/**
	 * Parse a string. Replace all element with match to the following pattern:
	 * #FIELDNAME# with the data from $this->formdata
	 *
	 * @param	String		$string
	 * @return	String
	 */
	public function parseWithFormData($string) {
		foreach($this->formdata as $key => $value) {
			if( is_string($value) || is_numeric($value) ) {
				if( stristr($string, '#' . $key . '#') ) {
					$string = str_replace('#' . $key . '#', $value, $string);
				}
			}
		}

		return $string;
	}



	/**
	 * Get data of all fields to store in the database
	 *
	 * @param	Array	$formData
	 * @return	Array
	 */
	public function getStorageData(array $formData = null) {
		if( ! is_null($formData) ) {
			$this->setFormData($formData);
		}

		$data	= $this->getHiddenFields(true);

		foreach($this->fields as $name => $field) {
			/**
			 * @var	TodoyuFormElement	$field
			 */
			$value	= $field->getStorageData();

			if( $value !== false ) {
				$data[$name] = $value;
			}
		}

		return $data;
	}



	/**
	 * Render hidden fields to HTML code
	 *
	 * @return	String
	 */
	public function renderHiddenFields() {
		$content	= '';
		$template	= Todoyu::$CONFIG['FORM']['templates']['hidden'];

		foreach( $this->hiddenFields as $name => $config ) {
			$data	= array(
				'htmlId'	=> $this->makeID($name),
				'htmlName'	=> $config['noWrap'] ? $name : $this->makeName($name),
				'value'		=> htmlspecialchars($config['value'], ENT_QUOTES, 'UTF-8')
			);

			$content .= Todoyu::render($template, $data);
		}

		return $content;
	}



	/**
	 * Render all fieldsets and their children to HTML code
	 *
	 * @return	String
	 */
	protected function renderFieldsets() {
		$content = '';

		foreach($this->fieldsets as $fieldset) {
			$content .= $fieldset->render();
		}

		return $content;
	}



	/**
	 * Get form data
	 *
	 * @return	Array
	 */
	protected function getData() {
		$this->setDefaultAttributes();
		$this->updateFieldValues();

		$data	= $this->getParsedAttributes();

		$data['hiddenFields']	= $this->renderHiddenFields();
		$data['fieldsets']		= $this->renderFieldsets();
		$data['htmlId']			= $this->makeID('', 'form');
		$data['isAjax']			= TodoyuRequest::isAjaxRequest();
		$data['id']				= $this->getRecordID();

		return $data;
	}



	/**
	 * Get parsed form data
	 *
	 * @return	Array
	 */
	protected function getParsedAttributes() {
		$parsedAttributes = array();
		
		foreach($this->attributes as $attrName => $attrValue) {
			$parsedAttributes[$attrName] = $this->parseWithFormData($attrValue);
		}

		return $parsedAttributes;
	}



	/**
	 * Set default attributes
	 *
	 *
	 */
	protected function setDefaultAttributes() {
		$this->setAttributeIfNotSet('action', TodoyuRequest::getRequestUrl());
		$this->setAttributeIfNotSet('method', 'post');

		if( $this->hasAttribute('onsubmit') ) {
			$this->setAttribute('onsubmit', $this->parseWithFormData($this->getAttribute('onsubmit')));
		}
	}



	/**
	 * Remove all fieldsets which contain no elements
	 */
	protected function removeEmptyFieldsetsBeforeRendering() {
		$allFieldsetNames		= $this->getFieldsetNames();
		$activeFieldsetNames	= array();

		foreach($this->getFieldnames() as $fieldName) {
			$activeFieldsetNames[] = $this->getField($fieldName)->getParentFieldsetNames();
		}

		$activeFieldsetNames	= TodoyuArray::flatten($activeFieldsetNames);
		$activeFieldsetNames	= array_unique($activeFieldsetNames);

		$emptyFieldsetNames		= array_diff($allFieldsetNames, $activeFieldsetNames);

		foreach($emptyFieldsetNames as $emptyFieldsetName) {
			$this->getFieldset($emptyFieldsetName)->remove();
		}
	}



	/**
	 * Render only a fieldset without other fieldsets and form stuff
	 *
	 * @param	String		$fieldsetName
	 * @return	String
	 */
	public function renderFieldset($fieldsetName) {
		return $this->getFieldset($fieldsetName)->render();
	}



	/**
	 * Render the form to HTML code
	 *
	 * @return	String
	 */
	public function render() {
		$this->removeEmptyFieldsetsBeforeRendering();

		$tmpl		= Todoyu::$CONFIG['FORM']['templates']['form'];
		$formData	= $this->getData();

			// Call render hooks
		$xmlPath	= TodoyuFileManager::pathWeb($this->xmlFile);
		$formData	= TodoyuFormHook::callRenderForm($xmlPath, $formData);

		return Todoyu::render($tmpl, $formData);
	}



	/**
	 * Render form without form tag
	 *
	 * @return	String
	 */
	public function renderContent() {
		$this->setAttribute('noFormTag', true);

		return $this->render();
	}
}

?>