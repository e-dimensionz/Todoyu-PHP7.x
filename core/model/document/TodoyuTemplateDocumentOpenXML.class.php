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

require_once( PATH_LIB . '/php/pclzip/pclzip.lib.php' );

/**
 * Document type: openXML
 * Documents which implement the openXML standard can extend from this class for a set uf useful methods
 *
 * @package		Todoyu
 * @subpackage	Document
 * @abstract
 */
abstract class TodoyuTemplateDocumentOpenXML extends TodoyuTemplateDocumentAbstract implements TodoyuTemplateDocumentIf {

	/**
	 * Base temp dir for document instance
	 *
	 * @var	String
	 */
	private $tempBasePath;

	/**
	 * Path to the content xml file
	 *
	 * @var	String
	 */
	protected $pathXML;

	/**
	 * Original XML content
	 *
	 * @var	String
	 */
	protected $xmlContent;

	/**
	 * Parsed XML content
	 *
	 * @var	String
	 */
	private $xmlParsed;



	/**
	 * Cleanup all temporary files when finished
	 */
	public function __destruct() {
		if( is_dir($this->getTempBasePath()) ) {
			TodoyuFileManager::deleteFolder($this->getTempBasePath());
		}
	}



	/**
	 * Initialize openXML document requirements
	 */
	protected final function init() {
		$this->makeTempStructure();

			// Encode HTML entities
		$this->data	= TodoyuArray::htmlspecialchars($this->data);
	}



	/**
	 * Create temporary structure required for openXML document handling
	 *  - Temp base folder
	 *  - Temp archive folder
	 *  - Copy of template file
	 */
	private function makeTempStructure() {
		$basePath	= $this->getTempBasePath();

			// Make temporary base dir
		TodoyuFileManager::makeDirDeep($basePath);
			// Make folder to extract template into
		TodoyuFileManager::makeDirDeep($basePath . '/archive');

			// Copy original template for modifying later
		copy($this->getTemplatePath(), $this->getTempFilePath());
	}



	/**
	 * Extract the template to the archive folder in temp dir
	 *
	 */
	private function extractTemplate() {
		TodoyuArchiveManager::extractTo($this->getTemplatePath(), $this->getExtractPath());
	}



	/**
	 * Get base temp path
	 *
	 * @return	String
	 */
	protected function getTempBasePath() {
		if( is_null($this->tempBasePath) ) {
			$this->tempBasePath = TodoyuFileManager::pathAbsolute('cache/temp/document/' . md5(microtime(true)));
		}

		return $this->tempBasePath;
	}



	/**
	 * Build absolute path located in temp folder from relative path
	 *
	 * @param	String		$relPath
	 * @return	String
	 */
	private function getTempPath($relPath) {
		return TodoyuFileManager::pathAbsolute($this->getTempBasePath() . '/' . ltrim($relPath, '\\/'));
	}



	/**
	 * Get path to temporary template file
	 *
	 * @return	String
	 */
	protected function getTempFilePath() {
		$extension		= pathinfo($this->getTemplatePath(), PATHINFO_EXTENSION);

		return $this->getTempPath('template.' . $extension);
	}



	/**
	 * Get path to extraction directory
	 * Contains extracted content of template
	 *
	 * @return	String
	 */
	protected function getExtractPath() {
		return $this->getTempPath('archive');
	}



	/**
	 * Get path to content xml document
	 *
	 * @return	String
	 */
	protected function getXmlPath() {
		return $this->pathXML;
	}



	/**
	 * Set path to content xml document (relative to template root)
	 *
	 * @param	String		$relPath
	 */
	protected function setXmlPath($relPath) {
		$this->pathXML = TodoyuFileManager::pathAbsolute($this->getExtractPath() . '/' . ltrim($relPath, '\\/'));
	}



	/**
	 * Get relative
	 * @return mixed
	 */
	protected function getXmlRelPath() {
		return str_replace($this->getExtractPath() . DIR_SEP, '', $this->getXmlPath());
	}



	/**
	 * Load the xml content from content.xml
	 *
	 * @param	String	$relPath
	 */
	protected function loadXMLContent($relPath) {
		$this->setXmlPath($relPath);
		$this->extractTemplate();

		if( is_file($this->getXmlPath()) ) {
			$this->xmlContent = file_get_contents($this->getXmlPath());
		} else {
			TodoyuLogger::logError('Can\'t load XML file with content from template. File not found: "' . $this->getXmlPath() . '"!');
		}
	}



	/**
	 * Render the xml file
	 */
	private function renderXML() {
//		TodoyuHeader::sendTypeText();
//		TodoyuHeader::sendTypeXML();
//		echo $this->xmlContent;
//		exit();

		$this->xmlParsed = Todoyu::render(new Dwoo_Template_String($this->xmlContent), $this->data);
	}



	/**
	 * Build archive with parsed content
	 */
	protected function buildArchive() {
			// Render template with data
		$this->renderXML();

			// Write parsed xml content into extracted document
		TodoyuFileManager::saveFileContent($this->getXmlPath(), $this->xmlParsed);

			// Open temp template file
		$zip	= new PclZip($this->getTempFilePath());
			// Delete content document in template archive
		$zip->delete(PCLZIP_OPT_BY_NAME, $this->getXmlRelPath());
			// Add parsed content document to archive
		$zip->add($this->getXmlPath(), PCLZIP_OPT_REMOVE_PATH, $this->getExtractPath());
	}



	/**
	 * Get parsed XML content
	 *
	 * @return	String
	 */
	protected function getXmlParsed() {
		if( is_null($this->xmlParsed) ) {
			$this->renderXML();
		}

		return $this->xmlParsed;
	}



	/**
	 * Send the file to the browser
	 *
	 * @param	String		$filename
	 */
	public function sendFile($filename) {
		parent::sendFile($this->getTempFilePath(), $filename, $this->getContentType());
	}



	/**
	 * Save the file to the server
	 *
	 * @param	String		$pathFile
	 * @return	Boolean
	 */
	public function saveFile($pathFile) {
		$pathFile	= TodoyuFileManager::pathAbsolute($pathFile);

		TodoyuFileManager::makeDirDeep(dirname($pathFile));

		return copy($this->getTempFilePath(), $pathFile);
	}



	/**
	 * Get path of the created document
	 *
	 * @return	String
	 */
	public function  getFilePath() {
		return $this->getTempFilePath();
	}



	/**
	 * Get file data
	 *
	 * @return	String
	 */
	public function getFileData() {
		return file_get_contents($this->getFilePath());
	}

}

?>