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
 * Simple (non-block) Dwoo plugins
 */

/**
 * Dwoo plugin function for label translation
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$key		label key
 * @param	String			$locale		locale (de,en,...)
 * @return	String
 */
function Dwoo_Plugin_Label_compile(Dwoo_Compiler $compiler, $key, $locale = null) {
	return 'TodoyuLabelManager::getLabel(' . $key . ', ' . $locale . ')';
}





/**
 * Dwoo plugin function for label translation with dynamic values
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo			$dwoo
 * @param	String			$key		label key
 * @param	Array			$values		Data variables
 * @param	String			$locale		locale (de_DE,en_GB,...)
 * @return	String
 */
function Dwoo_Plugin_LabelFormat(Dwoo $dwoo, $key, array $values, $locale = null) {
	return TodoyuLabelManager::getFormatLabel($key, $values, $locale);
}



/**
 * Get name of country
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idCountry
 * @return	String
 */
function Dwoo_Plugin_countryName(Dwoo $dwoo, $idCountry) {
	$idCountry	= (int) $idCountry;

	if( $idCountry > 0 ) {
		$country	= TodoyuStaticRecords::getCountry($idCountry);

		return TodoyuStaticRecords::getLabel('country', $country['iso_alpha3']);
	} else {
		return '';
	}
}



/**
 * Returns a wrapped label tag of a mail receiver, evoking person-info tooltip on rollOver
 *
 * @param	Dwoo			$dwoo
 * @param	String			$tuple
 * @param	Boolean			$encode
 * @return	String
 */
function Dwoo_Plugin_mailreceiverLabel(Dwoo $dwoo, $tuple, $encode = true) {
	$label = TodoyuMailReceiverManager::getMailReceiver($tuple)->getLabel();

	return $encode ? htmlspecialchars($label, ENT_QUOTES, 'UTF-8', false) : $label;
}



/**
 * Include given file's content with special- or all applicable characters converted to HTML character entities
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	String		$file
 * @return	String
 */
function Dwoo_Plugin_includeEscaped(Dwoo $dwoo, $file, $convertSpecialCharsOnly = true) {
	require_once( PATH . '/lib/php/dwoo/plugins/builtin/functions/include.php' );

	$content	= Dwoo_Plugin_include($dwoo, $file);

	return $convertSpecialCharsOnly == true ? htmlspecialchars($content, ENT_QUOTES, 'UTF-8', false) : htmlentities($content, ENT_QUOTES, 'UTF-8', false);
}



/**
 * Check if a value (or a list of) exists in an array
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	String		$value
 * @param	Array		$array
 * @return	Boolean
 */
function Dwoo_Plugin_inArray(Dwoo $dwoo, $value, $array) {
	if( ! is_array($value) ) {
		$value	= explode(',', $value);
	}
	if( ! is_array($array) ) {
		$array	= explode(',', $array);
	}

	$mix	= array_intersect($value, $array);

	return sizeof($mix) > 0 ;
}



/**
 * Helper function to unset array values, needed for non-referenceable parameters
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	Array		$array
 * @param	Mixed		$deletionValue
 * @return	Boolean
 */
function Dwoo_Plugin_unsetArrayValue(Dwoo $dwoo, $array, $deletionValue) {
	foreach($array as $itemKey => $itemValue) {
		if( $itemValue == $deletionValue ) {
			unset($array[$itemKey]);
		}
	}

	return $array;
}



/**
 * Encode string for HTML output
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$string
 * @param	Boolean			$doubleEncode
 * @return	String
 */
function Dwoo_Plugin_htmlencode_compile(Dwoo_Compiler $compiler, $string, $doubleEncode = false) {
	return 'htmlentities(' . $string . ', ENT_QUOTES, \'UTF-8\', ' . $doubleEncode . ')';
}



/**
 * Encode quotes to not interfere with html attribute quote parting
 * " => \042
 * ' => \047
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$string
 * @return	String
 */
function Dwoo_Plugin_escapeQuotesForHtmlAttributes_compile(Dwoo_Compiler $compiler, $string) {
	return 'TodoyuString::escapeQuotesForHtmlAttributes(' . $string . ')';
}



