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

Todoyu.Ext.project.QuickCreateProject = {

	/**
	 * Evoked upon opening of event quick create wizard popUp
	 *
	 * @method	onPopupOpened
	 */
	onPopupOpened: function() {
		Todoyu.Hook.exec('project.project.formLoaded', 0);
	},



	/**
	 * Save project
	 *
	 * @method	save
	 * @param	{Element}	form
	 * @return	{Boolean}
	 */
	save: function(form){
		Todoyu.Ui.closeRTE(form);

		$(form).request({
			parameters: {
				action:	'save',
				area:	Todoyu.getArea()
			},
			onComplete: this.onSaved.bind(this)
		});

		return false;
	},



	/**
	 * onSaved project handler - evoked after project has been created / saved
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response){
		var error					= response.hasTodoyuError();
		var notificationIdentifier	= 'project.quickcreateproject.saved';

		if( error ) {
			Todoyu.Headlets.getHeadlet('todoyuheadletquickcreate').updatePopupContent(response.responseText);
			Todoyu.notifyError('[LLL:project.ext.save.error]', notificationIdentifier);
		} else {
			var idProject	= response.getTodoyuHeader('idProject');
			Todoyu.Hook.exec('project.project.created', idProject);
			Todoyu.Hook.exec('project.project.saved', idProject);

			Todoyu.Popups.close('quickcreate');
			Todoyu.notifySuccess('[LLL:project.ext.save.success]', notificationIdentifier);
		}
	}

};