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
 * @module	Search
 */

Todoyu.Ext.search.PanelWidget.SearchFilterList = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.search,



	/**
	 * Initialize filter list drag and drop sortable
	 *
	 * @method	init
	 */
	init: function() {
		this.initSortableList();
	},



	/**
	 * Initialize sortability of filterset list items
	 *
	 * @method	initSortableList
	 */
	initSortableList: function() {
		new Todoyu.SortablePanelList('filterset-list', this.toggleList.bind(this), this.onUpdateFiltersetSorting.bind(this));
	},



	/**
	 * Refresh filter list
	 *
	 * @method	refresh
	 */
	refresh: function() {
		var url		= Todoyu.getUrl('search', 'panelwidgetsearchfilterlist');
		var options = {
			parameters: {
				action:	'update'
			},
			onComplete: this.initSortableList.bind(this)
		};
		var target	= 'panelwidget-searchfilterlist-content';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Toggle visibility of given type's listing in widget
	 *
	 * @method	toggleList
	 * @param	{String}	type
	 */
	toggleList: function(type, isExpanded) {
		this.saveListToggle(type, isExpanded);
	},



	/**
	 * Prompt for new name and rename given filterSet
	 *
	 * @method	renameFilterset
	 * @param	{Number}	idFilterSet
	 */
	renameFilterset: function(idFilterSet) {
		var currentName	= $('filterset-' + idFilterSet + '-label').title.stripScripts().strip();
		var newName		= prompt('[LLL:search.ext.filterset.rename]', currentName);

		if( newName !== null && newName.strip() !== '' ) {
			var label = $('filterset-' + idFilterSet + '-label');
			newName = newName.stripScripts().strip();

			label.update(newName.escapeHTML());
			label.title = newName.escapeHTML();

			this.saveFiltersetRename(idFilterSet, newName);
		}
	},



	/**
	 * Hide given filterSet (visual and pref)
	 *
	 * @method	hideFilterset
	 * @param	{Number}	idFilterSet
	 */
	hideFilterset: function(idFilterSet) {
		var className	= 'invisible';
		var element 	= $('filterset_' + idFilterSet).down('.visibility');
		var isVisible	= !element.hasClassName(className);

		element.toggleClassName(className);
		element.up('li').toggleClassName(className);

		var label	= isVisible ? '[LLL:core.global.hide]' : '[LLL:core.global.unhide]' ;

		element.title	= label;
		element.update(label);

		this.saveFiltersetVisibility(idFilterSet, ! isVisible);
	},



	/**
	 * Save given existing filterSet
	 *
	 * @method	saveFilterset
	 * @param	{Number}	idFilterSet
	 * @param	{String}	tab
	 */
	saveFilterset: function(idFilterSet, tab) {
		if( tab === this.ext.Filter.getActiveTab() ) {
			if( confirm('[LLL:search.ext.filterset.confirm.overwrite]') ) {
				this.ext.Filter.saveFilterset(idFilterSet, this.onFiltersetSaved.bind(this));
			}
		} else {
			alert('[LLL:search.ext.filterset.error.saveWrongType]');
		}
	},



	/**
	 * Handler being evoked after saving of given filterSet
	 *
	 * @method	onFiltersetSaved
	 * @param	{Number}			idFilterSet
	 * @param	{Ajax.Response}		response
	 */
	onFiltersetSaved: function(idFilterSet, response) {
		var tab = this.ext.Filter.getActiveTab();
		this.showFilterset(tab, idFilterSet);
	},



	/**
	 * Save new separator
	 *
	 * @method	saveNewSeparator
	 */
	saveNewSeparator: function() {
		var title 	= prompt('[LLL:search.ext.newSeparatorLabel]', '[LLL:search.ext.newSeparatorLabel.preset]');

			// Canceled saving
		if( title === null ) {
			return;
		}
			// No name entered
		if( title.strip() === '' ) {
			alert('[LLL:search.ext.filterset.error.saveEmptyName]');
			return;
		}

			// Save separator
		var url		= Todoyu.getUrl('search', 'panelwidgetsearchfilterlist');
		var options	= {
			parameters: {
				action:		'saveNewSeparator',
				title:		title,
				type:		this.ext.Filter.getActiveTab()
			},
			onComplete:		this.refresh.bind(this)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Delete given filterSet (visual and from prefs)
	 *
	 * @method	deleteFilterset
	 * @param	{Number}	idFilterSet
	 */
	deleteFilterset: function(idFilterSet) {
		if( confirm('[LLL:search.ext.filterset.confirm.delete]') ) {
			$('filterset_' + idFilterSet).remove();
			this.saveFiltersetDelete(idFilterSet);
		}
	},



	/**
	 * Saves a new filter
	 *
	 * @method	saveNewFilterset
	 */
	saveNewFilterset: function() {
		this.ext.Filter.saveNewFilterset(this.onNewFiltersetSaved.bind(this));
	},



	/**
	 * Handler being evoked after saving of new (= creating) filterSet (evokes refresh of widget).
	 *
	 * @method	onNewFiltersetSaved
	 * @param	{Ajax.Response}		response
	 */
	onNewFiltersetSaved: function(response) {
		this.refresh();
	},



	/**
	 * Load or Refresh and activate given filterSet
	 *
	 * @method	showFilterset
	 * @param	{String}	type
	 * @param	{Number}	idFilterset
	 */
	showFilterset: function(type, idFilterset) {
		if( type === this.ext.Filter.getActiveTab() ) {
			this.ext.Filter.loadFilterset(type, idFilterset);
		} else {
			this.ext.Filter.updateFilterArea(type, idFilterset);
		}
		this.ext.Filter.setFiltersetID(idFilterset);
		this.markActiveFilterset(idFilterset);
	},



	/**
	 * Mark currently active filterSet visually
	 *
	 * @method	markActiveFilterset
	 * @param	{Number}	idFilterSet
	 */
	markActiveFilterset: function(idFilterSet) {
		this.unmarkActiveFilterset();
		$('filterset_' + idFilterSet).addClassName('current');
	},



	/**
	 * Remove current from active filterSet (called on reset)
	 *
	 * @method	unmarkActiveFilterset
	 */
	unmarkActiveFilterset: function() {
		$('panelwidget-searchfilterlist-content').select('.listItem').invoke('removeClassName', 'current');
	},



	/**
	 * Remove all conditions from filter area
	 *
	 * @method	clearFilterArea
	 */
	clearFilterArea: function() {
		this.ext.Filter.reset();
		this.unmarkActiveFilterset();
		this.ext.Filter.Sorting.removeAll(true);

		this.saveCleanArea();
	},



	/**
	 * Save preference of clean area: active filterSet, tab
	 *
	 * @method	saveCleanArea
	 */
	saveCleanArea: function() {
		var action	= 'activeFilterset';
		var value	= this.ext.Filter.getActiveTab();
		var	idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},


	/**
	 * Callback for filter item sorting change
	 *
	 * @method	onUpdateFiltersetSorting
	 * @param	{String}	type
	 * @param	{Array}		items
	 */
	onUpdateFiltersetSorting: function(type, items) {
		this.saveFiltersetOrder(type, items);
	},



	/**
	 * Save order of filterSet items (conditions)
	 *
	 * @method	saveFiltersetOrder
	 * @param	{String}	type
	 * @param	{Array}		items
	 */
	saveFiltersetOrder: function(type, items) {
		var action		= 'filtersetOrder';
		var value	= Object.toJSON({
			type:	type,
			items:	items
		});
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);

		Todoyu.notifySuccess('[LLL:search.panelwidget-searchfilterlist.notify.orderchanged]')
	},



	/**
	 * Save expanded-state of given type list
	 *
	 * @method	saveListToggle
	 * @param	{String}		type
	 * @param	{Boolean}		expanded
	 */
	saveListToggle: function(type, expanded) {
		var action	= 'filterlistToggle';
		var value	= type + ':' + ( expanded ) ? 1 : 0;
		var idItem	= 0;

		this.ext.Preference.save(action, value, idItem);
	},



	/**
	 * Save preference: given renamed title of filterSet
	 *
	 * @method	saveFiltersetRename
	 * @param	{Number}	idFilterSet
	 * @param	{String}	name
	 */
	saveFiltersetRename: function(idFilterSet, name) {
		var action	= 'renameFilterset';

		this.ext.Preference.save(action, name, idFilterSet);
	},



	/**
	 * Save preference: visibility of given filterSet
	 *
	 * @method	saveFiltersetVisibility
	 * @param	{Number}	idFilterSet
	 * @param	{Boolean}	isVisible
	 */
	saveFiltersetVisibility: function(idFilterSet, isVisible) {
		var action	= 'toggleFiltersetVisibility';
		var value	= isVisible ? 1 : 0;

		this.ext.Preference.save(action, value, idFilterSet);
	},



	/**
	 * Save preference: deleted filterSet
	 *
	 * @method	saveFiltersetDelete
	 * @param	{Number}	idFilterSet
	 */
	saveFiltersetDelete: function(idFilterSet) {
		var action	= 'deleteFilterset';
		var value	= 1;

		this.ext.Preference.save(action, value, idFilterSet);
	}

};