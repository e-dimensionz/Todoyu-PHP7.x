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
 * Bookmark manager
 *
 * @package		Todoyu
 * @subpackage	Bookmark
 */
class TodoyuBookmarkBookmarkManager {

	/**
	 * @var String		Default table for database requests
	 */
	const TABLE	= 'ext_bookmark_bookmark';



	/**
	 * Get bookmark
	 *
	 * @param	Integer				$idBookmark
	 * @return	TodoyuBookmarkBookmark
	 */
	public static function getBookmark($idBookmark) {
		$idBookmark	= intval($idBookmark);

		return TodoyuRecordManager::getRecord('TodoyuBookmarkBookmark', $idBookmark);
	}



	/**
	 * Get bookmark of given item ID, type, creator person
	 *
	 * @param	Integer				$idItem
	 * @param	String|Integer		$typeKey
	 * @param	Integer				$idPersonCreate
	 * @return	TodoyuBookmarkBookmark
	 */
	public static function getBookmarkByItemId($idItem, $typeKey, $idPersonCreate = 0) {
		$idBookmark	= self::getBookmarkIdByItem($idItem, $typeKey, $idPersonCreate);
		
		return self::getBookmark($idBookmark);
	}



	/**
	 * Get bookmark ID by type and item iD
	 *
	 * @param	Integer			$idItem
	 * @param	String|Integer	$type
	 * @param	Integer			$idPerson
	 * @return	Integer
	 */
	public static function getBookmarkIdByItem($idItem, $type, $idPerson = 0) {
		$idItem		= intval($idItem);
		$idPerson	= Todoyu::personid($idPerson);
		$idType		= is_numeric($type) ? intval($type) : self::getTypeIndex($type);

		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		id_item				= ' . $idItem
				. ' AND	type				= ' . $idType
				. ' AND	id_person_create	= ' . $idPerson
				. ' AND	deleted				= 0';

		$idBookmark	= Todoyu::db()->getFieldValue($field, $table, $where);

		return intval($idBookmark);
	}



	/**
	 * Get type index of a type string
	 *
	 * @param	String		$typeKey
	 * @return	Integer
	 */
	public static function getTypeIndex($typeKey) {
		$constant	= 'BOOKMARK_TYPE_' . strtoupper(trim($typeKey));

		if( defined($constant) ) {
			return constant($constant);
		} else {
			return 0;
		}
	}



	/**
	 * Get bookmark ID to given bookmark of given type and given person
	 *
	 * @param	Integer		$idBookmark
	 * @return	Integer
	 */
	public static function getItemID($idBookmark) {
		$idBookmark	= intval($idBookmark);
		$bookmark	= self::getBookmark($idBookmark);

		return $bookmark->getItemID();
	}



	/**
	 * Add an item to the bookmarks
	 *
	 * @param	Integer		$type
	 * @param	Integer		$idItem
	 * @return	Integer		Bookmark ID
	 */
	public static function addItemToBookmarks($type, $idItem) {
		$type	= intval($type);
		$idItem	= intval($idItem);

		$data	= array(
			'type'			=> $type,
			'deleted'		=> 0,
			'id_item'		=> $idItem
		);

		$idBookmark	= TodoyuRecordManager::addRecord(self::TABLE , $data);

		self::setBookmarkSorting($idBookmark, $type);

		TodoyuHookManager::callHook('bookmark', 'bookmark.add', array($idBookmark, $type, $idItem));

		return $idBookmark;
	}



	/**
	 * Set sorting index for new bookmark
	 *
	 * @param	Integer		$idBookmark
	 * @param	Integer		$type
	 */
	private static function setBookmarkSorting($idBookmark, $type) {
		$idBookmark	= intval($idBookmark);
		$type		= intval($type);

			// Get max sorting
		$fields	= 'MAX(`sorting`) as sorting';
		$where	= '		deleted	= 0'
				. ' AND `type`	= ' . $type;
		$group	= '`type`';
		$field	= 'sorting';

		$max	= Todoyu::db()->getFieldValue($fields, self::TABLE, $where, $group, '', '', $field);

			// Update
		$data	= array(
			'sorting'	=> intval($max) + 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idBookmark, $data);
	}



