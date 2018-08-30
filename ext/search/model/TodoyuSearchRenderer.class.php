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
 * Search renderer
 *
 * @package		Todoyu
 * @subpackage	Search
 */
class TodoyuSearchRenderer extends TodoyuRenderer {

	/**
	 * Extension key
	 *
	 * @var	String
	 */
	const EXTKEY = 'search';



	/**
	 * Render search suggestion list
	 *
	 * @param	Array		$suggestions
	 * @return	String
	 */
	public static function renderSuggestions(array $suggestions) {
		$tmpl	= 'ext/search/view/suggest.tmpl';

		return Todoyu::render($tmpl, $suggestions);
	}



	/**
	 * Render headlet search box in the top panel
	 *
	 * @return	String
	 */
	public static function renderHeadlet() {
		$tmpl	= 'ext/search/view/headlet.tmpl';
		$data	= array(
			'query'			=> TodoyuRequest::getParam('query'),
			'searchModes'	=> TodoyuSearch::getSearchModes()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Render panel widgets
	 *
	 * @return	String
	 */
	public static function renderPanelWidgets() {
		return TodoyuPanelWidgetRenderer::renderPanelWidgets(self::EXTKEY);
	}



	/**
	 * Render listing of search results
	 *
	 * @param	String	$type
	 * @param	Array	$itemIDs
	 * @return	String
	 */
	public static function renderResultsListing($type, array $itemIDs) {
		$listRenderFunc	= TodoyuSearchFilterManager::getFilterTypeResultsRenderer($type);
//		TodoyuDebug::printInFirebug($listRenderFunc, 'listRenderFunc');

		return TodoyuFunction::callUserFunction($listRenderFunc, $itemIDs);
	}

}

?>