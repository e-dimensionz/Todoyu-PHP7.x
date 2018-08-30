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
 * Extension config manager. Manages writing /config/extensions.php file with current config
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuSysmanagerExtConfManager {

	/**
	 * Get path to extension config form
	 *
	 * @param	String		$extKey
	 * @return	String
	 */
	public static function getXmlPath($extKey) {
		return 'ext/' . strtolower($extKey) . '/config/form/admin/extconf.xml';
	}



	/**
	 * Check whether extension config form exists
	 *
	 * @param	String		$extKey
	 * @return	Boolean
	 */
	public static function hasExtConf($extKey) {
		$xmlPath = self::getXmlPath($extKey);

		return TodoyuFileManager::isFile($xmlPath);
	}



	/**
	 * Get extConf form
	 *
	 * @param	String			$extKey
	 * @param	Boolean			$loadData
	 * @return	TodoyuForm
	 */
	public static function getForm($extKey, $loadData = true) {
		$xmlPath	= self::getXmlPath($extKey);

		$form	= TodoyuFormManager::getForm($xmlPath);

		if( $loadData ) {
			$data	= self::getExtConf($extKey);
			$data	= TodoyuFormHook::callLoadData($xmlPath, $data, 0);
			$form->setFormData($data);
		}

		$form->setUseRecordID(false);

			// Modify form fields
		$formAction	= TodoyuString::buildUrl(array(
			'ext'		=> 'sysmanager',
			'controller'=> 'extconf'
		));

		$form->setAttribute('onsubmit', 'return Todoyu.Ext.sysmanager.ExtConf.onSave(this)');
		$form->setAttribute('action', $formAction);
		$form->setAttribute('name', 'config');

		$form->addHiddenField('extension', $extKey, true);


			// Add save and cancel buttons
		$xmlPathSave= 'ext/sysmanager/config/form/extconf-save.xml';
		$saveForm	= TodoyuFormManager::getForm($xmlPathSave);
		$buttons	= $saveForm->getFieldset('save');

		$form->addFieldset('save', $buttons);

		return $form;
	}



	/**
	 * Save current configuration (installed extensions and their config)
	 */
	private static function writeExtConfFile() {
		$file	= PATH_LOCALCONF . '/extconf.php';
		$tmpl	= 'ext/sysmanager/asset/template/extconf.php.tmpl';
		$data	= array(
			'extConf'	=> array()
		);

		$extKeys	= TodoyuExtensions::getInstalledExtKeys();

		foreach($extKeys as $extKey) {
			$extConf	= self::getExtConf($extKey);

			$data['extConf'][$extKey] = addslashes(serialize($extConf));
		}

			// Save file
		TodoyuFileManager::saveTemplatedFile($file, $tmpl, $data);
	}



	/**
	 * Update an extension configuration
	 *
	 * @param	String		$extKey
	 * @param	Array		$data
	 */
	public static function saveExtConf($extKey, array $data) {
		self::setExtConf($extKey, $data);

		self::writeExtConfFile();
	}



	/**
	 * Set extension configuration array in
	 *
	 * @param	String		$extKey
	 * @param	Array		$data
	 */
	public static function setExtConf($extKey, array $data) {
		Todoyu::$CONFIG['EXT'][$extKey]['extConf'] = $data;
	}



	/**
	 * @param	String	$extKey
	 * @param	Array	$update
	 */
	public static function updateExtConf($extKey, array $update) {
		$extConf	= self::getExtConf($extKey);
		$extConf	= array_merge($extConf, $update);

		self::saveExtConf($extKey, $extConf);
	}



	/**
	 * Get config array for an extension
	 *
	 * @param	String		$extKey
	 * @return	Array
	 */
	public static function getExtConf($extKey) {
		return TodoyuArray::assure(Todoyu::$CONFIG['EXT'][$extKey]['extConf']);
	}



	/**
	 * Get parameter value from extension configuration
	 *
	 * @param	String		$extKey
	 * @param	Mixed		$parameter
	 * @return	Mixed
	 */
	public static function getExtConfValue($extKey, $parameter) {
		$extConf	= self::getExtConf($extKey);

		return $extConf[$parameter];
	}

}

?>