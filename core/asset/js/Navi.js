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
 * @module	Core
 */

/**
 *	Todoyu Navigation
 *
 * @class		Navi
 * @namespace	Todoyu
 * @type		{Object}
 */
Todoyu.Navi = {

	/**
	 * Initialize
	 *
	 * @method	init
	 */
	init: function() {
		Todoyu.Ui.addWindowScrollObservers(this.onWindowScroll.bind(this));
		Todoyu.Ui.addWindowResizeObservers(this.onWindowResize.bind(this));
	},



	/**
	 * Un/hide to-top option depending on current window vertical scroll offset
	 *
	 * @method	onWindowScroll
	 * @param	{Event}		event
	 */
	onWindowScroll: function(event) {
		this.updateToTopButton();
	},


	/**
	 * @param	{Event}		event
	 */
	onWindowResize: function(event) {
		this.updateToTopButton();
	},



	/**
	 * Show or hide to-top button depending on scrollTop, update it' position
	 *
	 * @method	updateToTopButton
	 */
	updateToTopButton: function() {
		var scrollTop		= Todoyu.Ui.getScrollTop();
		var scrollTopButton	= this.getScrollTopButton();
		var isTopLinkVisible= Todoyu.Ui.isVisible(scrollTopButton);

		if( scrollTop > window.innerHeight / 8 ) {
			this.updateScrollTopButtonPosition();
			scrollTopButton.show();
		} else if( isTopLinkVisible ) {
			scrollTopButton.hide();
		}
	},



	/**
	 * @method	getScrollTopButton
	 * @return	{Element}
	 */
	getScrollTopButton: function() {
		return $('to-top');
	},



	/**
	 * Position top-button in bottom-right corner
	 *
	 * @method	updateScrollTopButtonPosition
	 */
	updateScrollTopButtonPosition: function() {
		var scrollTopButton	= this.getScrollTopButton();
		var rightContentEl	= $('right');
		var leftPosition	= Element.cumulativeOffset(rightContentEl).left + Element.getDimensions(rightContentEl).width + 50;

		scrollTopButton.setStyle({
			left:	leftPosition + 'px',
			top:	(window.innerHeight - (window.innerHeight / 6) ) + 'px'
		});
	}

};