/**
 * Format an integer to hours:minutes:seconds
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$seconds
 * @return	String
 */
function Dwoo_Plugin_HourMinSec_compile(Dwoo_Compiler $compiler, $seconds) {
	return 'TodoyuTime::formatTime(' . $seconds . ', true)';
}



/**
 * Format an integer to hours:minutes:seconds
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$seconds
 * @return	String
 */
function Dwoo_Plugin_HourMin_compile(Dwoo_Compiler $compiler, $seconds) {
	return 'TodoyuTime::formatTime(' . $seconds . ', false)';
}



/**
 * Get formatted file size
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$fileSize
 * @return	String
 */
function Dwoo_Plugin_filesize_compile(Dwoo_Compiler $compiler, $fileSize) {
	return 'TodoyuString::formatSize(' . $fileSize . ')';
}



/**
 * Limit string length to given length
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$string
 * @param	Integer			$maxLen
 * @param	Boolean			$dontSplitWords
 * @return	String
 */
function Dwoo_Plugin_cropText_compile(Dwoo_Compiler $compiler, $string, $maxLen, $dontSplitWords = true) {
	return 'TodoyuString::crop(' . $string . ', ' . $maxLen . ', \'..\', ' . $dontSplitWords . ')';
}



/**
 * Render numeric value with (at least) two digits
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$value
 * @return	String
 */
function Dwoo_Plugin_twoDigits_compile(Dwoo_Compiler $compiler, $value) {
	return 'sprintf(\'%02d\', ' . $value . ')';
}



/**
 * Debug some variable inside a Dwoo template
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	Mixed		$variable
 * @param	Boolean		$phpFormat
 * @return	String
 */
function Dwoo_Plugin_debug(Dwoo $dwoo, $variable, $phpFormat = false) {
	if ( $phpFormat ) {
			// Use PHP syntax formatting
		TodoyuDebug::printPHP($variable);

		return '';
	} else {
			// Simple print_r
		return '<pre style="z-index:200; background-color:#fff;">' . print_r($variable, true) . '</pre>';
	}
}



/**
 * View some variable (from inside a Dwoo template) in firebug
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	Mixed		$variable
 * @param	String		$label
 */
function Dwoo_Plugin_firebug(Dwoo $dwoo, $variable, $label = 'dwoo debug') {
	TodoyuDebug::printInFirebug($variable, $label);
}



/**
 * Special Todoyu date format. Format a date based on registered key in the core
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler		Dwoo compiler
 * @param	Integer			$timestamp		Timestamp to format
 * @param	String			$formatName		Format name
 * @return	String
 */
function Dwoo_Plugin_dateFormat_compile(Dwoo_Compiler $compiler, $timestamp, $formatName) {
	return 'TodoyuTime::format(' . $timestamp . ', ' . $formatName . ')';
}



/**
 * Special Todoyu time format. Format time like 23:59 (or 23:59:59)
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler		Dwoo compiler
 * @param	Integer			$timestamp		Timestamp to format
 * @param	Boolean			$withSeconds
 * @param	Boolean			$round
 * @return	String
 */
function Dwoo_Plugin_timeFormat_compile(Dwoo_Compiler $compiler, $timestamp, $withSeconds = false, $round = true) {
	return 'TodoyuTime::formatTime(' . $timestamp . ', ' . $withSeconds . ', ' . $round . ')';
}



/**
 * Format an SQL datetime string as date format of current locale
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler		Dwoo compiler
 * @param	Integer			$date			Timestamp to format
 * @param	String			$format			Format
 * @return	String
 */
function Dwoo_Plugin_formatSqlDate_compile(Dwoo_Compiler $compiler, $date, $format = 'date') {
	return 'TodoyuTime::formatSqlDate(' . $date . ', ' . $format . ')';
}



/**
 * Clean bad tags from HTML code
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler		Dwoo compiler
 * @param	String			$html
 * @return	String
 */
function Dwoo_Plugin_cleanHtml_compile(Dwoo_Compiler $compiler, $html) {
	return 'TodoyuHtmlFilter::clean(' . $html . ')';
}



