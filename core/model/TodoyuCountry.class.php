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
 * Country object
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCountry extends TodoyuBaseObject {

	/**
	 * Constructor
	 *
	 * @param	Integer		$idCountry
	 */
	public function __construct($idCountry) {
		parent::__construct($idCountry, 'static_country');
	}



	/**
	 * Get phone code of country
	 *
	 * @return	String
	 */
	public function getPhoneCode() {
		return $this->get('phone');
	}



	/**
	 * Get ISO alpha2 code of country
	 *
	 * @return	String
	 */
	public function getCode2() {
		return $this->get('iso_alpha2');
	}



	/**
	 * Get ISO alpha2 code of country
	 *
	 * @return	String
	 */
	public function getCode3() {
		return $this->get('iso_alpha3');
	}



	/**
	 * Get ISO number of country
	 *
	 * @return	Integer
	 */
	public function getIsoNum() {
		return (int) $this->get('iso_num');
	}



	/**
	 * Get currency iso number
	 *
	 * @return	Integer
	 */
	public function getIsoNumCurrency() {
		return (int) $this->get('iso_num_currency');
	}



	/**
	 * Get country label
	 *
	 * @return	String
	 */
	public function getLabel() {
		$label = '';

		if( $this->getCode3() ) {
			$tempLabel	= Todoyu::Label('core.static_country.' . $this->getCode3());

			if( strpos($tempLabel, 'static_country') === false ) {
				$label = $tempLabel;
			}
		}

		return $label;
	}



	/**
	 * Load foreign data
	 *
	 */
	protected function loadForeignData() {

	}



	/**
	 * Get country template data
	 * Foreign data: currency
	 *
	 * @param	Boolean		$loadForeignData
	 * @return	Array
	 */
	public function getTemplateData($loadForeignData = false) {
		if( $loadForeignData ) {
			$this->loadForeignData();
		}

		$this->data['label'] = $this->getLabel();

		return parent::getTemplateData();
	}

}

?>