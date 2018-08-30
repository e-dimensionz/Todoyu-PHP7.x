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
 * Abstract base class for document object classes
 *
 * @package		Todoyu
 * @subpackage	Document
 * @abstract
 */
abstract class TodoyuTemplateDocumentAbstract {

	/**
	 * Path to template
	 * 
	 * @var String
	 */
	private $template;

	/**
	 * Template content type
	 *
	 * @var	String
	 */
	private $contentType = '';

	/**
	 * Template data
	 *
	 * @var Array
	 */
	protected $data;

	/**
	 * Configuration
	 *
	 * @var Array
	 */
	protected $config;



	/**
	 * Initialize document
	 *
	 * @param	Array		$data
	 * @param	String		$template
	 * @param	Array		$config
	 */
	public final function __construct(array $data, $template, array $config = array()) {
		$this->data		= $data;
		$this->template	= TodoyuFileManager::pathAbsolute($template);
		$this->config	= $config;

		$this->init();
		$this->build();
	}



	/**
	 * Initialize document object. Called before build
	 */
	protected function init() {
		// Do nothing
	}



	/**
	 * Build the document with the template and the data
	 *
	 */
	abstract protected function build();



	/**
	 * Get document type
	 *
	 * @return	String
	 */
	public function getType() {
		return str_replace('TodoyuDocument', '', get_class($this));
	}



	/**
	 * Get path to original template
	 *
	 * @return	String
	 */
	protected function getTemplatePath() {
		return $this->template;
	}



	/**
	 * Get content type of document
	 *
	 * @return	String
	 */
	protected function getContentType() {
		return $this->contentType;
	}



	/**
	 * Set content type of document
	 *
	 * @param	String		$contentType
	 */
	protected function setContentType($contentType) {
		$this->contentType = $contentType;
	}

	

	/**
	 * Send a file to the browser
	 *
	 * @param	String		$pathFile		Path to file on the server
	 * @param	String		$filename		Filename in browser
	 * @param	String		$mimeType		Content-type of the file
	 */
	protected function sendFile($pathFile, $filename, $mimeType) {
		$pathFile	= TodoyuFileManager::pathAbsolute($pathFile);

		try {
			TodoyuFileManager::sendFile($pathFile, $mimeType, $filename);
		} catch(TodoyuExceptionFileDownload $e) {
			// @todo catch
		}
	}

}

?>