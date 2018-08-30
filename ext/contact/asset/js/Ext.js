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
 * Main contact object
 *
 * @class		Contact
 * @namespace	Todoyu.Ext
 */
Todoyu.Ext.contact = {

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
	 * @type	Todoyu.Ext.contact.PanelWidget.ContactSearch
	 */
	searchBox: null,


	/**
	 * Initialize
	 *
	 * @method	init
	 */
	init: function() {
		if( Todoyu.isInArea('contact') ) {
			this.initObservers();
			this.initPanelWidgets();

			Todoyu.Hook.add('core.listing.extended', Todoyu.Ext.contact.Company.initCompanyList.bind(Todoyu.Ext.contact.Company));
			Todoyu.Hook.add('core.listing.extended', Todoyu.Ext.contact.Person.initPersonList.bind(Todoyu.Ext.contact.Person));
		}
	},



	/**
	 * @method	initObservers
	 */
	initObservers: function() {
		this.initPersonQuickInfos();
		this.initListingObserver();
	},



	/**
	 * Initialize panel widgets
	 *
	 */
	initPanelWidgets: function() {

	},



	/**
	 * Get search text from panel widget
	 *
	 * @return	{String}
	 */
	getSearchText: function() {
		var searchWidget = Todoyu.R['panelwidgetsearchbox-contactsearch'];

		return searchWidget ? searchWidget.getSearchText() : '';
	},



	/**
	 * @method	initPersonQuickInfos
	 */
	initPersonQuickInfos: function() {
		$$('.quickInfoPerson').each(function(element) {
			Todoyu.Ext.contact.QuickInfoPerson.add(element.id);
		});
	},



	/**
	 * Install observer on contact listing
	 *
	 * @method	initObservers
	 */
	initListingObserver: function() {
		var typeKey	= this.getActiveTypeKey();

		if( Todoyu.exists('paging-' + typeKey) ) {
			$('paging-' + typeKey).on('click', 'td', this.onClickListTD.bind(this, typeKey));
		}
	},



	/**
	 * @method	onClickListTD
	 * @param	{String}		typeKey		'person'/'company'
	 * @param	{Event}			event
	 */
	onClickListTD: function(typeKey, event) {
		var parentSpan	= event.target.up('span');
		if( event.target.hasClassName('actions') || parentSpan && parentSpan.hasClassName('actions') ) {
			return ;
		}

		var itemID	= event.target.up('tr').id.split('-').last();
		switch( typeKey ) {
			case 'person':
				this.Person.show(itemID);
				break;
			case 'company':
				this.Company.show(itemID);
				break;
		}
	},



	/**
	 * Handler to be called on selecting tabs of contact
	 *
	 * @method	onTabSelect
	 * @param	{Event}		event
	 * @param	{String}	tab
	 */
	onTabSelect: function(event, tab) {
		this[tab.capitalize()].showList();
	},



	/**
	 * Update contact page content with response of AJAX request with given URL + options
	 *
	 * @method	updateContent
	 * @param	{String}		url
	 * @param	{Array}			options
	 * @param	{String}		[type]
	 */
	updateContent: function(url, options, type) {
		var typeKey	= type || url.split('controller=')[1];

		options.onComplete	= this.onContentUpdated.bind(this, typeKey, options.onComplete);

		Todoyu.Ui.updateContent(url, options);
	},



	/**
	 * Handler when content body has been updated - reinit listing observer
	 *
	 * @method	onContentUpdated
	 * @param	{String}		type		Type: person or company
	 * @param	{Function}		onComplete
	 * @param	{Ajax.Response}	response
	 */
	onContentUpdated: function(type, onComplete, response) {
		this.initObservers();

		this.setTabActive(type);

		if( onComplete ) {
			onComplete(response);
		}
	},



	/**
	 * Set tab active
	 *
	 * @method	setTabActive
	 * @param	{String}	type
	 */
	setTabActive: function(type) {
		Todoyu.Tabs.setActive('contact', type);
	},



	/**
	 * Switch display of contacts type to given type
	 *
	 * @method	changeType
	 * @param	{String}		type
	 */
	changeType: function(type) {
		this.setTabActive(type);

		var typeKey = type.capitalize();
		this[typeKey].showList();
	},



	/**
	 * Get key of currently active contact type
	 *
	 * @method	getActiveType
	 * @param	{String}		[listName]
	 * @return	{String}		'company' / 'person'
	 */
	getActiveTypeKey: function(listName) {
		listName	= listName || 'contact';

		return	Todoyu.Tabs.getActiveKey(listName);
	},



	/**
	 * Save contact pref
	 *
	 * @method	savePref
	 * @param	{String}	preference
	 * @param	{String}	value
	 * @param	{Number}	idItem
	 * @param	{String}	onComplete
	 */
	savePref: function(preference, value, idItem, onComplete) {
		Todoyu.Pref.save('contact', preference, value, idItem, onComplete);
	},



	/**
	 * Remove unused temporary contact (person / company) image files
	 *
	 * @method	removeUnusedImages
	 * @param	{Element}	form
	 * @param	{String}	typeKey		'person' / 'company'
	 */
	removeUnusedImages: function(form, typeKey) {
		var idRecord = form.id.split('-')[1];
		if( $(typeKey + '-' + idRecord + '-field-image-id') ) {
			var idImage	= $F(typeKey + '-' + idRecord + '-field-image-id')
			var url		= Todoyu.getUrl('contact', typeKey);

			var options = {
				parameters: {
					action:		'removeimage',
					idImage:	idImage
				}
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Check for duplicated contact information
	 *
	 * @param	{String}		fieldID
	 */
	checkForDuplicatedContactInformation: function(fieldID) {
		var value		= $(fieldID).getValue();

		var url = Todoyu.getUrl('contact', 'ext');
		var options = {
			parameters: {
				action:		'checkforduplicatedcontactinformation',
				value:		value
			},
			onComplete: this.onCheckForDuplicatedEntries.bind(this, fieldID)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Callback for duplicated-entry-check
	 *
	 * @param	{String}		fieldID
	 * @param	{Ajax.response}	response
	 */
	onCheckForDuplicatedEntries: function(fieldID, response) {
		var error = response.getTodoyuHeader('duplicates');
		Todoyu.Form.setFieldWarningStatus(fieldID, error);

		if( error ) {
			Todoyu.FormValidator.addWarningMessage(fieldID, response.responseText, !$(fieldID).up('.dialog'));
		}
	}
};