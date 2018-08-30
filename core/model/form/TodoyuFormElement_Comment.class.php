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
 * FormElement: Comment
 *
 * Comment without a form element, just text
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_Comment extends TodoyuFormElement {

	/**
	 * TodoyuFormElement comment constructor
	 *
	 * @param	String				$name
	 * @param	TodoyuFormFieldset		$fieldset
	 * @param	Array				$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		parent::__construct('comment', $name, $fieldset, $config);
	}



	/**
	 * Init comment
	 * Parse value as locallang
	 */
	protected function init() {

	}



	/**
	 * Get value (text) of comment
	 *
	 * @return	String
	 */
	public function getValueForTemplate() {
		$value		= '';
		$hasFunction= is_array($this->config['comment']) && is_array($this->config['comment']['@attributes']) && $this->config['comment']['@attributes']['type'] === 'function';

		if( $hasFunction ) {
			$value	= TodoyuFunction::callUserFunction($this->config['comment']['function'], $this);
		} else {
			$comment= trim($this->config['comment']);

			if( $comment !== '' ) {
				$value	= Todoyu::Label($comment);
			}
		}

		return $value;
	}



	/**
	 * Set content of comment
	 *
	 * @param	String		$content
	 */
	public function setCommentText($content) {
		$this->config['comment'] = $content;
	}



	/**
	 * Set field value ('attribute')
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$updateForm		Update the form. Can be false if the form already has the value
	 */
	public function setValue($value, $updateForm = true) {
		$this->setCommentText($value);

		if( $updateForm ) {
			$this->updateFormData($value);
		}
	}



	/**
	 * Comment fields are never stored in the database
	 *
	 * @return	Boolean
	 */
	public function isNoStorageField() {
		return true;
	}

}

?>