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
 * @module	Contact
 */

/**
 * Class to upload profile images & company logos
 */
Todoyu.Ext.contact.Upload = {

	/**
	 * Shows upload form on button click
	 *
	 * @method	showUploadForm
	 * @param	{String}	form
	 * @param	{String}	recordType		(person / company)
	 */
	showUploadForm: function(form, recordType) {
		var idRecord = $(form).id.split('-')[1];

		this.addUploadForm(idRecord, recordType);

		$(form).down('button.uploadContactImage').hide();
	},



	/**
	 * Remove upload form and unhide button making it shown
	 *
	 * @method	removeUploadForm
	 */
	removeUploadForm: function() {
		if( Object.isElement( $('contactimage-uploadform') ) ) {
			$('contactimage-uploadform').remove();
		}
		$$('button.uploadContactImage').first().show();
	},



	/**
	 * Add upload form for contact images
	 *
	 * @method	addUploadForm
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType	(person / company)
	 */
	addUploadForm: function(idRecord, recordType) {
		var url		= Todoyu.getUrl('contact', 'formhandling');
		var options	= {
			parameters: {
				action:		'contactimageuploadform',
				idRecord:	idRecord,
				recordType:	recordType
			}
		};
		var target	= $$('button.uploadContactImage')[0].id;
		Todoyu.Ui.append(target, url, options);
	},



	/**
	 * Upload contact image
	 *
	 * @method	upload
	 * @param	{String}	form
	 * @todo	clean up
	 */
	upload: function(form) {
		var field	= $(form).down('input[type=file]');

		if( field.value !== '' ) {
				// Create iFrame for contact image upload
			Todoyu.Form.addIFrame('contactimage');

			$(form).submit();
		}
	},



	/**
	 * Contact image upload finished handler
	 *
	 * @method	uploadFinished
	 * @param	{String}	recordType		(person / company)
	 * @param	{Number}	idRecord
	 * @param	{Number}	newImageKey
	 */
	uploadFinished: function(recordType, idRecord, newImageKey) {
		var form 		= $(recordType + '-' + idRecord + '-form');
		var imageKey	=	newImageKey.length > 1 ? newImageKey : idRecord;

		this.refreshPreviewImage(form, recordType, idRecord, imageKey, false);
		this.setReplaceIdToHiddenField(form, recordType, imageKey);

		this.removeUploadForm();
		Todoyu.notifySuccess('[LLL:contact.ext.contactimage.upload.success]', 'contact.upload');
	},



	/**
	 * Refreshes the preview image in the form
	 *
	 * @method	refreshPreviewImage
	 * @param	{String}	form
	 * @param	{String}	recordType		(person / company)
	 * @param	{Number}	idRecord
	 * @param	{String}	imageKey
	 * @param	{Boolean}	removed
	 */
	refreshPreviewImage: function(form, recordType, idRecord, imageKey, removed) {
		var url		= Todoyu.getUrl('contact', recordType);
		var options	= {
			parameters: {
				action:		'loadimage',
				record:		imageKey,
				removed:	removed ? 1 : 0
			},
			onComplete: this.onRefreshPreviewImage.bind(this, form, recordType, idRecord, removed)
		};
		var target	= form.down('div.fieldnamePreview img');

		Todoyu.Ui.replace(target, url, options);
	},



	/**
	 * After image has been refreshed: make "remove image" button visible
	 *
	 * @method	onRefreshPreviewImage
	 * @param	{Element}	form
	 * @param	{String}	recordType		'person' / 'company'
	 * @param	{Number}	idRecord
	 * @param	{Boolean}	removed
	 * @todo	add check for image being dummy (via http header?) only "real" pictures need the button
	 */
	onRefreshPreviewImage: function(form, recordType, idRecord, removed) {
		this.showRemoveImageButton(recordType, idRecord, !removed);
	},



	/**
	 * Sets the temporary ID (folder-name) of the uploaded image to the hidden field
	 *
	 * @method	setReplaceIdToHiddenField
	 * @param	{String}	form
	 * @param	{String}	recordType		(person / company)
	 * @param	{Number}	newImageKey
	 */
	setReplaceIdToHiddenField: function(form, recordType, newImageKey) {
		var idRecord	= form.id.split('-')[1];
		var field = $(recordType + '-' + idRecord + '-field-image-id');

		if( field ) {
			field.value = newImageKey;
		}
	},



	/**
	 * Check whether upload failed, determine reason (file too big / failure) and notify
	 *
	 * @method	uploadFailed
	 * @param	{Number}		error		1 = filesize exceeded, 2 = failure
	 * @param	{String}		filename
	 * @param	{Number}		maxFileSize
	 */
	uploadFailed: function(error, filename, maxFileSize) {
		this.removeUploadForm();

		var info	= {
			filename: 		filename,
			maxFileSize:	maxFileSize
		};

		var msg		= '';

		if( error === 1 || error === 2 ) {
			msg	= '[LLL:contact.ext.contactimage.upload.maxFileSizeExceeded]';
		} else {
			msg	= '[LLL:contact.ext.contactimage.upload.failed]';
		}

		Todoyu.notifyError(msg.interpolate(info), 'contact.upload');
	},



	/**
	 * Send Request to remove image of current user
	 *
	 * @method	removeImage
	 * @param	{String}	form
	 * @param	{String}	recordType
	 */
	removeImage: function(form, recordType) {
		var idRecord = $(form).id.split('-')[1];// this.getImageId(form, recordType);

		var url 	= Todoyu.getUrl('contact', 'formhandling');
		var options = {
			parameters: {
				action:		'removeimage',
				recordType:	recordType,
				record:		idRecord
			},
			onComplete: this.onImageRemoved.bind(this, form, recordType, idRecord)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handle image removed
	 *
	 * @param	{Form}			form
	 * @param	{String}		recordType
	 * @param	{Number}		idRecord
	 * @param	{Ajax.Response}	response
	 */
	onImageRemoved: function(form, recordType, idRecord, response) {
		this.refreshPreviewImage(form, recordType, idRecord, '', true);
	},



	/**
	 * Show or hide button to remove contact record image
	 *
	 * @method	showRemoveImageButton
	 * @param	{String}	recordType
	 * @param	{Number}	idRecord
	 * @param	{Boolean}	[visible]
	 */
	showRemoveImageButton: function(recordType, idRecord, visible) {
		visible	= visible || false;

		$(recordType + '-' + idRecord + '-field-remove')[visible ? 'show' : 'hide']();
	},



	/**
	 * Toggle image remove button
	 * Only show when image is set
	 *
	 * @param	{String}	recordType
	 * @param	{Number}	idRecord
	 */
	toggleRemoveButton: function(recordType, idRecord) {
		var image		= $('formElement-' + recordType + '-' + idRecord + '-field-preview').down('img');
		var method		= image.alt === 'none' ? 'hide' : 'show';
		var removeField	= $('formElement-' + recordType + '-' + idRecord + '-field-remove');

		if( removeField ) {
			removeField[method]();
		}
	},



	/**
	 * Init remove contact image button: hide if the current image is a dummy
	 *
	 * @method	initRemoveContactImageButton
	 */
	initRemoveContactImageButton: function() {
		var removeContactImageButtons	= $$('button.removeContactImage');
		if( removeContactImageButtons.length > 0 ) {
			var contactImage	= $$('fieldset.image div.fieldnamePreview span.commenttext img')[0]
			if(contactImage.src.indexOf('&dummy=1&') > -1 ) {
				removeContactImageButtons.each(function(element, index) {
					element.hide()
				});
			}
		}
	},



	/**
	 * Returns the ID of the image.
	 *
	 * @method	getImageId
	 * @param	{String}	form
	 * @param	{String}	recordType
	 */
	getImageId: function(form, recordType) {
		var field = $(form).down('[name = ' + recordType + '[image_id]]');

		if( field && field.getValue() ) {
			return field.getValue()
		} else {
			return $(form).id.split('-')[1];
		}
	}
};