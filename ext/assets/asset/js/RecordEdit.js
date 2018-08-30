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
 *	Assets handling inside of record form
 *
 * @class		assets
 * @namespace	Todoyu.Ext.assets.RecordEdit
 */
Todoyu.Ext.assets.RecordEdit = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.assets,



	/**
	 * Toggle all form elements depending on current state
	 * Elements: file list, delete button
	 *
	 * @method	toggleFormElements
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	toggleFormElements: function(idRecord, recordType) {
		var assetSelector	= this.getAssetSelector(idRecord, recordType);

		if( assetSelector ) {
			var hasFiles= assetSelector.select('option').size() > 0;
			var method	= hasFiles ? 'show' : 'hide';
			var field	= 'formElement-' + recordType + '-' + idRecord + '-field';

			$(field + '-assetlist')[method]();
			$(field + '-delete')[method]();
		}
	},



	/**
	 * Upload asset file
	 *
	 * @method	uploadFileInline
	 * @param	{Element}	field
	 * @param	{String}	recordType
	 */
	uploadFileInline: function(field, recordType) {
		if( $F(field) !== '' ) {
			var url	= Todoyu.getUrl('assets', recordType + 'edit', {
				action:		'uploadassetfile'
			});

			Todoyu.Form.submitFileUploadForm(field.form, url);
		}
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	uploadFinished: function(idRecord, recordType) {
		this.toggleFormElements(idRecord, recordType);
		this.refreshFileOptions(idRecord, recordType);

			// Update assets list and tab of task
		if( idRecord > 0 && $(recordType + '-' + idRecord + '-tabcontent-assets') ) {
			if( Todoyu.exists(recordType + '-' + idRecord + '-assets-commands') ) {
				Todoyu.Ext.assets.List.refresh(idRecord, recordType);
			} else {
				Todoyu.Ext.assets.updateTab(idRecord);
			}
		}

		Todoyu.notifySuccess('[LLL:core.file.upload.succeeded]', 'fileupload');
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		idRecord
	 * @param	{Number}		recordType
	 * @param	{Number}		error		1 = file size exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 */
	uploadFailed: function(idRecord, recordType, error, filename, maxFileSize) {
		this.toggleFormElements(idRecord, recordType);

		var info	= {
			filename:		filename,
			maxFileSize:	maxFileSize,
			id_task:		idRecord
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:core.file.upload.failed.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:core.file.upload.error.uploadfailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 'fileupload');
	},



	/**
	 * Delete selected temporary asset file from server
	 *
	 * @method	removeSelectedTempAsset
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	removeSelectedTempAsset: function(idRecord, recordType) {
		var fileKey		= this.getSelectedAssetFileID(idRecord, recordType);
		var filename	= this.getSelectedAssetFilename(idRecord, recordType);

		if( confirm('[LLL:core.file.confirm.delete]' + ' ' + filename) ) {
			var url		= Todoyu.getUrl('assets', recordType + 'Edit');
			var options	= {
				parameters: {
					action:		'deletesessionfile',
					filekey:	fileKey,
					record:		idRecord
				},
				onComplete: this.onRemovedAsset.bind(this, filename, idRecord)
			};

			Todoyu.Ui.update(this.getAssetSelector(idRecord, recordType), url, options);
		}
	},




	/**
	 * Cleanup: remove all temporary uploaded asset files
	 *
	 * @method	removeAllTempAssets
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	removeAllTempAssets: function(idRecord, recordType) {
		var url		= Todoyu.getUrl('assets', recordType + 'Edit');
		var options	= {
			parameters: {
				action:	'deleteuploads',
				record:	idRecord
			}
		};

		Todoyu.send(url, options);
	},



	/**
	 * Check whether the record has an assets tab. Missing if user does not have the right to use assets.
	 *
	 * @method	hasAssetsTab
	 * @param	{Number}		idRecord
	 * @param	{String}		recordType
	 */
	hasAssetsTab: function(idRecord, recordType) {
		return Todoyu.exists(recordType + '-' + idRecord + '-tab-assets');
	},



	/**
	 * Get asset selector element
	 *
	 * @method	getAssetSelector
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @return	{Element}
	 */
	getAssetSelector: function(idRecord, recordType) {
		return $(recordType + '-' + idRecord + '-field-assetlist');
	},



	/**
	 * Get ID of selected asset file (option value)
	 *
	 * @method	getSelectedAssetFileID
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @return	{String}
	 */
	getSelectedAssetFileID: function(idRecord, recordType) {
		return $F(this.getAssetSelector(idRecord, recordType));
	},



	/**
	 * Get selected template file (option label)
	 *
	 * @method	getSelectedAssetFilename
	 * @return	{String}
	 */
	getSelectedAssetFilename: function(idRecord, recordType) {
		var select	= this.getAssetSelector(idRecord, recordType);

		return select.selectedIndex >= 0 ? select.options[select.selectedIndex].text : '';
	},



	/**
	 * Evoked after completion of removal of asset file
	 *
	 * @method	onRemovedAsset
	 * @param	{String}			filename
	 * @param	{Number}			idRecord
	 * @param	{String}			recordType
	 * @param	{Ajax.Response}		response
	 */
	onRemovedAsset: function(filename, idRecord, recordType, response) {
		Todoyu.notifySuccess('[LLL:core.file.notify.delete.success]' + ' ' + filename, 'assets.recordEdit.onremovedasset');

		this.toggleFormElements(idRecord, recordType);
	},



	/**
	 * Refresh assets file selector options
	 *
	 * @method	refreshFileOptions
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	refreshFileOptions: function(idRecord, recordType) {
		var target	= this.getAssetSelector(idRecord, recordType);
		var url		= Todoyu.getUrl('assets', recordType + 'Edit');
		var options	= {
			parameters: {
				action:	'sessionFiles',
				record:	idRecord
			},
			onComplete: this.onRefreshedFileOptions.bind(this, idRecord, recordType)
		};

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Evoked upon completion of refresh of template file selector options: refresh file option buttons
	 *
	 * @method	onRefreshedFileOptions
	 * @param	{Number}		idRecord
	 * @param	{String}		recordType
	 * @param	{Ajax.Response}	response
	 */
	onRefreshedFileOptions: function(idRecord, recordType, response) {
		this.toggleFormElements(idRecord, recordType);
	},



	/**
	 * Hooked handler when task edit or create is being cancelled: remove temporary uploaded assets
	 *
	 * @method	onCancelledTaskEdit
	 * @hook	project.task.edit.cancelled
	 */
	onCancelledTaskEdit: function(idRecord) {
		this.onCancelledRecordEdit(idRecord, 'task');
	},



	/**
	 * Hooked handler when project edit or create is being cancelled: remove temporary uploaded assets
	 *
	 * @method	onCancelledTaskEdit
	 * @hook	project.task.edit.cancelled
	 */
	onCancelledProjectEdit: function(idRecord) {
		this.onCancelledRecordEdit(idRecord, 'project');
	},


	/**
	 * On Form cancelled Handler
	 *
	 * @param {Number}		idRecord
	 * @param {String}		recordType
	 */
	onCancelledRecordEdit: function(idRecord, recordType) {
		if( this.hasAssetsTab(idRecord, recordType) ) {
			this.removeAllTempAssets(idRecord, recordType);
		}
	},



	/**
	 *
	 * @method	onTaskEditFormLoaded
	 * @hook	project.task.formLoaded
	 * @param	{Number}	idTask
	 * @param	{Object}	options
	 */
	onTaskEditFormLoaded: function(idTask, options) {
		this.onRecordEditFormLoaded(idTask, 'task', options);
	},
	
	
	
	/**
	 *
	 * @method	onTaskEditFormLoaded
	 * @hook	project.project.formLoaded
	 * @param	{Number}	idProject
	 * @param	{Object}	options
	 */
	onProjectEditFormLoaded: function(idProject, options) {
		this.onRecordEditFormLoaded(idProject, 'project', options);
	},



	/**
	 * Handle record edit form loading
	 *
	 * @method	onTaskEditFormLoaded
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @param	{Object}	options
	 */
	onRecordEditFormLoaded: function(idRecord, recordType, options) {
		if( this.getAssetSelector(idRecord, recordType) ) {
			this.toggleFormElements(idRecord, recordType);
		}
	}

};