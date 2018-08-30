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

Todoyu.Ext.contact.Company =  {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.contact,



	/**
	 * Open new company record for editing
	 *
	 * @method	add
	 */
	add: function() {
		this.edit(0);
	},



	/**
	 * Edit given company
	 *
	 * @method	edit
	 * @param	{Number}	idCompany
	 */
	edit: function(idCompany) {
		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			parameters: {
				company:	idCompany,
				action:		'edit'
			},
			onComplete: this.onEdit.bind(this, idCompany)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * On edit company handler
	 *
	 * @method	onEdit
	 * @param	{Number}			idCompany
	 * @param	{Ajax.Response}		response
	 */
	onEdit: function(idCompany, response) {
		this.initEditForm(idCompany);
	},



	/**
	 * Init edit form
	 *
	 * @method	initEditForm
	 * @param	{Number}	idCompany
	 */
	initEditForm: function(idCompany) {
		this.ext.Upload.toggleRemoveButton('company', idCompany);
	},



	/**
	 * Confirm if really wanted and remove (delete) given company if
	 *
	 * @method	remove
	 * @param	{Number}	idCompany
	 */
	remove: function(idCompany) {
		if( confirm('[LLL:contact.ext.company.delete.confirm]') ) {
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				parameters: {
					action:		'remove',
					company:	idCompany
				},
				onComplete: this.onRemove.bind(this)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handle event after company deletion being performed
	 *
	 * @method	onRemoved
	 * @param	{Ajax.Response}	response
	 */
	onRemove: function(response) {
		var notificationIdentifier	= 'contact.company.removed';

		if( response.hasTodoyuError() ) {
			var message	= response.getTodoyuHeader('errormessage');
			Todoyu.notifyError(message, notificationIdentifier);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.company.delete.ok]', notificationIdentifier);
			this.showList();
		}
	},



	/**
	 * Save company record
	 *
	 * @method	save
	 * @param	{String}		form
	 */
	save: function(form) {
		$(form).request ({
			parameters: {
				action:	'save'
			},
			onComplete: this.onSave.bind(this)
		});

		return false;
	},



	/**
	 * Handler being evoked OnComplete of save company request: check for and notify success / error, update display
	 *
	 * @method	onSave
	 * @param	{Ajax.Response}		response
	 */
	onSave: function(response) {
		var error					= response.hasTodoyuError();
		var notificationIdentifier	= 'contact.company.saved';

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.company.saved.error]', notificationIdentifier);
			$('contact-form-content').update(response.responseText);
		} else {
				// Notify (implement)
			Todoyu.notifySuccess('[LLL:contact.ext.company.saved.ok]', notificationIdentifier);

			this.showList();
		}
	},



	/**
	 * Close company form, update list view
	 *
	 * @method	closeForm
	 * @param	{Element}	form
	 */
	closeForm: function(form) {
		this.removeUnusedImages(form);
		this.showList();
	},



	/**
	 * Update company list
	 *
	 * @method	showList
	 * @param	{String}		sword		(search word)
	 */
	showList: function(sword) {
		if( !sword ) {
			sword = this.ext.getSearchText();
		}

		var url = Todoyu.getUrl('contact', 'company');
		var options = {
			parameters: {
				action:	'list',
				sword:	sword
			},
			onComplete: this.initCompanyList.bind(this)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Close company detail view and reload companies list / area content
	 *
	 * @method	closeDetailView
	 */
	closeDetailView: function() {
		if( Todoyu.getArea() === 'contact' ) {
			this.showList();
		} else {
			document.location.reload();
		}
	},



	/**
	 * Show detail view of given company
	 *
	 * @method	show
	 * @param	{Number}		idCompany
	 */
	show: function(idCompany) {
		var url		= Todoyu.getUrl('contact', 'company');
		var options	= {
			parameters: {
				action:		'detail',
				company:	idCompany
			},
			onComplete: this.ext.onContentUpdated.bind(this.ext, 'company', null)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Save person record from wizard
	 *
	 * @method	saveWizard
	 * @param	{Form}		form
	 * @param	{String}	fieldName
	 * @return	{Boolean}
	 */
	saveWizard: function(form, fieldName) {
		$(form).request ({
			parameters: {
				action:	'saveCreateWizard',
				field: 	fieldName
			},
			onComplete: this.onSavedWizard.bind( this, fieldName)
		});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of saving from wizard. Check and notify success / error, update display
	 *
	 * @method	onSavedWizard
	 * @param	{String}			fieldName
	 * @param	{Ajax.Response}		response
	 */
	onSavedWizard: function(fieldName, response) {
		var error					= response.hasTodoyuError();
		var notificationIdentifier	= 'contact.company.saved';

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.company.saved.error]', notificationIdentifier);

			Todoyu.Popups.setContent('popup-' + fieldName, response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.company.saved.ok]', notificationIdentifier);

			$(fieldName).value				= response.getTodoyuHeader('record');
			$(fieldName + '-fulltext').value= response.getTodoyuHeader('label');

			Todoyu.Popups.close('popup-' + fieldName);
		}
	},



	/**
	 * Cancel handling for wizard: close popup
	 *
	 * @method	cancelWizard
	 */
	cancelWizard: function(form) {
		this.removeUnusedImages(form);
		Todoyu.Popups.closeLast();
	},



	/**
	 * Remove unused temporary image files
	 *
	 * @method	removeUnusedImages
	 * @param	{Element}	form
	 */
	removeUnusedImages: function(form) {
		this.ext.removeUnusedImages(form, 'company');
	},



	/**
	 * Check input for duplicated company names
	 *
	 * @param	{String}		fieldID
	 */
	checkDuplicatedEntries: function(fieldID) {
		var value = $(fieldID).getValue();
		var url		= Todoyu.getUrl('contact', 'company');

		var options	= {
			parameters: {
				action: 'checkduplicatedentries',
				fieldvalue: value
			},
			onComplete: Todoyu.Ext.contact.onCheckForDuplicatedEntries.curry(fieldID)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Install Quickinfo for company email, phone and address.
	 * Include installation on paging
	 */
	initCompanyList: function() {
		var listing = $('paging-company');

		if( listing ) {
			Todoyu.QuickInfo.install('companyEmail', '#paging-company span.email', this.getCompanyIdForQuickinfo.bind(this));
			Todoyu.QuickInfo.install('companyPhone', '#paging-company span.phone', this.getCompanyIdForQuickinfo.bind(this));
			Todoyu.QuickInfo.install('companyAddress', '#paging-company span.address', this.getCompanyIdForQuickinfo.bind(this));
			this.replaceEmails(listing);
		}
	},



	/**
	 * Extract company id from table row.
	 *
	 * @param	{Element}	element
	 * @returns {Number}
	 */
	getCompanyIdForQuickinfo: function(element) {
		return element.up('tr').id.split('-').last();
	},



	/**
	 *
	 * @param listing
	 */
	replaceEmails: function(listing) {
		if( listing ) {
			listing.select('span.email').each(function(element) {
				var email = element.innerHTML.strip();
				if( !element.down('a') && email != '-') {
					var link = 'mailto:' + email;

					var aTag = new Element('a', {
									href: link
								});

					aTag.update(email);
					element.update(aTag);
				}
			});
		}
	}
};