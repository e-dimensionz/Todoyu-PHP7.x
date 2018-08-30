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
 * TodoyuFormFieldset object for form
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormFieldset implements ArrayAccess {

	/**
	 * Name of the fieldset
	 *
	 * @var	String
	 */
	private $name;

	/**
	 * Parent element of the fieldset. Can be the form or another fieldset.
	 *
	 * @var	TodoyuFormFieldset
	 */
	private $parent;

	/**
	 * Attributes of the fieldset (like legend, class, etc)
	 *
	 * @var	Array
	 */
	private $attributes;

	/**
	 * Elements of the fieldsets. Can be a mix of fieldsets and FormElements
	 *
	 * @var	TodoyuFormElement[]|TodoyuFormFieldset[]
	 */
	private $elements = array();



	/**
	 * Initialize a new fieldset.
	 *
	 * @param	TodoyuFormFieldset	$parent		Reference to parent element (fieldset or the form)
	 * @param	String			$name		Name of the fieldset to be accessed over $form->FIELDSETNAME->method()
	 */
	public function __construct($parent, $name) {
		$this->parent	= $parent;
		$this->name		= $name;
	}



	/**
	 * Update elements parent to new cloned fieldset object
	 *
	 */
	public function __clone() {
		foreach($this->elements as $element) {
			$element->setParent($this);
		}
	}



	/**
	 * Get the form instance
	 *
	 * @return	TodoyuForm
	 */
	public function getForm() {
		return $this->getParent()->getForm();
	}



	/**
	 * Get parent element (fieldset or form)
	 *
	 * @return	TodoyuFormFieldset
	 */
	public function getParent() {
		return $this->parent;
	}



	/**
	 * Set fieldset parent
	 *
	 * @param	Object		$parent
	 */
	public function setParent($parent) {
		$this->parent = $parent;
	}



	/**
	 * Get fieldset name
	 *
	 * @return	String
	 */
	public function getName() {
		return $this->name;
	}



	/**
	 * Get the absolute name of the fieldset
	 * Concatenate all parent fieldsets with a dash parent-sub-sub-...
	 *
	 * @return	String
	 */
	public function getAbsoluteName() {
		if( $this->parent instanceof TodoyuForm ) {
			return $this->name;
		} else {
			return $this->getParent()->getAbsoluteName() . '-' . $this->name;
		}
	}



	/**
	 * Get field from the form
	 *
	 * @param	String		$name
	 * @return	TodoyuFormElement
	 */
	public function getField($name) {
		return $this->elements[$name];
	}



	/**
	 * Get a fieldset by name
	 *
	 * @param	String		$name
	 * @return	TodoyuFormFieldset
	 */
	public function getFieldset($name) {
		return $this->elements[$name];
	}



	/**
	 * Access elements in the fieldset over $form->FIELDSETNAME->ELEMENTNAME
	 *
	 * @param	String			$name		Name of the sub element
	 * @return	TodoyuFormElement
	 */
	public function __get($name) {
		return $this->elements[$name];
	}



	/**
	 * Delete an element in the fieldset
	 *
	 * @param	String		$name
	 */
	public function __unset($name) {
		unset($this->elements[$name]);
	}



	/**
	 * Set a fieldset attribute
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
	}



	/**
	 * Get a fieldset attribute
	 *
	 * @param	String		$name
	 * @return	Mixed
	 */
	public function getAttribute($name) {
		return $this->attributes[$name];
	}



	/**
	 * Set fieldset legend
	 *
	 * @param	String		$legend
	 */
	public function setLegend($legend) {
		$this->setAttribute('legend', $legend);
	}



	/**
	 * Set fieldset class(es)
	 *
	 * @param	String		$className
	 */
	public function setClass($className) {
		$this->setAttribute('class', $className);
	}



	/**
	 * Add class name
	 *
	 * @param	String		$classNames
	 */
	public function addClass($classNames) {
		$addClasses	= TodoyuArray::trimExplode(' ', $classNames);
		$classes	= TodoyuArray::trimExplode(' ', $this->getAttribute('class'));
		$classes	= TodoyuArray::mergeUnique($addClasses, $classes);

		$this->setClass(implode(' ', $classes));
	}



	/**
	 * Add a new fieldset to the fieldset
	 * Creates a new fieldset and adds it to the child list
	 * and return a reference to the fieldset
	 *
	 * To add a complete fieldset and its fields (e.g. form hook) use injectFieldset
	 * @see TodoyuFormFieldset->injectFieldset();
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Integer				$position
	 * @return	TodoyuFormFieldset
	 */
	public function addFieldset($name, TodoyuFormFieldset $fieldset = null, $position = null) {
		if( is_null($fieldset) ) {
			$fieldset = new TodoyuFormFieldset($this, $name);
		}

			// Set fieldset parent
		$fieldset->setParent($this);

			// If no position given, append element
		if( is_null($position) ) {
			$this->elements[$name] = $fieldset;
		} else {
				// If position available, insert element at given position
			$pos = explode(':', $position);

			$this->elements = TodoyuArray::insertElement($this->elements, $name, $fieldset, $pos[0], $pos[1]);
		}

		$this->getForm()->registerFieldset($name, $fieldset);

		return $fieldset;
	}



	/**
	 * Add the $field to the fieldset
	 *
	 * @param	String				$fieldName
	 * @param	TodoyuFormElement	$field			Field object
	 * @param	String				$position		Insert position. Format: after:title, before:status
	 * @return	TodoyuFormElement
	 */
	public function addField($fieldName, TodoyuFormElement $field, $position = null) {
			// Prevent back reference to original field
		$field = clone $field;

			// Set the new parent fieldset
		$field->setFieldset($this);
		$field->setName($fieldName);

			// If no position given, append element
		if( is_null($position) ) {
			$this->elements[$fieldName] = $field;
		} else {
				// If position available, insert element at given position
			list($insertMode, $insertReference) = explode(':', $position, 2);

			$this->elements = TodoyuArray::insertElement($this->elements, $fieldName, $field, $insertMode, $insertReference);
		}

		$this->getForm()->registerField($field);

		return $field;
	}



	/**
	 * Add all elements of a form to this field set
	 *
	 * @param	String		$xmlPath		Path to sub form XML file
	 * @param	Integer		$position
	 */
	public function addElementsFromXML($xmlPath, $position = null) {
		$xmlPath	= TodoyuFileManager::pathAbsolute($xmlPath);
		$form		= TodoyuFormManager::getForm($xmlPath);

		$fieldSets	= $form->getFieldsets();

		foreach($fieldSets as $fieldSet) {
			$this->injectFieldset($fieldSet, $position);

			$position = 'after:' . $fieldSet->getName();
		}
	}



	/**
	 * Add elements from another XML into the fieldset after the element named $name
	 *
	 * @see		$this->addElementsFromXML()
	 * @param	String		$xmlPath		Path to the xml file
	 * @param	String		$name			Name of the field to insert the elements after
	 */
	public function addElementsFromXMLAfter($xmlPath, $name) {
		$this->addElementsFromXML($xmlPath, 'after:' . $name);
	}



	/**
	 * Add elements from another XML into the fieldset before the element named $name
	 *
	 * @see		$this->addElementsFromXML()
	 * @param	String		$xmlPath		Path to the xml file
	 * @param	String		$name			Name of the field to insert the elements before
	 */
	public function addElementsFromXMLBefore($xmlPath, $name) {
		$this->addElementsFromXML($xmlPath, 'before:' . $name);
	}



	/**
	 * Inject an existing fieldset into the form
	 *
	 * @param	TodoyuFormFieldset		$fieldset
	 * @param	Integer				$position
	 * @return	TodoyuFormFieldset
	 */
	public function injectFieldset(TodoyuFormFieldset $fieldset, $position = null) {
		$fieldset->setParent($this);
		$fieldset->setFieldsToForm($this->getForm());

		return $this->addFieldset($fieldset->getName(), $fieldset, $position);
	}



	/**
	 * Add a field from custom config
	 *
	 * @param	String		$fieldName
	 * @param	String		$fieldType
	 * @param	Array		$fieldConfig
	 * @param	String		$position
	 * @return	TodoyuFormElement
	 */
	public function addFieldElement($fieldName, $fieldType, array $fieldConfig, $position = null) {
		$field	= TodoyuFormFactory::createField($fieldType, $fieldName, $this, $fieldConfig);

		return $this->addField($fieldName, $field, $position);
	}



	/**
	 * Remove a field (and cleanup field references)
	 *
	 * @param	String		$fieldName
	 * @param	Boolean		$cleanup
	 */
	public function removeField($fieldName, $cleanup = true) {
		unset($this->elements[$fieldName]);

		if( $cleanup ) {
			$this->getForm()->removeField($fieldName, false);
		}
	}



	/**
	 * Remove fieldset with all its elements
	 */
	public function remove() {
		$fieldNames	= $this->getFieldNames();

		foreach($fieldNames as $fieldName) {
			$field	= $this->getField($fieldName);
			if( !is_null($field)) {
				$this->getField($fieldName)->remove();
			}
		}

		$this->getForm()->removeFieldset($this->getName());
	}



	/**
	 * Get field names
	 *
	 * @return	Array
	 */
	public function getFieldNames() {
		$fieldNames	= array();

		foreach($this->elements as $element) {
			if( $element instanceof TodoyuFormElement ) {
					// Element is form element
				$fieldNames[] = $element->getName();
			} elseif( $element instanceof TodoyuFormFieldset ) {
					// Element is sub fieldset
				$fieldNames = array_merge($fieldNames, $element->getFieldNames());
			}
		}

		return $fieldNames;
	}



	/**
	 * Get data for template rendering
	 *
	 * @return	Array
	 */
	protected function getData() {
		$this->setAttribute('htmlId', $this->getForm()->makeID($this->name, 'fieldset'));
		$this->setAttribute('name', $this->name);

		return $this->attributes;
	}



	/**
	 * Render fieldset with all its child elements
	 *
	 * @return	String
	 */
	public function render() {
		$template	= Todoyu::$CONFIG['FORM']['templates']['fieldset'];
		$data		= $this->getData();

		$data['content'] =  $this->renderElements();;

		return Todoyu::render($template, $data);
	}



	/**
	 * Render fieldset elements (without wrapping fieldset)
	 *
	 * @return	String
	 */
	public function renderElements() {
		$content	= '';

		$odd = true;
		foreach($this->elements as $name => $element) {
			if( $element instanceof TodoyuFormElementInterface ) {
				if( ! $element->isHidden() ) {
					$content .= $element->render($odd) . "\n";
				}
			} elseif( $element instanceof TodoyuFormFieldset ) {
				$content .= $element->render($odd) . "\n";
			}

			$odd = ! $odd;
		}

		return $content;
	}



	/**
	 * ArrayAccess: Check if an attribute is set: isset($fieldset['legend'])
	 *
	 * @param	String		$name
	 * @return	Boolean
	 */
	public function offsetExists($name) {
		return isset($this->attributes[$name]);
	}



	/**
	 * ArrayAccess: Get an attribute from the fieldset: echo $fieldset['legend']
	 *
	 * @param	String		$name
	 * @return	String
	 */
	public function offsetGet($name) {
		return $this->getAttribute($name);
	}



	/**
	 * ArrayAccess: Set an attribute: $fieldset['legend'] = 'New Legend'
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public function offsetSet($name, $value) {
		$this->setAttribute($name, $value);
	}



	/**
	 * ArrayAccess: Delete attribute: unset($fieldset['legend'])
	 *
	 * @param	String		$name
	 */
	public function offsetUnset($name) {
		unset($this->attributes[$name]);
	}



	/**
	 * Return all sub fieldSets
	 *
	 * @return	Array
	 */
	public function getFieldsets() {
		$fieldsets = array();

		foreach($this->elements as $element) {
			if( $element instanceof TodoyuFormFieldset ) {
				$fieldsets[] = $element;
			}
		}

		return $fieldsets;
	}



	/**
	 * Get names of parent fieldsets
	 *
	 * @return	Array
	 */
	public function getParentFieldsetNames() {
		$fieldset	= $this;
		$parents	= array();

		while( ! ($fieldset->getParent() instanceof TodoyuForm)  ) {
			$parents[] = $fieldset->getParent()->getName();
			$fieldset = $fieldset->getParent();
		}

		return $parents;
	}



	/**
	 * Adds fields of a fieldset recursively to the form
	 *
	 * @param	TodoyuForm	$form
	 */
	public function setFieldsToForm(TodoyuForm $form) {
		/**	@var	TodoyuFormElement	$element */
		foreach($this->elements as $element) {
			if( $element instanceof TodoyuFormElement ) {
				/**	@var	TodoyuFormElement	$element */
				$form->registerField($element);
			} elseif( $element instanceof TodoyuFormFieldset ) {
				/**	@var	TodoyuFormFieldset	$element */
				$element->setFieldsToForm($form);
			}
		}
	}



	/**
	 * Bubble error: report a field error to its parent
	 *
	 * @param	TodoyuFormElement		$field
	 */
	public function bubbleError(TodoyuFormElement $field) {
		$this->getParent()->bubbleError($field);
	}
}

?>