/**
 * Substitute URLs by hyperlinks
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_linkUrls_compile(Dwoo_Compiler $compiler, $text) {
	return 'TodoyuString::replaceUrlWithLink(' . $text . ')';
}



/**
 * Substitute registered linkable elements in given text by their respective hyperlinks
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_substituteLinkableElements_compile(Dwoo_Compiler $compiler, $text) {
	return 'TodoyuString::substituteLinkableElements(' . $text . ')';
}



/**
 * Button template
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	String		$label		Button text
 * @param	String		$onclick	onClick JavaScript handler
 * @param	String		$class		CSS class
 * @param	Integer		$id			HTML id
 * @param	String		$title
 * @param	String		$type
 * @param	Boolean		$disable
 * @param	Boolean		$disabled
 * @return	String
 */
function Dwoo_Plugin_Button(Dwoo $dwoo, $label = '', $onclick = '', $class ='', $id = '', $title = '', $type = '', $disable = false, $disabled = false, $style = '') {
	$tmpl	= 'core/view/button.tmpl';
	$data	= array(
		'label'		=> $label,
		'onclick'	=> $onclick,
		'class'		=> $class,
		'id'		=> $id,
		'title'		=> $title,
		'type'		=> $type,
		'disable'	=> $disable  ? true : false,
		'disabled'	=> $disabled ? true : false,
		'style'		=> $style
	);

	return Todoyu::render($tmpl, $data);
}



/**
 * Header template
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	String		$title
 * @param	String		$class
 * @param	Boolean		$encode
 * @return	String
 */
function Dwoo_Plugin_Header(Dwoo $dwoo, $title, $class = '', $encode = true) {
	$tmpl	= 'core/view/headerLine.tmpl';
	$data	= array(
		'title'		=> Todoyu::Label($title),
		'class'		=> $class,
		'encode'	=> $encode
	);

	return Todoyu::render($tmpl, $data);
}



/**
 * Build page content title
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$title
 * @return	String
 */
function Dwoo_Plugin_Title_compile(Dwoo_Compiler $compiler, $title) {
	return '\'<h5>\' . htmlentities(Todoyu::Label(' . $title . '), ENT_QUOTES, \'UTF-8\', false) . \'</h5>\'';
}



/**
 * Check whether right is given. Get function string to check this
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$ext
 * @param	String			$right
 * @return	String
 */
function Dwoo_Plugin_allowed_compile(Dwoo_Compiler $compiler, $ext, $right) {
	return 'TodoyuRightsManager::isAllowed(' . $ext . ',' . $right . ')';
}



/**
 * Check if all given rights are allowed. Get function string to check this
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$ext
 * @param	String			$rightsList
 * @return	String
 */
function Dwoo_Plugin_allowedAll_compile(Dwoo_Compiler $compiler, $ext, $rightsList) {
	return 'Todoyu::allowedAll(' . $ext . ',' . $rightsList . ')';
}



/**
 * Check whether any of the given rights are allowed. Get function string to check this
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$ext
 * @param	String			$rightsList
 * @return	String
 */
function Dwoo_Plugin_allowedAny_compile(Dwoo_Compiler $compiler, $ext, $rightsList) {
	return 'Todoyu::allowedAny(' . $ext . ',' . $rightsList . ')';
}



/**
 * Check whether user has right, or given user ID is the current users ID. Get function string to check this
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$ext
 * @param	String			$right
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_allowedOrOwn_compile(Dwoo_Compiler $compiler, $ext, $right, $idPerson) {
	return 'TodoyuRightsManager::isAllowed(' . $ext . ',' . $right . ') || Todoyu::personid()==' . $idPerson;
}



/**
 * Check if user has right and given user ID is the current users ID
 * Get function string to check this
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$ext
 * @param	String			$right
 * @param	Integer			$idPerson
 * @return	Boolean
 */
function Dwoo_Plugin_allowedAndOwn_compile(Dwoo_Compiler $compiler, $ext, $right, $idPerson) {
	return 'TodoyuRightsManager::isAllowed(' . $ext . ',' . $right . ') && Todoyu::personid()==' . $idPerson;
}



/**
 * Subtract given subtrahend from given minuend
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Mixed			$minuend
 * @param	Mixed			$subtrahend
 * @return	Integer			difference
 */
