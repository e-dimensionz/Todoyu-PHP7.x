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
 * General form helper functions
 *
 * @class		Form
 * @namespace	Todoyu
 */
Todoyu.Form = {

	/**
	 * Index counter for sub forms. Starts at 100 to prevent colisions
	 * @property	subFormIndex
	 * @type		Number
	 */
	subFormIndex: 100,



	/**
	 * Initialize form display: expand invalid foreign records, focus first field
	 *
	 * @method	onFormDisplay
	 * @param	{String}  			idForm
	 * @param	{String}			name
	 * @param	{Number|String}		idRecord
	 */
	onFormDisplay: function(idForm, name, idRecord) {
		if( Todoyu.exists(idForm) ) {
			if( this.hasError(idForm) ) {
				this.expandInvalidForeignRecords(idForm);
				this.scrollToError(idForm);
			} else {
				this.focusFirstFormField(idForm);
			}

			this.callFormDisplayHooks(idForm, name, idRecord);
		}
	},



	/**
	 * Call hooks which are registered for the form display event
	 *
	 * @param	{String}	idForm
	 * @param	{String}	name
	 * @param	{Number}	idRecord
	 */
	callFormDisplayHooks: function(idForm, name, idRecord) {
		Todoyu.Hook.exec('form.display', idForm, name, idRecord);
		Todoyu.Hook.exec('form.display.' + name, idForm, name, idRecord);
	},



	/**
	 * Get the next index for a sub form to prevent name collisions
	 *
	 * @method	getNextIndex
	 * @return	{Number}
	 */
	getNextIndex: function() {
		return this.subFormIndex++;
	},




	/**
	 * Get record element
	 *
	 * @method	getForeignRecordElement
	 * @param	{Number}	idRecord
	 * @param	{String}	fieldName
	 * @param	{Number}	index
	 * @return	{Element}
	 */
	getForeignRecordElement: function(idRecord, fieldName, index) {
		return $('foreignrecord-' + idRecord + '-' + fieldName + '-' + index);
	},



	/**
	 * Check whether the given foreign record's field is an autocompleter (not a regular select)
	 *
	 * @method	isForeignRecordAutocompleter
	 * @param	{Element}	recordElement
	 * @return	{Boolean}
	 */
	isForeignRecordAutocompleter: function(recordElement) {
		var isAutocompleter = false;

		if( Todoyu.exists(recordElement) ) {
			var selectElement	= recordElement.down('select');
			isAutocompleter		= typeof selectElement === "undefined";
		}

		return isAutocompleter;
	},



	/**
	 * Get value of foreign record
	 *
	 * @method	getForeignRecordValue
	 * @param	{Number}	idRecord
	 * @param	{String}	fieldName
	 * @param	{Number}	index
	 * @return	{String}
	 */
	getForeignRecordValue: function(idRecord, fieldName, index) {
		var valueElement;
		var recordElement	= this.getForeignRecordElement(idRecord, fieldName, index);

		var isAutocompleter	= this.isForeignRecordAutocompleter(recordElement);
		if( isAutocompleter ) {
			valueElement	= recordElement.select('input[type="hidden"]').first();
		} else {
			valueElement	= recordElement.down('select');
		}

		return $F(valueElement);
	},



	/**
	 * Toggle display of sub form
	 *
	 * @method	toggleRecordForm
	 * @param	{Number}		idRecord
	 * @param	{String}		fieldName
	 * @param	{Number}		index
	 */
	toggleRecordForm: function(idRecord, fieldName, index) {
		var baseName	= 'foreignrecord-' + idRecord + '-' + fieldName + '-' + index;
		var formHtml	= baseName + '-formhtml';
		var trigger		= baseName + '-trigger';

		if( Todoyu.exists(trigger) ) {
			$(formHtml).toggle();
			var method	= $(formHtml).visible() ? 'addClassName' : 'removeClassName';

			$(trigger).down('span')[method]('expanded');
		}
	},



	/**
	 * Highlight a sub record
	 *
	 * @method	highlightRecordForm
	 * @param	{Number}	idRecord
	 * @param	{String}	fieldName
	 * @param	{Number}	index
	 */
	highlightRecordForm: function(idRecord, fieldName, index) {
		var record = this.getForeignRecordElement(idRecord, fieldName, index);

		$(record).highlight();
	},



	/**
	 * Remove sub form
	 *
	 * @method	removeRecord
	 * @param	{Number}		idRecord
	 * @param	{String}		fieldName
	 * @param	{Number}		index
	 */
	removeRecord: function(idRecord, fieldName, index) {
		var recordValue	= this.getForeignRecordValue(idRecord, fieldName, index);

		if( recordValue === '0' || recordValue === '' || confirm('[LLL:core.form.records.removeconfirm]') ) {
			var recordElement	= this.getForeignRecordElement(idRecord, fieldName, index);
			recordElement.remove();
		} else {
				// Click event toggled sub form, so toggle again
			this.toggleRecordForm(idRecord, fieldName, index);
		}
	},



	/**
	 * Add a new record
	 *
	 * @method	addRecord
	 * @param	{Number}		idRecord
	 * @param	{String}		formName
	 * @param	{String}		fieldName
	 * @param	{String}		updateExt
	 * @param	{String}		updateController
	 */
	addRecord: function(idRecord, formName, fieldName, updateExt, updateController) {
		var container	= $('foreignrecords-' + idRecord + '-' + fieldName);
		var index		= this.getNextIndex();

		var url 	= Todoyu.getUrl(updateExt, updateController);
		var options = {
			parameters: {
				action:		'addSubform',
				'form': 	formName,
				'field':	fieldName,
				'record':	idRecord,
				'index': 	index
			},
			onComplete: this.onRecordAdded.bind(this, container, idRecord, formName, fieldName, index)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Callback when new record added
	 *
	 * @method	onRecordAdded
	 * @param	{Number}		idRecord
	 * @param	{String}		formName
	 * @param	{String}		fieldName
	 * @param	{String}		index
	 * @param	{Ajax.Response}	response
	 */
	onRecordAdded: function(container, idRecord, formName, fieldName, index, response) {
		$(container).insert({'top':response.responseText});

		this.toggleRecordForm(idRecord, fieldName, index);
		this.focusFirstRecordField(idRecord, fieldName, index);
	},



	/**
	 * Focus first record field
	 *
	 * @method	focusFirstRecordField
	 * @param	{Number}		idRecord
	 * @param	{String}		fieldName
	 * @param	{Number}		index
	 */
	focusFirstRecordField: function(idRecord, fieldName, index) {
		var formHTML= $('foreignrecord-' + idRecord + '-' + fieldName + '-' + index + '-formhtml');
		var field	= formHTML.select('input[type!=hidden]', 'select', 'textarea').first();

		if( field )  {
			field.focus();
		}
	},



	/**
	 * Focus first form field
	 *
	 * @method	focusFirstFormField
	 * @param	{String}	form
	 */
	focusFirstFormField: function(form) {
		form = $(form);

		if( form ) {
				// Stop if auto focus is disabled
			if( form.hasClassName('noAutoFocus') ) {
				return;
			}

			var firstField = form.down(':input[type!=hidden]');

			if( firstField ) {
				firstField.focus();
			}
		}
	},



	/**
	 * Expand / collapse foreign record fields
	 *
	 * @method	toggleForeignRecords
	 * @param	{Array}		[fieldNames]
	 */
	toggleForeignRecords: function(fieldNames) {
		fieldNames = fieldNames || [];
		var visibleMethod, classMethod;

		if( this.isAnyFieldHidden(fieldNames) ) {
			visibleMethod	= 'show';
			classMethod		= 'addClassName';
		} else {
			visibleMethod	= 'hide';
			classMethod		= 'removeClassName';
		}

		fieldNames.each(function(fieldName){
			var fieldBox = $$('form div.fieldname' + fieldName.replace(/_/g,'').capitalize()).first();
			fieldBox.select('.foreignRecords .formtrigger .label').invoke(classMethod, 'expanded');
		});

		this.invokeForeignRecords(fieldNames, visibleMethod);
	},



	/**
	 * Confirm when toggling an exclusive (only one record of this type can be preferred) "is_preferred" flag
	 *
	 * @method	togglePreferredExclusive
	 * @param	{Element}	field
	 */
	togglePreferredExclusive: function(field) {
			// Switch off "is_preferred"
		if( field.checked ) {
			if( ! confirm('[LLL:core.form.confirm.toggle.is_preferred.on]') ) {
				field.checked	= false;
			}
		} else {
				// Switch on "is_preferred"
			if( ! confirm('[LLL:core.form.confirm.toggle.is_preferred.off]') ) {
				field.checked	= true;
			}
		}
	},



	/**
	 * Check whether any of the given fields is currently hidden
	 *
	 * @method	isAnyFieldHidden
	 * @param	{Array}	fieldNames
	 * @return	{Boolean}
	 */
	isAnyFieldHidden: function(fieldNames) {
		if( fieldNames.length > 0 ) {
			var fieldName = fieldNames[0];
			var parentField = $$('form div.fieldname' + fieldName.replace(/_/g,'').capitalize()).first();
			if( parentField !== undefined ) {
				var subForms	= parentField.select('div.databaseRelation div.databaseRelationFormhtml');

				var anyHidden	= subForms.any(function(item){
					return item.style.display == 'none';
				}.bind(this));
			}
		}

		return anyHidden;
	},



	/**
	 * Expand fields of foreign records
	 *
	 * @method	expandForeignRecords
	 * @param	{Array}		fieldNames
	 */
	expandForeignRecords: function(fieldNames) {
		this.invokeForeignRecords(fieldNames, 'show');
	},



	/**
	 * Invoke given method (e.g. 'show', 'hide') on all fields inside the parent of the given field names
	 *
	 * @method	invokeForeignRecords
	 * @param	{Array}		[fieldNames]
	 * @param	{String}	[method]
	 */
	invokeForeignRecords: function(fieldNames, method) {
		fieldNames	= fieldNames || [];
		method		= method || 'show';

		fieldNames.each(function(fieldName){
			var parentField = $$('form div.fieldname' + fieldName.replace(/_/g,'').capitalize()).first();
			if( parentField ) {
				var subForms	= parentField.select('div.databaseRelation div.databaseRelationFormhtml');

				subForms.invoke(method);
			}
		});
	},



	/**
	 * Show formHTML of invalid form elements in foreign records
	 *
	 * @method	expandInvalidForeignRecords
	 * @param	{String}		formID
	 */
	expandInvalidForeignRecords: function(formID) {
		$(formID).select('div.error').each(function(errorField){
			var formHTML = $(errorField).up('div.databaseRelationFormhtml');
			if( formHTML ) {
				formHTML.show();
			}
		});
	},



	/**
	 * Open popup for create wizard
	 *
	 * @method	openCreateWizard
	 * @param	{String}			fieldName
	 * @param	{Object}			config
	 * @return	{Todoyu.Popup}
	 */
	openCreateWizard: function(fieldName, config) {
		var url		= Todoyu.getUrl(config.ext,	config.controller);
		var options	= {
			parameters: {
				action:	config.action,
				record:	config.record,
				field:	fieldName
			}
		};
		var idPopup	= 'popup-' + fieldName;

		var title	= config.title ? config.title : 'Form Wizard';
		var width	= config.width ? config.width : 662;

		return Todoyu.Popups.open(idPopup, title, width, url, options);
	},



	/**
	 * Live-validation and correction of numeric value input field. Removes/corrects illegal/ambiguous characters
	 *
	 * @method	assistNumericInput
	 * @param	{Element}			field
	 * @param	{Boolean}			[allowFloat]
	 */
	assistNumericInput: function(field, allowFloat) {
		allowFloat		= allowFloat || false;
		var value		= $F(field);
		var orig		= value;
		var allowedChars= '0123456789.-';

		if(allowFloat) {
			value	= value.replace(',', '.');

			if( value.indexOf('.') !== value.lastIndexOf('.') ) {
				value = value.substring(0, value.lastIndexOf('.'));
			}
		}

		if( ! Todoyu.Validate.isOnlyAllowedChars(value, allowedChars) ) {
				// Filter-out any illegal characters
			var whitelist	= (allowFloat) ? /([0-9]|\.|\-)/g : /([0-9])/g;
			var illegalChars	= value.replace(whitelist, '');

			for( var i = 0; i <= illegalChars.length; i++ ) {
				value	= value.replace(illegalChars[i], '');
			}
		}

		if( orig != value ) {
			$(field).value = value;
		}
	},



	/**
	 * Assist input of time duration: correct abbreviated / alert on illegal input
	 *
	 * @method	assistDurationInput
	 * @param	{Element}	field
	 */
	assistDurationInput: function(field) {
		field		= $(field);
		var value	= $F(field).strip();

		if( value.match(/^\d+$/) !== null ) {		// Format like "1" => "1:00", also 11, 111, 1111...
			field.value  = value + ':00';
		} else if( value.match(/^\:\d{1,2}$/) ) {	// Format like ":30" => "0:30", also ":3", not ":333"
			field.value  = '0:' + Todoyu.String.twoDigit(value.replace(':', ''));
		} else if( value.match(/^\d+\:$/) ) {		// Format like "2:" => "2:00", also 22, 222, 2222...
			field.value  = value + '00';
		}

			// Detect and alert on empty or otherwise illegal (containing characters other than numbers and ":") input
		if( value === '' || value === ':' || value.match(/^[0-9\:]+$/) === null ) {
			this.setFieldErrorStatus(field, true);
			Todoyu.notifyError('[LLL:core.form.error.duration.invalidinput]', 'form.duration.error');
		} else {
			this.setFieldErrorStatus(field, false);
			Todoyu.Notification.closeTypeNotes('form.duration.error');
		}
	},



	/**
	 * Get label element of given field
	 *
	 * @method	getFieldLabelElement
	 * @param	{Element}				field
	 */
	getFieldLabelElement: function(field) {
		var labelBox	= $('formElement-' + field.id + '-labelbox');

		return labelBox.down('label');
	},



	/**
	 * Add an iFrame to the document body
	 *
	 * @method	addIFrame
	 * @param	{String}	key			Identifier
	 * @return	{Element}				IFrame element
	 */
	addIFrame: function(key) {
		var idIFrame= 'upload-iframe-' + key;

		if( !Todoyu.exists(idIFrame) ) {
			var iFrame	= new Element('iframe', {
				name:		idIFrame,
				id:			idIFrame,
				className:	'uploadIframe'
			});

			iFrame.hide();
			$(document.body).insert(iFrame);
		}

		return $(idIFrame)
	},



	/**
	 * Submit a form for file upload
	 * Set special encoding type and submit into an iframe
	 *
	 * @method	submitFileUploadForm
	 * @param	{Element}	form
	 * @param	{String}	[url]
	 */
	submitFileUploadForm: function(form, url) {
		var iFrame	= this.addIFrame(form.id);
		var specialAttributes = {
			enctype: 	'multipart/form-data',
			target:		iFrame.id
		};

		if( url ) {
			specialAttributes.action = url;
		}

		this.submitForm(form, specialAttributes);

		return iFrame;
	},



	/**
	 * Submit a form with custom parameters for the submit
	 * The custom parameters are restored after the form is submitted
	 * Useful to convert a normal form into a file upload form
	 *
	 * @method	submitForm
	 * @param	{Form}		form
	 * @param	{Object}	tempFormAttributes
	 */
	submitForm: function(form, tempFormAttributes) {
		form	= $(form);
		var backup	= {};

		$H(tempFormAttributes).each(function(pair){
			backup[pair.key]= form[pair.key];
			form[pair.key]	= pair.value;
		});

		form.submit();

		$H(backup).each(function(pair){
			form[pair.key]	= pair.value;
		});
	},



	/**
	 * Get a hidden iFrame
	 *
	 * @method	getIFrame
	 * @param	{String}		key
	 * @return	{Element}
	 */
	getIFrame: function(key) {
		return $('upload-iframe-' + key);
	},



	/**
	 * Open URL in new iFrame with given key
	 *
	 * @method	openIFrame
	 * @param	{String}	key
	 * @param	{String}	url
	 */
	openIFrame: function(key, url) {
		this.addIFrame(key);
		this.getIFrame(key).contentWindow.location.href = url;
	},



	/**
	 * Submit a form to an iFrame
	 *
	 * @method	submitToIFrame
	 * @param	{Element|String}	form
	 * @param	{String}			iFrameName
	 */
	submitToIFrame: function(form, iFrameName) {
		var iFrame	= this.addIFrame(iFrameName);

		$(form).writeAttribute('target', iFrame.name);

		$(form).submit();
	},



	/**
	 * Remove a hidden iFrame
	 *
	 * @method	removeIFrame
	 * @param	{String}	key
	 */
	removeIFrame: function(key) {
		var iFrame	= this.getIFrame(key);

		if( iFrame ) {
			iFrame.remove();
		}
	},



	/**
	 * Sets the value of the chosen icon to the hidden field
	 *
	 * @method	setIconSelectorValue
	 * @param	{String}	value
	 * @param	{String}	baseID
	 */
	setIconSelectorValue: function(value, baseID) {
		$(baseID).value = value;

		var selectedOld = $(baseID + '-selector').select('.selected').first();

		if( selectedOld ) {
			selectedOld.toggleClassName('selected');
		}

		$(baseID + '-listItem-' + value).toggleClassName('selected');
	},



	/**
	 * Disable save and cancel buttons in form
	 *
	 * @method	disableSaveButtons
	 * @param	{Element}	form
	 */
	disableSaveButtons: function(form) {
		$(form).down('fieldset.buttons').select('button').invoke('disable');
	},



	/**
	 * Enable save and cancel buttons in form
	 *
	 * @method	enableSaveButtons
	 * @param	{Element}	form
	 */
	enableSaveButtons: function(form) {
		$(form).down('fieldset.buttons').select('button').invoke('enable');
	},



	/**
	 * Set selected options of a select element
	 *
	 * @method	selectOptions
	 * @param	{Element}		element
	 * @param	{Array}			selection
	 */
	selectOptions: function(element, selection) {
		element		= $(element);
		selection	= selection.constructor === Array ? selection : [selection];

		element.selectedIndex = -1;

		$A(element.options).each(function(selection, option){
			if( selection.include(option.value) ) {
				option.selected = true;
			}
		}.bind(this, selection));
	},



	/**
	 * Get selected item pairs from a multi select
	 *
	 * @method	getSelectedItems
	 * @param	{Element}	element
	 * @return	{Object}	Format: value:text
	 */
	getSelectedItems: function(element) {
		var values	= $F(element);
		var options	= {};
		var items	= {};

		$(element).select('option').each(function(option){
			options[option.value] = option.innerHTML;
		});

		values.each(function(value){
			items[value] = options[value];
		});

		return items;
	},



	/**
	 * Custom handler for keyUp event inside textarea field - auto resize field by content
	 *
	 * @method	autoResizeTextArea
	 * @param	{String}			idElement	ID of textarea field element
	 */
	autoResizeTextArea: function(idElement) {
		var element		= $(idElement);
		var content		= $F(element).strip();
		var numTextRows	= Todoyu.String.countLines(content);

		element.rows = numTextRows < 2 ? 2 : numTextRows+1;
	},



	/**
	 * Scroll to the first form field with an error
	 *
	 * @method	scrollToError
	 * @param	{String|Element}	form
	 */
	scrollToError: function(form) {
		var firstErrorField	= $(form).down('.fElement.error');

		if( firstErrorField ) {
			Todoyu.Ui.scrollToElement(firstErrorField);
		}
	},



	/**
	 * Check whether the form contains a field in error status
	 *
	 * @method	hasError
	 * @param	{String|Element}	form
	 */
	hasError: function(form) {
		return $(form).down('.fElement.error') !== undefined;
	},



	/**
	 * Mark field as error/valid
	 *
	 * @method	setFieldErrorStatus
	 * @param	{String|Element}	input
	 * @param	{Boolean}			hasError
	 */
	setFieldErrorStatus: function(input, hasError) {
		this.setFieldStatus(input, hasError, 'error', 'errorMessge');
	},



	/**
	 * Mark field as warning/valid
	 *
	 * @method	setFieldErrorStatus
	 * @param	{String|Element}	input
	 * @param	{Boolean}			hasError
	 */
	setFieldWarningStatus: function(input, hasError) {
		this.setFieldStatus(input, hasError, 'warning', 'warningMessage');
	},



	/**
	 * Mark field with given error-level and handle Message field
	 *
	 * @param	{String|Element}	input
	 * @param	{Boolean}			hasError
	 * @param	{String}			level
	 * @param	{String}			htmlClassName
	 */
	setFieldStatus: function(input, hasError, level, htmlClassName) {
		var method	= hasError ? 'addClassName' : 'removeClassName';
		var field	= $(input).up('.fElement');

		if( field ) {
			field[method](level);
			field.down('.fLabel')[method](level);

				// Clear error message
			if( ! hasError ) {
				var errorMsg = field.down('.' + htmlClassName);
				if( errorMsg ) {
					errorMsg.update('');
				}
			}
		}
	},



	/**
	 * Check whether element is first input in form
	 * Ignore hidden fields
	 *
	 * @param	{String}	idElement
	 * @return	{Boolean}
	 */
	isFirstInputInForm: function(idElement) {
		var element = $(idElement);

		if( element ) {
			var firstElement = element.up('form').down(':input[type!=hidden]');

			if( firstElement ) {
				return firstElement.id === idElement;
			}
		}

		return false;
	}


};