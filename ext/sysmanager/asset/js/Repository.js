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
 * @module	Sysmanager
 */

/**
 * System manager repository connection
 *
 * @class		Repository
 * @namespace	Todoyu.Ext.sysmanager
 */
Todoyu.Ext.sysmanager.Repository = {

	ext: Todoyu.Ext.sysmanager,

	/**
	 * Dialog popup instance
	 *
	 * @property	dialog
	 * @type		Todoyu.Popup
	 */
	dialog: null,

	/**
	 * Initialize repository
	 *
	 * @method	init
	 */
	init: function() {
		this.Search.init();
		this.Update.init();
	},



	/**
	 * Get repository URL
	 *
	 * @method	getUrl
	 * @return	{String}
	 */
	getUrl: function() {
		return Todoyu.getUrl('sysmanager', 'repository');
	},



	/**
	 * Open tER in new browser window
	 *
	 * @method	moreExtensionInfo
	 * @param	{String}	terLink
	 */
	showExtensionInTER: function(terLink) {
		window.open(terLink, '_blank');
	},



	/**
	 * Show dialog for an extension
	 *
	 * @method	showExtensionDialog
	 * @param	{String}	extkey
	 * @param	{String}	action
	 * @param	{String}	title
	 * @param	{Function}	[callback]
	 * @param	{Boolean}	[local]
	 */
	showExtensionDialog: function(extkey, action, title, callback, local) {
		callback	= callback || Prototype.emptyFunction;
		var url		= this.getUrl();
		var options	= {
			parameters: {
				action:		action,
				extension:	extkey,
				local:		local ? 1 : 0
			},
			onComplete: callback
		};

		this.dialog = Todoyu.Popups.open(action, title, 600, url, options);
	},



	/**
	 * Close confirm dialog
	 *
	 * @method	closeDialog
	 */
	closeDialog: function() {
		if( this.dialog ) {
			this.dialog.close();
		}
	},



	/**
	 * Install observers on dependency and conflict lists
	 *
	 * @method	installWarningsObservers
	 */
	installWarningsObservers: function() {
		$('content-body').select('.warning ul').each(function(list){
			var type = list.up('.dependency') ? 'dependency' : 'conflict';

			list.on('click', 'li', this.onWarningClick.bind(this, type));
		}, this);
	},



	/**
	 * Handler when clicked on a warning
	 *
	 * @method	onWarningClick
	 * @param	{String}	type
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	onWarningClick: function(type, event, element) {
		var extKey	= element.id.split('-').last();

		if( this.getViewName() === 'update' ) {
			if( type === 'conflict' ) {
				this.showInstalledExtensions();
			} else {
				this.showSearch(extKey);
			}
		} else {
			if( type === 'conflict' ) {
				this.showInstalledExtensions();
			} else {
				this.showSearch(extKey);
			}
		}
	},



	/**
	 * Show list with installed extensions
	 *
	 * @method	showInstalledExtensions
	 */
	showInstalledExtensions: function() {
		this.ext.Extensions.showList();
	},



	/**
	 * Show search with results for query
	 *
	 * @method	showSearch
	 * @param	{String}	query
	 */
	showSearch: function(query) {
		this.ext.Extensions.showTab(null, 'search', null, function(query){
			this.Search.searchFor(query);
		}.bind(this, query));
	},



	/**
	 * Get name of current view ("search" or "update")
	 *
	 * @method	getViewName
	 * @return	{String}
	 */
	getViewName: function() {
		return Todoyu.Tabs.getActiveKey('extension');
	}

};