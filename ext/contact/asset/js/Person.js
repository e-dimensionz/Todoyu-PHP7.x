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

Todoyu.Ext.contact.Person =  {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.contact,



	/**
	 * Add person (create and edit new person record)
	 *
	 * @method	add
	 */
	add: function() {
		this.edit(0);
	},



	/**
	 * Edit (person)
	 *
	 * @method	edit
	 * @param	{Number}		idPerson
	 */
	edit: function(idPerson) {
		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			parameters: {
				action:	'edit',
				person:	idPerson
			},
			onComplete: this.onEdit.bind(this, idPerson)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * On edit (person) handler
	 *
	 * @method	onEdit
	 * @param	{Number}			idPerson
	 * @param	{Ajax.Response}		response
	 */
	onEdit: function(idPerson, response) {
		this.initEditForm(idPerson);
	},



	/**
	 * Initialize edit form
	 *
	 * @method	initEditForm
	 * @param	{Number}		idPerson
	 */
	initEditForm: function(idPerson) {
		this.initObservers(idPerson);
		this.showLoginFields(idPerson);
		this.ext.Upload.toggleRemoveButton('person', idPerson);
	},




	/**
	 * Initialize observers
	 *
	 * @method	initObservers
	 * @param	{Number}			idPerson
	 */
	initObservers: function(idPerson) {
		this.observeFieldsForShortname(idPerson);

		if( this.canSeeAccountFields(idPerson) ) {
			$('person-' + idPerson + '-field-is-active').on('change', this.showLoginFields.bind(this, idPerson));
		}
	},



	/**
	 * Toggle display of login related fields of given person
	 *
	 * @method	showLoginFields
	 * @param	{Number}		idPerson
	 * @param	{Event}		event
	 */
	showLoginFields: function(idPerson, event) {
		if( this.canSeeAccountFields(idPerson) ) {
			if( $('person-' + idPerson + '-field-is-active').checked ) {
				$('person-' + idPerson + '-fieldset-loginfields').show();
			} else {
				$('person-' + idPerson + '-fieldset-loginfields').hide();
			}
		}
	},



	/**
	 * Check whether accounts fields
	 *
	 * @method	canSeeAccountFields
	 * @param	{Number}	idPerson
	 * @return	{Boolean}
	 */
	canSeeAccountFields: function(idPerson) {
		return Todoyu.exists('person-' + idPerson + '-field-is-active');
	},



	/**
	 * Delete given person record
	 *
	 * @method	remove
	 * @param	{Number}		idPerson
	 */
	remove: function(idPerson) {
		if( confirm('[LLL:contact.ext.person.delete.confirm]') ) {
			var url = Todoyu.getUrl('contact', 'person');
			var options = {
				parameters: {
					action:	'remove',
					person:	idPerson
				},
				onComplete: this.onRemoved.bind(this, idPerson)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Handler being evoked after onComplete of person deletion: update listing display
	 *
	 * @method	onRemoved
	 * @param	{Number}			idPerson
	 * @param	{Ajax.Response}		response
	 */
	onRemoved: function(idPerson, response) {
		Todoyu.notifySuccess('[LLL:contact.ext.person.delete.ok]');

		this.showList();
	},



	/**
	 * Start observation of modification of first- / lastname input fields (evoke auto-generation of shortname than)
	 *
	 * @method	observeFieldForShortname
	 * @param	{Number}		idPerson
	 */
	observeFieldsForShortname: function(idPerson) {
		$('person-' + idPerson + '-field-lastname').on('keyup', 'input', this.generateShortName.bind(this, idPerson));
		$('person-' + idPerson + '-field-firstname').on('keyup', 'input', this.generateShortName.bind(this, idPerson));
	},



	/**
	 * Generate person short name from it's first- + lastname
	 *
	 * @method	generateShortName
	 * @param	{Number}	idPerson
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	generateShortName: function(idPerson, event, element) {
		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');

		if( lastname.length >= 2 && firstname.length >= 2 ) {
			var firstnamePart	= firstname.strip().replace(' ', '').substr(0,2).toUpperCase();
			var lastnamePart	= lastname.strip().replace(' ', '').substr(0,2).toUpperCase();

			$('person-' + idPerson + '-field-shortname').value = firstnamePart + lastnamePart;
		}
	},



	/**
	 * Updates working location selector with options of chosen company
	 *
	 * @method	updateCompanyAddressRecords
	 * @param	{Object}	inputField
	 * @param	{String}	idField
	 * @param	{String}	selectedValue
	 * @param	{String}	selectedText
	 * @param	{Object}	autocompleter
	 */
	updateCompanyAddressRecords: function(inputField, idField, selectedValue, selectedText, autocompleter) {
		var refFieldName	= autocompleter.options['referencedFieldName'].replace('_', '-');
		var baseID			= idField.id.substr(0, idField.id.indexOf('-field-') + 6);
		var idAddressList	= baseID + '-' + refFieldName;

		if( Todoyu.exists(idAddressList) ) {
			var url = Todoyu.getUrl('contact', 'company');
			var options = {
				parameters: {
					action:		'getCompanyAddressOptions',
					idCompany:	selectedValue
				},
				onComplete: this.onUpdateCompanyAddressRecords.bind(this, $(idAddressList))
			};

			Todoyu.Ui.update(idAddressList, url, options);
		}
	},



	/**
	 * Highlights the referenced selector of company address after updating the company-autocompleter
	 *
	 * @method	onUpdateCompanyAddressRecords
	 * @param	{String}	addressList
	 */
	onUpdateCompanyAddressRecords: function(addressList) {
		new Effect.Highlight($(addressList), {
			startcolor:	'#fffe98',
			endcolor:	'#ffffff',
			duration:	2.0
		});
	},



	/**
	 * Save person form
	 *
	 * @method	save
	 * @param	{String}		form
	 * @return	{Boolean}
	 */
	save: function(form) {
		this.saveForm(form, this.onSaved.bind(this));

		return false;
	},



	/**
	 * Handler evoked upon onComplete of person saving: check for and notify success / error, update display
	 *
	 * @method	onSaved
	 * @param	{Number}			idPerson
	 * @param	{Form}				form
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(idPerson, form, response) {
		var notificationIdentifier	= 'contact.person.saved';
		Todoyu.notifySuccess('[LLL:contact.ext.person.saved]', notificationIdentifier);

		this.showList();
	},



	/**
	 * Save form and call successCallback on success
	 *
	 * @param	{Form}		form
	 * @param	{Function}	successCallback
	 * @return	{Boolean}
	 */
	saveForm: function(form, successCallback) {
		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onFormSaved.bind(this, form, successCallback)
		});

		return false;
	},



	/**
	 * Handle form saved
	 * Replace form with new content on error or call successCallback on success
	 *
	 * @param	{Form}			form
	 * @param	{Function}		successCallback
	 * @param	{Ajax.Response}	response
	 */
	onFormSaved: function(form, successCallback, response) {
		var notificationIdentifier	= 'contact.person.saved';

		var idPerson	= response.getTodoyuHeader('idRecord');

		if( response.hasTodoyuError() ) {
			Todoyu.notifyError('[LLL:contact.ext.person.saved.error]', notificationIdentifier);
			form.replace(response.responseText);

			this.initEditForm(idPerson);
		} else {
			successCallback(idPerson, form, response);
		}
	},



	/**
	 * Close form by reloading the persons list
	 *
	 * @method	closeForm
	 */
	closeForm: function(form) {
		this.removeUnusedImages(form);
		this.showList();
	},



	/**
	 * Show (filtered) persons list
	 *
	 * @method	showList
	 * @param	{String}		[searchText]
	 */
	showList: function(searchText) {
		if( !searchText ) {
			searchText = this.ext.getSearchText();
		}

		var url = Todoyu.getUrl('contact', 'person');
		var options = {
			parameters: {
				action:	'list',
				sword:	searchText
			},
			onComplete: this.initPersonList.bind(this)
		};

		this.ext.updateContent(url, options);
	},



	/**
	 * Close person detail view and reload persons list / area content
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
	 * Show info about given person's data
	 *
	 * @method	show
	 * @param	{Number}		idPerson
	 */
	show: function(idPerson) {
		var url		= Todoyu.getUrl('contact', 'person');
		var options	= {
			parameters: {
				action:	'detail',
				person:	idPerson
			},
			onComplete: this.ext.onContentUpdated.bind(this.ext, 'person', null)
		};

		Todoyu.Ui.updateContentBody(url, options);
	},



	/**
	 * Save person record from wizard
	 *
	 * @method	saveWizard
	 * @param	{Object}		form
	 * @param	{String}		target
	 * @return	{Boolean}
	 */
	saveWizard: function(form, target) {
		$(form).request ({
			parameters: {
				action:		'saveWizard',
				idTarget:	target
			},
			onComplete: this.onSavedWizard.bind( this, target)
		});

		return false;
	},



	/**
	 * Handler evoked upon onComplete of saving from wizard. Check and notify success / error, update display
	 *
	 * @method	onSaveWizard
	 * @param	{String}			target
	 * @param	{Ajax.Response}		response
	 */
	onSavedWizard: function(target, response) {
		var error					= response.hasTodoyuError();
		var notificationIdentifier	= 'contact.person.saved';

		if( error ) {
			Todoyu.notifyError('[LLL:contact.ext.person.saved.error]', notificationIdentifier);

			Todoyu.Popups.setContent('popup-' + target, response.responseText);
		} else {
			Todoyu.notifySuccess('[LLL:contact.ext.person.saved]', notificationIdentifier);

			var label		= response.getTodoyuHeader('recordLabel');

			$(target).value = response.getTodoyuHeader('idRecord');
			$(target + '-fulltext').value = label;

			Todoyu.Popups.close('popup-' + target);
		}
	},



	/**
	 * Cancel handling for wizard: close popup
	 *
	 * @method	cancelWizard
	 * @param	{Element}		form
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
		this.ext.removeUnusedImages(form, 'person');
	},



	/**
	 * Check for duplicated person entry
	 *
	 * @param	{String}		fieldID
	 */
	checkDuplicatedEntries: function(fieldID) {
		var idPerson	= fieldID.split('-')[1];

		var lastname	= $F('person-' + idPerson + '-field-lastname');
		var firstname	= $F('person-' + idPerson + '-field-firstname');

		if( lastname && firstname ) {
			var url = Todoyu.getUrl('contact', 'person');
			var options	= {
				parameters: {
					action: 'checkduplicatedentries',
					firstname: firstname,
					lastname: lastname
				},
				onComplete: this.onCheckDuplicatedEntries.bind(this, idPerson)
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Call-back for duplicated-person-check
	 *
	 * @param	{Integer}		idPerson
	 * @param	{Ajax.response}	response
	 */
	onCheckDuplicatedEntries: function(idPerson, response) {
		var fieldIDFirstname	= 'person-' + idPerson + '-field-firstname';
		var fieldIDLastname		= 'person-' + idPerson + '-field-lastname';

		var error	= response.getTodoyuHeader('duplicates');

		Todoyu.Form.setFieldWarningStatus(fieldIDFirstname, error);
		Todoyu.Form.setFieldWarningStatus(fieldIDLastname, error);

		if( error ) {
			Todoyu.FormValidator.addWarningMessage(fieldIDFirstname, response.responseText, !$(fieldIDFirstname).up('.dialog'));
		}
	},



	/**
	 * Install Quickinfo for person email and phone.
	 * Include installation on paging
	 */
	initPersonList: function() {
		var listing = $('paging-person');

		if( listing ) {
			Todoyu.QuickInfo.install('personEmail', '#paging-person span.email', this.getPersonIdForQuickinfo.bind(this));
			Todoyu.QuickInfo.install('personPhone', '#paging-person span.phone', this.getPersonIdForQuickinfo.bind(this));
			Todoyu.Ext.contact.Company.replaceEmails(listing);
		}
	},



	/**
	 * Extract person id from table row.
	 *
	 * @param	{Element}	element
	 * @returns {Number}
	 */
	getPersonIdForQuickinfo: function(element) {
		return element.up('tr').id.split('-').last();
	}
};