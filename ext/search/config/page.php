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

	// Add add JS inits, menu entry
if( Todoyu::allowed('search', 'general:use') ) {
//	TodoyuPage::addJsInit('Todoyu.Ext.search.init.bind(Todoyu.Ext.search)', 100);

		// Menu entries
	if( Todoyu::allowed('search', 'general:area') ) {
		TodoyuFrontend::addMenuEntry('search', 'search.ext.page.title', 'index.php?ext=search', 50);

			// Add filter types as sub menu
		$filterTypes= TodoyuSearchManager::getFilters();
		$filterTypes= TodoyuArray::sortByLabel($filterTypes, 'position');

		foreach($filterTypes as $type => $typeConfig) {
			$allowed	= true;

			if( isset($typeConfig['config']['require']) ) {
				$required	= explode('.', $typeConfig['config']['require']);
				$allowed	= Todoyu::allowed($required[0], $required[1]);
			}

				// Add entry
			if( $allowed ) {
				$parentKey	= 'search';
				$key		= 'search' . ucfirst($typeConfig['key']);
				$label		= $typeConfig['config']['label'];
				$href		= 'index.php?ext=search&amp;tab=' . $typeConfig['key'];
				$position	= $typeConfig['config']['position'] + 100;

				TodoyuFrontend::addSubmenuEntry($parentKey, $key, $label, $href, $position);
			}
		}
	}

		// Add quick search headlet
	TodoyuHeadManager::addHeadlet('TodoyuSearchHeadletQuickSearch');
}

?>