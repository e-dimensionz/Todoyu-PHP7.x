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
 * Export base
 *
 * @package		Todoyu
 * @subpackage	Core
 * @abstract
 */
abstract class TodoyuExportBase {

	/**
	 * @var array
	 */
	protected $exportData = array();



	/**
	 * @var string
	 */
	protected	$filename	= '';



	/**
	 * Constructor
	 *
	 * @param	Array	$exportData
	 * @param	Array	$customConfig
	 */
	public function __construct(array $exportData, array $customConfig = array()) {
		$this->exportData	= $exportData;

		$this->init($customConfig);
	}



	/**
	 * @abstract
	 * @param	Array	$customConfig
	 */
	protected abstract function init(array $customConfig);



	/**
	 * Setter method for filename
	 *
	 * @param	String		$filename
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
	}



	/**
	 * Getter for content data
	 *
	 * @abstract
	 * @return	String
	 */
	public abstract function getContent();



	/**
	 * Get headers for download
	 *
	 * @param	String		$type
	 * @param	String		$filename
	 * @return	Array
	 */
	protected function getHeaders($type, $filename) {
		$filename	= empty($filename) ? $this->filename : $filename;

		return array(
			'Content-Type'			=> $type,
			'Content-Disposition'	=> 'attachment; filename="' . $filename . '"',
			'Pragma'				=> 'no-cache',
			'Expires'				=> 0
		);
	}



	/**
	 * Send headers for download
	 *
	 * @param	String		$type
	 * @param	String		$filename
	 */
	protected function sendHeaders($type, $filename) {
		$headers	= $this->getHeaders($type, $filename);

		foreach($headers as $name => $value) {
			header($name . ': ' . $value);
		}
	}



	/**
	 * Sends the file to download
	 *
	 * @param	String	$type
	 * @param	String	$filename
	 */
	public function download($type, $filename = '') {
		$this->sendHeaders($type, $filename);

		echo $this->getContent();
		exit();
	}

}

?>