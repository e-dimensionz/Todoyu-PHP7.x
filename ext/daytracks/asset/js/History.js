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
 * @module	Daytracks
 */

Todoyu.Ext.daytracks.History = {

	/**
	 * @property	popup
	 * @type		Object
	 */
	popup:			null,

	/**
	 * @property	popupID
	 * @type		String
	 */
	popupID:		'daytracks-history',

	/**
	 * @property	showDetails
	 * @type		Boolean
	 */
	showDetails:	false,



	/**
	 * Get popUp
	 *
	 * @method	getPopup
	 * @return	Object
	 */
	getPopup: function() {
		return Todoyu.Popups.getPopup(this.popupID);
	},



	/**
	 * Display daytracks history popUp
	 *
	 * @method	show
	 */
	show: function() {
		var url		= Todoyu.getUrl('daytracks', 'history');
		var options	= {
			parameters: {
				action:	'history'
			}
		};
		var title	= '[LLL:daytracks.ext.history.title]';

		this.popup	= Todoyu.Popups.open(this.popupID, title, 540, url, options);
	},



	/**
	 * Update shown history
	 *
	 * @method	update
	 */
	update: function() {
		var range	= $F('daytracks-history-rangeselector').split('-');

		if( $('daytracks-history-switchuserselector') ) {
			var user	= $F('daytracks-history-switchuserselector');
		}

		var url		= Todoyu.getUrl('daytracks', 'history');
		var options = {
			parameters: {
				action:		'history',
				year:		range[0],
				month:		range[1],
				details:	this.showDetails ? 1 : 0,
				user: user
			}
		};

		Todoyu.Popups.updateContent(this.popupID, url, options);
	},



	/**
	 * Toggle display of details of historic time tracks
	 *
	 * @method	toggleDetails
	 */
	toggleDetails: function() {
		this.showDetails = ! this.showDetails;
		this.update();
	}

};