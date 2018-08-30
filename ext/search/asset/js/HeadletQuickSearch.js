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
 * @module	Search
 */

Todoyu.Ext.search.Headlet.QuickSearch = Class.create(Todoyu.Headlet, {

	/**
	 * Reference to extension
	 *
	 * @property	ext
	 * @type		Object
	 */
	ext:		Todoyu.Ext.search,

	/**
	 * @property	query
	 * @type		Element
	 */
	query:		null,

	/**
	 * @property	button
	 * @type		Element
	 */
	button:		null,

	/**
	 * @property	content
	 * @type		Element
	 */
	content:	null,

	/**
	 * @property	bodyClickObserver
	 * @type		Object
	 */
	bodyClickObserver: null,



	/**
	 * Initialize quick search headlet: install click observer, initialize search input autoCompleter value suggestion and modes selector
	 *
	 * @method	init
	 * @param	{Function}	$super		Parent constructor: Todoyu.Headlet.initialize
	 * @param	{String}	name
	 */
	initialize: function($super, name) {
		$super(name);

		this.query	= $('todoyusearchheadletquicksearch-query');

		if( this.query ) {
			this.button	= this.getButton();
			this.content= this.getContent();

			this.query.on('click', this.onQueryClick.bind(this));

			this.Suggest.init(this);
			this.Mode.init(this);
		}

	},



	/**
	 * Handle headlet button clicks: toggle headlet content visibility
	 *
	 * @method	onButtonClick
	 * @param	{Function}	$super		Todoyu.Headlet.onButtonClick
	 * @param	{Event}		event
	 */
	onButtonClick: function($super, event) {
		$super(event);

		if( this.isVisible() ) {
			this.focus();
		}

//		this.saveOpenStatus();
	},



	/**
	 * Upon clicking search query input: hide modes selection
	 *
	 * @method	onQueryClick
	 * @param	{Event}		event
	 */
	onQueryClick: function(event) {
		this.Mode.hideModes();
	},



	/**
	 * Upon click: hide mode selector and result suggestions.
	 *
	 * @method	onBodyClick
	 * @param	{Function}	$super		Todoyu.Headlet.onBodyClick
	 * @param	{Event}		event
	 */
	onBodyClick: function($super, event) {
		this.hideExtras();
	},



	/**
	 * Hide quick search content and extras
	 *
	 * @method	hide
	 * @param	{Function}	$super		Todoyu.Headlet.hide
	 */
	hide: function($super) {
		$super();

		this.hideExtras();
//		this.saveOpenStatus();
	},



	/**
	 * Hide extras of quick search: mode selector, result suggestions
	 *
	 * @method	hideExtras
	 */
	hideExtras: function() {
		this.Mode.hideModes();
		this.Suggest.hideResults();
	},



	/**
	 * Focus search query input field
	 *
	 * @method	focus
	 */
	focus: function() {
		this.query.select();
	},




	/**
	 * Submit quick search form
	 *
	 * @method	submit
	 * @todo	is disabled, check and enable
	 */
	submit: function() {
		//$('headlet-quicksearch-form').submit();
		Todoyu.notifyInfo('redirect to full search disabled at the moment');
	},



	/**
	 * If any search query given: submit search form
	 *
	 * @method	submitIfNotEmpty
	 */
	submitIfNotEmpty: function() {
		if( ! this.isEmpty() ) {
			this.submit();
		}
	},



	/**
	 * Get search query input
	 *
	 * @method	getValue
	 * @return	{String}
	 */
	getValue: function() {
		return $F(this.query).strip();
	},



	/**
	 * Check whether search query is empty
	 *
	 * @method	isEmpty
	 * @return	{Boolean}
	 */
	isEmpty: function() {
		return this.getValue() === '';
	},



	Mode: {

		/**
	 	 * Reference to extension
	 	 *
	 	 * @property	ext
	 	 * @type		Object
	 	 */
		ext: Todoyu.Ext.search,

		headlet: null,

		mode: 0,

		button: null,

		modes: null,

		positioned: false,



		/**
		 * Initialize quick search modes option: declare properties, setup click observer
		 *
		 * @method	init
		 * @param	{Object}	headlet
		 */
		init: function(headlet) {
			this.headlet = headlet;

			this.button = $('todoyusearchheadletquicksearch-mode-button');
			this.modes	= $('todoyusearchheadletquicksearch-modes');

			this.button.on('click', this.onModeButtonClick.bind(this));
			this.modes.on('click', 'li', this.onModeListClick.bind(this));
		},



		/**
		 * Show quick search modes selector
		 *
		 * @method	showModes
		 * @param	{Event}		event
		 */
		onModeButtonClick: function(event) {
			event.stop();

			if( this.modes.visible() ) {
				this.hideModes();
			} else {
				this.modes.show();

				if( ! this.positioned ) {
					this.positionModes();
				}

				var numModes = $('todoyusearchheadletquicksearch-modes').select('li').size();
				var newHeight= numModes * 21 + 26;
				$('todoyusearchheadletquicksearch-form').setStyle({
					height: newHeight + 'px'
				});

				this.headlet.Suggest.hideResults();
			}
		},



		/**
		 * Handler when clicked on mode list
		 *
		 * @method	onModeListClick
		 * @param	{Event}		event
		 * @param	{Element}	element
		 */
		onModeListClick: function(event, element) {
			event.stop();

			var mode	= element.className.replace('searchmode', '').toLowerCase();

			this.setMode(mode);
		},



		/**
		 * Hide quick search modes selector
		 *
		 * @method	hideModes
		 */
		hideModes: function() {
			this.modes.hide();
			$('todoyusearchheadletquicksearch-form').style.height='18px';
		},



		/**
		 * Activate given quick search mode
		 *
		 * @method	setMode
		 * @param	{String}	mode
		 */
		setMode: function(mode) {
			$('todoyusearchheadletquicksearch-mode').value = mode;
			$('todoyusearchheadletquicksearch-form').writeAttribute('class', 'icon searchmode' + mode.capitalize());

			this.hideModes();
			this.headlet.focus();
			this.headlet.Suggest.updateResults();
		},



		/**
		 * Get currently active quick search mode
		 *
		 * @method	getMode
		 * @return	{String}
		 */
		getMode: function() {
			return $F('todoyusearchheadletquicksearch-mode');
		},



		/**
		 * Set search modes sub menu position
		 *
		 * @method	positionModes
		 */
		positionModes: function() {
			var contentDim		= this.headlet.content.getDimensions();
			var modeWidth		= this.modes.getWidth();

			var top		= contentDim.height - 24;
			var left	= contentDim.width - modeWidth + 1;

			this.modes.setStyle({
//				position:	'absolute',
				left:		left + 'px'
			});

			this.positioned = true;
		}

	},



	Suggest: {

		/**
		 * Reference to extension
		 *
		 * @property	ext
		 * @type		Object
		 */
		ext:			Todoyu.Ext.search,

		headlet:		null,

		suggest:		null,

		delay:			0.5,

		navigatePos:	-1,

		navigateActive:	null,

		numElements:	0,

		timeout:		null,



		/**
		 * Initialize quick search query input suggesting
		 *
		 * @method	init
		 * @param	{Object}	headlet
		 */
		init: function(headlet) {
			this.headlet	= headlet;
			this.suggest	= $('todoyusearchheadletquicksearch-suggest');

				// Move suggest to body (to scroll)
			document.body.appendChild(this.suggest);
			this.headlet.query.on('keyup', this.onQueryChange.bind(this));
		},



		/**
		 * Handler when search query has changed
		 *
		 * @method	onQueryChange
		 * @param	{Event}		event
		 */
		onQueryChange: function(event) {
			window.clearTimeout(this.timeout);

				// Pressed [ENTER]
			if( event.keyCode === Event.KEY_RETURN ) {
				if( this.isNavigating() ) {
					this.goToActiveElement();
				} else {
					this.timeout = this.updateResults.bind(this).delay(this.delay);
				}
				return;
			}

				// Pressed navigation arrows
			if( event.keyCode === Event.KEY_DOWN || event.keyCode === Event.KEY_UP ) {
				if( this.suggest.visible() ) {
					var down = event.keyCode === Event.KEY_DOWN;
					this.navigate(down);
				}
				return;
			}

				// Pressed [ESC] (hide results or whole headlet)
			if( event.keyCode === Event.KEY_ESC ) {
				if( this.isResultsVisible() ) {
					this.hideResults();
				} else {
					this.headlet.hide();
				}
				return;
			}

			if( this.headlet.isEmpty() ) {
				this.hideResults();
			} else {
				this.timeout = this.updateResults.bind(this).delay(this.delay);
			}
		},



		/**
		 * Check if user is navigating in result list (up and down)
		 *
		 * @method	isNavigating
		 * @return	{Boolean}
		 */
		isNavigating: function() {
			return this.navigatePos > -1;
		},



		/**
		 * Enter description here...
		 *
		 * @method	goToActiveElement
		 */
		goToActiveElement: function() {
			eval(this.navigateActive.down().readAttribute('onclick'));
			this.hide();
		},



		/**
		 * Navigate in result list (up and down)
		 *
		 * @method	navigate
		 * @param	{Boolean}	down		Navigate down. Yes or No?
		 */
		navigate: function(down) {
				// Deactivate selection
			if( this.navigateActive !== null ) {
				this.navigateActive.removeClassName('active');
			}

				// Increment or decrement to new position
			if( down ) {
				this.navigatePos++;
			} else {
				this.navigatePos--;
			}

				// If navigating over the top, stop walking upwards and do nothing
			if( this.navigatePos <= -1 ) {
				this.navigatePos = -1;
				this.navigateActive = null;
				return;
			}

				// If navigating over the last element, set position to last element (stay on last element)
			if( this.navigatePos >= this.numElements ) {
				this.navigatePos = this.numElements-1;
			}

				// Select active element
			this.navigateActive = this.suggest.down('li li', this.navigatePos);

				// Set element active
			this.navigateActive.addClassName('active');
		},



		/**
		 * Update suggestion container with new results
		 *
		 * @method	updateResults
		 */
		updateResults: function() {
			if( this.headlet.isEmpty() ) {
				return;
			}

			var url		= Todoyu.getUrl('search', 'suggest');
			var options	= {
				parameters: {
					action:	'suggest',
					query:	this.headlet.getValue(),
					mode:	this.headlet.Mode.getMode()
				},
				onComplete:	this.onResultsUpdated.bind(this)
			};

			Todoyu.Ui.update(this.suggest, url, options);
		},



		/**
		 * Handler when results have been updated
		 *
		 * @method	onResultsUpdated
		 * @param	{Ajax.Response}		response
		 */
		onResultsUpdated: function(response) {
			this.navigatePos = -1;
			this.numElements = this.suggest.select('li li').size();

			this.showResults();
		},



		/**
		 * Show suggested results container on right position
		 *
		 * @method	showResults
		 */
		showResults: function() {
			var contentDim		= this.headlet.content.getDimensions();
			var contentOffset	= this.headlet.content.cumulativeOffset();
			var suggestDim		= this.suggest.getDimensions();

			this.suggest.setStyle({
				left:	contentOffset.left - suggestDim.width + contentDim.width - 1 + 'px',
				top:	contentOffset.top + contentDim.height + 'px'
			});

			Todoyu.Ui.scrollToTop();
			this.suggest.show();
		},



		/**
		 * Hide suggested results
		 *
		 * @method	hideResults
		 */
		hideResults: function() {
			this.suggest.hide();
		},



		/**
		 * Check whether results are visible
		 *
		 * @method	isResultsVisible
		 * @return  {Boolean}
		 */
		isResultsVisible: function() {
			return this.suggest.visible();
		}
	}

});