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
 * Manager server messages
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapServerMessageManager {

	/**
	 * Extract addresses
	 *
	 * @param	stdClass[]	$addressObjects
	 * @return	Array[]
	 */
	public static function extractAddresses($addressObjects) {
		$extracted	= array();

		if( is_array($addressObjects) ) {
			foreach($addressObjects as $addressObject) {
				/** @var	stdClass	$addressObject */
				$extracted[] = self::extractAddress($addressObject);
			}
		}

		return $extracted;
	}



	/**
	 * Extract address from address object
	 *
	 * @param	stdClass		$addressObject
	 * @return	Array
	 */
	public static function extractAddress(stdClass $addressObject) {
		return array(
			'mailbox'	=> strtolower($addressObject->mailbox),
			'host'		=> strtolower($addressObject->host),
			'address'	=> strtolower($addressObject->mailbox . '@' . $addressObject->host),
			'name'		=> TodoyuImapServerMessageManager::decodeHeader($addressObject->personal)
		);
	}



	/**
	 * Extract information from message header string given by imap_headers()
	 *
	 * @param	String		$messageHeaderString
	 * @return	Array|Boolean
	 */
	public static function getMessageHeaderStringInfo($messageHeaderString) {
		$headerPattern	= "/(?:(?'flags'[RUFAD ]*)(?: )+)?(?'number'\\d+)\\)\\s?(?'date'\\d+-\\w+-\\d+) (?'name'[^{]*) (?'junkinfo'{[^}]+} )?(?'subject'.*?) \\((?'chars'\\d+) \\w+\\)/";
		$found			= preg_match($headerPattern, $messageHeaderString, $match) === 1;

		if( $found ) {
			return array(
				'header'	=> $messageHeaderString,
				'flags'		=> array_fill_keys(TodoyuArray::trim(str_split($match['flags']), true), true),
				'number'	=> intval($match['number']),
				'date'		=> trim($match['date']),
				'time'		=> TodoyuTime::parseDateTime($match['date'], '%d-%b-%Y'),
				'name'		=> trim($match['name']),
				'nonJunk'	=> trim($match['junkinfo']) === '{NonJunk}',
				'subject'	=> trim($match['subject']),
				'chars'		=> intval($match['chars'])
			);
		} else {
			return false;
		}
	}



	/**
	 * Decode message header
	 *
	 * @param	String		$encodedString
	 * @return	String
	 */
	public static function decodeHeader($encodedString) {
		$decodeData		= imap_mime_header_decode($encodedString);
		$decodedString	= '';

		foreach($decodeData as $decodedPart) {
			$charset		= $decodedPart->charset === 'default' ? 'US-ASCII' : $decodedPart->charset;
			$decodedString .= mb_convert_encoding($decodedPart->text, 'utf-8', $charset);
		}

			// Remove quotes (from names)
		$decodedString = str_replace("'", '', $decodedString);

		return $decodedString;
	}



//	function decodeMimeString($mimeStr, $inputCharset = 'utf-8', $targetCharset = 'utf-8', $fallbackCharset = 'iso-8859-1') {
//		$encodings = mb_list_lowerencodings();
//		$inputCharset = strtolower($inputCharset);
//		$targetCharset = strtolower($targetCharset);
//		$fallbackCharset = strtolower($fallbackCharset);
//
//		$decodedStr = '';
//		$mimeStrs = imap_mime_header_decode($mimeStr);
//		for($n = sizeOf($mimeStrs), $i = 0; $i < $n; $i++) {
//			$mimeStr = $mimeStrs[$i];
//			$mimeStr->charset = strtolower($mimeStr->charset);
//			if( ($mimeStr == 'default' && $inputCharset == $targetCharset) || $mimStr->charset == $targetCharset) {
//				$decodedStr .= $mimStr->text;
//			} else {
//				$decodedStr .= mb_convert_encoding( $mimeStr->text, $targetCharset,  (in_array($mimeStr->charset, $encodings) ?  $mimeStr->charset : $fallbackCharset)));
//			}
//		}
//		return $decodedStr;
//	}






	/**
	 * Decode content (to utf8) depending on the current encoding
	 *
	 * @param	String				$encodedString
	 * @param	Integer				$encoding
	 * @param	String|Boolean		$currentEncoding
	 * @return	String
	 */
	public static function decodePartContent($encodedString, $encoding, $currentEncoding = false) {
		$encoding		= intval($encoding);
		$isNotUtf8Yet	= strtoupper($currentEncoding) !== 'UTF-8';

		switch( $encoding ) {
			case IMAP_ENCODING_7BIT:
				if( $currentEncoding ) {
					$decodedString	= iconv($currentEncoding, 'UTF-8', $encodedString);
				} else {
					$decodedString	= imap_utf7_decode($encodedString);
					$decodedString	= $isNotUtf8Yet ? utf8_encode($decodedString) : $decodedString;
				}
				break;

			case IMAP_ENCODING_BASE64:
				$decodedString = imap_base64($encodedString);
				break;

			case IMAP_ENCODING_QUOTED_PRINTABLE:
				$decodedString	= imap_qprint($encodedString);
				$decodedString	= $isNotUtf8Yet ? utf8_encode($decodedString) : $decodedString;
				break;

			case IMAP_ENCODING_8BIT:
				$decodedString	= $isNotUtf8Yet ? utf8_encode($encodedString) : $encodedString;
				break;

			case IMAP_ENCODING_BINARY:
			case IMAP_ENCODING_OTHER:
			default:
				$decodedString = $encodedString;
				break;
		}

		return $decodedString;
	}

}

?>