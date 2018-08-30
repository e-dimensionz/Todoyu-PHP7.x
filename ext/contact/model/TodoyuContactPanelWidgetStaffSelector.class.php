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
 * Staff selector panel widget
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPanelWidgetStaffSelector extends TodoyuPanelWidgetSearchList {

	/**
	 * Preference name for selected items
	 *
	 * @var	String
	 */
	protected $selectionPref	= 'staffselector';

	/**
	 * Cached selection
	 *
	 * @var	Array
	 */
	protected $selection;



	/**
	 * Constructor (init widget)
	 *
	 * @param	Array		$config
	 * @param	Array		$params
	 * @param	Integer		$idArea
	 */
	public function __construct(array $config, array $params = array(), $idArea = 0) {
		parent::__construct(
			'contact',										// ext key
			'staffselector',								// panel widget ID
			'contact.panelwidget-staffselector.title',	// widget title text
			$config,										// widget config array
			$params,										// widget parameters
			$idArea											// area ID
		);

			// Add classes
		$this->addHasIconClass();

		$this->setJsObject('Todoyu.Ext.contact.PanelWidget.StaffSelector');

			// Generate person color CSS and image
		TodoyuColors::generate();
	}



	/**
	 * Render content
	 *
	 * @param	Boolean		$listOnly		Render list items only
	 * @return	String
	 */
	public function renderContent($listOnly = false) {
		$searchList	= parent::renderContent($listOnly);
		$selection	= ($listOnly ? '' : $this->renderSelection());

		return $searchList . $selection;
	}



	/**
	 * Render selection box
	 * Selected persons and groups
	 *
	 * @return	String
	 */
	protected function renderSelection() {
		$tmpl	= 'ext/contact/view/panelwidget/staffselector.tmpl';

		$data	= array(
			'items'	=> $this->getSelectedItems(),
			'id'	=> $this->getID()
		);

		return Todoyu::render($tmpl, $data);
	}



	/**
	 * Get items for search result list
	 *
	 * @return	Array
	 */
	protected function getItems() {
		$items		= array();
		$searchWords= $this->getSearchWords();

		if( sizeof($searchWords) === 0 ) {
			return $items;
		}
		
			// Get matching "virtual" group items (preference)
		$virtualGroups	= $this->searchVirtualGroups($searchWords, 0, true);
		foreach($virtualGroups as $idPref => $virtualGroup) {
			
			$virtualGroup	= json_decode($virtualGroup['value']);
			$items[] = array(
				'id'	=> 'v' . $idPref,
				'label'	=> $virtualGroup->title,
				'title'	=> $virtualGroup->title,
				'class'	=> 'virtualgroup'
			);
		}

			// Get matching grouped (by jobtype) items
		if( $this->isGroupSearchActive() ) {
			$groups	= $this->searchGroups($searchWords);
			foreach($groups as $group) {
				$jobTypePersons	= $this->getJobtypePersons($group['id']);
				$items[] = array(
					'id'	=> 'g' . $group['id'],
					'label'	=> $group['label'] . ' (' . sizeof($jobTypePersons) . ')',
					'title'	=> $group['label'] . ' (' . sizeof($jobTypePersons) . ')',
					'class'	=> 'group'
				);
			}
		}

			// Get matching person items
		$persons= $this->searchPersons($searchWords);
		foreach($persons as $person) {
			$colorIndex	= TodoyuColors::getColorIndex($person['id']);
			$items[] = array(
				'id'	=> 'p' . $person['id'],
				'label'	=> $person['label'],
				'title'	=> $person['label'],
				'class'	=> 'person enumColBorLef' . $colorIndex
			);
		}

		return $items;
	}



	/**
	 * Get search words
	 *
	 * @return	Array
	 */
	protected function getSearchWords() {
		return TodoyuString::trimExplode(' ', $this->getSearchText(), true);
	}



	/**
	 * Search groups which match to search words
	 *
	 * @param	Array		$searchWords
	 * @return	Array
	 */
	protected function searchGroups(array $searchWords) {
		$searchFields	= array(
			'jt.title'
		);
		$like	= TodoyuSql::buildLikeQueryPart($searchWords, $searchFields);

		$fields	= '	jt.id,
					jt.title as label';
		$table	= '	ext_contact_jobtype jt,
					ext_contact_mm_company_person mmcp,
					ext_contact_person p,
					ext_contact_company c';
		$where	= '		mmcp.id_jobtype	= jt.id'
				. ' AND mmcp.id_person	= p.id'
				. ' AND mmcp.id_company	= c.id'
				. ' AND	jt.deleted		= 0'
				. ' AND p.deleted		= 0'
				. ' AND c.deleted		= 0'
				. ' AND c.is_internal	= 1'
				. '	AND	' . $like;
		$order	= 'jt.title';
		$group	= 'jt.id';
		$limit	= 10;

		$selectedJobtypeIDs	= $this->getSelectedGroupIDs();

		if( sizeof($selectedJobtypeIDs) > 0 ) {
			$where .= ' AND jt.id NOT IN(' . implode(',', $selectedJobtypeIDs) . ')';
		}

		return Todoyu::db()->getArray($fields, $table, $where, $group, $order, $limit);
	}



	/**
	 * Search persons which match the search words
	 *
	 * @param	Array	$searchWords
	 * @return	Array
	 */
	protected function searchPersons(array $searchWords) {
		$selectedPersons= $this->getSelectedPersonIDs();

		return TodoyuContactPersonManager::searchStaff($searchWords, $selectedPersons, 10);
	}



	/**
	 * Get person IDs of given jobtype
	 *
	 * @param	Integer		$idJobtype
	 * @return	Integer[]
	 */
	protected function getJobtypePersons($idJobtype) {
		return TodoyuContactJobTypeManager::getPersonIDsWithJobtype($idJobtype);
	}



	/**
	 * Get person IDs of given "virtual" group (pref)
	 *
	 * @param	Integer	$idItem
	 * @return  Integer[]
	 */
	protected function getVirtualGroupPersons($idItem) {
		$virtualGroup	= self::getVirtualGroup($idItem);

		if( ! $virtualGroup ) {
			return array();
		}

			// Get person IDs of selected groups and persons of virt. group
		$selectionItems	= json_decode($virtualGroup->items);

		return self::getPersonIDsOfSelection($selectionItems);
	}



	/**
	 * Get selected persons.
	 * -persons with selected virtual groups (pref)
	 * -persons with selected jobtypes
	 * -selected persons
	 *
	 * @param	Array		$selection
	 * @return	Integer[]
	 */
	public function getPersonIDsOfSelection($selection = array()) {
		if( empty($selection) ) {
			$selection	= $this->getSelection();
		}

		$persons	= array();

		foreach($selection as $item) {
				// Ignore item with dash (they are disabled)
			if( substr($item, 0, 1) === '-' ) {
				continue;
			}

			$typePrefix	= substr($item, 0, 1);
			$idItem		= substr($item, 1);

				// Add persons
			switch($typePrefix) {
					// Add person IDs from given person IDs, e.g "p1"
				case 'p':
					$persons[] = intval(substr($item, 1));
					break;

					// Add person IDs from given (jobtype) group, e.g. "g1"
				case 'g':
					$persons = array_merge($persons, $this->getJobtypePersons($idItem));
					break;

					// Add person IDs from given virtual group (pref), e.g. "v1"
				case 'v':
					$persons = array_merge($persons, $this->getVirtualGroupPersons($idItem));
					break;
			}
		}

		$persons	= array_unique($persons);

		return TodoyuArray::intval($persons);
	}



	/**
	 * Get items for selection list
	 *
	 * @return	Array
	 */
	public function getSelectedItems() {
		$selection	= $this->getSelection();
		$items		= array();

		foreach($selection as $item) {
				// Handle disabled items
			$disabled	= false;
			if( substr($item, 0, 1) === '-' ) {
				$disabled	= true;
				$item		= substr($item, 1);
			}

			$disabledClass	= $disabled ? ' disabled' : '';

				// Add item per type (p: person / g: group / v: virtualgroup)
			$prefix		= substr($item, 0, 1);
			$idRecord	= intval(substr($item, 1));

			switch($prefix) {
				case 'p':
					$person		= TodoyuContactPersonManager::getPerson($idRecord);
					$itemLabel	= $person->getFullName(true);
					$colorIndex	= TodoyuColors::getColorIndex($person['id']);
					$itemClass	= 'person enumColBorLef' . $colorIndex;
					break;

				case 'g':
					$itemLabel	= TodoyuContactJobTypeManager::getJobType($idRecord)->getTitle();
					$itemClass	= 'group';
					break;

				case 'v':
					$itemLabel	= self::getVirtualGroup($idRecord)->title;
					$itemClass	= 'virtualgroup';
					break;
			}

			$items[]	= array(
				'id'	=> $prefix . $idRecord,
				'label'	=> $itemLabel,
				'title'	=> $itemLabel,
				'class'	=> $itemClass . $disabledClass
			);
		}

		return $items;
	}



	/**
	 * Get active selection from preference
	 *
	 * @return	Array
	 */
	public function getSelection() {
		if( is_null($this->selection) ) {
			$pref			= TodoyuContactPreferences::getPref($this->selectionPref, 0, AREA);
			$this->selection= TodoyuArray::trimExplode(',', $pref);
		}

		return $this->selection;
	}



	/**
	 * Get IDs of selected groups (jobtypes)
	 *
	 * @return	Integer[]
	 */
	protected function getSelectedGroupIDs() {
		return $this->getSelectedTypeIDs('g');
	}



	/**
	 * Get IDs of selected persons
	 *
	 * @return	Integer[]
	 */
	protected function getSelectedPersonIDs() {
		return $this->getSelectedTypeIDs('p');
	}



	/**
	 * Get IDs of selected items of a specific type
	 * Type is marked with the first letter in the key
	 *
	 * @param	String		$type
	 * @return	Integer[]
	 */
	protected function getSelectedTypeIDs($type) {
		$items		= $this->getSelection();
		$typeItems	= array();

		foreach($items as $item) {
			$item = ltrim($item, '-');
			if( substr($item, 0, 1) === $type ) {
				$typeItems[] = intval(substr($item, 1));
			}
		}

		return $typeItems;
	}



	/**
	 * Save selected items in preference
	 *
	 * @param	Array	$selection
	 */
	public function saveSelection(array $selection) {
		$selection	= TodoyuArray::trim($selection, true);
		$value		= implode(',', $selection);

		TodoyuContactPreferences::savePref($this->selectionPref, $value, 0, true, AREA);
	}



	/**
	 * Check whether group search is active in config
	 *
	 * @return	Boolean
	 */
	protected function isGroupSearchActive() {
		return $this->config['group'] === true;
	}



	/**
	 * Validate group title (ensure uniqueness)
	 *
	 * @param	String		$title
	 * @return	String
	 */
	public static function validateGroupTitle($title) {
		$groupTitles	= self::getVirtualGroupTitles();

		if( in_array($title, $groupTitles) ) {
			$title = self::validateGroupTitle($title . '-2');
		}

		return $title;
	}



	/**
	 * Save staff selector preferences
	 *
	 * @param	Array	$prefs
	 */
	public static function savePrefs(array $prefs) {
		$prefs	= json_encode($prefs);

		TodoyuContactPreferences::savePref('panelwidget-staffselector', $prefs, 0, true, AREA);
	}



	/**
	 * Save staff selector selection as "virtual" group (preference for current person)
	 *
	 * @param	String	$title
	 * @param	String	$groupItems		JSON encoded array of type-prefixed IDs of persons and groups
	 * @return	Integer					Auto generated ID
	 */
	public function saveVirtualGroup($title, $groupItems) {
		$pref	= json_encode(array(
			'title' => $title,
			'items' => $groupItems
		));

		$idPref = TodoyuContactPreferences::savePref('panelwidget-staffselector-group', $pref, 0, false);
		return $idPref;
	}



	/**
	 * Get virtual group preference of given ID of given/current person
	 *
	 * @param	Integer		$idPref
	 * @param	Integer		$idPerson
	 * @return  stdClass|Boolean
	 */
	public static function getVirtualGroup($idPref, $idPerson = 0) {
		$idPref		= (int) $idPref;
		$idPerson	= Todoyu::personid($idPerson);

		$record = Todoyu::db()->getRecord(TodoyuPreferenceManager::TABLE, $idPref);

		if( $record['preference'] !== 'panelwidget-staffselector-group' &&
			(!TodoyuAuth::isAdmin() || intval($record['id_person']) !== $idPerson)
		) {
				// Record is different type of pref. or not belongs to another person
			return false;
		}

		return json_decode($record['value']);
	}



	/**
	 * Get all virtual group prefs of given person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getVirtualGroups($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		return TodoyuContactPreferences::getPrefs('panelwidget-staffselector-group', 0, 0, $idPerson);
	}



	/**
	 * Get titles of all virtual groups of given/current person
	 *
	 * @param	Integer		$idPerson
	 * @return	String[]
	 */
	public static function getVirtualGroupTitles($idPerson = 0) {
		$titles	= array();

		$virtualGroups	= self::getVirtualGroupsIndexed($idPerson);
		foreach($virtualGroups as $virtualGroup) {
			$virtualGroup	= json_decode($virtualGroup['value']);

			$titles[]	=strtolower($virtualGroup->title);
		}

	 	return $titles;
	}



	/**
	 * Get all virtual group prefs of given person
	 *
	 * @param	Integer		$idPerson
	 * @return	Array
	 */
	public static function getVirtualGroupsIndexed($idPerson = 0) {
		$idPerson	= Todoyu::personid($idPerson);

		$indexField = 'id';
		$fields		= 'id,value';
		$table		= TodoyuPreferenceManager::TABLE;
		$where	= '		id_person	= ' . $idPerson
				. ' AND	ext			= ' . EXTID_CONTACT
				. ' AND	area		= 0'
				. ' AND	preference	= \'panelwidget-staffselector-group\' ';

		return Todoyu::db()->getIndexedArray($indexField, $fields, $table, $where);
	}



	/**
	 * Filter virtual groups preferences of given person by matching titles
	 *
	 * @param	Array		$searchWords
	 * @param	Integer		$idPerson
	 * @param	Boolean		$excludeSelection
	 * @return  Array
	 */
	protected function searchVirtualGroups($searchWords, $idPerson = 0, $excludeSelection = false) {
		$searchWords	= TodoyuArray::strtolower($searchWords);
		$virtualGroups	= self::getVirtualGroupsIndexed($idPerson);

		if( $excludeSelection ) {
			$selection	= $this->getSelectedTypeIDs('v');
		}
		
		foreach($virtualGroups as $idPref => $virtualGroup) {
			$virtualGroup	= json_decode($virtualGroup['value']);
			$groupTitle		=  strtolower($virtualGroup->title);

			$matchFound	= false;
			foreach($searchWords as $sword) {
				if( strpos($groupTitle, $sword) !== false ) {
					$matchFound = true;
					break;
				}
			}

			if( !$matchFound || ($excludeSelection && in_array($idPref, $selection)) ) {
				unset($virtualGroups[$idPref]);
			}
		}

		return $virtualGroups;
	}

}

?>