	/**
	 * Remove an item from the bookmarks
	 *
	 * @param	Integer		$type
	 * @param	Integer		$idItem
	 * @param	Integer		$idPerson
	 */
	public static function removeItemFromBookmarks($type, $idItem, $idPerson = 0) {
		$type		= intval($type);
		$idItem		= intval($idItem);
		$idPerson	= Todoyu::personid($idPerson);
		
		$idBookmark	= self::getBookmarkIdByItem($idItem, $type, $idPerson);

		self::removeBookmark($idBookmark);
	}



	/**
	 * Remove bookmarked item (of any type, by ID)
	 *
	 * @param	Integer		$idBookmark
	 */
	public static function removeBookmark($idBookmark) {
		$update	= array(
			'deleted'	=> 1
		);

		TodoyuRecordManager::updateRecord(self::TABLE, $idBookmark, $update);

		TodoyuHookManager::callHook('bookmark', 'bookmark.delete', array($idBookmark));
	}



	/**
	 * Add task to bookmarks
	 *
	 * @param	Integer		$idTask
	 * @return	Integer
	 */
	public static function addTaskToBookmarks($idTask) {
		$idTask	= intval($idTask);

		return self::addItemToBookmarks('task', $idTask);
	}



	/**
	 * Remove task from bookmarks
	 *
	 * @param	Integer		$idTask
	 */
	public static function removeTaskFromBookmarks($idTask) {
		$idTask	= intval($idTask);

		self::removeItemFromBookmarks(BOOKMARK_TYPE_TASK, $idTask);
	}



	/**
	 * Check whether an item of a type is bookmarked
	 *
	 * @param	String		$typeKey
	 * @param	Integer		$idItem
	 * @return	Boolean
	 */
	public static function isItemBookmarked($typeKey, $idItem) {
		$type		= self::getTypeIndex($typeKey);
		$idPerson	= TodoyuAuth::getPersonID();

		$field	= 'id';
		$table	= self::TABLE;
		$where	= '		`type`				= ' . $type .
				  ' AND	id_person_create	= ' . $idPerson .
				  ' AND	id_item				= ' . $idItem .
				  ' AND	deleted				= 0';

		return Todoyu::db()->hasResult($field, $table, $where);
	}



	/**
	 * Check whether task is bookmarked
	 *
	 * @param	Integer	$idTask
	 * @return	Boolean
	 */
	public static function isTaskBookmarked($idTask) {
		$idTask	= intval($idTask);

		return self::isItemBookmarked('task', $idTask);
	}



	/**
	 * Get the contextmenu part of the bookmarks, depending on the task already exists as a bookmark
	 *
	 * @param	Integer	$idTask
	 * @param	Array	$items
	 * @return	Array
	 */
	public static function getTaskContextMenuItems($idTask, array $items) {
		$idTask		= intval($idTask);

			// Ignore 0-task and deleted tasks
		if( $idTask === 0 || TodoyuProjectTaskManager::isDeleted($idTask) ) {
			return $items;
		}

		$ownItems	= Todoyu::$CONFIG['EXT']['bookmark']['ContextMenu']['Task'];
		$allowed	= array();

		if( self::isTaskBookmarked($idTask) ) {
			if( TodoyuBookmarkRights::isRemoveAllowed($idTask, BOOKMARK_TYPE_TASK) ) {
				$allowed['removebookmark']	= $ownItems['removebookmark'];
			}
		} else {
			if( TodoyuBookmarkRights::isAddAllowed($idTask, BOOKMARK_TYPE_TASK) ) {
				$allowed['addbookmark']	= $ownItems['addbookmark'];
			}
		}

		$items	= array_merge_recursive($items, $allowed);

		return $items;
	}



