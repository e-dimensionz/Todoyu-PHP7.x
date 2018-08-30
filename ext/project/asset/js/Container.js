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
 * Task container
 *
 * @class		Container
 * @namespace	Todoyu.Ext.project
 */
Todoyu.Ext.project.Container = {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:	Todoyu.Ext.project,



	/**
	 * Evoke editing of given container (handled via task editing)
	 *
	 * @method	edit
	 * @param	{Number}	idContainer
	 */
	edit: function(idContainer) {
		this.ext.Task.edit(idContainer);
	},



	/**
	 * Clone container
	 *
	 * @method	clone
	 * @param	{Number}	idContainer
	 */
	clone: function(idContainer) {
		this.ext.Task.clone(idContainer);
	},



	/**
	 * Copy container
	 *
	 * @method	cut
	 * @param	{Number}	idContainer
	 */
	cut: function(idContainer) {
		this.ext.Task.cut(idContainer);
	},



	/**
	 * Copy container
	 *
	 * @method	copy
	 * @param	{Number}	idContainer
	 */
	copy: function(idContainer) {
		this.ext.Task.copy(idContainer);
	},



	/**
	 * Remove given container
	 *
	 * @method	remove
	 * @param	{Number}	idContainer
	 */
	remove: function(idContainer) {
		this.ext.Task.remove(idContainer, true);
	},



	/**
	 * Add sub task to container
	 *
	 * @method	addSubTask
	 * @param	{Number}	idContainer
	 */
	addSubTask: function(idContainer) {
		this.ext.Task.addSubTask(idContainer);
	},



	/**
	 * Add sub container to given container
	 *
	 * @method	addSubContainer
	 * @param	{Number}	idContainer
	 */
	addSubContainer: function(idContainer) {
		this.ext.Task.addSubContainer(idContainer);
	}

};