function Dwoo_Plugin_subtract_compile(Dwoo_Compiler $compiler, $minuend, $subtrahend) {
	return '(floatval(' . $minuend . ')-floatval(' . $subtrahend . '))';
}



/**
 * Convert HTML code to text, keep as much format as possible
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$html
 * @param	Boolean			$decodeEntity
 * @return	String			Text version
 */
function Dwoo_Plugin_html2text_compile(Dwoo_Compiler $compiler, $html, $decodeEntity = false) {
	return 'TodoyuString::html2text(' . $html . ', ' . $decodeEntity . ')';
}



/**
 * Render select element with options
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	Array		$options
 * @param	Array		$value		Array to allow for multi selection
 * @param	String		$id			HTML id
 * @param	String		$name		HTML name
 * @param	String		$class
 * @param	Integer		$size
 * @param	Boolean		$multiple
 * @param	Boolean		$disabled
 * @param	String		$onchange
 * @param	String		$onclick
 * @param	Boolean		$noPleaseSelect
 * @param	Array		$value
 * @return	String
 */
function Dwoo_Plugin_select(Dwoo $dwoo, array $options, array $value = array(), $id = '', $name = '', $class = '', $size = 0, $multiple = false, $disabled = false, $onchange = '', $onclick = '', $noPleaseSelect = false) {
	$tmpl	= 'core/view/select.tmpl';
	$data	= array(
		'htmlId'		=> $id,
		'htmlName'		=> $name,
		'class'			=> $class,
		'size'			=> $size == 0 ? sizeof($options) : $size,
		'multiple'		=> $multiple,
		'disabled'		=> $disabled,
		'onchange'		=> $onchange,
		'onclick'		=> $onclick,
		'value'			=> $value,
		'options'		=> $options,
		'noPleaseSelect'=> $noPleaseSelect
	);

		// Append brackets to ensure multiple values are submitted
	if( $multiple ) {
		if( $data['htmlName'] !== '' && substr($data['htmlName'], -2) !== '[]' ) {
			$data['htmlName'] .= '[]';
		}
	}

	return Todoyu::render($tmpl, $data);
}



/**
 * Render select element with grouped options
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo 		$dwoo
 * @param	Array		$options
 * @param	Array		$value		Array to allow for multi selection
 * @param	String		$id			HTML id
 * @param	String		$name		HTML name
 * @param	String		$class
 * @param	Integer		$size
 * @param	Boolean		$multiple
 * @param	Boolean		$disabled
 * @param	String		$onchange
 * @param	String		$onclick
 * @param	Boolean		$noPleaseSelect
 * @param	Array		$value
 * @return	String
 */
function Dwoo_Plugin_selectGrouped(Dwoo $dwoo, array $options, array $value = array(), $id = '', $name = '', $class = '', $size = 0, $multiple = false, $disabled = false, $onchange = '', $onclick = '', $noPleaseSelect = false) {
	$data	= array(
		'id'			=> $id,
		'name'			=> $name,
		'class'			=> $class,
		'size'			=> $size,
		'multiple'		=> $multiple,
		'disabled'		=> $disabled,
		'onchange'		=> $onchange,
		'onclick'		=> $onclick,
		'value'			=> $value,
		'options'		=> $options,
		'noPleaseSelect'=> $noPleaseSelect
	);

	return TodoyuRenderer::renderSelectGrouped($data);
}



/**
 * Replace line breaks "\n" with ODT style line breaks
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_OdtLinebreaks_compile(Dwoo_Compiler $compiler, $text) {
	return 'str_replace("\n", \'<text:line-break/>\', ' . $text . ')';
}


/**
 * Replace spaces with &nbsp; entities
 * Prevent line breaks on spaces
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	String			$text
 * @return	String
 */
function Dwoo_Plugin_nobreak_compile(Dwoo_Compiler $compiler, $text) {
	return 'str_replace(\' \', \'&nbsp;\', ' . $text . ')';
}



/**
 * Check in template if user is logged in
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler		$compiler
 * @return	String
 */
function Dwoo_Plugin_isLoggedIn_compile(Dwoo_Compiler $compiler) {
	return 'TodoyuAuth::isLoggedIn()';
}



