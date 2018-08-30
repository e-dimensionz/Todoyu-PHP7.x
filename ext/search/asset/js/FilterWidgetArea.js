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
 * @module	Search
 */

Todoyu.Ext.search.Filter.WidgetArea = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.search,

	/**
	 * @property	areaID
	 * @type		String
	 */
	areaID: 'widget-area',

	/**
	 * @property	autocompleters
	 * @type		Object
	 */
	autocompleters: {},

	/**
	 * Special configuration added by some widgets, this is a temporary container for widget config
	 *
	 * @property	specialConfig
	 * @type		Object
	 */
	specialConfig: {},



	/**
	 * Add given filter widget to search page widget area
	 *
	 * @method	add
	 * @param	{String}	name
	 * @param	{String}	type
	 * @param	{String}	condition
	 * @param	{String}	value
	 * @param	{Boolean}	negate
	 * @param	{Function}	onComplete
	 */
	add: function(name, type, condition, value, negate, onComplete) {
		var url		= Todoyu.getUrl('search', 'widgetarea');
		var options	= {
			parameters: {
				action:		'add',
				name:		name,
				type:		type,
				condition:	condition,
				value:		value,
				negate:		negate ? 1 : 0
			},
			onComplete:	this.onAdded.bind(this, name, condition, value, onComplete)
		};
		var target	= this.areaID;

		Todoyu.Ui.insert(target, url, options);
	},



	/**
	 * Evoked after adding filter widget. Installs widget autoCompleter and negation handling, focuses the widget
	 *
	 * @method	onAdded
	 * @param	{String}			name
	 * @param	{String}			condition
	 * @param	{String|Null}		value
	 * @param	{String}			onComplete
	 * @param	{Ajax.Response}		response
	 */
	onAdded: function(name, condition, value, onComplete, response) {
		var widgetID	= condition + '-' + name;

		this.installAutocomplete.bind(this).defer(widgetID);
		this.installNegation.bind(this).defer(widgetID);

		this.focusWidget(widgetID);

		if( value === null ) {
			value = this.updateConditionValueFromWidget(widgetID);
		}

		if( onComplete ) {
			onComplete(name, condition, value, response);
		}
	},



	/**
	 * Try to fetch the value from widget from element
	 *
	 * @method	updateConditionValueFromWidget
	 * @param	{String}	widgetID
	 * @return	{String|Array|Null}
	 */
	updateConditionValueFromWidget: function(widgetID) {
		var widget = $(widgetID), valueField, value;

		if( widget) {
			valueField = widget.down(':input.value');

			if( valueField ) {
				value = $F(valueField);

				this.ext.Filter.setConditionValue(widgetID, value);

				return value;
			}
		}

		return null;
	},



	/**
	 * Remove given widget from widget area
	 *
	 * @method	remove
	 * @param	{String}	name
	 */
	remove: function(name) {
		$(name).remove();
	},



	/**
	 * Clear widget area (refresh)
	 *
	 * @method	clear
	 */
	clear: function() {
		$(this.areaID).update('');
	},



	/**
	 * Get amount of filter widgets in widget area
	 *
	 * @method	getNumOfWidgets
	 * @return	{Number}
	 */
	getNumOfWidgets: function() {
		return $(this.areaID).select('.filterWidget').size();
	},



	/**
	 * Focus form element of given filter widget
	 *
	 * @method	focusWidget
	 * @param	{String}	widgetID
	 */
	focusWidget: function(widgetID) {
		var formElement	= $(widgetID).down('.widgetbody').children[0];

		formElement.focus();
	},



	/**
	 * Install autoCompleter to ('textAC' input field of) given filter widget
	 *
	 * @method	installAutocomplete
	 * @param	{String}	name
	 * @param	{Number}	[index]
	 */
	installAutocomplete: function(name, index) {
		var index	= index || 0;

		if( $(name) ) {
			var acField = $(name).select('input.textAC')[index];

			if( Object.isElement(acField) ) {
				var acUrl	= Todoyu.getUrl('search', 'filtercontroller');
//				var widgetID= acField.id.split('-').slice(2, 4).join('-');
				var options	= {
					parameters:	Object.toQueryString({
									action:			'autocompletion',
									completionID:	name,
									filtertype:		this.ext.Filter.getActiveTab()
								}),
					paramName:			'sword',
					minChars:			2,
					afterUpdateElement:	this.onAutocompleteSelect.bind(this, name),
					onCleared:			this.onAutocompleteCleared.bind(this, name)
				};
				var suggestID= acField.id + '-suggestions';

					// Override config with specialConfig if available
				if( this.specialConfig[name] && this.specialConfig[name]['acOptions'] ) {
					options = $H(options).merge(this.specialConfig[name]['acOptions']).toObject();

					if( typeof options.afterUpdateElement === 'string' ) {
						options.afterUpdateElement = Todoyu.getFunctionFromString(options.afterUpdateElement, true).bind(this, name);
					}
				}

				this.autocompleters[name] = new Todoyu.Autocompleter(acField, suggestID, acUrl, options);
			}
		}
	},



	/**
	 * Handle selection of autoCompleter suggestion: send value to condition of widget
	 *
	 * @method	onAutocompleteSelect
	 * @param	{String}	name
	 * @param	{Element}	textInput
	 * @param	{Element} 	listElement
	 */
	onAutocompleteSelect: function(name, textInput, listElement) {
		var idItem	= listElement.id;

		this.ext.Filter.updateConditionValue(name, idItem);
	},



	/**
	 * Handler when autocompleter was cleared because of invalid value
	 *
	 * @method	onAutocompleteCleared
	 * @param	{String}				name		Name of the filter
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onAutocompleteCleared: function(name, autocompleter) {
		this.ext.Filter.updateConditionValue(name, '');
	},



	/**
	 * Install widget negating option click observer
	 *
	 * @method	installNegation
	 * @param	{String}	name
	 */
	installNegation: function(name) {
		if( $(name) ) {
			var negElement = $(name).select('span.negation')[0];

			if( Object.isElement(negElement) ) {
				negElement.on('click', 'span.negation', this.onNegation.bind(this, name));
			}
		}
	},



	/**
	 * Handle filter widget negation: invoke toggle of child elements' conditions
	 *
	 * @method	onNegation
	 * @param	{Event}		event
	 * @param	{String}	name
	 */
	onNegation: function(name, event, element) {
		this.ext.Filter.toggleConditionNegation(name);

		element.childElements().invoke('toggle');
	},



	/**
	 * Add given "special" configuration to widget area
	 *
	 * @method	addSpecialConfig
	 * @param	{String}	name
	 * @param	{Object}	config
	 */
	addSpecialConfig: function(name, config) {
		this.specialConfig[name] = config;
	}

};