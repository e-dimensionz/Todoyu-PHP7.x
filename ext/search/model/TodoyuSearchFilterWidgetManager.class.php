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
 * Manager for the filter widgets
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchFilterWidgetManager	{

	/**
	 * Get configuration array for a widget
	 *
	 * @param	String		$type
	 * @param	String		$widgetName
	 * @return	Array
	 */
	public static function getWidgetConfig($type, $widgetName) {
		TodoyuExtensions::loadAllFilters();

		$type		= strtoupper(trim($type));
		$widgetName	= trim($widgetName);

		$config		= TodoyuSearchFilterManager::getFilterConfig($type, $widgetName);

		if( !$config ) {
			return array();
		}

			// Add default negation labels if negation is just true
		if( gettype($config['wConf']['negation']) === 'string' ) {
			$config['wConf']['negation'] = array(
				'labelTrue'	=> 'search.ext.negation.' . $config['wConf']['negation'] . '.true',
				'labelFalse'=> 'search.ext.negation.' . $config['wConf']['negation'] . '.false'
			);
		}

			// If no configuration available, log
		if( sizeof($config) === 0 ) {
			TodoyuLogger::logError('Filter widget not found', array($type, $widgetName));
		}

		return $config;
	}



	/**
	 * Get type configuration for a field type of a widget
	 *
	 * @param	String		$type
	 * @return	Array
	 */
	public static function getWidgetTypeConfig($type) {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT']['search']['widgettypes'][$type]);
	}



	/**
	 * Get extended widget configuration
	 * Extends the normal widget config with: widgetID, widgetDefinitions,	widgetFilterName, value, negate
	 *
	 * @param	String		$type
	 * @param	String		$widgetKey
	 * @param	String		$widgetName
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public static function getExtendedWidgetConfig($type, $widgetKey, $widgetName = 'new1', $value = '', $negate = false) {
		$config		= self::getWidgetConfig($type, $widgetKey);

		$extend		= array(
			'widgetID'			=> $widgetKey . '-' . $widgetName,
			'widgetDefinitions'	=> self::getWidgetTypeConfig($config['widget']),
			'widgetFilterName'	=> $widgetKey,
			'value'				=> $value,
			'negate'			=> $negate
		);

		$config = array_merge($config, $extend);

		if( TodoyuFunction::isFunctionReference($config['widgetDefinitions']['configFunc']) ) {
			$config = TodoyuFunction::callUserFunction($config['widgetDefinitions']['configFunc'], $config);
		}

		return $config;
	}



	/**
	 * Prepare definition of given filter widget
	 *
	 * @param	String	$filterType
	 * @param	String	$widgetName
	 * @param	String	$numOfWidget
	 * @param	Mixed	$value
	 * @param	Boolean	$negate
	 * @return	Array
	 */
	public static function getFilterWidgetDefinitions($filterType, $widgetName, $numOfWidget, $value = '', $negate = false) {
		$definitions = self::getFilterDefinitionsArray($filterType, $widgetName);

		$definitions['widgetDefinitions'] = self::getWidgetTypeConfig($definitions['widget']);

			// Create ID for the widget
		$definitions['widgetID'] = $widgetName . '-' . $numOfWidget;

			// Add filter name to widget
		$definitions['widgetFilterName'] = $widgetName;

			// Add value to definitions
		$definitions['value'] = $value;

			// Add negate value to definitions
		$definitions['negate'] = $negate;

		if( TodoyuFunction::isFunctionReference($definitions['widgetDefinitions']['configFunc']) ) {
			$definitions = TodoyuFunction::callUserFunction($definitions['widgetDefinitions']['configFunc'], $definitions);
		}

		return $definitions;
	}



	/**
	 * Check whether template of given widget definition exists
	 *
	 * @param	Array	$widgetDefinitions
	 * @return	Mixed	String / Boolean
	 */
	public static function checkOnWidgetTemplate($widgetDefinitions) {
		$file = $widgetDefinitions['widgetDefinitions']['tmpl'];

		return is_file($file) ? $file : false;
	}



	/**
	 * Get autoCompletion suggestions to given input of given record type
	 *
	 * @param	String		$type
	 * @param	String		$sword
	 * @param	String		$widgetKey
	 * @return	Array
	 */
	public static function getAutocompletionResults($type, $sword, $widgetKey) {
		$widgetKeyArray	= explode('-', $widgetKey);

		$widgetName		= $widgetKeyArray[0];
		$numOfWidget	= $widgetKeyArray[1];

		$definitions = self::getFilterWidgetDefinitions($type, $widgetName, $numOfWidget);

		$funcRefString = $definitions['wConf']['FuncRef'];
		$funcRefParams = TodoyuArray::assure($definitions['wConf']['FuncParams']);

		if( TodoyuFunction::isFunctionReference($funcRefString) ) {
			$results = TodoyuFunction::callUserFunction($funcRefString, $sword, $funcRefParams);
		} else {
			TodoyuLogger::logError('Invalid AC-callback function', array('widget'=>$widgetName, 'acFunc'=>$funcRefString));
			$results = array();
		}

		return $results;
	}



	/**
	 * Handles the option func of every select-filter-widget.
	 * Function given from config array.
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function prepareSelectionOptions($definitions) {
		$optionMethod = $definitions['wConf']['FuncRef'];

		if( TodoyuFunction::isFunctionReference($optionMethod) ) {
			$definitions = TodoyuFunction::callUserFunction($optionMethod, $definitions);
		}

		return $definitions;
	}



	/**
	 * handles the given manipulation function for autoCompletion to set the correct label
	 *
	 * defined in filters config (LabelFuncRef)
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	public function manipulateAutocompleteDefinitions($definitions) {
		$optionMethod = $definitions['wConf']['LabelFuncRef'];

		if( $definitions['wConf']['autocomplete'] == true && intval($definitions['value']) > 0 ) {
			if( TodoyuFunction::isFunctionReference($optionMethod) ) {
				$definitions = TodoyuFunction::callUserFunction($optionMethod, $definitions);
//				self::proceedLabelFunc($definitions);
			}
		}

		return $definitions;
	}



	/**
	 * Gets Negation labels
	 *
	 * @param	String	$widgetName
	 * @param	String	$label
	 * @return	String
	 */
	public static function getFilterWidgetNegationLabel($widgetName, $label) {
		$filterType = TodoyuSearchPreferences::getActiveTab();

		return Todoyu::$CONFIG['FILTERS'][strtoupper($filterType)]['widgets'][$widgetName]['wConf']['negation'][$label];
	}



	/**
	 * Proceeds the label function
	 *
	 * @param	Array	$definitions
	 * @return	Array
	 */
	protected static function proceedLabelFunc($definitions) {
		$methodString = $definitions['wConf']['LabelFuncRef'];

		if( TodoyuFunction::isFunctionReference($methodString) ) {
			$definitions = TodoyuFunction::callUserFunction($methodString, $definitions);
		}

		return $definitions;
	}



	/**
	 * Gets the filter definitions
	 *
	 * @param	String	$filterType
	 * @param	String	$widgetName
	 * @return	Array
	 */
	protected static function getFilterDefinitionsArray($filterType, $widgetName) {
		TodoyuExtensions::loadAllFilters();

		$filterType	= strtoupper(trim($filterType));
		$widgetName	= trim($widgetName);

		$definitions	= TodoyuArray::assure(Todoyu::$CONFIG['FILTERS'][$filterType]['widgets'][$widgetName]);

		if( sizeof($definitions) === 0 ) {
			TodoyuLogger::logError('Widget definitions not found', array($filterType, $widgetName));
		}

		return $definitions;
	}

}

?>