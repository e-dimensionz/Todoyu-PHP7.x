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
 * @module	Core
 */

/**
 * @namespace	Todoyu
 * @class		ContentItemTab
 */
Todoyu.ContentItemTab = {

	/**
	 * Handle onSelect event of tab: show affected tab which the event occured on
	 *
	 * @method	onSelect
	 * @param	{String}	extKey
	 * @param	{Event}		event
	 */
	onSelect: function(extKey, event) {
		var idParts	= event.findElement('li').id.split('-');

		this.show(idParts[1], idParts[3], idParts[0], extKey);
	},



	/**
	 * Show given tab of given Item
	 *
	 * @method	show
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey
	 * @param	{String}	itemKey
	 * @param	{String}	extKey
	 * @param	{Function}	onComplete
	 */
	show: function(idItem, tabKey, itemKey, extKey, onComplete) {
		var tabContainer = this.buildTabID(idItem, tabKey, itemKey);

		if( ! Todoyu.exists(tabContainer) ) {
			this.createTabContainer(idItem, tabKey, itemKey);
			this.load(idItem, tabKey, itemKey, extKey, onComplete);
			this.activate(idItem, tabKey, itemKey);
		} else {
			this.saveSelection(idItem, tabKey, itemKey, extKey);
			this.activate(idItem, tabKey, itemKey);
			Todoyu.callIfExists(onComplete, this, idItem, tabKey, itemKey);
		}
	},



	/**
	 * Load given tab of given item
	 *
	 * @method	load
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 * @param	{String}	extKey
	 * @param	{Function}	onComplete
	 */
	load: function(idItem, tabKey, itemKey, extKey, onComplete) {
		var url 	= Todoyu.getUrl('core', 'contenttab');
		var options	= {
			parameters: {
				action:		'tabload',
				idItem:		idItem,
				tabKey:		tabKey,
				itemKey:	itemKey,
				extKey:		extKey
			},
			onComplete:	this.onLoaded.bind(this, idItem, tabKey, itemKey, onComplete)
		};

		var tabDiv	= this.buildTabID(idItem, tabKey, itemKey);
		Todoyu.Ui.update(tabDiv, url, options);
	},



	/**
	 * Handler when tab is loaded
	 *
	 * @method	onLoaded
	 * @param	{Number}		idItem
	 * @param	{String}		tabKey
	 * @param	{String}		itemKey
	 * @param	{Function}		onComplete callback
	 */
	onLoaded: function(idItem, tabKey, itemKey, onComplete) {
		this.activate(idItem, tabKey, itemKey);
		Todoyu.callIfExists(onComplete, this, idItem, tabKey, itemKey);

		Todoyu.Hook.exec('core.contentTab.onLoaded', idItem, itemKey, tabKey);
	},



	/**
	 * Check if a tab of a item is already loaded
	 *
	 * @method	isLoaded
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey
	 * @param	{String}	itemKey
	 */
	isLoaded: function(idItem, tabKey, itemKey) {
		return Todoyu.exists(itemKey + '-' + idItem + '-tabcontent-' + tabKey);
	},



	/**
	 * Create tab container to given item.
	 *
	 * @method	creatTabContainer
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 */
	createTabContainer: function(idItem, tabKey, itemKey) {
		$(itemKey + '-' + idItem + '-tabcontent').insert({
			top: new Element(
				'div', {
					id:		this.buildTabID(idItem, tabKey, itemKey),
					'class':	'tab ' + tabKey
				}
			)
		});
	},



	/**
	 * Render element ID of given tab of given item
	 *
	 * @method	buildTabID
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 * @return	{String}
	 */
	buildTabID: function(idItem, tabKey, itemKey) {
		return itemKey + '-' + idItem + '-tabcontent-' + tabKey;
	},



	/**
	 * Activate given tab of given item: hide other tabs, activate tab head, set tab content visible
	 *
	 * @method	activate
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 */
	activate: function(idItem, tabKey, itemKey) {
		this.hideAll(idItem, itemKey);
		this.setVisible(idItem, tabKey, itemKey);
		Todoyu.Tabs.setActive(itemKey + '-' + idItem, tabKey);
	},



	/**
	 * Save given item's selected (given) tab
	 *
	 * @method	saveSelection
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 * @param	{String}	extKey
	 */
	saveSelection: function(idItem, tabKey, itemKey, extKey) {
		var url = Todoyu.getUrl('core', 'contenttab');
		var options	= {
			parameters: {
				action:		'tabselected',
				idItem:		idItem,
				tabKey:		tabKey,
				itemKey:	itemKey,
				extKey:		extKey
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Hide all tabs of given item
	 *
	 * @method	hideAll
	 * @param	{Number}	idItem
	 * @param	{String}	itemKey
	 */
	hideAll: function(idItem, itemKey) {
		this.getContainer(idItem, itemKey).select('.tab').invoke('hide');
	},




	/**
	 * Set given tab of given item visible
	 *
	 * @method	setVisible
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 */
	setVisible: function(idItem, tabKey, itemKey) {
		$(this.buildTabID(idItem, tabKey, itemKey)).show();
	},



	/**
	 * Get tabs container element of given item
	 *
	 * @method	getContainer
	 * @param	{Number}	idItem
	 * @param	{String}	itemKey
	 * @return	{Element}
	 */
	getContainer: function(idItem, itemKey) {
		return $(itemKey + '-' + idItem + '-tabcontainer');
	},



	/**
	 * Get tab head ID of given tab of given item
	 *
	 * @method	getHeadID
	 * @param	{Number}	idItem
	 * @param	{String}	tabKey	(e.g 'timetracking' / 'comment' / 'assets')
	 * @param	{String}	itemKey
	 * @return	{String}
	 */
	getHeadID: function(idItem, tabKey, itemKey) {
		return itemKey + '-' + idItem + '-tabhead-' + tabKey;
	},



	/**
	 * Extract tabKey (e.g 'timetracking' / 'comment' / 'assets') out of item ID
	 *
	 * @method	getKeyFromID
	 * @param	{Number}	idItem
	 * @return	{String}
	 */
	getKeyFromID: function(idItem) {
		return idItem.split('-').last();
	}

};