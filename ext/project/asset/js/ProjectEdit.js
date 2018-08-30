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

Todoyu.Ext.project.Project.Edit = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.project,



	/**
	 * Create form wrap DIVs
	 *
	 * @method	createFormWrapDivs
	 * @param	{Number}	idProject
	 */
	createFormWrapDivs: function(idProject) {
		var idDetails	= 'project-' + idProject + '-details';
		var idData		= 'project-' + idProject + '-data';

			// Create data DIV above project details DIV element
		if( ! Todoyu.exists(idData) ) {
			var data = new Element('div', {
				id:		idData,
				'class':	'data edit'
			});

			$(idDetails).insert({
				top: data
			});
		}

			// Set data DIV visually to 'editing'-style, display it
		$(idData).addClassName('edit');
		$(idDetails).show();
	},



	/**
	 * Load project form
	 *
	 * @method	loadForm
	 * @param	{Number}	idProject
	 */
	loadForm: function(idProject) {
		var url 	= Todoyu.getUrl('project', 'project');
		var options = {
			parameters: {
				action:		'edit',
				project:	idProject
			},
			onComplete: this.onFormLoaded.bind(this, idProject)
		};
		var target	= 'project-' + idProject + '-data';

		Todoyu.Ui.update(target, url, options);
	},



	/**
	 * Form has been loaded
	 *
	 * @method	onFormLoaded
	 * @param	{Number}	idProject
	 */
	onFormLoaded: function(idProject) {
		Todoyu.Hook.exec('project.project.formLoaded', idProject);
	},



	/**
	 * Save project
	 *
	 * @method	save
	 * @param	{Element}	form
	 */
	save: function(form){
		Todoyu.Ui.closeRTE(form);

		var idProject	= form.id.split('-')[1];
		Todoyu.Hook.exec('project.project.preSaveProject', idProject);

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
	 * On created handler
	 *
	 * @method	onProjectCreated
	 * @param	{Number}	idProject
	 */
	onProjectCreated: function(idProject) {
		if( Todoyu.isInArea('project') ) {
			this.ext.ProjectTaskTree.openProject(idProject);

			Todoyu.Ui.scrollToTop();
		}
	},



	/**
	 * onSaved project custom event handler
	 *
	 * @method	onSaved
	 * @param	{Ajax.Response}		response
	 */
	onSaved: function(response){
		var idProject	= response.getTodoyuHeader('idProject');
		var idProjectOld= response.getTodoyuHeader('idProjectOld');

		var notificationIdentifier	= 'project.project.saved';

		if( response.hasTodoyuError() ) {
			this.updateFormDiv(idProjectOld, response.responseText);
			Todoyu.notifyError('[LLL:project.ext.save.error]', notificationIdentifier);
		} else {
			this.ext.ProjectTaskTree.removeProject(idProjectOld);
			this.ext.ProjectTaskTree.openProject(idProject);

			Todoyu.Ui.scrollToTop();

			Todoyu.Hook.exec('project.project.saved', idProject);
			Todoyu.notifySuccess('[LLL:project.ext.save.success]', notificationIdentifier);
		}
	},



	/**
	 * Update form DIV
	 *
	 * @method	updateFormDiv
	 * @param	{Number}		idProject
	 * @param	{String}		formHTML
	 */
	updateFormDiv: function(idProject, formHTML) {
		$('project-' + idProject + '-data').update(formHTML);
	},



	/**
	 * Cancel project editing / creation
	 *
	 * @method	cancel
	 * @param	{Number}	idProject
	 */
	cancel: function(idProject) {
		Todoyu.Ui.closeRTE('project-' + idProject + '-form');

		if( idProject === 0 ) {
				// If the form of a new project is canceled
			this.ext.ProjectTaskTree.removeProject(idProject);
			idProject = this.ext.ProjectTaskTree.getActiveProjectID();

				// If there is a project
			if( idProject !== false ) {
				this.ext.ProjectTaskTree.openProject(idProject);
				this.ext.ProjectTaskTree.moveTabToFront(idProject);
			} else {
					// No project-tab found? reload to show startup-wizard
				Todoyu.goTo('project');
			}
		} else {
				// If the for of an existing project is canceled
			this.ext.Project.showDetails(idProject);
			this.ext.TaskTree.toggle(idProject);

			this.ext.Project.refresh(idProject);
			Todoyu.Ui.scrollToTop();
		}

		Todoyu.Hook.exec('project.project.edit.cancelled', idProject);
	},



	/**
	 * Handler when customer/company field is autocompleted
	 *
	 * @method	onCompanyAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onCompanyAutocomplete: function(response, autocompleter) {
		if( response.isEmptyAcResult() ) {
			Todoyu.notifyInfo('[LLL:project.ext.ac.company.notFoundInfo]');
			return false;
		}
	},



	/**
	 * Handler when person field is autocompleted
	 *
	 * @method	onPersonAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onPersonAutocomplete: function(response, autocompleter) {
		if( response.isEmptyAcResult() ) {
			Todoyu.notifyInfo('[LLL:project.ext.ac.person.notFoundInfo]');
			return false;
		}
	},



	/**
	 * Handler when projectleader (person) field is autocompleted
	 *
	 * @method	onProjectleaderAutocomplete
	 * @param	{Ajax.Response}			response
	 * @param	{Todoyu.Autocompleter}	autocompleter
	 */
	onProjectleaderAutocomplete: function(response, autocompleter) {
		return this.onPersonAutocomplete(response, autocompleter);
	}

};