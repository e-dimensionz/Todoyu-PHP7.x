/**
 * Javascript objects are all children or sub elements of the global Todoyu object.
 * All extensions are located in Todoyu.Ext.* in their own namespace.
 * 
 * You should check all your scripts with http://www.jslint.com/
 * One bad script can break the whole JS execution on the page, so be careful
 */

Todoyu.Ext.project.Task = {

	/**
	 * To initialize stuff when document is loaded, use the init() function
	 * JS functions registered in PHP with Page::addJsOnloadedFunction() are called when DOM is loaded
	 */
	init: function() {
		this.installObservers();
	},



	/**
	 * One function for one job
	 * So put all observer installations in a special function, so everyone can find it
	 */
	installObservers: function() {
			// Don't forget to bind the handler to the current object
		$('an-element-id').observe('change', this.onSomeElementChange.bindAsEventListener(this));
	},



	/**
	 * Prefix event handlers with "on", so we know it is one
	 * @param	{Event}		event
	 */
	onSomeElementChange: function(event) {

	}

}; // Don't forget the closing semicolon. IE hates that!