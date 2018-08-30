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
 * Project portal
 *
 * @class		Portal
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.Portal = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.project,



	/**
	 * Initialize portal JS
	 *
	 * @method	init
	 */
	init: function() {
		Todoyu.Hook.add('project.project.created', this.onProjectCreate.bind(this));
		Todoyu.Hook.add('project.project.saved', this.onProjectSaved.bind(this));
	},



	/**
	 * Handler after project has been created: evoke refresh of project listing
	 *
	 * @method	onProjectCreate
	 * @param	{Number}	idProject
	 */
	onProjectCreate: function(idProject) {
		this.refreshProjectListing();
	},



	/**
	 * Handler after project has been saved: evoke refresh of project listing
	 *
	 * @method	onProjectSaved
	 * @param	{Number}	idProject
	 */
	onProjectSaved: function(idProject) {
		this.refreshProjectListing();
	},



	/**
	 * Check whether selection tab is active and project list exists in DOM
	 *
	 * @method	isProjectListingActive
	 * @return	{Boolean}
	 */
	isProjectListingActive: function() {
		if( Todoyu.Ext.portal.Tab.getActiveTab() === 'selection' ) {
			return Todoyu.exists('projectlist');
		}

		return false;
	},



	/**
	 * Evoke refresh of project listing (if in selection tab with existing listing)
	 *
	 * @method	refreshProjectListing
	 */
	refreshProjectListing: function() {
		if( this.isProjectListingActive() ) {
			Todoyu.Ext.portal.Tab.refresh();
		}
	}

};