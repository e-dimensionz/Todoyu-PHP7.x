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
 * Parses an XML form structure into a form object
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormXmlParser {

	/**
	 * Form instance to add all elements to
	 *
	 * @var	TodoyuForm
	 */
	private $form;

	/**
	 * File where form structure is defined
	 *
	 * @var	String
	 */
	private $xmlFile;

	/**
	 * XML object to process
	 *
	 * @var	SimpleXMLElement
	 */
	private $xml;



	/**
	 * Parse form definition into a form object
	 *
	 * @param	TodoyuForm		$form
	 * @param	String			$xmlPath
	 * @return	TodoyuForm
	 */
	public static function parse(TodoyuForm $form, $xmlPath) {
		$xmlPath	= TodoyuFileManager::pathAbsolute($xmlPath);
		$parser		= new self($xmlPath);

		$parser->addElementsToForm($form);

		return $form;
	}


	/**
	 * Initialize
	 *
	 * @param	String		$xmlPath
	 */
	private function __construct($xmlPath) {
		$this->xmlFile	= TodoyuFileManager::pathAbsolute($xmlPath);

		if( !is_file($xmlPath) ) {
			TodoyuLogger::logFatal('Form XML file not found (' . $xmlPath . ')');
			return;
		}

			// Load xml file as simple xml object
		$this->xml	= simplexml_load_file($xmlPath, null, LIBXML_NOCDATA);

	}



	/**
	 * All elements of xml structure to form object
	 *
	 * @param	TodoyuForm		$form
	 */
	private function addElementsToForm(TodoyuForm $form) {
		$this->form = $form;

			// Parse form attributes
		$this->parseAttributes();
			// Parse hidden fields
		$this->parseHiddenFields();
			// Parse main fieldsets
		$this->parseTopFieldsets();
	}



	/**
	 * Parse form attributes from xml
	 *
	 */
	private function parseAttributes() {
		if( $this->xml->attributes ) {
			foreach( $this->xml->attributes->attribute as $attribute ) {
				$this->form->setAttribute((string)$attribute['name'], (string)$attribute);
			}
		}
	}



	/**
	 * Parse hidden fields from xml
	 *
	 */
	private function parseHiddenFields() {
		if( $this->xml->hiddenFields ) {
			foreach( $this->xml->hiddenFields->field as $field ) {
				$this->form->addHiddenField((string)$field['name'], (string)$field['value'], ((string)$field['noStorage']==='true'), ((string)$field['noWrap']==='true'));
			}
		}
	}



	/**
	 * Parse fieldsets with their fields from XML
	 *
	 */
	private function parseTopFieldsets() {
		$children	= $this->xml->fieldsets->children();

		if( is_object($children) ) {
			foreach($children as $fieldset) {
				$this->addFieldset($this->form, $fieldset);
			}
		}
	}



	/**
	 * Add a fieldset to the form object or a fieldset from a XML node
	 *
	 * @param	TodoyuFormFieldset			$parentElement
	 * @param	SimpleXmlElement	$fieldsetXmlObj
	 * @return	Boolean
	 */
	private function addFieldset(&$parentElement, SimpleXmlElement $fieldsetXmlObj) {
			// If restricted to internal persons
		if( $fieldsetXmlObj->restrictInternal ) {
			if( ! Todoyu::person()->isAdmin() && ! Todoyu::person()->isInternal() ) {
				return false;
			}
		}

			// If restricted by rights
		if( $fieldsetXmlObj->restrict ) {
			$config	= TodoyuArray::fromSimpleXML($fieldsetXmlObj);

				// Check if fieldset has restrictions and if they match
			if( ! $this->isAllowed($config) ) {
				return false;
			}
		}



		$fieldset = $parentElement->addFieldset((string)$fieldsetXmlObj['name']);

			// Set legend if available
		if( $fieldsetXmlObj->legend ) {
			$fieldset->setLegend((string)$fieldsetXmlObj->legend);
		}

			// Set class if available
		if( $fieldsetXmlObj->class ) {
			$fieldset->addClass((string)$fieldsetXmlObj->class);
		}

			// If fieldset has an "elements" tag, add all elements
		if( $fieldsetXmlObj->elements ) {
			foreach( $fieldsetXmlObj->elements->children() as $nodeName => $element ) {
				switch( $nodeName ) {
					case 'fieldset':
						$this->addFieldset($fieldset, $element);
						break;

					case 'field':
						$this->addField($fieldset, $element);
						break;

					default:
						TodoyuLogger::logError('Unknown field type (not field or fieldset)');
				}
			}
		}

		return true;
	}



	/**
	 * Add a field to a fieldset from an XML node
	 *
	 * @param	TodoyuFormFieldset		$parentFieldset
	 * @param	SimpleXmlElement		$fieldXmlObj
	 * @return	Boolean
	 */
	private function addField(TodoyuFormFieldset $parentFieldset, SimpleXmlElement $fieldXmlObj) {
		$type	= trim($fieldXmlObj['type']);
		$name	= trim($fieldXmlObj['name']);

		$config	= TodoyuArray::fromSimpleXML($fieldXmlObj);

			// Check if field has restrictions and if they match
		if( ! $this->isAllowed($config) ) {
			return false;
		}

		$field	= TodoyuFormFactory::createField($type, $name, $parentFieldset, $config);

		if( $field instanceof TodoyuFormElement ) {
			$parentFieldset->addField($name, $field);
			return true;
		} else {
			TodoyuLogger::logError('Invalid form config? Can\'t create field object for field: "' . $name . '"', $config);
			return false;
		}

	}



	/**
	 * Check if a field has requirements
	 *
	 * @param	Array		$config			Field config
	 * @return	Boolean
	 */
	private function isAllowed(array $config) {
		if( isset($config['restrictAdmin']) ) {
			return TodoyuAuth::isAdmin();
		} elseif( isset($config['restrict']) ) {
			$restrict	= $config['restrict'];
			$and		= strtoupper(trim($restrict['@attributes']['conjunction'])) === 'AND';
			$rights		= TodoyuArray::assure($restrict['allow']);

				// SimpleXML handles the elements different if there is only one.
				// If only one element, pack it into an array, so behaviour is the same
			if( sizeof($rights) === 1 ) {
				$rights = array($rights);
			}

				// Check each right
			foreach($rights as $right) {
					// Check if the ext and right keys are available
				if( isset($right['@attributes']['ext']) && isset($right['@attributes']['right']) ) {
						// Check if right is allowed
					if( Todoyu::allowed($right['@attributes']['ext'], $right['@attributes']['right']) ) {
							// If right allowed and conjunction is OR, field is allowed
						if( ! $and ) {
							return true;
						}
					} else {
							// If right is disallowed and conjunction is AND, field is disallowed
						if( $and ) {
							return false;
						}
					}
				} elseif( isset($right['@attributes']['function']) ) {
						// Use function to decide if allowed
					$function	= $right['@attributes']['function'];

					if( TodoyuFunction::isFunctionReference($function) ) {
						$allowed	= TodoyuFunction::callUserFunction($function, $config);

						if( $allowed && !$and ) {
							return true;
						} elseif( !$allowed && $and ) {
							return false;
						}
					} else {
						TodoyuLogger::logError('FormElement rights function not found <' . $function . '>');
						return true;
					}
				} else {
					TodoyuLogger::logError('Misconfigured right in form');
				}
			}

				// If all rights processed without a return
				// AND = allowed, all rights passed		OR = disallowed, no right matched
			return $and;
		} elseif( isset($config['restrictInternal']) ) {
				// Restricted to internal persons
			return Todoyu::person()->isAdmin() ||  Todoyu::person()->isInternal();
		}

			// If no requirements found for the filed, allow it
		return true;
	}

}

?>