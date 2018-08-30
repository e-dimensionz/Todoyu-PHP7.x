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
 * Comment search
 *
 * @package		Todoyu
 * @subpackage	Comment
 */
class TodoyuCommentSearch implements TodoyuSearchEngineIf {

	/**
	 * Search comment in full-text mode, search for comment numbers. Return IDs of matching comments.
	 *
	 * @param	Array		$find		Keywords which have to be in the comments
	 * @param	Array		$ignore		Keywords which must not be in the comment
	 * @param	Integer		$limit
	 * @return	Array		comment IDs
	 */
	public static function searchComments(array $find, array $ignore = array(), $limit = 100) {
			// Find comment IDs via full-text search
		$table	= 'ext_comment_comment';
		$fields	= array('comment');

		$commentIDs	= TodoyuSearch::searchTable($table, $fields, $find, $ignore, $limit);

			// Find more IDs via extraction of comment numbers contained in search words
		$commentIDs	= array_merge($commentIDs, self::getCommentIdNumsFromSearchWords($find));

		return array_unique($commentIDs);
	}



	/**
	 * Identify and extract (converted to numeric IDs) comment identification numbers from search words like 'K1', 'K2', etc.
	 *
	 * @param	Array	$find
	 * @return	Array
	 */
	private static function getCommentIdNumsFromSearchWords(array $find) {
		$ids	= array();

		foreach($find as $sword) {
			$sword	= str_replace('c', '', strtolower($sword));
			$id		= intval($sword);

			if( $id > 0 ) {
				$ids[]	= $id;
			}
		}

		return $ids;
	}



	/**
	 * Get suggestions of comments suiting to given search request
	 *
	 * @param	Array		$find
	 * @param	Array		$ignore
	 * @param	Integer		$limit
	 * @return	Array
	 */
	public static function getSuggestions(array $find, array $ignore = array(), $limit = 5) {
		$limit			= intval($limit);
		$suggestions	= array();

		$commentIDs		= self::searchComments($find, $ignore, $limit);

			// Get comment details
		if( sizeof($commentIDs) > 0 ) {
			$fields	= '	c.id,
						c.comment,
						c.date_create,
						t.id as taskid,
						t.tasknumber,
						t.id_project,
						t.title as tasktitle,
						u.lastname,
						u.firstname,
						p.title as projecttitle,
						comp.shortname as company';

			$table	= '	ext_comment_comment c,
						ext_project_task t,
						ext_project_project p,
						ext_contact_person u,
						ext_contact_company comp';

			$where	= '	c.id IN(' . implode(',', $commentIDs) . ')
						AND c.id_task			= t.id
						AND c.id_person_create	= u.id
						AND t.id_project		= p.id
						AND p.id_company		= comp.id';

			$order	= '	c.date_create DESC';

			$comments = Todoyu::db()->getArray($fields, $table, $where, '', $order);

			foreach($comments as $comment) {
				if( TodoyuCommentRights::isSeeAllowed($comment['id']) ) {
					$textShort	= TodoyuString::getSubstring(strip_tags($comment['comment']), $find[0], 20, 30);
					$textShort	= str_ireplace($find[0], '<strong>' . $find[0] . '</strong>', $textShort);

					$labelTitle = TodoyuString::wrap($comment['tasktitle'], '<span class="keyword">|</span>') . ' | ' . $comment['id_project'] . '.' . $comment['tasknumber'] . ' | ' . 'c' . $comment['id'];

					$suggestions[] = array(
						'labelTitle'=> $labelTitle,
						'labelInfo'	=> $textShort,
						'title'		=> strip_tags($labelTitle),
						'onclick'	=> 'location.href=\'index.php?ext=project&amp;project=' . $comment['id_project'] . '&amp;task=' . $comment['taskid'] . '&amp;tab=comment#task-comment-' . $comment['id'] . '\''
					);
				}
			}
		}

		return $suggestions;
	}

}

?>