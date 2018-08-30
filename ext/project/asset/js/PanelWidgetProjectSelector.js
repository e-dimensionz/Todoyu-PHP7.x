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
 * Panel widget: project(s) selector
 *
 * @class
 * @extends		Todoyu.PanelWidgetSearchList
 */
Todoyu.Ext.project.PanelWidget.ProjectSelector = Class.create(Todoyu.PanelWidgetSearchList, {

	/**
	 * Selection element
	 * @var	{Element}	selection
	 */
	selection: null,

	/**
	 * Timeout for save request
	 */
	timeoutSave: null,

	/**
	 * Save group button
	 * @var	{Element}	buttonSaveGroup
	 */
	buttonSaveGroup: null,


	/**
	 * Constructor Initialize with search word
	 *
	 * @method	initialize
	 * @param	{Function}	$super		Parent constructor: Todoyu.PanelWidgetSearchList.initialize
	 * @param	{String}	search
	 */
	initialize: function($super, search) {
		$super({
			id:			'projectselector',
			search:		search,
			ext:		'project',
			controller:	'panelwidgetprojectselector',
			action:		'list'
		});

		this.config.actionSelection	= 'save';
			// Save reference to selection list
		this.selection	= $('panelwidget-projectselector-selection');

		this.buttonSaveGroup	= $('panelwidget-projectselector-button-savegroup');
		this.buttonSaveGroup.remove();	//@todo implement group handling


		this.initProjectSelectorObservers();

		this.addItemsIcons(true, true, true);

		this.markFirstAsHot();
	},



	/**
	 * Initialize search input and list observers
	 *
	 * @method	initObservers
	 */
	addPanelWidgetObservers: function() {
		this.input.on('keyup', this.onSearchKeyUp.bind(this));
		this.list.on('click', '', this.onItemClick.bind(this));
	},




	/**
	 * Init project selector specific observers
	 *
	 * @method	initProjectSelectorObservers
	 */
	initProjectSelectorObservers: function() {
			// Observe selection list for disable and remove clicks
		this.selection.on('click', 'li', this.onSelectionItemClick.bind(this));
		this.selection.on('click', 'li span.remove', this.onRemoveClick.bind(this));
		this.selection.on('click', 'li span.deletegroup', this.onDeleteGroupClick.bind(this));

			// Observe search result item clicks
		this.list.on('click', 'li span.deletegroup', this.onDeleteGroupClick.bind(this));

			// Observe input for return clicks
		this.input.on('keyup', this.onInputKeyUps.bind(this));

			// Observe "save selection as group" button
		this.buttonSaveGroup.on('click', 'button', this.onSaveGroupButtonClick.bind(this));
	},



	/**
	 * Add icons to listed items of search results and selection
	 *
	 * @method	addItemsIcons
	 * @param	{Boolean}	addDeleteGroupIcons
	 * @param	{Boolean}	addAddIcons
	 * @param	{Boolean}	addRemoveIcons
	 */
	addItemsIcons: function(addDeleteGroupIcons, addAddIcons, addRemoveIcons) {
			// Add (+) select icon to all selectable virtual group, group and project items
		if( addAddIcons ) {
			this.addAddIconsToList();
		}

			// Add "delete group" icon to all virtual group items
		if( addDeleteGroupIcons ) {
			var items	= $('panelwidget-projectselector-content').select('li.virtualgroup a');
			this.addDeleteGroupIcons(items);
		}

			// Add (X) removal icon to all active selection items
		if( addRemoveIcons ) {
			this.addRemoveIconToItems();
		}
	},



	/**
	 * Handler when clicked on item
	 *
	 * @method	onItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onItemClick: function(event, item) {
			// Click on anchor or (+) add icon
		if( ! item.hasClassName('deletegroup') ) {
			this.addItemToSelection(item);
			this.saveSelection();
		}
	},



	/**
	 * Handler for keyup event in search input
	 * Handle return key press to add the hot element
	 * to the selection
	 *
	 * @method	onInputKeyUps
	 * @param	{Event}		event
	 */
	onInputKeyUps: function(event) {
		if( event.keyCode === Event.KEY_RETURN ) {
			this.onReturnKey();
		}
	},



	/**
	 * Handle enter key press in search field
	 *
	 * @method	onReturnKey
	 */
	onReturnKey: function() {
			// Add "hot" item to selection
		var firstItem	= this.list.down('li');
		if( firstItem ) {
			this.addItemToSelection(firstItem);
			this.input.select();
			this.saveSelection();
			this.markFirstAsHot();
		} else {
				// Activate first highlighted item from selection
			this.toggleMatchingElementsInSelection();
			this.saveSelection();
		}
	},



	/**
	 * Toggle activeness of all elements matching the search text
	 *
	 * @method	toggleMatchingElementsInSelection
	 */
	toggleMatchingElementsInSelection: function() {
		this.getMatchingSelectionElements(this.getSearchText()).invoke('toggleClassName', 'disabled');
	},



	/**
	 * Get first highlighted (if any) item from projets selection
	 *
	 * @method	getAllSelectedAndHighlightedItems
	 * @return	{Element[]}
	 */
	getAllSelectedAndHighlightedItems: function() {
		return this.selection.select('li').findAll(function(item) {
			return item.style.backgroundColor !== '';
		});
	},



	/**
	 * Mark first item in result list as hot
	 * Hot means, when the user presses return,
	 * this item will be added to the selection
	 *
	 * @method	markFirstAsHot
	 */
	markFirstAsHot: function() {
		this.list.select('li').invoke('removeClassName', 'hot');

		var first	= this.list.down('li');

		if( first ) {
			first.addClassName('hot');
		}
	},



	/**
	 * Add an item to the selection list
	 *
	 * @method	addItemToSelection
	 * @param	{Element}	item
	 */
	addItemToSelection: function(item) {
			// "normalize" item - get LI tag of clicked item
		var itemTagName = item.tagName ? item.tagName.toLowerCase() : '';
		if( itemTagName !== 'li') {
			item	= item.up('li');
		}
			// Remove add button from item
		if( item.down('span.add') ) {
			item.down('span.add').remove();
		}

			// Remove 'no items' label from empty selection
		if( this.isSelectionEmpty() ) {
			this.selection.update('');
		}

			// Move item to selection
		this.selection.insert({
			bottom: item
		});

			// "Virtual" group: Add "delete group" icon to item
		if( item.hasClassName('virtualgroup') ) {
			this.addDeleteGroupIconToItems([item]);
		}
			// Add "remove from selection" button to item
		this.addRemoveIconToItems([item]);

			// Sort selection items
		var nodes	= this.selection.select('li');
		this.sortNodes(nodes);

			// Highlight new item
		new Effect.Highlight(item, {
			duration:		2.0,
			afterFinish:	function() {
				this.removeAttribute('style');
			}.bind(item)
		});
	},



	/**
	 * Sort selection items (virtual groups, groups then projects by alphabet)
	 *
	 * @method	sortSelect
	 * @param	{Element[]}		nodes
	 */
	sortNodes: function(nodes) {
			// Collect nodes grouped by type
		var hashProjects			= {};
		var hashVirtualGroups	= {};
		var hashGroups			= {};

		nodes.each(function(item) {
			var hash = item.hasClassName('project') ? hashProjects : ( item.hasClassName('group') ? hashGroups : hashVirtualGroups);
			var label	= item.down('a').innerHTML.stripTags().strip();
			hash[label] = item;
		});

			// Update selection with sorted item nodes
		this.selection.update('');

		this.insertSelectionNodesSorted(hashVirtualGroups);
		this.insertSelectionNodesSorted(hashGroups);
		this.insertSelectionNodesSorted(hashProjects);
	},



	/**
	 * Insert given items alphabetically sorted into selection
	 *
	 * @method	insertSelectionNodesSorted
	 * @param	{Object}	hashItems
	 */
	insertSelectionNodesSorted: function(hashItems) {
		var sortedItems	= Object.keys(hashItems).sort();

		sortedItems.each(function(key){
			this.selection.insert(hashItems[key]);
		}, this);
	},



	/**
	 * Handler when clicked on a selection item: enable / disable that item
	 *
	 * @method	onSelectionItemClick
	 * @param	{Event}		event
	 * @param	{Element}	item
	 */
	onSelectionItemClick: function(event, item) {
		var eventElement	= event.element();

		if( ! eventElement.hasClassName('remove') && ! eventElement.hasClassName('deletegroup') ) {
			item.toggleClassName('disabled');
			this.saveSelection();
		}
	},



	/**
	 * Handler when clicked on delete group icon
	 *
	 * @method	onDeleteGroupClick
	 * @param	{Event}		event
	 * @param	{Element}	deleteIcon
	 */
	onDeleteGroupClick: function(event, deleteIcon) {
		event.stop();

		if( confirm('[LLL:project.panelwidget-projectselector.deletegroup.confirm]') ) {
			var item	= deleteIcon.up('li');
			var idPref = item.id.split('-')[3].replace('v', '');

			this.deleteGroup(idPref);
		}
	},



	/**
	 * Delete group preference with given ID
	 *
	 * @method	deleteGroup
	 * @param	{Number}		idPref
	 */
	deleteGroup: function(idPref) {
			// Delete from selection pref
		var url = Todoyu.getUrl('project', 'panelwidgetprojectselector');
		var options	= {
			parameters: {
				action:	'deleteGroup',
				group:	idPref
			},
			onComplete: this.onVirtualGroupDeleted.bind(this, idPref)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after "virtual" group (pref) has been deleted
	 *
	 * @method	onGroupDeleted
	 * @param	{Number}			idVirtualGroup
	 * @param	{Ajax.Response}		response
	 */
	onVirtualGroupDeleted: function(idVirtualGroup, response) {
		if( response.hasTodoyuError() ) {
			Todoyu.notifySuccess('[LLL:project.panelwidget-projectselector.deletegroup.error]');
		} else {
			Todoyu.notifySuccess('[LLL:project.panelwidget-projectselector.deletegroup.success]');

				// Remove item from widget items (results or selection), store updated selection and refresh widget
			var item	= $('panelwidget-projectselector-item-v' + idVirtualGroup);
			item.remove();
			this.addMessageIfSelectionEmpty();
			this.saveSelection();
		}
	},



	/**
	 * Handler when clicked on remove icon
	 *
	 * @method	onRemoveClick
	 * @param	{Event}		event
	 * @param	{Element}	removeIcon
	 */
	onRemoveClick: function(event, removeIcon) {
		event.stop();
		var item	= removeIcon.up('li');

		this.removeItemFromSelection(item);
	},



	/**
	 * Remove item from selection list
	 * Includes check+message for empty selection and storing of updated selection
	 *
	 * @method	removeItemFromSelection
	 * @param	{Element}	item
	 */
	removeItemFromSelection: function(item) {
		new Effect.SlideUp(item, {
			duration: 0.3,
			afterFinish: function() {
				if( item.parentNode ) {
					item.remove();
				}
				this.addMessageIfSelectionEmpty();
				this.saveSelection();
			}.bind(this)
		});
	},



	/**
	 * When selection contains no item, add place holder label
	 *
	 * @method	addMessageIfSelectionEmpty
	 */
	addMessageIfSelectionEmpty: function() {
		if( this.isSelectionEmpty() ) {
			this.selection.update('<p>[LLL:project.panelwidget-projectselector.selection.empty]</p>');
		}
	},



	/**
	 * Check whether selection contains any items
	 *
	 * @method	isSelectionEmpty
	 * @return	{Boolean}
	 */
	isSelectionEmpty: function() {
		return !this.selection.down('li');
	},



	/**
	 * Save preference of selected items
	 *
	 * @method	saveSelection
	 */
	saveSelection: function(noDelay) {
		clearTimeout(this.timeoutSave);
		if( noDelay !== true ) {
			this.timeoutSave = this.saveSelection.bind(this, true).delay(0.5);
			return ;
		}

		var items	= this.getSelectedItems();
		var url		= Todoyu.getUrl(this.config.ext, this.config.controller);
		var options	= {
			parameters: {
				action:		this.config.actionSelection,
				selection:	items.join(',')
			},
			onComplete: this.onSelectionSaved.bind(this, items)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler when selection preference was saved
	 * Fire change event to notify other about the change
	 *
	 * @method	onSelectionSaved
	 * @param	{Array}			items
	 * @param	{Ajax.Response}	response
	 */
	onSelectionSaved: function(items, response) {
			// Update list to possibly redisplay items in results that have been removed from selection
		if( this.getSearchText() !== '' ) {
			this.update();
		}

			// Fire registered callbacks
		Todoyu.PanelWidget.fire('projectselector', response.responseJSON);
	},



	/**
	 * Get item IDs from selection list
	 * Disabled items are prefixed with a minus
	 *
	 * @method	getSelectedItems
	 * @return	{String[]}				selected items' IDs
	 */
	getSelectedItems: function() {
		return this.selection.select('li').collect(function(item){
			var itemKey	= item.id.split('-').last();
			var prefix	= item.hasClassName('disabled') ? '-' : '';

			return prefix + itemKey;
		});
	},



	/**
	 * Handle list update
	 * Call parent and extra hook
	 *
	 * @method	onListUpdated
	 * @param	{Function}		$super
	 * @param	{Ajax.Response}	response
	 */
	onListUpdated: function($super, response) {
		$super(response);
		Todoyu.Hook.exec('project.projectselector.updated');
	},

	

	/**
	 * Handler when list was updated
	 *
	 * @method	onUpdated
	 * @param	{Ajax.Response}	response
	 */
	onUpdated: function(response) {
		this.addItemsIcons(true, true, false);
		this.markFirstAsHot();
	},



	/**
	 * Handler on an empty result
	 *
	 * @method	onEmptyResult
	 * @param	{Function}			$super		Todoyu.PanelWidgetSearchList.onEmptyResult
	 * @param	{Ajax.Response}		response
	 */
	onEmptyResult: function($super, response) {
		if( this.getSearchText() !== '' ) {
			this.highlightMatchingSelectedItems(this.getSearchText());
		}
	},



	/**
	 * Highlight all already selected items which match the current search word
	 *
	 * @method	highlightMatchingSelectedItems
	 * @param	{String}	search
	 */
	highlightMatchingSelectedItems: function(search) {
		this.getMatchingSelectionElements(search).each(function(li){
			li.highlight({
				duration:3.0
			});
		});
	},



	/**
	 * Get elements in selection which match the search word
	 *
	 * @method	getMatchingSelectionElements
	 * @param	{String}	search
	 * @return	{Element[]}
	 */
	getMatchingSelectionElements: function(search) {
		var pattern	= new RegExp(search, 'i');

		return this.selection.select('li').findAll(function(li){
			var name = li.down('a').innerHTML.stripTags().strip();
			return pattern.test(name);
		});
	},



	/**
	 * Add deletion icons to all virtual group items in the search results list
	 *
	 * @method	addDeleteIconsToList
	 * @param	{Element}	[items]
	 */
	addDeleteGroupIcons: function(items) {
		items	= items || this.list;

		items.each(function(item){
			if( !item.down('span.deletegroup') ) {
				item.insert(new Element('span', {
					'class':	'deletegroup',
					title:		'[LLL:project.panelwidget-projectselector.icon.deletegroup]'
				}));
			}
		});
	},



	/**
	 * Add icons for adding to active selection to all items in the search results list
	 *
	 * @method	addAddIconsToList
	 */
	addAddIconsToList: function() {
		this.list.select('li a').each(function(item){
			item.insert(new Element('span', {
				'class':	'add',
				title:		'[LLL:project.panelwidget-projectselector.icon.additem]'
			}));
		});
	},



	/**
	 * Add removal icons to items.
	 *
	 * @method	addRemoveIconsToList
	 * @param	{Element[]}		[items]
	 */
	addRemoveIconToItems: function(items) {
		items	= items || this.selection.select('li');

		items.each(function(item){
			var anchor = item.down('a');
			this.insertItemSpan(anchor, 'remove', '[LLL:project.panelwidget-projectselector.icon.removefromselection]');
		}, this);
	},



	/**
	 * Add "delete group" icons to items.
	 *
	 * @method	addDeleteGroupIconsToSelectionItems
	 * @param	{Element[]}								[items]
	 */
	addDeleteGroupIconToItems: function(items) {
		items	= items || this.selection.select('li');

		items.each(function(item){
			var anchor = item.down('a');
			this.insertItemSpan(anchor, 'deletegroup', '[LLL:project.panelwidget-projectselector.icon.deletegroup]');
		}, this);
	},



	/**
	 * Insert span with given CSS class and title into given item if not there yet
	 *
	 * @method	insertItemSpan
	 * @param	{Element}	item
	 * @param	{String}	className
	 * @param	{String}	title
	 */
	insertItemSpan: function(item, className, title) {
		if( ! item.down('span.' + className) ) {
			item.insert(new Element('span', {
				'class':	className,
				title:		title
			}));
		}
	},



	/**
	 * Check whether any item is selected or not
	 *
	 * @method	isAnyItemSelected
	 * @param	{Boolean}		[isActive]
	 * @return	{Boolean}
	 */
	isAnyItemSelected: function(isActive) {
		var items = this.getSelectedItems();

		if( isActive ) {
			return items.any(function(itemKey){
				return itemKey.substr(0, 1) !== '-';
			});
		} else {
			return items.size() > 0;
		}
	},



	/**
	 * Get all selected elements (projects). Gets also group and other types
	 *
	 * @method	getSelectedProjects
	 * @return	{Array}
	 */
	getSelectedProjects: function() {
		return this.getSelectedItems().findAll(function(item){
			return item.substr(0, 1) !== '-';
		});
	},



	/**
	 * Save projects and groups as "virtual" group preference
	 *
	 * @method	onSaveGroupButtonClick
	 */
	onSaveGroupButtonClick: function() {
			// No projects selected
		if( !this.isAnyItemSelected(true) ) {
			alert('LLL:project.panelwidget-projectselector.selection.empty');
			return;
		}

		var title 	= prompt('[LLL:project.panelwidget-projectselector.newGroupLabel.prompt]', '[LLL:project.panelwidget-projectselector.newGroupLabel.prompt.preset]');

			// Canceled saving
		if( title === null ) {
			return;
		}
			// No name entered
		if( title.strip() === '' ) {
			alert('[LLL:project.panelwidget-projectselector.newGroupLabel.error.saveEmptyName]');
			return;
		}

			// Save group items (projects and groups, as type-prefixed IDs e.g. g1 g2 g3 p1 p2 p3...)
		var url		= Todoyu.getUrl('project', 'panelwidgetprojectselector');
		var options	= {
			parameters: {
				action:	'saveGroup',
				title:	title,
				items:	Object.toJSON(this.getSelectedItems())
			},
			onComplete:	this.onSavedVirtualGroup.bind(this, title)
		};

		Todoyu.send(url, options);
	},



	/**
	 * Handler after "virtual" group has been saved: add the new group into active selection
	 *
	 * @method	onSavedGroup
	 * @param	{Ajax.Response}		response
	 */
	onSavedVirtualGroup: function(groupLabel, response) {
		var idPreference	= response.getTodoyuHeader('idPreference');

		if( idPreference != 0 ) {
			Todoyu.notifySuccess('[LLL:project.panelwidget-projectselector.newGroupLabel.saved.success]');

				// Render and insert selection item
			var item	= new Element('li', {
				'id':		'panelwidget-projectselector-item-v' + idPreference,
				'class':	'virtualgroup'
			}).insert({
				bottom: new Element('a', {
					'title':	groupLabel,
					'href':		'javascript:void(0)'
				}).update(groupLabel)
			});

			this.addItemToSelection(item);

			this.addDeleteGroupIconToItems([item]);
			this.addRemoveIconToItems([item]);

			this.sortNodes(this.selection.select('li'));
		}
	}

});