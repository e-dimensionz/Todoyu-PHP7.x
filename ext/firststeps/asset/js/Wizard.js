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
 * Firststeps wizard
 */
Todoyu.Ext.firststeps.Wizard = {

	/**
	 * @property	Wizard
	 * @type		{Object}
	 */
	wizard: null,



	/**
	 * Open first steps wizard
	 *
	 * @method	open
	 */
	open: function() {
		Todoyu.Wizard.open('firststeps', this.onLoaded.bind(this));
	},



	/**
	 * Close wizard without confirmation
	 *
	 * @method	close
	 */
	close: function() {
		Todoyu.Wizard.close(true);
	},



	/**
	 * Handler when wizard was loaded
	 * Initialize event handlers
	 *
	 * @method	onLoaded
	 * @param	{String}		wizardName
	 * @param	{Ajax.Response}	response
	 */
	onLoaded: function(wizardName, response) {
		switch( Todoyu.Wizard.getStepName() ) {
			case 'jobtypes':
			case 'projectroles':
			case 'activities':
			case 'userroles':
				this.initAutoExtendingList(Todoyu.Wizard.getStepName());
				break;

			case 'company':
				break;

			case 'employees':
				this.initEmployees();
				break;

			case 'customers':
				this.initCustomers();
				break;

			case 'project':
				this.initProject();
				break;
		}
	},



	/**
	 * Initialize auto extending list
	 *
	 * @method	initAutoExtendingList
	 * @param	{String}	listClass
	 */
	initAutoExtendingList: function(listClass) {
		var list	= $('wizard').down('ul.' + listClass);

		new Todoyu.AutoExtendingList(list);
	},



	/**
	 * Initialize remove buttons. Add callback
	 *
	 * @method	initRemoveButtons
	 * @param	{Function}	callback
	 */
	initRemoveButtons: function(callback) {
		Todoyu.Wizard.getForm().down('ul.list').select('.remove').each(function(remove){
			remove.on('click', 'li', callback);
		}, this);
	},



	/**
	 * Initialize employee set
	 *
	 * @method	initEmployees
	 */
	initEmployees: function() {
		Todoyu.Wizard.setNoSave(true);

		this.initRemoveButtons(this.removeEmployee.bind(this));
	},



	/**
	 * Add new employee
	 *
	 * @method	addEmployee
	 */
	addEmployee: function() {
		Todoyu.Wizard.setNoSave(false);
		Todoyu.Wizard.submit('');
	},



	/**
	 * Remove an employee
	 *
	 * @method	removeEmployee
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	removeEmployee: function(event, element) {
		var idPerson	= element.id.split('-').last();
		var name		= element.innerHTML.stripTags();

		if( confirm("[LLL:firststeps.ext.wizard.employees.confirmRemove]\n\n" + name) ) {
			var url		= Todoyu.getUrl('firststeps', 'ext');
			var options	= {
				parameters: {
					action: 'removeEmployee',
					person: idPerson
				},
				onComplete: function(response) {
					Effect.BlindUp(element, {
						afterFinish: function() {
							element.remove();
						}
					});
				}
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Initialize customer step
	 *
	 * @method	initCustomers
	 */
	initCustomers: function() {
		Todoyu.Wizard.setNoSave(true);
		this.initRemoveButtons(this.removeCustomer.bind(this));
	},



	/**
	 * Remove a customer
	 *
	 * @method	removeCustomer
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	removeCustomer: function(event, element) {
		var idCompany	= element.id.split('-').last();
		var name		= element.innerHTML.stripTags();

		if( confirm("[LLL:firststeps.ext.wizard.customers.confirmRemove]\n\n" + name) ) {
			var url		= Todoyu.getUrl('firststeps', 'ext');
			var options	= {
				parameters: {
					action: 'removeCompany',
					company: idCompany
				},
				onComplete: function(response) {
					Effect.BlindUp(element, {
						afterFinish: function() {
							element.remove();
						}
					});
				}
			};

			Todoyu.send(url, options);
		}
	},



	/**
	 * Initialize project step
	 *
	 * @method	initProject
	 */
	initProject: function() {
		this.initRemoveButtons(this.removeAssignedPerson.bind(this));
	},



	/**
	 * Add a person to the project
	 * 
	 * @method	addPerson
	 */
	addPerson: function() {
		Todoyu.Wizard.submit('', this.onPersonAdded.bind(this));
	},



	/**
	 * Handler when person was added
	 *
	 * @method	onPersonAdded
	 * @param	{Ajax.Response}	response
	 */
	onPersonAdded: function(response) {
		$('data-field-id-person').selectedIndex = -1;
		$('data-field-id-role').selectedIndex = -1;
	},



	/**
	 * Remove assigned person from project
	 *
	 * @method	removeAssignedPerson
	 * @param	{Event}		event
	 * @param	{Element}	element
	 */
	removeAssignedPerson: function(event, element) {
		var idPerson	= element.id.split('-').last();
		var name		= element.innerHTML.stripTags();

		if( confirm("[LLL:firststeps.ext.wizard.project.confirmRemovePerson]\n\n" + name) ) {
			var url		= Todoyu.getUrl('firststeps', 'ext');
			var options	= {
				parameters: {
					action: 'removeAssignedPerson',
					project: 1,
					person: idPerson
				},
				onComplete: function(response) {
					Effect.BlindUp(element, {
						afterFinish: function() {
							element.remove();
						}
					});
				}
			};

			Todoyu.send(url, options);
		}
	}

};