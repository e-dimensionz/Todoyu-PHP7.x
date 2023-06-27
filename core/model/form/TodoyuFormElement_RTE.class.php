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
 * FormElement: RichTextEditor (tinyMCE)
 *
 * Richtext editor
 *
 * @package		Todoyu
 * @subpackage	Form
 */
class TodoyuFormElement_RTE extends TodoyuFormElement_Textarea {

	/**
	 * Initialize RTE form element
	 *
	 * @param	String		$name
	 * @param	TodoyuFormFieldset	$fieldset
	 * @param	Array		$config
	 */
	public function __construct($name, TodoyuFormFieldset $fieldset, array $config = array()) {
		TodoyuFormElement::__construct('RTE', $name, $fieldset, $config);
	}



	/**
	 * Build RTE initialisation JavaScript code to convert the textarea into a RTE
	 * when displayed on the page
	 *
	 * @return	String
	 */
	private function buildRTEjs() {
		$extraOptions	= TodoyuArray::assure($this->config['tinymce'] ?? []);
		$config			= array();

			// Add own callback to focus the active editor (auto_focus fails because of a bug)
		if( TodoyuRequest::isAjaxRequest() ) {
			$config['focus'] = true;
		}

		$jsonExtraOptions	= sizeof($extraOptions) > 0 ? json_encode($extraOptions) : '{}';
		$jsonConfig			= sizeof($config) > 0 ? json_encode($config) : '{}';

		return 'Todoyu.Ui.initRTE(\'' . $this->getHtmlID() . '\', ' . $jsonExtraOptions . ', ' .$jsonConfig . ');';
	}



	/**
	 * Get field data
	 *
	 * @return	Array
	 */
	public function getData() {
		$data	= parent::getData();

		$data['rteJs'] = $this->buildRTEjs();

		return $data;
	}



	/**
	 * Set RTE text. Removed <pre> tags (copy from email programs) and adds <br> tags for the newlines in <pre>
	 *
	 * @param	String		$value
	 * @param	Boolean		$updateForm
	 */
	public function setValue($value, $updateForm = true) {
		$value	= TodoyuString::cleanRTEText($value);

		parent::setValue($value, $updateForm);
	}



	/**
	 * Check if field is valid for required flag
	 *
	 * @return	Boolean
	 */
	public function validateRequired() {
		return TodoyuValidator::isNotEmpty($this->getValue());
	}

}

?>