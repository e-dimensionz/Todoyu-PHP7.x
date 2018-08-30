<?php
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
 * Panel widget: status filter
 *
 * @package		Todoyu
 * @subpackage	Project
 * @abstract
 */
abstract class TodoyuProjectPanelWidgetStatusFilter extends TodoyuPanelWidget {

	/**
	 * Name of the preference
	 *
	 * @var	String
	 */
	protected $pref;

	/**
	 * Path to template
	 *
	 * @var	String
	 */
	protected $tmpl = 'ext/project/view/panelwidget/statusfilter.tmpl';


	/**
	 * Constructor of status filter base
	 * Pass all arguments to the widget constructor
	 *
	 * @param	String		$ext		Extension key where the widget is located
	 * @param	String		$id			Panel widget ID (class name without TodoyuPanelWidget)
	 * @param	String		$title		Title of the panel widget
	 * @param	Array		$config		Configuration array for the widget
	 * @param	Array		$params		Custom parameters for current page request
	 */
	public function __construct($ext, $id, $title, array $config, array $params = array()) {
		parent::__construct(
			$ext,	// ext key
			$id,	// panel widget ID
			$title,	// widget title text
			$config,// widget config array
			$params	// widget parameters
		);

		$this->addHasIconClass();
		$this->addClass('panelWidgetStatusFilter');
	}



	/**
	 * Get selected status IDs from preferences
	 *
	 * @return	Array
	 */
	public abstract function getSelectedStatuses();



	/**
	 * Render content of the panel widget (status list)
	 *
	 * @return	String
	 */
	public function renderContent() {
		$data	= array(
			'id'		=> $this->getID(),
			'statuses'	=> $this->getStatusesInfos(),
			'selected'	=> $this->getSelectedStatuses()
		);

		return Todoyu::render($this->tmpl, $data);
	}



	/**
	 * Get panelWidget statuses infos
	 *
	 * @return	Array
	 */
	abstract protected function getStatusesInfos();



	/**
	 * Store prefs of the status filter panel widget
	 *
	 * @param	Array	$statusIDs
	 */
	public function saveSelectedStatuses(array $statusIDs) {
		$statuses	= implode(',', $statusIDs);

		TodoyuProjectPreferences::savePref($this->pref, $statuses, 0, true, AREA);
	}

}

?>