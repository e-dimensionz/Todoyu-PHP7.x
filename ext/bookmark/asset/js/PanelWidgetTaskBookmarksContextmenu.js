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
 * @module	Bookmark
 */

/**
 *	Context menu for task entries of task bookmarks panel widget
 *
 * @class		ContextMenu
 * @namespace	Todoyu.Ext.bookmark.PanelWidget.TaskBookmarks
 */
Todoyu.Ext.bookmark.PanelWidget.TaskBookmarks.ContextMenu = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.bookmark,



	/**
	 * Attach context menu to bookmarks panel widget
	 *
	 * @method	attach
	 */
	attach: function() {
		Todoyu.ContextMenu.attach('TaskBookmarksPanelWidget', '.contextmenutaskbookmarks', this.getID.bind(this));
	},



	/**
	 * Detach context menu from bookmarks panel widget
	 *
	 * @method	detach
	 */
	detach: function() {
		Todoyu.ContextMenu.detach('.contextmenutaskbookmarks');
	},



	/**
	 * Extract ID of clicked bookmark from event
	 *
	 * @method	getID
	 * @param	{Element}	element
	 * @param	{Event}		event
	 * @return	{String}
	 */
	getID: function(element, event) {
		return event.findElement('li').id.split('-').last();
	}

};