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
 * HTML filter to escape bad HTML code
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuHtmlFilter {

	/**
	 * Get config array of "bad" (causing danger when output w/o being escaped) HTML tags
	 *
	 * @return	Array
	 */
	private static function getBadTags() {
		return Todoyu::$CONFIG['SECURITY']['badHtmlTags'];
	}



	/**
	 * Callback to escape bad simple HTML tags
	 *
	 * @param	Array		$match
	 * @return	String
	 */
	private static function escapeBadTag(array $match) {
		return htmlentities($match[0], ENT_QUOTES, 'UTF-8', false);
	}



	/**
	 * Callback to escape bad HTML tags
	 *
	 * @param	Array		$match
	 * @return	String
	 */
	private static function escapeBadTags(array $match) {
		return '&lt;' . $match[1] . $match[2] . '&gt;' . $match[3] . '&lt;/' . $match[1] . '&gt;';
	}



	/**
	 * Escape comments in HTML
	 *
	 * @param	String	$html
	 * @return	String
	 */
	private static function escapeHtmlComments($html) {
		$replace	= array(
			'<![CDATA['	=> '&lt;![CDATA[',
			']]>'		=> ']]&gt;',
			'<!--'		=> '&lt;!--',
			'-->'		=> '--&gt;'
		);

		$html	= str_replace(array_keys($replace), array_values($replace), $html);

		return $html;
	}



	/**
	 * Clean HTML code: escape all found "bad" tags (and comments)
	 *
	 * @param	String		$html
	 * @return	String
	 */
	public static function clean($html) {
		if( trim($html) === '' ) {
			return '';
		}
			// Get "bad" HTML tags (protect the user): tags being disallowed to be output non-escaped
		$badTags	= self::getBadTags();
			// Find/replace each bad tag by it's escaped version
		foreach($badTags as $badTag) {
			$patternStandard= '|<(' . $badTag . ')([^>]*)>(.*?)</' . $badTag . '>|sim';
			$html			= preg_replace_callback($patternStandard, array('TodoyuHtmlFilter','escapeBadTags'), $html);

			$patternSimple	= '|<(' . $badTag . ')([^>]*)>(.*?)|sim';
			$html			= preg_replace_callback($patternSimple, array('TodoyuHtmlFilter','escapeBadTag'), $html);
		}

			// Escape comment variations
		$html	= self::escapeHtmlComments($html);

		return $html;
	}

}

?>