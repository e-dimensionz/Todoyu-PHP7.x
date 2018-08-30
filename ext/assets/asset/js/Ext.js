/****************************************************************************
* todoyu is published under the BSD License:
* http://www.opensource.org/licenses/bsd-license.php
*
* Copyright (c) 2013, snowflake productions GmbH, Switzerland
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
 * @module	Assets
 */

/**
 *	Main assets object
 *
 * @class		assets
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.assets = {

	/**
	 * @property	PanelWidget
	 * @type		Object
	 */
	PanelWidget: {},

	/**
	 * @property	Headlet
	 * @type		Object
	 */
	Headlet: {},



	/**
	 * Initialize assets
	 *
	 * @method	init
	 */
	init: function() {
		this.registerHooks();
		this.initObservers();
	},



	/**
	 * @method	initObservers
	 */
	initObservers: function() {
		this.initAssetQuickInfos();
	},



	/**
	 * Install observers for asset quickInfo
	 *
	 * @method	initAssetQuickInfos
	 */
	initAssetQuickInfos: function() {
		$$('.quickInfoAsset').each(function(element, index) {
			Todoyu.Ext.assets.QuickInfoAsset.add(element.id);
		});
	},



	/**
	 * Register JS hooks of assets
	 *
	 * @method	registerHooks
	 */
	registerHooks: function() {
		Todoyu.Hook.add('project.task.edit.cancelled', this.RecordEdit.onCancelledTaskEdit.bind(this.RecordEdit));
		Todoyu.Hook.add('project.task.formLoaded', this.RecordEdit.onTaskEditFormLoaded.bind(this.RecordEdit));
		Todoyu.Hook.add('project.project.edit.cancelled', this.RecordEdit.onCancelledProjectEdit.bind(this.RecordEdit));
		Todoyu.Hook.add('project.project.formLoaded', this.RecordEdit.onProjectEditFormLoaded.bind(this.RecordEdit));
	},



	/**
	 * Download asset
	 * Checks first if download is possible
	 *
	 * @method	download
	 * @param	{Number}	idAsset
	 */
	download: function(idAsset) {
		this.checkDownloadStatus(idAsset);
	},



	/**
	 * Send check request to determine whether the file can be downloaded
	 *
	 * @method	checkDownloadStatus
	 * @param	{Number}	idAsset
	 */
	checkDownloadStatus: function(idAsset) {
		var url		= Todoyu.getUrl('assets', 'asset');
		var options	= {
			parameters: {
				action: 'downloadStatus',
				asset:	idAsset
			},
			onComplete: this.onDownloadStatusChecked.bind(this, idAsset)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle download check result - initiate sending file / notify error
	 *
	 * @method	onDownloadStatusChecked
	 * @param	{Number}		idAsset
	 * @param	{Ajax.Response}	response
	 */
	onDownloadStatusChecked: function(idAsset, response) {
		var status	= response.responseJSON;

		if( status.status ) {
			this.downloadAsset(idAsset);
		} else {
			Todoyu.notifyError('[LLL:assets.ext.error.download]: ' + status.error, 'assets.downloadError');
		}
	},



	/**
	 * Download the asset
	 * Redirect the browser to the real download URL
	 *
	 * @method	downloadAsset
	 * @param	{Number}	idAsset
	 */
	downloadAsset: function(idAsset) {
		var params	= {
			action: 'download',
			asset:	idAsset
		};

		Todoyu.goTo('assets', 'asset', params);
	},



	/**
	 * Download (zipped) selection of assets of given task
	 *
	 * @method	downloadSelection
	 * @param	{Number}	idRecord
	 */
	downloadSelection: function(idRecord, recordType) {
		var selectedAssets = this.List.getSelectedAssets(idRecord, recordType);

		if( selectedAssets.size() === 0 ) {
			Todoyu.notifyError('[LLL:assets.ext.error.minimumFile]');
		} else if( selectedAssets.size() === 1 ) {
			Todoyu.notifyInfo('[LLL:assets.ext.download.normal]');
			this.download(selectedAssets.first());
		} else {
			Todoyu.notifyInfo('[LLL:assets.ext.download.compressed]');
			var params = {
				action:	'download',
				record:	idRecord,
				recordType: recordType,
				assets:	selectedAssets.join(',')
			};

			Todoyu.goTo('assets', 'zip', params);
		}
	},



	/**
	 * Toggle visibility of an asset
	 *
	 * @method	toggleVisibility
	 * @param	{Number}	idAsset
	 */
	toggleVisibility: function(idAsset) {
		var url		= Todoyu.getUrl('assets', 'asset');
		var options	= {
			parameters: {
				action:	'togglevisibility',
				asset:	idAsset
			},
			onComplete: this.onToggledVisibility(idAsset)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Called after asset visibility has been toggled: notify success
	 *
	 * @method	onToggledVisibility
	 * @param	{Number}			idAsset
	 * @param	{Ajax.Response}		response
	 */
	onToggledVisibility: function(idAsset, response) {
		var isPublic	= $$('[id$=asset-' + idAsset + '-icon-public][class*=not]').length == 0;

		if( isPublic ) {
			Todoyu.notifySuccess('[LLL:assets.ext.togglepublic.notifiy.ispublic]', 'assets.public.toggle');
		} else {
			Todoyu.notifySuccess('[LLL:assets.ext.togglepublic.notifiy.notpublic]', 'assets.public.toggle');
		}
	},



	/**
	 * Remove given asset
	 *
	 * @method	remove
	 * @param	{Number}	idAsset
	 */
	remove: function(idAsset, recordType) {
		if( confirm('[LLL:assets.ext.delete.confirm]') ) {
			var url		= Todoyu.getUrl('assets', 'asset');
			var options	= {
				parameters: {
					action:	'delete',
					asset:	idAsset
				},
				onComplete: this.onRemoved.bind(this, idAsset, recordType)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler to be called after having deleted a file: updates file list
	 *
	 * @method	onRemoved
	 * @param	{Number}			idAsset
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idAsset, recordType, response) {
		$$('[id$=asset-'+idAsset+']').invoke('fade');

		var idTask	= response.getTodoyuHeader('idTask');
		var label	= response.getTodoyuHeader('tabLabel');

		Todoyu.Notification.notifySuccess('[LLL:assets.ext.delete.notifiy.success]');

		if( recordType == 'task') {
			Todoyu.Ext.project.Task.refreshHeader(idTask);
			this.setTabLabel(idTask, label);
			this.updateTab(idTask);
		}
	},



	/**
	 * Update assets tab of given task
	 *
	 * @method	updateTab
	 * @param	{Number}		idTask
	 */
	updateTab: function(idTask) {
		var url		= Todoyu.getUrl('assets', 'tasktab');
		var options	= {
			parameters: {
				action:	'tab',
				task:	idTask
			},
			onComplete: this.onUpdateTab.bind(this, idTask)
		};
		var target	= 'task-' + idTask + '-tabcontent-assets';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * @method	onUpdateTab
	 * @param	{Number}		idTask
	 */
	onUpdateTab: function(idTask) {
		Todoyu.Ext.assets.QuickInfoAsset.install();
	},



	/**
	 * Set label of task assets tab
	 *
	 * @method	setTabLabel
	 * @param	{Number}	idTask
	 * @param	{String}	label
	 */
	setTabLabel: function(idTask, label) {
		$('task-' + idTask + '-tab-assets-label').select('.labeltext').first().update(label);
	},



	/**
	 * Add new asset to given task: expand task details and open assets tab with new asset form
	 *
	 * @method	addTaskAsset
	 * @param	{Number}	idTask
	 */
	addTaskAsset: function(idTask) {
		Todoyu.Ext.project.Task.showDetails(idTask, 'assets', this.onTaskAssetTabLoaded.bind(this));
	},



	/**
	 * Handler when task asset tab is loaded: show asset upload form
	 *
	 * @method	onTaskAssetTabLoaded
	 * @param	{Number}	idTask
	 * @param	{String}	tab
	 */
	onTaskAssetTabLoaded: function(idTask, tab) {

	}

};