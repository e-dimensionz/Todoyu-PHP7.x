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

/**
 * Panel widget: filter preset list
 */
Todoyu.Ext.portal.PanelWidget.FilterPresetList = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.portal,

	/**
	 * List of all select items with filtersets
	 *
	 * @property	lists
	 * @type		Object
	 */
	lists: {},



	/**
	 * Init
	 *
	 * @method	init
	 */
	init: function() {
			// Find all lists
		var lists = $('panelwidget-filterpresetlist-content').select('select');

			// Add lists to internal storage and install an onchange observer
		lists.each(function(list){
			var type = list.id.split('-').last();
			this.lists[type] = list;

			list.on('change', this.onSelectionChange.bind(this, type));

				// Fix height of selects in webkit/chrome (but not on mobile phones/iPhones)
			if( Prototype.Browser.WebKit && !Prototype.Browser.MobileSafari ) {
				list.setStyle({
					height: list.select('option').size() * 15 + 'px'
				});
			}
		}, this);
	},



	/**
	 * Handler when selection in one of the lists is changed
	 *
	 * @method	onSelectionChange
	 * @param	{String}		type		List type
	 * @param	{Event}			event
	 */
	onSelectionChange: function(type, event) {
			// Deselect all other option groups
		this.deselectOtherTypes(type);

			// Add params for tab refresh
		var params	= {
			filtersets: this.getFiltersets(),
			type:		type
		};

			// Refresh tab content
		this.ext.Tab.showTab('selection', true, params);
	},



	/**
	 * Get selected filterset IDs
	 *
	 * @method	getFiltersets
	 * @return	{String[]}
	 */
	getFiltersets: function() {
		return $H(this.lists).collect(function(pair){
			return $F(pair.value);
		}).flatten();
	},



	/**
	 * Update result counter for filterset in list
	 *
	 * @param	{Number}	idFilterset
	 * @param	{Number}	numResults
	 */
	updateFiltersetResultCounter: function(idFilterset, numResults) {
		var filtersetElement = this.getFiltersetOptionElement(idFilterset);

		if( filtersetElement ) {
			var label	= Todoyu.String.replaceCounter(filtersetElement.innerHTML, numResults);

			filtersetElement.update(label);
		}
	},



	/**
	 * Get option element in list for filterset
	 *
	 * @param	{Number}	idFilterset
	 * @return	{String}
	 */
	getFiltersetOptionElement: function(idFilterset) {
		return $('panelwidget-filterpresetlist-content').down('option[value=' + idFilterset + ']');
	},



	/**
	 * Deselect all options in the other lists, because only one type can be active
	 *
	 * @method	deselectOtherTypes
	 * @param	{String}		type
	 */
	deselectOtherTypes: function(type) {
		$H(this.lists).each(function(type, pair){
			if( pair.key !== type ) {
				pair.value.select('option').each(function(option){
					option.selected = false;
				});
			}
		}.bind(this, type));
	},



	/**
	 * Manage filtersets
	 *
	 * @method	manageFiltersets
	 */
	manageFiltersets: function() {
		Todoyu.goTo('search', 'ext');
	}

};