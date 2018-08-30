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

Todoyu.Ext.sysmanager.Extensions = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.sysmanager,



	/**
	 * Show extension list in sysmanager
	 *
	 * @method	showList
	 */
	showList: function() {
		this.showTab();
		this.activateTab('list');
	},



	/**
	 * Show given extension tab in sysmanager
	 *
	 * @method	showTab
	 * @param	{String}	extKey
	 * @param	{String}	tab			'info' / 'config' (/'extensions')
	 * @param	{Object}	params
	 */
	showTab: function(extKey, tab, params, onComplete) {
		var url		= Todoyu.getUrl('sysmanager', 'extensions');
		var options	= {
			parameters: {
				action:	'tabview',
				tab:	tab,
				extkey:	extKey
			},
			onComplete: this.onTabShowed.bind(this, extKey, tab, params, onComplete)
		};

		if( typeof(params) === 'object' ) {
			$H(options.parameters).update(params).toObject();
		}

		Todoyu.Ui.updateContent(url, options);
		Todoyu.Ui.scrollToTop();
	},



	/**
	 * Activate given tab in sysmanager area
	 *
	 * @method	activateTab
	 * @param	{String}	tab
	 */
	activateTab: function(tab) {
		Todoyu.Tabs.setActive('extension', tab);
	},



	/**
	 * Evoked upon completion of loading of tab
	 *
	 * @method	onTabShowed
	 * @param	{String}	extKey
	 * @param	{String}	tab
	 * @param	{Array}		params
	 */
	onTabShowed: function(extKey, tab, params, onComplete) {
		if( Object.isFunction(onComplete) ) {
			onComplete(extKey, tab, params);
		}
	},



	/**
	 * On tab click handler
	 *
	 * @method	onTabClick
	 * @param	{Event}		event
	 * @param	{String}	tabKey
	 */
	onTabClick: function(event, tabKey) {
		var extKey, tab;

		if( tabKey.indexOf('_') !== -1 ) {
			var parts	= tabKey.split('_');
			extKey	= parts[0];
			tab		= parts[1];
		} else {
			extKey	= '';
			tab		= tabKey;
		}

		this.showTab(extKey, tab);
	},



	/**
	 * Download given extension
	 *
	 * @method	download
	 * @param	{String}	extKey
	 */
	download: function(extKey) {
		Todoyu.goTo('sysmanager', 'extensions', {
			action:		'download',
			extension:	extKey
		});
	},



	/**
	 * Show rights of given extension
	 *
	 * @method	showRights
	 * @param	{String}		extKey
	 */
	showRights: function(extKey) {
		Todoyu.Ext.sysmanager.loadModule('rights', {
			extkey: extKey,
			tab:	'rights'
		});
	},



	/**
	 * Load records module of sysmanager
	 *
	 * @method	showRecords
	 * @param	{String}	extKey
	 */
	showRecords: function(extKey) {
		Todoyu.Ext.sysmanager.loadModule('records', {
			extkey: extKey
		});
	},



	/**
	 * Remove an extension from the server (delete all files)
	 *
	 * @method	remove
	 * @param	{String}		extKey
	 */
	remove: function(extKey) {
		if( confirm('[LLL:sysmanager.extension.remove.confirm]') ) {
			var url		= Todoyu.getUrl('sysmanager', 'extensions');
			var options	= {
				parameters: {
					action:		'remove',
					extension:	extKey
				},
				onComplete:	this.onRemoved.bind(this, extKey)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler when an extension is removed from the server
	 *
	 * @method	onRemoved
	 * @param	{String}			extKey
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(extKey, response) {
		var notificationIdentifier	= 'sysmanager.extension.removed';

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:sysmanager.extension.notify.remove.error]', notificationIdentifier);
		} else {
			Todoyu.notifySuccess('[LLL:sysmanager.extension.notify.remove.success]', notificationIdentifier);
		}

		this.Import.showList();
	}

};