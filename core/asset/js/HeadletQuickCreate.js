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
 * Quickcreate headlet
 *
 * @package		Todoyu
 * @subpackage	Core
 */
Todoyu.CoreHeadlets.QuickCreate = Class.create(Todoyu.Headlet, {

	popupid: 'quickcreate',

	/**
	 * Popup reference
	 * @property	popup
	 * @type		Todoyu.Popup
	 */
	popup:	null,




	/**
	 * Handler: When clicked on menu entry
	 *
	 * @method	onMenuClick
	 * @param	{Event}		event
	 */
	onMenuClick: function(ext, type, event, item) {
		this.openTypePopup(ext, type);
		this.hide();
	},



	/**
	 * Open creator wizard popup
	 *
	 * @method	openTypePopup
	 * @param	{String}		ext
	 * @param	{String}		type
	 */
	openTypePopup: function(ext, type) {
		if( ! Todoyu.Popups.getPopup(this.popupid) ) {
			var ctrl 	= 'Quickcreate' + type;
			var url		= Todoyu.getUrl(ext, ctrl);
			var options	= {
				parameters: {
					action:	'popup',
					'area':		Todoyu.getArea()
				},
				onComplete: this.onPopupOpened.bind(this, ext, type)
			};
			var title	= '[LLL:core.global.create]' + ': ' + this.getMenuItemLabel(ext, type);
			var width	= 600;

			this.popup = Todoyu.Popups.show({
				id:				this.popupid,
				title:			title,
				width:			width,
				contentUrl: 	url,
				requestOptions: options
			});

			Todoyu.Hook.exec('headlet.quickcreate.' + type + '.popupOpened');
		}
	},



	/**
	 * Handler after popup opened: call mode's onPopupOpened-handler
	 *
	 * @method	onPopupOpened
	 * @param	{String}	ext
	 */
	onPopupOpened: function(ext, type) {
		$(this.name).addClassName(type);

		var quickCreateObject	= 'Todoyu.Ext.' + ext + '.QuickCreate' + Todoyu.String.ucwords(type);

		Todoyu.Popups.focusFirstField();

		Todoyu.callUserFunction(quickCreateObject + '.onPopupOpened');
	},




	/**
	 * Close wizard popup
	 *
	 * @method	closePopup
	 */
	closePopup: function() {
		this.popup.close();
	},



	/**
	 * Update quick create popup content
	 *
	 * @method	updatePopupContent
	 * @param	{String}		content
	 */
	updatePopupContent: function(content) {
		this.popup.setHTMLContent(content);
	}

});