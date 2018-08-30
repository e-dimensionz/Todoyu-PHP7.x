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
 * @module	Profile
 */

/**
 * Profile headlet object
 *
 * @class		Profile
 * @namespace	Todoyu.Ext.profile.Headlet
 */
Todoyu.Ext.profile.Headlet.Profile = Class.create(Todoyu.Headlet, {

	/**
	 * Handler for button clicks - redirect to profile
	 *
	 * @method	onButtonClick
	 * @param	{Event}		event
	 */
	onButtonClick: function(event) {
		Todoyu.goTo('profile', '', {}, '', !!event.ctrlKey);
	}

});