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

Todoyu.Ext.project.TaskParentAC = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext: Todoyu.Ext.project,

	/**
	 * @property	field
	 * @type		Element
	 */
	field:			null,

	/**
	 * @property	url
	 * @type		String
	 */
	url:			null,

	/**
	 * @property	acContainer
	 * @type		Element
	 */
	acContainer:	null,

	/**
	 * @property	acOptions
	 * @type		Object
	 */
	acOptions: {
		paramName: 'query'
	},



	/**
	 * Init project task parent autocompleter 
	 *
	 * @method	init
	 * @param	{String}	idField
	 */
	init: function(idField) {
		this.field 		= $(idField);
		this.acContainer= $('taskparent-ac');
		this.url		= Todoyu.getUrl('project', 'taskparent-ac');

		new Ajax.Autocompleter(this.field, this.acContainer.id, this.url, this.acOptions);
	}

};