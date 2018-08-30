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
 * Exception for database errors
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuDbException extends Exception {

	/**
	 * Query which caused the error
	 *
	 * @var	String
	 */
	private $query;




	/**
	 * Init TodoyuDbException
	 *
	 * @param	String		$message
	 * @param	Integer		$code
	 * @param	String		$query
	 */
	public function  __construct($message, $code, $query) {
		parent::__construct($message, $code);

		$this->query = $query;
	}



	/**
	 * Get query
	 *
	 * @return	String
	 */
	public function getQuery() {
		return $this->query;
	}



	/**
	 * Get file without site path
	 *
	 * @return	String
	 */
	public function getFileShort() {
		return str_replace(PATH, '', $this->getFile());
	}



	/**
	 * Render database error as HTML
	 *
	 * @param	Boolean		$fullDoc		Render full HTML document with (<html><body, etc)
	 * @return	String		HTML view of error
	 */
	public function getErrorAsHtml($fullDoc = false) {
			// Remove full site path
		$trace	= $this->getTrace();
		foreach($trace as $index => $step) {
			$trace[$index]['file'] = TodoyuFileManager::removeSitePath($trace[$index]['file']);
		}

		$data = array(
			'message'	=> $this->getMessage(),
			'code'		=> $this->getCode(),
			'file'		=> $this->getFileShort(),
			'line'		=> $this->getLine(),
			'query'		=> $this->getQuery(),
			'trace'		=> $trace
		);

		$tmpl	= 'core/view/dberror_html.tmpl';

		$content= Todoyu::render($tmpl, $data);

		if( $fullDoc ) {
			$tmpl	= 'core/view/htmldoc.tmpl';
			$data	= array(
				'title'		=> 'Database Error!',
				'content'	=> $content
			);
			$content= Todoyu::render($tmpl, $data);
		}

		return $content;
	}



	/**
	 * Render database error as JSON
	 *
	 * @todo	Implement a useful format
	 * @return	String
	 */
	public function getErrorAsJson() {
		return json_encode(array('error'=>$this->getMessage()));
	}



	/**
	 * Render database error in plain text
	 *
	 * @return	String
	 */
	public function getErrorAsPlain() {
			// Remove full site path
		$trace	= $this->getTrace();
		foreach($trace as $index => $step) {
			$trace[$index]['file'] = TodoyuFileManager::removeSitePath($trace[$index]['file']);
		}

		$data	= array('message'	=> $this->getMessage(),
						'code'		=> $this->getCode(),
						'file'		=> $this->getFileShort(),
						'line'		=> $this->getLine(),
						'query'		=> $this->getQuery(),
						'trace'		=> $trace);

		//$content	= 'D'
		$tmpl	= 'core/view/dberror_plain.tmpl';

		$content= Todoyu::render($tmpl, $data);

		return $content;
	}
}

?>