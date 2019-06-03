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
 * Company filter
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactCompanyFilter extends TodoyuSearchFilterBase implements TodoyuFilterInterface {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_company';



	/**
	 * Init filter object
	 *
	 * @param	Array		$activeFilters		Active filters
	 * @param	String		$conjunction
	 * @param	Array		$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('COMPANY', self::TABLE, $activeFilters, $conjunction, $sorting);
	}



	/**
	 * Get company IDs which match to all filters
	 *
	 * @param	String		$sortingFallback		Force sorting column
	 * @param	String		$limit			Limit result items
	 * @return	Array
	 */
	public function getCompanyIDs($sortingFallback = 'sorting', $limit = '') {
		return parent::getItemIDs($sortingFallback, $limit, false);
	}



	/**
	 * General access to the result items
	 *
	 * @param	String		$sortingFallback
	 * @param	String		$limit
	 * @return	Array
	 */
	public function getItemIDs($sortingFallback = 'sorting', $limit = '', $showDeleted = false) {
		return $this->getCompanyIDs($sortingFallback, $limit);
	}



	/**
	 * Fulltext search over company attributes
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_fulltext($value, $negate = false) {
		$value		= trim($value);
		$queryParts	= false;

		if( $value !== '' ) {
			$tables	= array(self::TABLE);

			$logic		= $negate ? ' NOT LIKE ':' LIKE ';
			$conjunction= $negate ? ' AND ':' OR ';

			$keyword= TodoyuSql::escape($value);
			$where	= ' ((	'
					.								self::TABLE . '.title		' . $logic . ' \'%' . $keyword . '%\''
					.		$conjunction . '	' . self::TABLE . '.shortname	' . $logic . ' \'%' . $keyword . '%\''
					. ' ))';

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Fulltext search over company name
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_name($value, $negate = false) {
		$value		= trim($value);
		$queryParts	= false;

		if( $value !== '' ) {
			$tables	= array(self::TABLE);

			$logic		= $negate ? ' NOT LIKE ':' LIKE ';
			$keyword	= TodoyuSql::escape($value);
			$where		= ' ( ' . self::TABLE . '.title		' . $logic . ' \'%' . $keyword . '%\'' . ' )';

			$queryParts	= array(
				'tables'=> $tables,
				'where'	=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Filter contact information fulltext
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_contactinformation($value, $negate = false) {
		$tables = array(
			self::TABLE,
			'ext_contact_contactinfo',
			'ext_contact_mm_company_contactinfo'
		);

		$where	= ' ext_contact_contactinfo.deleted							= 0 '
				. ' AND ' . TodoyuSql::buildLikeQueryPart(array($value), array('ext_contact_contactinfo.info'))
				. ' AND ext_contact_mm_company_contactinfo.id_contactinfo	= ext_contact_contactinfo.id'
				. ' AND ' . self::TABLE . '.id								= ext_contact_mm_company_contactinfo.id_company';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Filter for internal company
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array					Query parts
	 */
	public function Filter_isInternal($value, $negate = false) {
		$tables	= array(self::TABLE);

		$isInternal	= $negate ? 0 : 1;
		$where		= self::TABLE . '.is_internal = ' . $isInternal;

		$queryParts	= array(
			'tables'	=> $tables,
			'where'		=> $where
		);

		return $queryParts;
	}



	/**
	 * Filter condition: dateEnter
	 *
	 * @param	String		$date		Formatted (according to current locale) date string
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_dateEnter($date, $negate = false) {
		return $this->makeFilter_date('date_enter', $date, $negate);
	}



	/**
	 * Setup query parts for task date_... fields (create, update, start, end, deadline) filter
	 *
	 * @param	String			$field
	 * @param	Integer			$date		Formatted (according to current locale) date string
	 * @param	Boolean			$negate
	 * @return	Array|Boolean				Query parts array / false if no date timestamp given (or 1.1.1970 00:00)
	 */
	public static function makeFilter_date($field, $date, $negate = false) {
		return TodoyuSearchFilterHelper::makeFilter_date(self::TABLE, $field, $date, $negate);
	}



	/**
	 * Filter by country of address
	 *
	 * @param	Array		$idCountry
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_country($idCountry, $negate = false) {
		$idCountry	= (int) $idCountry;

		if( $idCountry === 0 ) {
			return false;
		}

		$tables = array(
			'ext_contact_address',
			'ext_contact_mm_company_address',
			self::TABLE
		);
		$compare= $negate ? ' != ' : ' = ';

		$where	= '			ext_contact_address.id_country '		. $compare . $idCountry
				. ' AND		ext_contact_mm_company_address.id_address	= ext_contact_address.id '
				. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_address.id_company ';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Filter by employed person
	 *
	 * @param	Array		$idPerson
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_person($idPerson, $negate = false) {
		$idPerson	= (int) $idPerson;

		if( $idPerson === 0 ) {
			return false;
		}

		$tables = array(
			'ext_contact_mm_company_person',
			self::TABLE
		);
		$compare= $negate ? ' != ' : ' = ';
		$where	= '			ext_contact_mm_company_person.id_person		' . $compare . $idPerson
				. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_person.id_company ';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Get filter for street of address
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_street($value, $negate = false) {
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( !empty($valueParts) ) {
			$fields	= array('ext_contact_address.street');
			$queryParts	= array(
					'tables'=> array(
						'ext_contact_address',
						'ext_contact_mm_company_address',
						self::TABLE
					),
					'where'	=> TodoyuSql::buildLikeQueryPart($valueParts, $fields)
							.  ' AND	ext_contact_mm_company_address.id_address	= ext_contact_address.id '
							. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_address.id_company '
			);


		}

		return $queryParts;
	}



	/**
	 * Get filter for zip of address
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_zip($value, $negate = false) {
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( !empty($valueParts) ) {
			$fields	= array('ext_contact_address.zip');
			$queryParts	= array(
					'tables'=> array(
						'ext_contact_address',
						'ext_contact_mm_company_address',
						self::TABLE
					),
					'where'	=> TodoyuSql::buildLikeQueryPart($valueParts, $fields)
							.  ' AND	ext_contact_mm_company_address.id_address	= ext_contact_address.id '
							. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_address.id_company '
			);


		}

		return $queryParts;
	}



	/**
	 * Filter by city of address
	 *
	 * @param	Array		$city
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_city($city, $negate = false) {
		$city	= trim($city);

		if( empty($city) ) {
			return false;
		}

		$tables = array(
			'ext_contact_address',
			'ext_contact_mm_company_address',
			self::TABLE
		);
		$compare= $negate ? ' != ' : ' = ';

		$where	= '			ext_contact_address.city '		. $compare . ' "' . $city . '" '
				. ' AND		ext_contact_mm_company_address.id_address	= ext_contact_address.id '
				. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_address.id_company ';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Filter for no more active companies
	 *
	 * @param	Integer		$value
	 * @param	Boolean		$negate
	 * @return	Array				Query parts
	 */
	public function Filter_isNotActive($value, $negate = false) {
		$tables	= array(self::TABLE);

		$isNotActive	= $negate ? 0 : 1;
		$where		= self::TABLE . '.is_notactive = ' . $isNotActive;

		$queryParts	= array(
			'tables'	=> $tables,
			'where'		=> $where
		);

		return $queryParts;
	}

}

?>