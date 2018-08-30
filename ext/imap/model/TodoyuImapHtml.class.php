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
 * Clean html mails for usage in todoyu
 *
 * @package		Todoyu
 * @subpackage	Imap
 */
class TodoyuImapHtml {

	/**
	 * Get simplified html of message
	 * All disturbing parts are removed
	 *
	 * @param	String		$html
	 * @return	String
	 */
	public static function getSimpleHtml($html) {
		$html	= self::getBody($html);

		$html	= self::removeInlineStyleTags($html);
		$html	= self::removeClassAttributes($html);
		$html	= self::removeEmptyElements($html);
		$html	= self::removeInlineImages($html);
		$html	= self::removeAllTagAttributes($html);
		$html	= self::removeLinebreaksAroundTags($html);
		$html	= self::removeNotAllowedTags($html);
		$html	= self::removeHtmlWhitespaces($html);
		$html	= self::removeBadTags($html);

		return trim($html);
	}



	/**
	 * Get safe html
	 * Remove risky parts
	 *
	 * @param	String		$html
	 * @return	String
	 */
	public static function getSafeHtml($html) {
		$html	= self::removeInlineScriptTags($html);
		$html	= self::removeBadTagAttributes($html);

		return trim($html);
	}



	/**
	 * Get safe and simplified html
	 *
	 * @param	String		$html
	 * @return	String
	 */
	public static function getSaveAndSimpleHtml($html) {
		$html	= self::getSimpleHtml($html);
		$html	= self::getSafeHtml($html);
		$html	= TodoyuHtmlFilter::clean($html);

		return $html;
	}



	/**
	 * Extract only the body part
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function getBody($html) {
		if( stristr($html, '</body>') !== false ) {
			$bodyPattern	= "/<body[^>]*>(?'body'.*)<\/body>/is";
			$found			= preg_match($bodyPattern, $html, $matches) === 1;

			if( $found ) {
				$html	= trim($matches['body']);
			}
		}

		return $html;
	}



	/**
	 * Remove empty elements
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeEmptyElements($html) {
		$remove	= array(
			'<o:p></o:p>',
			'<o:p>&nbsp;</o:p>'
		);
		$html	= str_replace($remove, '', $html);

		$emptyParagraphPattern = '/<p[^>]*?>\s*?<\/p>/is';
		$html	= preg_replace($emptyParagraphPattern, '', $html);

		return $html;
	}



	/**
	 * Remove all tag attributes
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeBadTagAttributes($html) {
//		$attributePattern	= '/ (class|style|on\w+?)="?[^"]+"?/is';
		$attributePattern	= '/ (on\w+?)="[^"]+"?/is';

		$html	= preg_replace($attributePattern, '', $html);

		return $html;
	}


	private static function removeClassAttributes($html) {
		$attributePattern	= '/ class="?[^">]+"?/is';

		$html	= preg_replace($attributePattern, '', $html);

		return $html;
	}



	/**
	 * Remove all tag attributes
	 *
	 * @param	String		$html
	 * @return	String
	 */
	protected static function removeAllTagAttributes($html) {
		$attributePattern	= '/<(\w+) ([^\/>]+)(\/?)>/is';
		$replace			= '<\1 \3>';

		return preg_replace($attributePattern, $replace, $html);
	}



	/**
	 * Remove inline images
	 *
	 * @todo	Try to handle the inline images
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeInlineImages($html) {
		$label			= Todoyu::Label('imap.ext.inlineImageReplacement');
		$imagePattern	= '/<img[^>]+src="?cid:([^>]+)"?[\/]?>/i';
		$replacePattern	= '[' . $label. ': \1]';

		$html	= preg_replace($imagePattern, $replacePattern, $html);

		return $html;
	}



	/**
	 * Remove whitespaces
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeLinebreaksAroundTags($html) {
//		$afterPattern	= '/>[\n\r\t\s]+/i';
//		$afterReplace	= '>';
//		$html	= preg_replace($afterPattern, $afterReplace, $html);

		$beforePattern	= '/[\n\r\t\s]+<\//i';
		$beforeReplace	= '</';

		$html	= preg_replace($beforePattern, $beforeReplace, $html);

		$html	= str_replace("\t", '', $html);

		return $html;
	}



	/**
	 * Remove not allowed tags
	 * Prepare for removing tables
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeNotAllowedTags($html) {
		$html	= str_replace('</tr>', '<br />', $html);
		$html	= TodoyuCommentCommentManager::filterHtmlTags($html);

		return $html;
	}



	/**
	 * Replace useless whitespaces
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeHtmlWhitespaces($html) {
		$pattern	= "/(\n){2,}/is";
		$replace	= "\n";

		return preg_replace($pattern, $replace, trim($html));
	}



	/**
	 * Remove bad tags
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeBadTags($html) {
		$badTags	= array(
			'div',
			'o:p',
			'span'
		);

		foreach($badTags as $badTag) {
			$html	= str_ireplace('</' . $badTag . '>', '', str_ireplace('<' . $badTag . '>', '', $html));
		}

		return $html;
	}



	/**
	 * Remove inline script tags
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeInlineScriptTags($html) {
		$inlineScript	= '/<script[^>]*?(\/>|>.*?<\/script>)/is';

		return preg_replace($inlineScript, '', $html);
	}



	/**
	 * Remove inline style tags
	 *
	 * @param	String		$html
	 * @return	String
	 */
	private static function removeInlineStyleTags($html) {
		$inlineStyle	= '/<style[^>]*?>.*?<\/style>/is';

		return preg_replace($inlineStyle, '', $html);
	}

}

?>