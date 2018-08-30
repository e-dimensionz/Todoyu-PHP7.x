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
 * Manage server message parts
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessagePartManager {

	/**
	 * Factory method for server message parts
	 *
	 * @param	TodoyuImapServerMessage		$serverMessage
	 * @param	String						$section
	 * @param	stdClass					$part
	 * @return	TodoyuImapServerMessagePart|TodoyuImapServerMessagePartAttachment|TodoyuImapServerMessagePartInlineImage
	 */
	public static function getMessagePart(TodoyuImapServerMessage $serverMessage, $section, stdClass $part) {
		if( $part->subtype === 'ALTERNATIVE' ) {
			return new TodoyuImapServerMessagePartAlternative($serverMessage, $section, $part);
		} elseif( $part->subtype === 'RFC822' ) {
			return new TodoyuImapServerMessagePartRFC822($serverMessage, $section, $part);
		} elseif( $part->disposition === 'attachment' ) {
			return new TodoyuImapServerMessagePartAttachment($serverMessage, $section, $part);
		} elseif( in_array($part->subtype, array('PNG', 'JPEG', 'GIF')) ) {
			return new TodoyuImapServerMessagePartInlineImage($serverMessage, $section, $part);
		} else {
			return new TodoyuImapServerMessagePart($serverMessage, $section, $part);
		}
	}



	/**
	 * @static
	 * @param stdClass $part
	 * @return bool|String
	 */
	public static function getPartCharset(stdClass $part) {
		return self::getPartParameter($part, 'charset');
	}



	/**
	 *
	 * @param	stdClass		$part
	 * @param	String			$parameterName
	 * @return	String|Boolean
	 */
	public static function getPartParameter(stdClass $part, $parameterName) {
		if( is_array($part->parameters) ) {
			foreach($part->parameters as $parameter) {
				if( strtolower($parameter->attribute) === strtolower($parameterName) ) {
					$name			= $parameter->value;
					$decodedObjects	= imap_mime_header_decode($name);
					$decoded 		= $decodedObjects[0];

					return trim($decoded ? $decoded->text : $name);
				}
			}
		}

		return false;
	}

}

?>