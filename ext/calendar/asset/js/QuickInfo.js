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
 * Handle event quick infos
 */
Todoyu.Ext.calendar.QuickInfo	= {

	/**
	 * Install quick info for a source type
	 *
	 * @method	install
	 * @param	{String}	sourceName
	 */
	install: function(sourceName) {
		Todoyu.QuickInfo.install('event', this.getSelector(sourceName), this.getElementID.bind(this));
	},



	/**
	 * Uninstall quick info for a source type
	 *
	 * @method	uninstall
	 * @param	{String}	sourceName
	 */
	uninstall: function(sourceName) {
		Todoyu.QuickInfo.uninstall(this.getSelector(sourceName));
	},



	/**
	 * Get selector for source type
	 *
	 * @method	getSelector
	 * @param	{String}	sourceName
	 */
	getSelector: function(sourceName) {
		return '.event.source' + Todoyu.String.ucwords(sourceName);
	},



	/**
	 * Extract element ID from element
	 *
	 * @method	getElementID
	 * @param	{Element}	observedElement
	 * @param	{Event}	event
	 */
	getElementID: function(observedElement, event) {
		return observedElement.id.split('-').slice(1).join('-');
	}

};