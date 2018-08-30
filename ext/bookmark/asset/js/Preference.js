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

Todoyu.Ext.bookmark.Preference = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.bookmark,



	/**
	 * Save given preference of bookmark extension
	 *
	 * @method	save
	 * @param	{String}	action
	 * @param	{String}	value
	 * @param	{String}	idItem
	 * @param	{Function}	onComplete
	 */
	save: function(action, value, idItem, onComplete) {
		Todoyu.Pref.save('bookmark', action, value, idItem, onComplete);
	},



	/**
	 * Save preference: current order of bookmark filters
	 *
	 * @method	saveOrder
	 * @param	{String}	value
	 */
	saveOrder: function(value) {
		var action = 'saveOrder';
		this.sendAction(action, value);
	},



	/**
	 * Wrapper method to evoke given bookmark action with given value
	 *
	 * @method	sendAction
	 * @param	{String}	action
	 * @param	{String}	value
	 */
	sendAction: function(action, value) {
		var url = Todoyu.getUrl('bookmark', 'preference');
		var options = {
			parameters: {
				action:	action,
				value:	value
			}
		};

		Todoyu.send(url, options);
	}

};