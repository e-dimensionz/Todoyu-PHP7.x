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
 * Document type: Microsoft Office Text Document (docx)
 *
 * @package		Todoyu
 * @subpackage	Document
 */
class TodoyuTemplateDocumentDocx extends TodoyuTemplateDocumentOpenXML {

	/**
	 * Build parsed template
	 */
	protected function build() {
			// Set content type
		$this->setContentType('vnd.openxmlformats-officedocument.wordprocessingml');
			// Load the XML content from the template file
		$this->loadXMLContent('word/document.xml');
			// Prepare the XML content (move some markers)
		$this->prepareXML();
			// Create an archive again for the odt
		$this->buildArchive();
	}



	/**
	 * Prepare template XML content
	 */
	protected function prepareXML() {
		$this->prepareConditions();
//		$this->prepareRowXML();
	}



	/**
	 * Prepare if/else conditions in template XML content
	 */
	protected function prepareConditions() {
		$ifPattern	= '|<w:p[^>]*?>((?!</w:p>).)*?<w:t>if</w:t>((?!</w:p>).)*?</w:p>|s';
		$elsePattern= '|<w:p[^>]*?>((?!</w:p>).)*?<w:t>else</w:t>((?!</w:p>).)*?</w:p>|s';

		preg_match_all($ifPattern, $this->xmlContent, $ifMatches);
		preg_match_all($elsePattern, $this->xmlContent, $elseMatches);

		$replace= array();

		foreach($ifMatches[0] as $match) {
			$replace[$match] = strip_tags($match);
		}
		foreach($elseMatches[0] as $match) {
			$replace[$match] = strip_tags($match);
		}

		$this->xmlContent = str_replace(array_keys($replace), array_values($replace), $this->xmlContent);
	}



	/**
	 * Find table rows in XML and substitute ROW markers
	 */
	protected function prepareRowXML() {
/*			// Remove text spans around the row tags
//		$patternRowTagA	= '|<text:span[^>]*?>(\[ROW:)</text:span>|sm';
//		$patternRowTagB	= '|<text:span[^>]*?>(--ROW\])</text:span>|sm';
//
//		$this->xmlContent	= preg_replace($patternRowTagA, '\1', $this->xmlContent);
//		$this->xmlContent	= preg_replace($patternRowTagB, '\1', $this->xmlContent);
*/
			// Pattern to find all table rows
		$patternRow		= '|<w:tr.*?>.*?</w:tr>|s';
			// Pattern to find sub parts in a table row if  it contains the row syntax '[--ROW:'
		$patternRowParts= '|(<table:table-row[^>]*?>)(.*?)\[--ROW:({.*?})(.*?)({/.*?})--ROW\](.*?)(</table:table-row>)|sm';
		$replaces		= array();

		// (\[--ROW:{.*?foreach[^}]*?})(.*?)

			// Find all rows
		preg_match_all($patternRow, $this->xmlContent, $rowMatches);

			// Check for the row syntax in the matched row parts and modify the row
		foreach($rowMatches[0] as $rowXML) {
			if( preg_match($patternRowParts, $rowXML, $partMatches) ) {
				$replaces[$rowXML] = $partMatches[3] . $partMatches[1] . $partMatches[2] . $partMatches[4] . $partMatches[6] . $partMatches[7] . $partMatches[5];
			}
		}

		$this->xmlContent = str_replace(array_keys($replaces), array_values($replaces), $this->xmlContent);
	}
}

?>