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
 * @module	Portal
 */

Todoyu.Ext.portal.Tab = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.portal,



	/**
	 * onSelect event handler
	 *
	 * @method	onSelect
	 * @param	{Event}		event
	 * @param	{String}	tabKey
	 */
	onSelect: function(event, tabKey) {
		this.showTab(tabKey);
	},



	/**
	 * Load given tab
	 *
	 * @method	showTab
	 * @param	{String}		tabKey
	 * @param	{Boolean}		activateTab
	 * @param	{Object}		extraParams
	 */
	showTab: function(tabKey, activateTab, extraParams) {
		var url		= Todoyu.getUrl('portal', 'tab');
		var options	= {
			parameters: {
				action:	'update',
				tab:	tabKey
			},
			onComplete: this.onTabShowed.bind(this, tabKey, extraParams)
		};
		var target	= 'content-body';

			// Add extra params
		if( extraParams ) {
			options.parameters.params = Object.toJSON(extraParams);
		}

		Todoyu.Ui.update(target, url, options);

		if( activateTab === true ) {
			Todoyu.Tabs.setActive('portal', tabKey);
		}
	},



	/**
	 * Handler when tab is showed and updated
	 *
	 * @method	onTabShowed
	 * @param	{String}			tabKey
	 * @param	{Object}			[extraParams]
	 * @param	{Ajax.Response}		[response]
	 */
	onTabShowed: function(tabKey, extraParams, response) {
		var numItems	= response.getTodoyuHeader('items') || 0;
		var filtersets	= extraParams ? extraParams.filtersets || [] : [];

		this.updateNumResults(tabKey, numItems, filtersets);

		Todoyu.Ui.scrollToTop();

		Todoyu.Hook.exec('portal.tab.showed', tabKey);
	},



	/**
	 * Update the label of a tab
	 *
	 * @method	setTabLabel
	 * @param	{String}		tabKey
	 * @param	{String}		newLabel
	 */
	setTabLabel: function(tabKey, newLabel) {
		$('portal-tab-' + tabKey + '-label').down('span.labeltext').update(newLabel);
	},



	/**
	 * Update the number of results in the tablabel
	 * Replace the number in the brackets
	 * @example	'Tasks (43)' => 'Tasks (33)'
	 *
	 * @method	updateNumResults
	 * @param	{String}			tabKey
	 * @param	{Number}			numResults
	 * @param	{Array}				[filtersets]
	 */
	updateNumResults: function(tabKey, numResults, filtersets) {
		filtersets	= filtersets || [];
		this.updateNumResultsInPortalTab(tabKey, numResults);

		if( tabKey === 'selection' && filtersets.size() === 1 ) {
			this.ext.updateResultCounterOfActiveFiltersetInList(filtersets.first(), numResults);
		}
	},



	/**
	 * Update amount of result items in portal tab
	 *
	 * @param	{String}	tabKey
	 * @param	{Number}	numResults
	 */
	updateNumResultsInPortalTab: function(tabKey, numResults) {
		Todoyu.Tabs.updateTabCounter('portal', tabKey, numResults);
	},



	/**
	 * Get number of results showed in the tab (parsed from tab label)
	 *
	 * @method	getNumResults
	 * @param	{String}	tabKey
	 * @return	{Number}
	 */
	getNumResults: function(tabKey) {
		return Todoyu.Tabs.getTabCounter('portal', tabKey);
	},



	/**
	 * Get key of currently active tab
	 *
	 * @method	getActiveTab
	 * @return	{String}
	 */
	getActiveTab: function() {
		return Todoyu.Tabs.getActiveKey('portal');
	},



	/**
	 * Check whether selection tab
	 *
	 * @return {Boolean}
	 */
	isSelectionTabActive: function() {
		return this.getActiveTab() === 'selection';
	},



	/**
	 * Refresh tabs display
	 *
	 * @method	refresh
	 */
	refresh: function() {
		this.showTab(this.getActiveTab());
	}

};