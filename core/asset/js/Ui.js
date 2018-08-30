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
 * User Interface
 *
 * @class		Ui
 * @namespace	Todoyu
 */
Todoyu.Ui = {

	/**
	 * @property	bodyClickObservers
	 * @type		Array
	 */
	bodyClickObservers: [],

	/**
	 * @property	windwScrollObservers
	 * @type		Array
	 */
	windowScrollObservers: [],

	/**
	 * @property	windowResizeObservers
	 * @type		Array
	 */
	windowResizeObservers: [],

	/**
	 * RTE options "cache"
	 */
	rteOptions: false,

	/**
	 * Default calendar options
	 *
	 * @type	Object
	 */
	calendarDefaultOptions: false,

	/**
	 * Calendar options of instances
	 *
	 * @type	Object
	 */
	calendarOptions: {},



	/**
	 * Get options for RTE element
	 *
	 * @method	getRteOptions
	 * @param	{String}	idElement
	 * @param	{Object}	[extraOptions]
	 */
	getRteOptions: function(idElement, extraOptions) {
		extraOptions	= extraOptions || {};

		if( !this.rteOptions ) {
			this.rteOptions = {
				mode:					'exact',
				plugins:				'autoresize,paste',
				theme:					'advanced',
				width:					'100%',
				content_css:			'core/asset/css/tinymce.scss',
				valid_elements:			'strong/b,em/i,p,br,u,ol,ul,li,pre,span[style],hr,a[href|title|target=_blank]',
					/* Plugin Autoresize */
				autoresize_max_height:	400,
					/* Plugin Paste */
				paste_remove_spans:			true,
				paste_remove_styles:		true,
				paste_text_linebreaktype:	'p',
				paste_postprocess:			this.onTinyMcePasteCleanup,
					/* Advanced theme */
				theme_advanced_toolbar_location:	'bottom',
				theme_advanced_buttons1:	'bold,italic,strikethrough,|,bullist,numlist,outdent,indent,|,link,unlink,|,hr',
				theme_advanced_buttons2: 	'',
				theme_advanced_buttons3:	'',
				theme_advanced_statusbar_location: 'none'
			};
		}

			// Set element ID
		var elementOptions		= Object.clone(this.rteOptions);
		elementOptions			= Object.extend(elementOptions, extraOptions);
		elementOptions.elements = idElement;

		return elementOptions;
	},



	/**
	 * Update content of element
	 *
	 * @method	update
	 * @param	{Element|String}	container
	 * @param	{String}			url
	 * @param	{Object}			options
	 */
	update: function(container, url, options) {
		var containerEl	= $(container);
		options 		= Todoyu.Ajax.getDefaultOptions(options);

		this.closeRTE(containerEl);

		if( containerEl ) {
			return new Ajax.Updater(containerEl, url, options);
		} else {
			Todoyu.log('You tried to update "' + container + '" which is not part of the DOM! (No request sent)');
		}
	},



	/**
	 * Replace whole element
	 *
	 * @method	replace
	 * @param	{Element|String}	container
	 * @param	{String}			url
	 * @param	{Object}			options
	 */
	replace: function(container, url, options) {
		var containerEl	= $(container);
		options 		= Todoyu.Ajax.getDefaultOptions(options);

		this.closeRTE(containerEl);

		if( containerEl ) {
			return new Todoyu.Ajax.Replacer(containerEl, url, options);
		} else {
			Todoyu.log('You tried to replace "' + container + '" which is not part of the DOM!');
		}
	},



	/**
	 * Prepend before element
	 *
	 * @method	prepend
	 * @param	{String}	container
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	prepend: function(container, url, options) {
		options = Todoyu.Ajax.getDefaultOptions(options);
		options.insertion = 'before';

		return this.update(container, url, options);
	},



	/**
	 * Append to element
	 *
	 * @method	append
	 * @param	{String}	container
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	append: function(container, url, options) {
		options = Todoyu.Ajax.getDefaultOptions(options);
		options.insertion = 'after';

		return this.update(container, url, options);
	},



	/**
	 * Insert after element
	 *
	 * @method	insert
	 * @param	{String}	container
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	insert: function(container, url, options) {
		options = Todoyu.Ajax.getDefaultOptions(options);
		options.insertion = 'bottom';

		return this.update(container, url, options);
	},



	/**
	 * Hide element
	 *
	 * @method	hide
	 * @param	{String|Element}	idElement
	 */
	hide: function(idElement) {
		if( Todoyu.exists(idElement) ) {
			$(idElement).hide();
		}
	},



	/**
	 * Show element
	 *
	 * @method	show
	 * @param	{String}	idElement
	 */
	show: function(idElement) {
		if( Todoyu.exists(idElement) ) {
			$(idElement).show();
		}
	},



	/**
	 * Toggle element visibility
	 *
	 * @method	toggle
	 * @param	{String}	idElement
	 */
	toggle: function(idElement) {
		if( Todoyu.exists(idElement) ) {
			$(idElement).toggle();
		}
	},



	/**
	 * Update toggle (expand/collapse) icon
	 *
	 * @method	updateToggleIcon
	 * @param	{String}	elementPrefix
	 * @param	{String}	idElement
	 */
	updateToggleIcon: function(elementPrefix, idElement) {
		if( $(elementPrefix + idElement + '-details').visible() ) {
			$(elementPrefix + idElement + '-toggler').addClassName('expanded');
		} else {
			$(elementPrefix + idElement + '-toggler').removeClassName('expanded');
		}
	},



	/**
	 * Update element content
	 *
	 * @method	updateContent
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	updateContent: function(url, options) {
		return this.update('content', url, options);
	},



	/**
	 * Update content with new HTML
	 *
	 * @method	setContent
	 * @param	{String}		content
	 */
	setContent: function(content) {
		this.closeRTE('content');

		$('content').update(content);
	},



	/**
	 * Update content tabs DIV
	 *
	 * @method	setContentTabs
	 * @param	{String}		tabs
	 */
	setContentTabs: function(tabs) {
		$('content-tabs').update(tabs);
	},



	/**
	 * Update content body div
	 *
	 * @method	setContentBody
	 * @param	{String}		body
	 */
	setContentBody: function(body) {
		this.closeRTE('content-body');

		$('content-body').update(body);
	},



	/**
	 * Update content body with request
	 *
	 * @method	updateContentBody
	 * @param	{String}		url
	 * @param	{Object}		options
	 */
	updateContentBody: function(url, options) {
		return this.update('content-body', url, options);
	},



	/**
	 * Update (left column) panel
	 *
	 * @method	updatePanel
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	updatePanel: function(url, options) {
		return this.update('leftCol', url, options);
	},



	/**
	 * Update context menu
	 *
	 * @method	updateContextMenu
	 * @param	{String}	url
	 * @param	{Object}	options
	 */
	updateContextMenu: function(url, options) {
		return this.update('contextmenu', url, options);
	},



	/**
	 * Update page
	 *
	 * @method	updatePage
	 * @param	{String}	ext
	 * @param	{String}	controller
	 */
	updatePage: function(ext, controller) {
		var url = {ext: ext};

		if( Object.isString(controller) ) {
			if( !controller.empty ) 	{
				url.controller = controller;
			}
		}

		location.href = '?' + Object.toQueryString(url);
	},



	/**
	 * Refresh odd/even classnames of given list items
	 *
	 * @method	refreshListItemsParity
	 * @param	{Element[]}				items
	 */
	refreshListItemsParity: function(items) {
		var parity	= 'odd';

		items.each(function(item) {
			if( parity === 'odd' ) {
				item.replaceClassName('even', 'odd');
				parity	= 'even';
			} else {
				item.replaceClassName('odd', 'even');
				parity	= 'odd';
			}
		});

	},



	/**
	 * Disable screen by adding todoyu overlay
	 *
	 * @method	disableScreen
	 */
	disableScreen: function() {
		WindowUtilities.disableScreen('todoyu', 'overlay_modal', 0.7, '', document.body);
	},



	/**
	 * Enable screen by removing todoyu overlay
	 *
	 * @method	enableScreen
	 */
	enableScreen: function() {
		$('overlay_modal').remove();
	},



	/**
	 * Add hover effect to element
	 *
	 * @method	addHoverEffect
	 * @param	{Element|String}	element
	 */
	addHoverEffect: function(element) {
		element	= $(element);

		element.on('mouseover', this.hoverEffect.bind(this, true, element));
		element.on('mouseout', this.hoverEffect.bind(this, false, element));
	},



	/**
	 * Hover effect handler (handles both mouseOver/ Out)
	 *
	 * @method	hoverEffect
	 * @param	{Boolean}		over
	 * @param	{Element}		element
	 * @param	{Event}			event
	 */
	hoverEffect: function(over, element, event) {
		if( over ) {
			element.addClassName('hover');
		} else {
			element.removeClassName('hover');
		}
	},



	/**
	 * Set favIcon from file at given path
	 *
	 * @method	setFavIcon
	 * @param	{String}	hrefIcon
	 */
	setFavIcon: function(hrefIcon) {
		var link	= document.createElement('link');
		link.type	= 'image/x-icon';
		link.rel	= 'shortcut icon';

		link.href	= hrefIcon;

		$$('head')[0].appendChild(link);
	},



	/**
	 * Set favIcon back to original one
	 *
	 * @method	resetFavIcon
	 */
	resetFavIcon: function() {
		this.setFavIcon('favicon.ico');
	},



	/**
	 * Fix element anchor position
	 *
	 * @method	fixAnchorPosition
	 */
	fixAnchorPosition: function() {
		if( location.hash !== '') {
			var anchor	= location.hash.substr(1);

			this.scrollToElement.bind(this, anchor).delay(0.4);
		}
	},



	/**
	 * Scroll to given element
	 *
	 * @method	scrollToElement
	 * @param	{Element}		element
	 */
	scrollToElement: function(element) {
		var header = $('header'),
			fixedTop;

		element = $(element);

		if( header && element ) {
			fixedTop	= element.cumulativeOffset().top - header.getHeight();

			window.scrollTo(0, fixedTop);
		}
	},



	/**
	 * Scroll window content by given values
	 *
	 * @method	scrollBy
	 * @param	{Number}	x
	 * @param	{Number}	y
	 */
	scrollBy: function(x, y) {
		//alert('scroll: ' + y);
		window.scrollBy(x, y);
	},



	/**
	 * Scroll to top of the page
	 *
	 * @method	scrollToTop
	 */
	scrollToTop: function() {
		Effect.ScrollTo('header', {
			'duration': 0.3
		});
	},



	/**
	 * Collapse / expand element
	 *
	 * @method	collapseExpandElement
	 * @param	{Number}	idElement
	 * @param	{Element}	toggle
	 */
	collapseExpandElement: function(idElement, toggle) {
		var options = {
			'duration': 0.3
		};

		var content = $(idElement);

		if( content.visible() ) {
			Effect.SlideUp(content, options);
		} else {
			Effect.SlideDown(content, options);
		}

		toggle.toggleClassName('expand');
	},



	/**
	 * Evoke twinkeling effect upon given element
	 *
	 * @method	twinkle
	 * @param	{Element}		element
	 */
	twinkle: function(element) {
		Todoyu.Ui.hide(element);
		Effect.Appear(element);
	},



	/**
	 * Check whether given element is currently visible
	 *
	 * @method	isVisible
	 * @param	{Element}	element
	 * @return	{Boolean}
	 */
	isVisible: function(element) {
		if( Todoyu.exists(element) ) {
			return $(element).visible();
		} else {
			return false;
		}
	},



	/**
	 * Detect whether capsLock is on during given keyPress event
	 *
	 * @method	isCapsLock
	 * @param	{Event}		event
	 * @return	{Boolean}
	 */
	isCapsLock: function(event) {
		charCode = event.keyCode ? event.keyCode : event.which;
 		shiftKey = event.shiftKey ? event.shiftKey: ( charCode === 16 );

		return (( charCode >= 65 && charCode <= 90) && !shiftKey) || ((charCode >= 97 && charCode <= 122) && shiftKey);
	},



	/**
	 * Show duration picker
	 *
	 * @method	showDurationPicker
	 * @param	{String}			idElement		ID of element the picker belongs to
	 * @param	{Object}			[config]
	 * @return	Todoyu.TimePicker
	 */
	showDurationPicker: function(idElement, config) {
		config	= config || {};

			// Form element is part of a dialog? close picker when dialog is closed
		var parentDialog	= $(idElement).up('.dialog');
		if( Todoyu.exists(parentDialog) ) {
			parentDialog.on('close', this.hideDurationPicker.bind(this, idElement));
		}

		return new Todoyu.TimePicker(idElement, config);
	},



	/**
	 * Hide duration picker belonging to given form element
	 *
	 * @method	hideDurationPicker
	 * @param	{String}	idElement		ID of element the picker belongs to
	 */
	hideDurationPicker: function(idElement) {
		this.hide(idElement + '-durationpicker');
	},



	/**
	 * Set document title (shown in browser window title bar)
	 *
	 * @method	setTitle
	 * @param	{String}		title
	 */
	setTitle: function(title) {
		title	= Todoyu.String.html_entity_decode(title);

		document.title = title + ' - todoyu';
	},



	/**
	 * Get document title, without the " - todoyu" postfix (shown in browser window title bar)
	 *
	 * @method	getTitle
	 * @return	{String}
	 */
	getTitle: function(strip) {
		if( strip === false ) {
			return document.title;
		} else {
			return document.title.replace(/ - todoyu/, '');
		}
	},



	/**
	 * Observe body for click events
	 *
	 * @method	observeBody
	 */
	observeBody: function() {
		$(document.body).on('click', this.onBodyClick.bind(this));
	},



	/**
	 * Handler when clicked on the body
	 *
	 * @method	onBodyClick
	 * @param	{Event}		event
	 */
	onBodyClick: function(event) {
		this.bodyClickObservers.each(function(func){
			func(event);
		}, this);
	},


	/**
	 * Observe for scroll events
	 *
	 * @method	observeScroll
	 */
	observeScroll: function() {
		Event.observe(window, 'scroll', this.onWindowScroll.bind(this));
	},



	/**
	 * Observe for window resize
	 *
	 * @method	observeResize
	 */
	observeResize: function() {
		Event.observe(window, 'resize', this.onWindowScroll.bind(this));
	},



	/**
	 * @method	onWindowScroll
	 * @param	{Event}		event
	 */
	onWindowScroll:function(event) {
		this.windowScrollObservers.each(function(func){
			func(event);
		}, this);
	},



	/**
	 * @method	onWindowResize
	 * @param	{Event}		event
	 */
	onWindowResize:function(event) {
		this.windowResizeObservers.each(function(func){
			func(event);
		}, this);
	},



	/**
	 * Add an observer for the body
	 *
	 * @method	addBodyClickObserver
	 * @param	{Function}	func
	 */
	addBodyClickObserver: function(func) {
		this.bodyClickObservers.push(func);
	},



	/**
	 * Add a window scroll observer
	 *
	 * @method	addWindowScrollObservers
	 * @param	{Function}	func
	 */
	addWindowScrollObservers: function(func) {
		this.windowScrollObservers.push(func);
	},



	/**
	 * Add a window resize observer
	 *
	 * @method	addBodyClickObserver
	 * @param	{Function}	func
	 */
	addWindowResizeObservers: function(func) {
		this.windowResizeObservers.push(func);
	},



	/**
	 * Initialize RTE for element
	 *
	 * @method	initRTE
	 * @param	{String}	idElement
	 * @param	{Object}	extraOptions
	 * @param	{Object}	config
	 */
	initRTE: function(idElement, extraOptions, config) {
		var options = this.getRteOptions(idElement, extraOptions || {});

		tinyMCE.init(options);

		if( config.focus && Todoyu.Form.isFirstInputInForm(idElement) ) {
			this.setFocusOnActiveRTE.bind(this).delay(2);
		}
	},



	/**
	 * Focus active RTE editor
	 *
	 * @method	setFocusOnActiveRTE
	 */
	setFocusOnActiveRTE: function() {
		if( tinyMCE.activeEditor ) {
			tinyMCE.activeEditor.focus();
		} else {
			this.setFocusOnActiveRTE.bind(this).delay(1);
		}
	},



	/**
	 * Save all RTEs in the document
	 * Sometimes, double instances exist. Prevents saving if missing instances of an editor
	 * Use this function instead of tinyMCE.triggerSave();
	 *
	 * @method	saveRTE
	 */
	saveRTE: function() {
		window.tinyMCE.editors.each(function(editor, index){
			if( editor && Todoyu.exists(editor.editorId) ) {
				editor.save();
				return;
			}

				// Delete item if element does not exist
			delete window.tinyMCE.editors[index];
		});
	},



	/**
	 * Removes tinyMCE controls and save the editor
	 * Prevents "ghost" objects which will break the save process
	 *
	 * @method	closeRTE
	 * @param	{Element|String}	container		Area to look for tinyMCE instances (Can be a form, the whole window or the element itself)
	 */
	closeRTE: function(container) {
		container	= $(container);

		this.saveRTE();

		if( !container ) {
			container = $(document.body);
		}

			// Remove controls for all editors in the range
		container.select('textarea.RTE').each(function(textarea){
			if( tinyMCE.editors[textarea.id] ) {
				tinyMCE.execCommand('mceRemoveControl', false, textarea.id);
			}
		});
	},



	/**
	 * Initialize form records element
	 *
	 * @param	{String}	type
	 * @param	{String}	htmlId
	 * @param	{Object}	options
	 */
	initFormRecords: function(type, htmlId, options) {
		var x = new Todoyu.FormRecords(type, htmlId, options);
	},



	/**
	 * Initialize popup calendar
	 *
	 * @method	initCalendar
	 * @param	{Object}	fieldConfig
	 */
	initCalendar: function(fieldConfig) {
		fieldConfig	= this.buildCalendarFieldConfig(fieldConfig);

			// Store calendar options
		this.calendarOptions[fieldConfig.inputField] = Object.clone(fieldConfig);

			// Add validator if not disabled
		if( fieldConfig.validate !== false ) {
			Todoyu.DateField.addValidator(fieldConfig.inputField, fieldConfig.ifFormat);
		}

		Calendar.setup(fieldConfig);
	},



	/**
	 * Get custom calendar config
	 *
	 * @method	buildCalendarFieldConfig
	 * @param	{Object}	[fieldConfig]
	 * @return	{Object}
	 */
	buildCalendarFieldConfig: function(fieldConfig) {
		fieldConfig	= fieldConfig || {};

		if( !this.calendarDefaultOptions ) {
			this.calendarDefaultOptions = {
				range:		[1990,2020],
				align:		"br",
				firstDay:	1,
				onClose:	Todoyu.Helper.onCalendarDateChanged.bind(Todoyu.Helper)
			};
		}

			// Merge with default options
		fieldConfig = $H(this.calendarDefaultOptions).merge(fieldConfig).toObject();
			// Parse functions and arrays
		fieldConfig	= this.parseCalendarConfig(fieldConfig);

		return fieldConfig;
	},



	/**
	 * Parse calendar config which is given as string
	 * Convert functions and arrays which are given as string to their real format
	 *
	 * @method	parseCalendarConfig
	 * @param	{Object}	fieldConfig
	 * @return	{Object}
	 */
	parseCalendarConfig: function(fieldConfig) {
		var functions	= ['disableFunc', 'dateStatusFunc', 'flatCallback', 'onSelect', 'onClose', 'onUpdate'];
		var arrays		= ['range', 'position'];

		functions.each(function(functionName){
			if( fieldConfig[functionName] && !Object.isFunction(fieldConfig[functionName]) ) {
				fieldConfig[functionName] = Todoyu.getFunctionFromString(fieldConfig[functionName], true);
			}
		});

		arrays.each(function(arrayName){
			if( fieldConfig[arrayName] && !Object.isArray(fieldConfig[arrayName]) ) {
				fieldConfig[arrayName] = eval(fieldConfig[arrayName]);
			}
		});

		return fieldConfig;
	},



	/**
	 * Get calendar options for a field
	 *
	 * @method	getCalendarOptions
	 * @param	{String|Element}	field
	 */
	getCalendarOptions: function(field) {
		var options = this.calendarOptions[$(field).id];

		return options ? Object.clone(options) : {};
	},



	/**
	 * Center an element on the screen
	 *
	 * @method	centerElement
	 * @param	{Element|String}	element
	 */
	centerElement: function(element) {
		element			= $(element);
		var elementDim	= element.getDimensions();
		var screenDim	= document.viewport.getDimensions();

		var left	= parseInt((screenDim.width-elementDim.width)/2);
		var top		= parseInt((screenDim.height-elementDim.height)/2);

		element.setStyle({
			'top': top + 'px',
			'left':left + 'px'
		});

		return element;
	},



	/**
	 * Build a button element
	 *
	 * @method	buildButton
	 * @param	{String}	id
	 * @param	{String}	className
	 * @param	{String}	label
	 * @param	{Function}	onClick
	 */
	buildButton: function(id, className, label, onClick) {
		var button	= new Element('button', {
			title:	label,
			'class':'button ' + className,
			type:	'button',
			id:		id
		});
		button.insert(new Element('span', {
			'class': 'icon'
		}));
		button.insert(new Element('span', {
			'class': 'label'
		}).update(label));
		button.insert(new Element('span', {
			'class': 'rgt'
		}));

		if( onClick ) {
			button.on('click', 'button', onClick);
		}

		return button;
	},



	/**
	 * TinyMCE paste plugin callback
	 * Remove first <br> tag from pasted text (prevents line break before content in webkit)
	 *
	 * @method	onTinyMcePasteCleanup
	 * @param	{Object}	plugin
	 * @param	{Object}	pasteObject
	 */
	onTinyMcePasteCleanup: function(plugin, pasteObject) {
		pasteObject.node.innerHTML = pasteObject.node.innerHTML.replace('<br />', '');
	},



	/**
	 * @method	showInfoBalloon
	 * @param	{String}	key
	 */
	showInfoBalloon: function(key) {
		$('info-balloon-' + key).show();
	},



	/**
	 * @method	hideInfoBalloon
	 * @param	{String}	key
	 */
	hideInfoBalloon: function(key) {
		$('info-balloon-' + key).hide();
	},



	/**
	 * Get window vertical scroll offset
	 *
	 * @method	getScrollTop
	 * @returns {Number}
	 */
	getScrollTop: function(){
	    if(typeof pageYOffset!= 'undefined'){
	        return pageYOffset;
	    } else{
	        var elBody		= document.body; //IE 'quirks'
	        var elDocument	= document.documentElement; //IE with doctype

	        elDocument		= elDocument.clientHeight? elDocument : elBody;

	        return elDocument.scrollTop;
	    }
	},



	/**
	 * Get window inner height
	 *
	 * @returns {Number}
	 */
	getWindowInnerHeight: function() {
		return window.innerHeight || window.document.documentElement.clientHeight ||  window.document.body.clientHeight;
	}

};