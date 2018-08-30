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
 * Context menu for tasks
*/
Todoyu.Ext.project.ContextMenuProject = {

	/**
	 * Attach project context menu
	 *
	 * @method	attach
	 */
	attach: function() {
		Todoyu.ContextMenu.attach('project', '.contextmenuproject', this.getID.bind(this));
	},



	/**
	 * Detach project context menu
	 *
	 * @method	detach
	 */
	detach: function() {
		Todoyu.ContextMenu.detach('.contextmenuproject');
	},



	/**
	 * Get ID of project the context menu refers to
	 *
	 * @method	getID
	 * @param	{Element}	element
	 * @param	{Event}		event
	 * @return	{String}
	 */
	getID: function(element, event) {
		return element.id.split('-')[1];
	}

};