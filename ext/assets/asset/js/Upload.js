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
 * Asset upload methods
 *
 * @class		Upload
 * @namespace	Todoyu.Ext.assets
 */
Todoyu.Ext.assets.Upload = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.assets,

	/**
	 * Upload activity flag
	 *
	 * @property	active
	 * @type		Boolean
	 */
	active: false,

	/**
	 * Upload iframges
	 *
	 * @property	iframes
	 * @type		Element[]
	 */
	iframes: {},



	/**
	 * onChange handler of assets upload form to given task
	 *
	 * @method	onChange
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	onChange: function(idRecord, recordType) {
		this.showProgressBar(idRecord, recordType);
		this.submit(idRecord, recordType);
	},



	/**
	 * Assets upload form submission handler
	 *
	 * @method	submit
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType		'person' / 'company'
	 */
	submit: function(idRecord, recordType) {
		this.active	= true;
		var form	= this.getForm(idRecord, recordType);
		var iFrame	= Todoyu.Form.submitFileUploadForm(form);

		this.iframes[recordType + idRecord] = iFrame;

			// Register callback to check after 20 seconds if upload failed
		this.uploadFailingDetection.bind(this, idRecord, recordType, iFrame).delay(20);
	},



	/**
	 * Check if upload iframe has loaded, but not set upload flag
	 * This means an error page has been loaded and the upload failed
	 *
	 * @method	uploadFailingDetection
	 * @param	{Number}	idRecord
	 * @param	{Element}	iFrame
	 */
	uploadFailingDetection: function(idRecord, recordType, iFrame) {
		if( this.active === true && iFrame.contentDocument.URL !== 'about:blank' ) {
			this.uploadFailed(idRecord, recordType);
		}
	},



	/**
	 * Get asset upload form file field's value of given task
	 *
	 * @method	getField
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType		'person' / 'company'
	 * @return	{Element}
	 */
	getField: function(idRecord, recordType) {
		return $(recordType + '-' + idRecord + '-asset-file');
	},



	/**
	 * Get assets upload form
	 *
	 * @method	getForm
	 * @param	{Number}	idTask
	 * @param	{String}	recordType
	 * @return	{Element}
	 */
	getForm: function(idTask, recordType) {
		return $(recordType + '-' + idTask + '-asset-form');
	},



	/**
	 * Show assets uploader
	 *
	 * @method	showProgressBar
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType		'person' / 'company'
	 * @param	{Boolean}	show
	 */
	showProgressBar: function(idRecord, recordType, show) {
		show	= show !== false;

		if( idRecord == 0 && !show ) {
			$$('.uploadProgress').invoke('hide');
		} else {
			$(recordType + '-' + idRecord + '-asset-progress')[show?'show':'hide']();
		}
	},



	/**
	 * Asset upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{Number}		idRecord
	 * @param	{String}		recordType		'person' / 'company'
	 * @param	{String}		tabLabel
	 */
	uploadFinished: function(idRecord,recordType, tabLabel) {
		this.active = false;
		delete this.iframes[recordType + idRecord];
		this.showProgressBar(idRecord, recordType, false);

		if( Todoyu.exists(recordType + '-' + idRecord + '-assets-commands') ) {
				// If tab is currently expanded
			Todoyu.Ext.assets.List.refresh(idRecord, recordType);
		} else if(recordType == 'task') {
				// Tab is collapsed
			Todoyu.Ext.project.Task.refreshHeader(idRecord);
			Todoyu.Ext.assets.updateTab(idRecord);
			Todoyu.Ext.assets.setTabLabel(idRecord, tabLabel);
		}

		Todoyu.notifySuccess('[LLL:assets.ext.uploadOk]');
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		idRecord
	 * @param	{String}		recordType
	 * @param	{Number}		[error]			1 = file size exceeded, 2 = failure
	 * @param	{String}		[filename]
	 * @param	{Number}		[maxFileSize]
	 */
	uploadFailed: function(idRecord, recordType, error, filename, maxFileSize) {
		error	= error || 0;
		filename= filename || '';

		this.active = false;

		delete this.iframes[recordType + idRecord];

		this.showProgressBar(idRecord, recordType, false);

		var info	= {
			filename:		filename,
			maxFileSize:	maxFileSize
		};
		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:assets.ext.maxFileSizeExceeded]';
		} else if( error === 3 ) {
			msg = '[LLL:assets.ext.maxLengthFileNameExceeded]';
		} else {
			msg	= '[LLL:assets.ext.uploadFailed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 'fileupload');
	},



	/**
	 * Cancel file upload
	 *
	 * @method	cancelUpload
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType		'person' / 'company'
	 */
	cancelUpload: function(idRecord, recordType) {
		var iFrame	= this.iframes[recordType + idRecord];

		if( iFrame ) {
			iFrame.src = 'about:blank';
			delete this.iframes[recordType + idRecord];
		}

		this.showProgressBar(idRecord, recordType, false);
		Todoyu.notifyInfo('[LLL:assets.ext.uploadCanceled]');
	}

};