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

/**
 * Filter sorting
 *
 * @class		Sorting
 * @namespace	Todoyu.Ext.search.Filter
 */
Todoyu.Ext.search.Filter.Sorting = {

	/**
	 * Ext back ref
	 *
	 * @var	{Object}
	 */
	ext: Todoyu.Ext.search,

	/**
	 * Counter to avoid conflicts when adding the same filter twice
	 *
	 * @var	{Number}
	 */
	counter: 0,



	/**
	 * Get sorting container element
	 *
	 * @return	{Element}
	 */
	getContainer: function() {
		return $('sorting-flags');
	},



	/**
	 * Build a sort DOM element
	 *
	 * @method	buildElement
	 * @param	{String}	name
	 * @param	{String}	label
	 * @param	{Boolean}	desc
	 */
	buildElement: function(name, label, desc) {
		var sortEl	= new Element('div', {
			'class': 'sort',
			id: 'sort-flag-' + name + '-' + this.counter++
		});
		var labelEl	= new Element('span', {
			'class': 'label'
		}).update(label);
		var dirEl	= new Element('span', {
			'class': 'action dir',
			title:	'[LLL:search.ext.sorting.direction.asc]'
		});
		var removeEl= new Element('span', {
			'class': 'action remove',
			title: '[LLL:search.ext.sorting.remove]'
		});

		if( desc ) {
			dirEl.addClassName('desc');
			dirEl.title = '[LLL:search.ext.sorting.direction.desc]';
		}

			// Nest the elements
		sortEl.insert(labelEl);
		sortEl.insert(dirEl);
		sortEl.insert(removeEl);

			// Add observers
		labelEl.on('click', '.sort', this.onDirClick.bind(this));
		dirEl.on('click', '.sort', this.onDirClick.bind(this));
		removeEl.on('click', '.sort', this.onRemoveClick.bind(this));

		return sortEl;
	},



	/**
	 * Add a new element
	 *
	 * @method	add
	 * @param	{String}	name			Name of the sorting
	 * @param	{String}	label			Label for the button
	 * @param	{Boolean}	desc			Sort descending
	 * @param	{Boolean}	noUpdate		Do not call update and toggle after adding
	 */
	add: function(name, label, desc, noUpdate) {
		this.getContainer().insert(this.buildElement(name, label, desc));

		if( !noUpdate ) {
			this.update();
			this.toggleContainer();
		}
	},



	/**
	 * All a list of sortings
	 *
	 * @method	addAll
	 * @param	{Object}	sortings
	 */
	addAll: function(sortings) {
		this.removeAll(true);

		sortings.each(function(sorting){
			this.add(sorting.name, sorting.label, sorting.dir==='desc', true);
		}, this);

		this.toggleContainer();
	},



	/**
	 * Remove all sortings
	 * No animation, just remove them all
	 *
	 * @method	removeAll
	 * @param	{Boolean}	noUpdate		No no update the results
	 */
	removeAll: function(noUpdate) {
		this.getSortings().invoke('remove');

		this.toggleContainer();

		if( !noUpdate ) {
			this.update();
		}
	},



	/**
	 * Remove a sorting (with animation) {
	 *
	 * @method	remove
	 * @param	{Element}	sort		Sorting DOM element
	 * @param	{Boolean}	noUpdate
	 */
	remove: function(sort, noUpdate) {
		this.animateRemove(sort, this.onRemoved.bind(this, noUpdate));
	},



	/**
	 * Callback when sorting was removed
	 *
	 * @method	onRemoved
	 * @param	{Boolean}	noUpdate
	 */
	onRemoved: function(noUpdate) {
		if( !noUpdate ) {
			this.toggleContainer();
			this.update();
		}
	},



	/**
	 * Toggle the container depending on if its empty
	 */
	toggleContainer: function() {
		if( this.getSortings().size() === 0 ) {
			this.getContainer().hide();
		} else {
			this.getContainer().show();
		}
	},



	/**
	 * Handler when clicked on direction icon
	 *
	 * @method	onDirClick
	 * @param	{Event}		event
	 * @param	{Element}	sort
	 */
	onDirClick: function(event, sort) {
		this.toggleDirIcon(sort);

		this.update();
	},



	/**
	 * Toggle direction icon
	 *
	 * @method	toggleDirIcon
	 * @param	{Element}	sort
	 */
	toggleDirIcon: function(sort) {
		var dir = sort.down('.dir');

		if( dir.hasClassName('desc') ) {
			dir.removeClassName('desc');
			dir.title = '[LLL:search.ext.sorting.direction.asc]';
		} else {
			dir.addClassName('desc');
			dir.title = '[LLL:search.ext.sorting.direction.desc]';
		}
	},



	/**
	 * Handler when clicked on remove
	 *
	 * @method	onRemoveClick
	 * @param	{Event}		event
	 * @param	{Element}	sort
	 */
	onRemoveClick: function(event, sort) {
		this.remove(sort, false);
	},



	/**
	 * Animate remove of sorting and call the callback when finished
	 *
	 * @method	animateRemove
	 * @param	{Element}	sort
	 * @param	{Function}	callback
	 */
	animateRemove: function(sort, callback) {
		Effect.DropOut(sort, {
			duration: 0.5,
			afterFinish: function() {
				sort.remove();

				if( callback ) {
					callback();
				}
			}
		});
	},



	/**
	 * Update the search results
	 *
	 * @method	update
	 */
	update: function() {
		if( this.ext.Filter.Conditions.hasConditions() ) {
			this.ext.Filter.updateResults();
		}
	},



	/**
	 * Get direction of sorting
	 *
	 * @method	getDirection
	 * @param	{Element}	sort
	 * @return	{String}
	 */
	getDirection: function(sort) {
		return $(sort).down('.dir').hasClassName('desc') ? 'desc' : 'asc';
	},



	/**
	 * Get name of a sorting element
	 *
	 * @method	getName
	 * @param	{Element}	sort
	 * @return	{String}
	 */
	getName: function(sort) {
		return sort.id.split('-')[2];
	},



	/**
	 * Set direction of element
	 *
	 * @method	setDirection
	 * @param	{Element}	sort
	 * @param	{String}	dir		asc or desc
	 */
	setDirection: function(sort, dir) {
		$(sort).down('.dir').removeClassName('desc').removeClassName('asc').addClassName(dir);
	},



	/**
	 * Get all sorting elements
	 *
	 * @method	getSortings
	 * @return	{Element[]}
	 */
	getSortings: function() {
		return this.getContainer().select('.sort');
	},



	/**
	 * Get all sortings as config object
	 *
	 * @method	getAll
	 * @param	{Boolean}	asJson
	 * @return	{Object[]|String}
	 */
	getAll: function(asJson) {
		var sortings = this.getSortings().collect(function(sort){
			return {
				name:	this.getName(sort),
				dir:	this.getDirection(sort)
			};
		}, this);

		if( asJson ) {
			sortings = Object.toJSON(sortings);
		}

		return sortings;
	}

};