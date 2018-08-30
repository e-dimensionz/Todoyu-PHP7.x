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
 * TodoyuExportBase
 * CSV export
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuExportCSV extends TodoyuExportBase {

	/**
	 * @var string
	 */
	private $delimiter	= ';';



	/**
	 * @var string
	 */
	private $enclosure	= '"';



	/**
	 * @var string
	 */
	private $charset	= 'utf-8';



	/**
	 * @var	Boolean
	 */
	private $useTableHeaders	= true;



	/**
	 * filepointer to the temporary file
	 *
	 * @var	Resource
	 */
	private $filePointer;

	/**
	 * Path to temporary file
	 *
	 * @var	String
	 */
	private $tmpFile;



	/**
	 * Destructor - deletes the temporary file
	 */
	public function __destruct() {
		$this->closeFile();

		if( is_file($this->tmpFile) ) {
			unlink($this->tmpFile);
		}
	}



	/**
	 * Preparation for the CSV export
	 *
	 * @param	Array	$customConfig
	 */
	protected function init(array $customConfig) {
		$pathTemp	= 'cache/output';
		TodoyuFileManager::makeDirDeep($pathTemp);

		$this->tmpFile		= TodoyuFileManager::pathAbsolute($pathTemp . '/tmpExport_' . NOW . '.csv');
		$this->filePointer	= fopen($this->tmpFile, 'w');
		$this->filename		= 'export_' . NOW . '.csv';

		$defaultConfig	= Todoyu::$CONFIG['EXPORT']['CSV'];

		$this->delimiter		= $customConfig['delimiter'] ? $customConfig['delimiter'] : ($defaultConfig['delimiter'] ? $defaultConfig['delimiter'] : $this->delimiter);
		$this->enclosure		= $customConfig['enclosure'] ? $customConfig['enclosure'] : ($defaultConfig['enclosure'] ? $defaultConfig['enclosure'] : $this->enclosure);
		$this->charset			= $customConfig['charset'] ? $customConfig['charset'] : ($defaultConfig['charset'] ? $defaultConfig['charset'] : $this->charset);
		$this->useTableHeaders	= $customConfig['useTableHeaders'] ? $customConfig['useTableHeaders'] : ($defaultConfig['useTableHeaders'] ? $defaultConfig['useTableHeaders'] : $this->useTableHeaders);
	}



	/**
	 * Creates CSV temp file and returns its content
	 *
	 * @return	String
	 */
	public function getContent() {
		$headers = $this->prepareHeaders();

		if( $this->useTableHeaders ) {
			if( is_array($headers) ) {
				fputcsv($this->filePointer, $headers, $this->delimiter, $this->enclosure);
			}
		}

		foreach($this->exportData as $record) {
			$record = $this->unifyRecord($record, $headers);

			fputcsv($this->filePointer, $record, $this->delimiter, $this->enclosure);
		}

		$this->closeFile();

		return file_get_contents($this->tmpFile);
	}



	/**
	 * Close temporary file
	 *
	 */
	private function closeFile() {
		if( is_resource($this->filePointer) )  {
			fclose($this->filePointer);
		}
	}



	/**
	 * Creates an array which merges all possible keys (headers) of an array, to avoid missmatch in the csv-file
	 *
	 * @return	Array
	 */
	protected function prepareHeaders() {
		$colTitles = array();

		foreach($this->exportData as $record) {
			foreach($record as $key => $data) {
				$newKey = explode('_', $key);

				if( !is_array($colTitles[$newKey[0]]) ) {
					$colTitles[$newKey[0]] = array();
				}

				$colTitles[$newKey[0]] = TodoyuArray::mergeUnique($colTitles[$newKey[0]], array($key));
			}
		}

		return TodoyuArray::flatten($colTitles);
	}



	/**
	 * Unifies the layout of the record by the given unified headers array
	 *
	 * @param	Array	$record
	 * @param	Array	$headers
	 * @return	Array
	 */
	private function unifyRecord($record, $headers) {
		$realRecord = array();

			foreach($headers as $key) {
				$realRecord[$key] = $record[$key];
			}

			$record = $realRecord;

		return $record;
	}



	/**
	 * Sends the file to download
	 *
	 * @param	String	$filename
	 */
	public function download($filename = '') {
		parent::download('text/csv', $filename);
	}

}

?>