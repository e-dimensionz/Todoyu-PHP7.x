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
 * @package			Todoyu
 * @subpackage		Imap
 */
class TodoyuImapMessageActionController extends TodoyuActionController {

	/**
	 * Get message eml file for download
	 *
	 * @param	Array		$params
	 */
	public function emlAction(array $params = array()) {
		$idMessage	= intval($params['message']);
		$message	= TodoyuImapMessageManager::getMessage($idMessage);

		$message->sendAsEmlDownload();
	}



	/**
	 * Serve inline image
	 * echo file with matching headers to simulate file download
	 *
	 * @param	Array	$params
	 */
	public function inlineImageAction(array $params) {
		$imageKey	= trim($params['image']);

		TodoyuImapInlineImageManager::sendToBrowser($imageKey);
	}

}

?>