/**
 * Build timerange selector HTML code
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	String		$id
 * @param	String		$name
 * @param	Array		$range
 * @param	String		$nameWrap
 * @return	String
 */
function Dwoo_Plugin_timerange_compile(Dwoo_Compiler $compiler, $id, $name, $range = array(), $nameWrap = '') {
	return 'TodoyuRenderer::renderTimerange(' . $id . ', ' . $name . ', ' . $range . ', ' . $nameWrap . ')';
}



/**
 * Render content message with label
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler $compiler
 * @param	String		$label
 * @param	String		$class
 * @param	String		$content
 * @return	String
 */
function Dwoo_Plugin_contentMessage_compile(Dwoo_Compiler $compiler, $label, $class = '', $content = '') {
	return 'Todoyu::render(\'core/view/contentMessage.tmpl\', array(\'labels\'=>explode(\'|\', ' . $label . '),\'class\'=>' . $class . ',\'content\'=>' . $content . '))';
}



/**
 * Render CSS classnames from boolean attribute names+values of given record. E.g. is_preferred => isPreferred0 / isPreferred1
 *
 * @param	Dwoo	$dwoo
 * @param	Array	$record
 * @return	String
 */
function Dwoo_Plugin_getRecordBooleanColumnsClassnames(Dwoo $dwoo, array $record) {
	$classNames = '';

	foreach($record as $columnKey => $columnValue) {
			// Boolean field column containing TRUE
		if( substr($columnKey, 0, 3) === 'is_' ) {
			$classParts = explode('_', $columnKey);
			foreach($classParts as $index => $part) {
				$classNames .= ($index > 0 ? ucfirst($part) : strtolower($part));
			}
			$classNames .= $columnValue . ' ';
		}
	}

	return trim($classNames);
}



/**
 * Render duration in suiting format
 *
 * @package		Todoyu
 * @subpackage	Calendar
 *
 * @param	Dwoo_Compiler 		$compiler
 * @param	Integer			$seconds
 * @return	String
 */
function Dwoo_Plugin_formatDuration_compile(Dwoo_Compiler $compiler, $seconds) {
	return 'TodoyuTime::formatDuration(' . $seconds . ')';
}



/**
 * Render timespan in suiting format
 *
 * @package		Todoyu
 * @subpackage	Calendar
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$dateStart
 * @param	Integer			$dateEnd
 * @return	String
 */
function Dwoo_Plugin_formatRange_compile(Dwoo_Compiler $compiler, $dateStart, $dateEnd) {
	return 'TodoyuTime::formatRange(' . $dateStart . ', ' . $dateEnd . ')';
}



/**
 * Format the amount of hours
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler 	$compiler
 * @param	Integer			$workload
 * @param	Boolean			$leadingZero
 * @return	String
 */
function Dwoo_Plugin_formatHours_compile(Dwoo_Compiler $compiler, $workload, $leadingZero = false) {
	return 'TodoyuTime::formatHours(' . $workload . ', ' . $leadingZero . ')';
}



/**
 * Plugin for listing renderer
 *
 * @param	Dwoo		$dwoo
 * @param	String		$ext
 * @param	String		$list
 * @param	Integer		$offset
 * @param	Boolean		$noPaging
 * @param	Array		$params
 * @return	String
 */
function Dwoo_Plugin_List(Dwoo $dwoo, $ext, $list, $offset = 0, $noPaging = false, array $params = array()) {
	return TodoyuListingRenderer::render($ext, $list, $offset, $noPaging, $params);
}



/**
 * Render balloon info
 *
 * @param 	Dwoo_Compiler	$compiler
 * @param 	String			$label
 * @param 	String			$id
 * @param 	String 			$content		If not given: renders the label as content
 * @param 	Integer			$balloonWidth	Default 200 is set via CSS
 * @return	String
 */
function Dwoo_Plugin_infoBalloon_compile(Dwoo_Compiler $compiler, $label, $id = '', $content = '', $balloonWidth = 0) {
	return 'Todoyu::render(\'core/view/infoballoon.tmpl\', array(\'label\'=> ' . $label . ',\'id\'=>' . $id . ',\'content\'=>' . $content . ',\'balloonWidth\'=>' . $balloonWidth . '))';
}

?>