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
 * Comment project
 *
 * @package		Todoyu
 * @subpackage	comment
 */
class TodoyuCommentProject extends TodoyuProjectProject {

	/**
	 * Get comment fallback ID
	 *
	 * @return	Integer
	 */
	public function getCommentFallbackID() {
		return $this->getInt('ext_comment_fallback');
	}



	/**
	 * Get comment fallback
	 *
	 * @return	TodoyuCommentFallback
	 */
	public function getCommentFallback() {
		return TodoyuCommentFallbackManager::getFallback($this->getCommentFallbackID());
	}



	/**
	 * Check whether the project has a comment fallback
	 *
	 * @return	Boolean
	 */
	public function hasCommentFallback() {
		return $this->getCommentFallbackID() !== 0;
	}



	/**
	 * @param	Integer	$idTask
	 * @param	Array	$data
	 * @return	Array
	 */
	public function applyCommentFallback($idTask, array $data) {
		$fallback	= false;

		if( $this->hasCommentFallback() ) {
			$fallback = $this->getCommentFallback();
		} else {
			if( TodoyuCommentFallbackManager::hasGlobalFallback() ) {
				$fallback = TodoyuCommentFallbackManager::getGlobalFallback();
			}
		}

		if( $fallback ) {
			$data = $fallback->applyFallbackData($idTask, $data);
		}

		return $data;
	}

}

?>