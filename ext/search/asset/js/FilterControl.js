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

Todoyu.Ext.search.FilterControl = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:					Todoyu.Ext.search,



	/**
	 * Initialize search filter controls: install observers
	 *
	 * @method	init
	 */
	init: function() {
		this.installObservers();
	},



	/**
	 * Install observers
	 *
	 * @method	installObservers
	 */
	installObservers: function() {
		$('filtercontrol-conditions').on('change', this.onConditionsChange.bind(this));
		$('filtercontrol-conjunction').on('change', this.onConjunctionChange.bind(this));

		if( $('filtercontrol-sorting') ) {
			$('filtercontrol-sorting').on('change', this.onSortingChange.bind(this));
		}
	},






	/**
	 * Handler when condition changes (new filter-condition is selected for being added)
	 *
	 * @method	onConditionsChange
	 * @param	{Event}		event
	 */
	onConditionsChange: function(event, select) {
		var value 		= $F(select);
		var type		= value.split('_').first();
		var condition	= value.split('_').slice(1).join('_');

		select.selectedIndex = 0;

		this.ext.Filter.setFiltersetID(0);

		this.ext.Filter.addNewCondition(type, condition, null, false);
	},



	/**
	 * Handle change on conjunction select element
	 *
	 * @method	onConjunctionChange
	 * @param	{Event}	event
	 */
	onConjunctionChange: function(event, select) {
		this.ext.Filter.updateResults();
	},



	/**
	 * Handle change on sorting select element
	 *
	 * @method	onSortingChange
	 * @param	{Event}		event
	 * @param	{Element}	select
	 * @method	onSortingChange
	 */
	onSortingChange: function(event, select) {
		var name	= $F(select);
		var label	= select.options[select.selectedIndex].text;

		select.selectedIndex = 0;

		if( name != 0 ) {
			this.ext.Filter.Sorting.add(name, label, false);
		}
	},



	/**
	 * Get selected conjunction
	 *
	 * @method	getConjunction
	 * @return	{String}
	 */
	getConjunction: function() {
		return $F('filtercontrol-conjunction');
	},



	/**
	 * Set conjunction value
	 *
	 * @method	setConjunction
	 * @param	{String}	conjunction		AND or OR
	 */
	setConjunction: function(conjunction) {
		$('filtercontrol-conjunction').selectedIndex = (conjunction === 'AND' ? 0 : 1);
	}

};