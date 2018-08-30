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
 * List assets
 *
 * @class		List
 * @namespace	Todoyu.Ext.assets
 */
Todoyu.Ext.assets.List = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.assets,



	/**
	 * Initialize list
	 *
	 * @method	initList
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	initList: function(idRecord, recordType) {
		this.addListObservers(idRecord, recordType);
	},



	/**
	 * Initialize control
	 *
	 * @method	initControl
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	initControl: function(idRecord, recordType) {
		this.toggleButtons(idRecord, recordType);
	},



	/**
	 * Toggle control buttons: hide download selection if no files available
	 *
	 * @method	toggleButtons
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	toggleButtons: function(idRecord, recordType) {
		var button = $(recordType + '-' + idRecord + '-asset-button-downloadselection');

		if( button ) {
			var method	= this.hasListElements(idRecord, recordType) ? 'show' : 'hide';
			button[method]();
		}
	},



	/**
	 * Check whether list exists and contains elements
	 *
	 * @method	hasListElements
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @return	{Boolean}
	 */
	hasListElements: function(idRecord, recordType) {
		var list = $(recordType + '-' + idRecord + '-assets-list');

		if( list ) {
			return !!list.down('tbody tr');
		}

		return false;
	},



	/**
	 * Add observers for list
	 *
	 * @method	addObservers
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	addListObservers: function(idRecord, recordType) {
		Todoyu.Ext.assets.QuickInfoAsset.install();

			// Check all button
		$(recordType + '-' + idRecord + '-assets-checkallbox').on('click', this.toggleSelectAll.bind(this, idRecord, recordType));

			// Select asset row
		var assetsTableBody	= $(recordType + '-' + idRecord + '-assets-tablebody');
		assetsTableBody.on('click', 'tr', this.select.bind(this));

			// Install actions on options of all asset item rows
		assetsTableBody.select('tr.asset').each(function(row){
			var idAsset	= row.id.split('-').last();

				// Click 'filename': download asset
			row.down('.filename a').on('click', 'td', this.handleDownloadClick.bind(this, idAsset));

				// @note asset preview is handled via quickInfo

				// Click 'visibility': toggle asset visibility
			if( row.down('a.visibility') ) {
				row.down('a.visibility').on('click', 'a', this.handleVisibilityToggle.bind(this, idAsset));
			}

				// Click 'download': download asset
			if( row.down('a.download') ) {
				row.down('a.download').on('click', 'td', this.handleDownloadClick.bind(this, idAsset));
			}

				// Click 'delete': (confirm and) delete asset
			if( row.down('a.delete') ) {
				row.down('a.delete').on('click', 'td', this.handleRemoveClick.bind(this, idAsset, recordType));
			}
		}, this);
	},





	/**
	 * Refresh assets list of given task
	 *
	 * @method	refresh
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	refresh: function(idRecord, recordType) {
		var list	= recordType + '-' + idRecord + '-assets-list';
		var url		= Todoyu.getUrl('assets', 'list');
		var options	= {
			parameters: {
				action:		'list',
				recordType:	recordType,
				record:		idRecord
			},
			onComplete: this.onRefreshed.bind(this, idRecord, recordType)
		};

		if( Todoyu.exists(list) ) {
			Todoyu.Ui.replace(list, url, options);
		} else {
			var target	= recordType + '-' + idRecord + '-tabcontent-assets';
			Todoyu.Ui.insert(target, url, options);
		}
	},



	/**
	 * Re-init after refresh
	 *
	 * @method	onRefreshed
	 * @param	{Number}		idRecord
	 * @param	{String}		recordType
	 * @param	{Ajax.Response}	response
	 */
	onRefreshed: function(idRecord, recordType, response) {
		this.initControl(idRecord, recordType);
		Todoyu.Ext.assets.QuickInfoAsset.install();
	},



	/**
	 * Select given asset
	 *
	 * @method	select
	 * @param	{Event}		event
	 * @param	{Element}	row
	 */
	select: function(event, row) {
		var idAsset	= $(row).id.split('-').last();
		var recordType = $(row).id.split('-').first();

		if( row.hasClassName('selected') ) {
			this.unCheck(idAsset, recordType);
		} else {
			this.check(idAsset, recordType);
		}
	},



	/**
	 * Toggle given asset visibility (hide from customers?)
	 *
	 * @method	handleVisibilityToggle
	 * @param	{Number}	idAsset
	 * @param	{Event}		event
	 * @param	{Element}	link
	 */
	handleVisibilityToggle: function(idAsset, event, link) {
		event.stop();

		$$('[id$=asset-' + idAsset + '-icon-public]').invoke('toggleClassName', 'not');

		this.ext.toggleVisibility(idAsset);
	},



	/**
	 * Download handler when clicking on a filename
	 *
	 * @method	handleDownloadClick
	 * @param	{Number}	idAsset
	 * @param	{Event}		event
	 * @param	{Element}	cell
	 */
	handleDownloadClick: function(idAsset, event, cell) {
		event.stop();

		this.ext.download(idAsset);
	},



	/**
	 * Handle download click
	 *
	 * @method	handleRemoveClick
	 * @param	{Number}	idAsset
	 * @param	{String}	recordType
	 * @param	{Event}		event
	 * @param	{Element}	cell
	 */
	handleRemoveClick: function(idAsset, recordType, event, cell) {
		event.stop();

		this.ext.remove(idAsset, recordType);
	},



	/**
	 * Set given asset checked
	 *
	 * @method	check
	 * @param	{Number}	idAsset
	 * @param	{String}	recordType
	 */
	check: function(idAsset, recordType) {
		this.handleCheck(idAsset, recordType, true);
	},



	/**
	 * Set asset unchecked
	 *
	 * @method	unCheck
	 * @param	{Number}	idAsset
	 * @param	{String}	recordType
	 */
	unCheck: function(idAsset, recordType) {
		this.handleCheck(idAsset, recordType, false);
	},



	/**
	 * @method	handleCheck
	 * @param	{Number}	idAsset
	 * @param	{String}	recordType
	 * @param	{Boolean}	check
	 */
	handleCheck: function(idAsset, recordType, check) {
		var idElement = recordType + '-asset-'+ idAsset;
		var assetElement = $(idElement);
		var method = check ? 'addClassName' : 'removeClassName';
		var idRecord = assetElement.up('tbody').id.split('-')[1];

		$(idElement + '-checkbox').checked = check;
		assetElement[method]('selected');

		this.toggleSelectionDownload(idRecord, recordType);
	},



	/**
	 * Toggle status of download selection button depending on the amount of selected assets
	 * Disable if no asset is selected
	 *
	 * @method	toggleSelectionDownload
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 */
	toggleSelectionDownload: function(idRecord, recordType) {
		var oneSelected = $(recordType + '-' + idRecord + '-assets-tablebody').select(':checkbox:checked').size() > 0;
		var method		= oneSelected ? 'enable' : 'disable';
		var button		= $(recordType + '-' + idRecord + '-asset-button-downloadselection');

		if( button ) {
			button[method]();
		}
	},



	/**
	 * Get assets checkbox elements of given task
	 *
	 * @method	getAllAssetsCheckboxes
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @param	{Boolean}	checkedOnly
	 * @return	{Element[]}
	 */
	getAssetsCheckboxes: function(idRecord, recordType, checkedOnly) {
		checkedOnly	= checkedOnly ? checkedOnly : false;

		var list	= $(recordType + '-' + idRecord + '-assets-tablebody');
		var selector	= checkedOnly ? 'input:checked' : 'input';

		return list.select(selector);
	},



	/**
	 * Get selected assets of given task
	 *
	 * @method	getSelectedAssets
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @return	{Number[]}	Asset IDs
	 */
	getSelectedAssets: function(idRecord, recordType) {
		var boxes	= this.getAssetsCheckboxes(idRecord, recordType, true);

		return boxes.collect(function(box) {
			return box.value;
		});
	},



	/**
	 * Check whether all assets of the given task are selected
	 *
	 * @method	areAllAssetsSelected
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @return	{Boolean}
	 */
	areAllAssetsSelected: function(idRecord, recordType) {
		var boxes	= this.getAssetsCheckboxes(idRecord, recordType, false);

		return boxes.all(function(box){
			return box.checked
		});
	},



	/**
	 * De/Select all assets of given task
	 *
	 * @method	selectAll
	 * @param	{Number}	idRecord
	 * @param	{String}	recordType
	 * @param	{Event}		event
	 */
	toggleSelectAll: function(idRecord, recordType,  event) {
		var allChecked	= this.areAllAssetsSelected(idRecord, recordType);
		var boxes		= this.getAssetsCheckboxes(idRecord, recordType, false);

		boxes.each(function(item){
			if( allChecked !== true ) {
				this.check(item.value, recordType);
			} else {
				this.unCheck(item.value, recordType);
			}
		}, this);

		$(recordType + '-' + idRecord + '-assets-checkallbox').checked = ! allChecked;
	}

};