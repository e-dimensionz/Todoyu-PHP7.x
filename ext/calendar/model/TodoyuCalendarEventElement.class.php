<?php
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
 * Event element to be rendered in a calendar view
 *
 * @package		Todoyu
 * @subpackage	Calendar
 */
abstract class TodoyuCalendarEventElement {

	/**
	 * Event element
	 *
	 * @var	TodoyuCalendarEvent
	 */
	protected $event;

	/**
	 * Parent view
	 *
	 * @var	TodoyuCalendarView
	 */
	protected $view;

	/**
	 * CSS class names for element
	 *
	 * @var	Array
	 */
	protected $classNames	= array();



	/**
	 * Initialize element. Inject event
	 *
	 * @param	TodoyuCalendarEvent		$event
	 * @param	TodoyuCalendarView		$view
	 */
	public function __construct(TodoyuCalendarEvent $event, TodoyuCalendarView $view) {
		$this->event	= $event;
		$this->view		= $view;
	}



	/**
	 * Get event element
	 *
	 * @return	TodoyuCalendarEvent
	 */
	public function getEvent() {
		return $this->event;
	}



	/**
	 * Get view
	 *
	 * @return	TodoyuCalendarView
	 */
	public function getView() {
		return $this->view;
	}



	/**
	 * Get name of the view
	 *
	 * @return	String
	 */
	public function getViewName() {
		return $this->getView()->getName();
	}



	/**
	 * Check whether element is overlapping with other element (based on event dates)
	 *
	 * @param	TodoyuCalendarEventElement		$eventElement
	 * @return	Boolean
	 */
	public function isOverlapping(TodoyuCalendarEventElement $eventElement) {
		return $this->getRange()->isOverlapping($eventElement->getRange());
	}



	/**
	 * Get element range (at least a half our, so it's visible)
	 *
	 * @return	TodoyuDateRange
	 */
	public function getRange() {
		$range	= $this->getEvent()->getRange();
		$range->setMinLength(30 * TodoyuTime::SECONDS_MIN);

		return $range;
	}



	/**
	 * Add an event element class
	 *
	 * @param	String		$className
	 */
	public function addClass($className) {
		$this->classNames[$className]	= $className;
	}



	/**
	 * Remove an event element class
	 *
	 * @param	String		$className
	 */
	public function removeClass($className) {
		unset($this->classNames[$className]);
	}



	/**
	 * Add multiple event element classes
	 *
	 * @param	String[]	$classNames
	 */
	public function addClasses(array $classNames) {
		$this->classNames = array_merge($this->classNames, $classNames);
	}



	/**
	 * Get class names
	 *
	 * @return	String[]
	 */
	public function getClasses() {
		return array_unique($this->classNames);
	}



	/**
	 * Get template data to render element
	 * Merge event and custom element data depending of current view
	 *
	 * @param	Integer
	 * @return	Array
	 */
	public function getTemplateData($date = 0) {
		$elementTemplateData= $this->getElementTemplateData($date);
		$eventTemplateData	= $this->getEvent()->getTemplateData(true);

		return array_merge($eventTemplateData, $elementTemplateData);
	}



	/**
	 * Get color config
	 *
	 * @return	Array
	 */
	protected function getColor() {
		$assignedPersons	= $this->getEvent()->getAssignedPersons();

		if( sizeof($assignedPersons) === 1 ) {
			$person		= array_pop($assignedPersons);
			$idPerson	= $person->getID();

			$colorData	= TodoyuContactPersonManager::getSelectedPersonColor(array($idPerson));

			$color		= $colorData[$idPerson];
		} else {
			$color	= array(
				'id' => 'multiOrNone'
			);
		}

		return $color;
	}



	/**
	 * Get element specific template data
	 *
	 * @param	Integer		$date
	 * @return	Array
	 */
	protected function getElementTemplateData($date = 0) {
		$elementData	= array();

			// Add base classes
		$this->addClass('event');
		$this->addClass('source' . ucfirst($this->getEvent()->getSource()));
		$this->addClass('type' . ucfirst($this->getEvent()->getType()));
		$this->addClass('quickInfoEvent');

			// Add access classes
		if( $this->getEvent()->hasAccess() ) {
			$this->addClass('hasAccess');
			$this->addClass('contextmenuevent');
		} else {
			$this->addClass('noAccess');
		}
		if( $this->getEvent()->canEdit() ) {
			$this->addClass('canEdit');
		}

			// Add event classes
		$this->addClasses($this->getEvent()->getClassNames());

			// Add color info
		$color	= $this->getColor();
		$this->addClass('enumColBGFade' . $color['id']);

		$elementData['classNames']	= implode(' ', $this->getClasses());
		$elementData['color']		= $color;
		$elementData['source']		= $this->getEvent()->getSource();
		$elementData['type']		= $this->getEvent()->getType();
		$elementData['view']		= $this->getViewName();

			// Override title if private and not assigned
		if( $this->getEvent()->isPrivate() && !$this->getEvent()->isPersonAssigned() ) {
			$elementData['title']	= '<' . Todoyu::Label('calendar.event.privateEvent.info') . '>';
		}

		return $elementData;
	}



	/**
	 * Render event for view
	 *
	 * @return	String
	 */
	public function render(TodoyuDayRange $range = null) {
		$tmpl	= $this->getTemplate();
		$data	= $this->getTemplateData();

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get path to template
	 *
	 * @return	String
	 */
	abstract protected function getTemplate();

}

?>