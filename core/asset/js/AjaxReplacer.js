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
 * @module	Core
 */

/**
 * Modification of Ajax.Updater which replaces the target element
 *
 * @class		Replacer
 * @namespace	Todoyu.Ajax
 * @extends		{Ajax.Request}
 * @constructor
 */
Todoyu.Ajax.Replacer = Class.create(Ajax.Request, {

	/**
	 * Initialize AJAX replacer
	 *
	 * @method	initialize
	 * @param	{Function}		$super		Constructor of Ajax.Request
	 * @param	{String}		container
	 * @param	{String}		url
	 * @param	{Object}		[options]
	 */
	initialize: function($super, container, url, options) {
		options = options || { };
		options.onComplete = (options.onComplete || Prototype.emptyFunction).wrap(function(proceed, transport, json) {
			$(container).replace(transport.responseText);
			proceed(transport, json);
		});
		$super(url, options);
	}

});