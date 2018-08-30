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
 * @module	Project
 */

/**
 *	Panel widget: StatusFilter JS
 */
Todoyu.Ext.project.PanelWidget.ProjectStatusFilter = Class.create(Todoyu.PanelWidgetStatusSelector, {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,

	/**
	 * PanelWidget ID
	 *
	 * @property	key
	 * @type		String
	 */
	key:	'projectstatusfilter',



	/**
	 * Initialize the panelWidget: setup properties, install element observers
	 *
	 * @method	initialize
	 * @param	{Function}	$super				Parent constructor: Todoyu.PanelWidgetStatusSelector.initialize
	 * @param	{Array}		selectedStatusIDs	Selected status IDs
	 */
	initialize: function($super, selectedStatusIDs) {
		$super('panelwidget-projectstatusfilter-list');
	},



	/**
	 * Handler when selection is changed
	 *
	 * @method	onChange
	 * @param	{Event}		event
	 */
	onChange: function(event) {
//		this.fireUpdate(this.key);
		this.savePreference();

		return true;
	},



	/**
	 * Save the current selected statuses as preference
	 *
	 * @method	savePreference
	 */
	savePreference: function() {
		var pref	= this.getSelectedStatuses().join(',');
		var action	= 'panelwidget' + this.key;

		Todoyu.Pref.save('project', action, pref, 0, this.onSaved.bind(this));
	},



	/**
	 * @method	onSaved 
	 */
	onSaved: function() {
		Todoyu.Hook.exec('project.projectstatus.changed', this.getSelectedStatuses());
	}

});