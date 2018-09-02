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
 * Form element for database relations (1:n n:n)
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_DatabaseRelation extends TodoyuFormElement {

	/**
	 * Constructor of the class
	 *
	 * @param	String			$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array			$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config  = array()) {
		parent::__construct('databaseRelation', $name, $fieldset, $config);
	}



	/**
	 * Initialize database relation field
	 */
	protected function init() {
			// Add a validator which checks the sub records
		$this->config['validate']['validateSubRecords'] = array();
	}



	/**
	 * Set value
	 *
	 * @param	Array		$value
	 */
	public function setValue($value, $updateForm = true) {
		$records	= TodoyuArray::assure($value);

		parent::setValue($records);
	}



	/**
	 * Get value (records array)
	 *
	 * @return	Array
	 */
	public function getValue() {
		return TodoyuArray::assure($this->config['value']);
	}



	/**
	 * Get record data
	 *
	 * @param	Integer		$index
	 * @return	Array
	 */
	public function getRecord($index) {
		return TodoyuArray::assure($this->config['value'][$index]);
	}



	/**
	 * Get all indexes if the records
	 * This are not the IDs, this are position indexes for editing
	 *
	 * @return	Array
	 */
	public function getRecordIndexes() {
		return is_array($this->config['value']) ? array_keys($this->config['value']) : array();
	}



	/**
	 * Get field data for rendering
	 *
	 * @return Array
	 */
	public function getData() {
		$data = parent::getData();

			// Records template data
		$data['records']	= $this->getRecordsTemplateData();

			// Records general information
		$data['fieldname']	= $this->getName();
		$data['formname']	= $this->getForm()->getName();
		$data['idRecord']	= $this->getForm()->getRecordID();

		return $data;
	}



	/**
	 * Render the field, including registered rendering hooks
	 *
	 * @param	Boolean	$odd
	 * @return	String
	 */
	public function render($odd = false) {
		return parent::render($odd);
	}



	/**
	 * Render new record without data
	 *
	 * @param	Integer		$index
	 * @return	String
	 */
	public function renderNewRecord($index = 0) {
		$tmpl	= 'core/view/form/FormElement_DatabaseRelation_Record.tmpl';
		$data	= array();

			// Get record data
		$data['record']		= $this->getRecordTemplateData($index);

			// Records general information
		$data['fieldname']	= $this->getName();
		$data['idRecord']	= $this->getForm()->getRecordID();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render foreign record form
	 *
	 * @param	Integer		$index
	 * @return	String
	 */
	protected function renderRecordForm($index) {
		$recordForm	= $this->getRecordForm($index);

			// Evoke assigned validators
		if( $this->getForm()->isSubformValidationActive() ) {
			$recordForm->isValid();
		}

			// Render
		return $recordForm->render();
	}



	/**
	 * Get form object for a record at a specific index
	 *
	 * @param	Integer		$index
	 * @return	TodoyuForm
	 */
	public function getRecordForm($index) {
		$xmlPath = $this->getRecordsFormXml();

			// Load form data
		$recordData	= $this->getRecord($index);
		$idRecord	= (int) $recordData['id'];

			// Load record data from hooks
		$recordData	= TodoyuFormHook::callLoadData($xmlPath, $recordData, $idRecord);

			// Construct form object
		$formParams	= array(
			'field'	=> $this,
			'data'	=> $recordData
		);
		$recordForm	= TodoyuFormManager::getForm($xmlPath, $idRecord, $formParams);

			// Set form data
		$recordForm->setFormData($recordData);
		$recordForm->setVars(array(
			'parent'		=> $this->getForm()->getRecordID(), // @todo remove when nowhere else used anymore
			'parentRecordID'=> $this->getForm()->getRecordID(),
			'parentForm'	=> $this->getForm()
		));
		$recordForm->setUseRecordID(false);
		$recordForm->setRecordID($idRecord);
		$recordForm->setAttribute('noFormTag', true);

		$formName	= $this->getForm()->getName() . '[' . $this->getName() . '][' . $index . ']';
		$recordForm->setName($formName);

		return $recordForm;
	}



	/**
	 * Get configuration array for foreign records
	 *
	 * @return	Array
	 */
	protected function getRecordsConfig() {
		return $this->getAttribute('record');
	}



	/**
	 * Load foreign record from base record
	 *
	 * @return	Array
	 */
	protected function getRecords() {
		return TodoyuArray::assure($this->getValue());
	}



	/**
	 * Get template data for all records
	 *
	 * @return	Array
	 */
	protected function getRecordsTemplateData() {
		$records	= $this->getRecords();

		foreach($records as $index => $record) {
			$records[$index] = $this->getRecordTemplateData($index);
		}

		return $records;
	}



	/**
	 * Get path to record form xml
	 *
	 * @return	String
	 */
	protected function getRecordsFormXml() {
		$recordConfig = $this->getRecordsConfig();

		return $recordConfig['form'];
	}



	/**
	 * Get record label defined by config
	 *
	 * @param	Integer		$index
	 * @return	String
	 */
	protected function getRecordLabel($index) {
		$config	= $this->getRecordsConfig();
		$record	= $this->getRecord($index);
		$label	= '';
		$type	= $config['label']['@attributes']['type'];

			// Get label by type
		switch( $type ) {
			case 'function':
				$function	= $config['label']['function'];

				if( TodoyuFunction::isFunctionReference($function) ) {
					$funcLabel	= TodoyuFunction::callUserFunction($function, $this, $record);
					if( $funcLabel !== false ) {
						$label = $funcLabel;
					}
				}
				break;

			case 'field':
				$field	= $config['label']['field'];
				$label	= trim($record[$field]);
				break;
		}

			// If no label found, check if there is a noLabel tag
		if( empty($label) && ! empty($config['label']['noLabel']) ) {
			$label	= $config['label']['noLabel'];
		}

			// If still no label found, get default "no label" tag
		if( empty($label) ) {
			$label = 'core.form.databaserelation.nolabel';
		}

		return Todoyu::Label($label);
	}



	/**
	 * Get record with template data
	 *
	 * @param	Integer		$index
	 * @return	Array
	 */
	protected function getRecordTemplateData($index) {
		$record	= $this->getRecord($index);

		$record['_index']	= $index;
		$record['_label']	= $this->getRecordLabel($index);
		$record['_formHTML']= $this->renderRecordForm($index);

		return $record;
	}



	/**
	 * Check if all record forms are valid
	 *
	 * @return	Boolean
	 */
	public function areAllRecordsValid() {
		$indexes	= $this->getRecordIndexes();

		foreach($indexes as $index) {
			$form	= $this->getRecordForm($index);
			$valid	= $form->isValid();

			if( !$valid ) {
				return false;
			}
		}

		return true;
	}



	/**
	 * Validate required option
	 * If the field has no validators, but is required, we have to perfom an "required" check
	 * Because a databaseRelation can contain any kind of data, a custom validator function is required.
	 * The function has to be referenced in record->validateRequired in the xml
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		$customValidator	= $this->config['record']['validateRequired'];

		if( TodoyuFunction::isFunctionReference($customValidator) ) {
			$records	= $this->getRecords();
			$valid		= true;

			foreach($records as $record) {
				if( ! TodoyuFunction::callUserFunction($customValidator, $this, $record) ) {
					$valid = false;
					break;
				}
			}
		} else {
			$records	= $this->getRecords();
			$valid		= false;

			foreach($records as $record) {
				if( $record['id'] !== '0' ) {
					$valid = true;
					break;
				}
			}
		}

		return $valid;
	}



	/**
	 * Get amount of sub records
	 *
	 * @return	Integer
	 */
	public function getRecordsAmount() {
		return sizeof($this->config['value']);
	}



	/**
	 * Get storage data of the sub records
	 *
	 * @return	Array
	 */
	protected function getStorageDataInternal() {
		$indexes	= $this->getRecordIndexes();
		$storageData= array();

		foreach($indexes as $index) {
			$storageData[$index] = $this->getRecordForm($index)->getStorageData();
		}

		return $storageData;
	}

}

?>