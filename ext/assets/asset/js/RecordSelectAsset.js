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
 * Methods for handling record items in forms
 *
 * @class		RecordSelectAsset
 * @namespace	Todoyu
 */
Todoyu.Ext.assets.RecordSelectAsset = Class.create(Todoyu.FormRecords, {

	/**
	 * @method	init
	 * @param	{Todoyu.FormRecords}		$super
	 */
	init: function($super) {
		Event.stopObserving(this.searchField);
		Event.stopObserving(this.results);
		Event.stopObserving(this.selection);

		this.searchField.on('change', this.onSelectAsset.bind(this));
		this.results.on('click', 'li', this.onResultItemSelect.bind(this));
		this.selection.on('click', 'span.remove', this.onSelectedItemRemove.bind(this));
	},



	/**
	 * @method	onSelectAsset
	 */
	onSelectAsset: function() {
		$(this.searchField).select('option').each(function(option){
			if(this.searchField.getValue() == option.value && !this.isSelected(this.searchField.getValue()) && this.searchField.getValue() != 0) {
				this.addSelectedItem(option.value, option.innerHTML, option.innerHTML);
			}
		}, this);

		this.searchField.setValue(0);
	},



	/**
	 * @method	isSelected
	 * @param	{Number}		selected
	 * @return	{Boolean}
	 */
	isSelected: function(selected) {
		var selectedIDs = this.getSelectedItemIDs();

		return selectedIDs.include(selected);
	}

});