	/**
	 * Gets Bookmarks of current person
	 *
	 * @param	Integer		$type
	 * @return	Array
	 */
	public static function getPersonBookmarks($type) {
		$type		= intval($type);

		$fields		= self::TABLE.'.*';

		$tables	= self::TABLE;
		$where	= self::TABLE . '.deleted						= 0'
				. ' AND	' . self::TABLE .  '.id_person_create	= ' . Todoyu::personid()
				. ' AND	' . self::TABLE . '.type				= ' . $type;

			// Dont get bookmarks of deleted tasks
		if( $type	=== TASK_TYPE_TASK ) {
			$tables	.= ', ext_project_task';

			$where .= ' AND ext_project_task.id			= ' . self::TABLE . '.id_item '
					. ' AND ext_project_task.deleted	= 0';
		}

		$order	= self::TABLE . '.sorting';

		return Todoyu::db()->getArray($fields, $tables, $where, $order);
	}



	/**
	 * Get task bookmarks of current person
	 *
	 * @return	Array
	 */
	public static function getTaskBookmarks() {
		return self::getPersonBookmarks(BOOKMARK_TYPE_TASK);
	}



	/**
	 * Updates bookmarks order (in panelwidget) of current person in database
	 *
	 * @param	Array	$items
	 */
	public static function saveOrder(array $items) {
		foreach($items as $sorting => $idItem) {
			$where	= 'id_item	= ' . $idItem . ' AND id_person_create = ' . Todoyu::personid();
			$data	= array('sorting'	=> $sorting);

			Todoyu::db()->doUpdate(self::TABLE, $where, $data);
		}
	}



	/**
	 * Save bookmark data as record
	 *
	 * @param	Array		$data
	 * @return	Integer		Bookmark ID
	 */
	public static function saveBookmark(array $data) {
		$idBookmark	= intval($data['id']);

			// Update bookmark data
		self::updateBookmark($idBookmark, $data);

			// Remove bookmark record from cache
		self::removeFromCache($idBookmark);

		return $idBookmark;
	}



	/**
	 * Update a bookmark record
	 *
	 * @param	Integer		$idBookmark
	 * @param	Array		$data
	 */
	public static function updateBookmark($idBookmark, array $data) {
		$idBookmark	= intval($idBookmark);

		TodoyuRecordManager::updateRecord(self::TABLE, $idBookmark, $data);

		TodoyuHookManager::callHook('bookmark', 'bookmark.update', array($idBookmark, $data));
	}



	/**
	 * Update bookmark label
	 *
	 * @param	Integer		$idBookmark
	 * @param	String		$label
	 */
	public static function updateBookmarkTitle($idBookmark, $label) {
		self::updateBookmark($idBookmark, array(
			'title'	=> $label
		));

		TodoyuHookManager::callHook('bookmark', 'bookmark.rename', array($idBookmark, $label));
	}



	/**
	 * Update task bookmark label
	 *
	 * @param	Integer		$idTask
	 * @param	String		$label
	 */
	public static function updateTaskBookmarkTitle($idTask, $label) {
		self::updateItemBookmarkTitle('task', $idTask, $label);
	}



	/**
	 * Update title of bookmark item
	 *
	 * @param	String		$type
	 * @param	Integer		$idItem
	 * @param	String		$label
	 */
	public static function updateItemBookmarkTitle($type, $idItem, $label) {
		$typeIndex	= self::getTypeIndex($type);
		$idBookmark = self::getBookmarkIdByItem($idItem, $typeIndex);

		self::updateBookmarkTitle($idBookmark, $label);
	}



	/**
	 * Remove bookmark object from cache
	 *
	 * @param	Integer		$idBookmark
	 */
	public static function removeFromCache($idBookmark) {
		$idBookmark	= intval($idBookmark);

		TodoyuRecordManager::removeRecordCache('TodoyuBookmarkBookmark', $idBookmark);
		TodoyuRecordManager::removeRecordQueryCache(self::TABLE, $idBookmark);
	}



	/**
	 * Callback to render the content for the bookmark panelwidget
	 *
	 * @param	Integer		$idTask
	 * @param	Boolean		$info		Don't care
	 * @return	String		Content of the panelwidget
	 */
	public static function callbackTrackingToggle($idTask, $info) {
		/** @var	TodoyuBookmarkPanelWidgetTaskBookmarks $panelWidget */
		$panelWidget	= TodoyuPanelWidgetManager::getPanelWidget('bookmark', 'TaskBookmarks');

		return $panelWidget->renderContent();
	}

}

?>