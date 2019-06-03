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
 * Person
 *
 * @package		Todoyu
 * @subpackage	Contact
 */
class TodoyuContactPersonFilter extends TodoyuSearchFilterBase {

	/**
	 * Default table for database requests
	 *
	 * @var	String
	 */
	const TABLE = 'ext_contact_person';



	/**
	 * Init filter object
	 *
	 * @param	Array		$activeFilters
	 * @param	String		$conjunction
	 * @param	Array		$sorting
	 */
	public function __construct(array $activeFilters = array(), $conjunction = 'AND', array $sorting = array()) {
		parent::__construct('PERSON', self::TABLE, $activeFilters, $conjunction, $sorting);
	}



	/**
	 * Get person IDs which match to the given filters
	 *
	 * @param	Integer		$limit		Limit of results
	 * @return	Integer[]
	 */
	public function getPersonIDs($limit = 100) {
		$sortingFallback	= self::TABLE . '.lastname, ' . self::TABLE . '.firstname';
		$limit				= intval($limit);

		return parent::getItemIDs($sortingFallback, $limit);
	}



	/**
	 * Full-text filter for all textual person data
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Fulltext($value, $negate = false) {
		$value		= trim($value);
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( !empty($valueParts)  ) {
			$fields		= array(
				self::TABLE . '.username',
				self::TABLE . '.lastname',
				self::TABLE . '.firstname',
				self::TABLE . '.shortname',
				self::TABLE . '.email'
			);
			$queryParts	= array(
				'where'		=> TodoyuSql::buildLikeQueryPart($valueParts, $fields),
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter for name (firstname, lastname, shortname)
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Name($value, $negate = false) {
		$valueParts	= TodoyuArray::trimExplode(' ', $value, true);
		$queryParts	= false;

		if( !empty($valueParts)  ) {
			$fields	= array(
				self::TABLE . '.lastname',
				self::TABLE . '.firstname',
				self::TABLE . '.shortname'
			);

			$queryParts	= array(
				'where'		=> TodoyuSql::buildLikeQueryPart($valueParts, $fields),
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter for company
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_Company($value, $negate = false) {
		$value	= intval($value);
		$queryParts	= false;

		if( $value > 0 ) {
			$tables	= array(self::TABLE, 'ext_contact_mm_company_person');
			$compare= $negate ? '!= ' : '= ';

			$where	= '			ext_contact_mm_company_person.id_company ' . $compare . $value
					. ' AND ' . self::TABLE . '.id							= ext_contact_mm_company_person.id_person ';

			$queryParts	= array(
				'tables'	=> $tables,
				'where'		=> $where
			);
		}

		return $queryParts;
	}



	/**
	 * Get filter config to search for persons name attributes and its company name
	 *
	 * @param	String		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_NameAndCompany($value, $negate = false) {
		$filterName		= $this->Filter_Name($value, $negate);
		$filterCompany	= $this->Filter_Company($value, $negate);

		if( !$filterName ) {
			$filterName = array(
				'tables'=>array()
			);
		}
		if( !$filterCompany ) {
			$filterCompany = array(
				'tables' => array()
			);
		}

		$tables	= TodoyuArray::mergeUnique($filterName['tables'], $filterCompany['tables']);
		$join	= TodoyuArray::mergeUnique($filterName['join'], $filterCompany['join']);

		if( isset($filterName['where']) && isset($filterCompany['where']) ) {
			$where = '((' . $filterName['where'] . ') OR (' . $filterCompany['where'] . '))';
		} else {
			$where = $filterName['where'] . $filterCompany['where'];
		}

		return array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $join
		);
	}



	/**
	 * Filter internal persons
	 * Persons which are employee in an internal company
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_isInternal($value, $negate = false) {
		$tables	= array(
			'ext_contact_mm_company_person',
			'ext_contact_company'
		);

		$compare	= $negate ? ' != ' : ' = ';
		$where	= '		ext_contact_company.deleted		= 0'
				. ' AND	ext_contact_company.is_internal	' . $compare . ' 1';

		$joins	= array(
			'ext_contact_person.id						= ext_contact_mm_company_person.id_person',
			'ext_contact_mm_company_person.id_company	= ext_contact_company.id'
		);

		return array(
			'tables'=> $tables,
			'where'	=> $where,
			'join'	=> $joins
		);
	}



	/**
	 * Filter person salutation
	 *
	 * @param	Mixed		$value
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_salutation($value, $negate = false) {
		$where	=	self::TABLE . '.deleted		= 0';

		if( $value === 'm' ) {
			$where 	.= ' AND	' . self::TABLE . '.salutation	= \'m\'';
		} else {
			$where 	.= ' AND	' . self::TABLE . '.salutation	IN (\'w\', \'f\') ';
		}

		return array(
			'where'	=> $where,
		);
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
			'ext_contact_mm_person_contactinfo'
		);

		$where	= ' ext_contact_contactinfo.deleted							= 0 '
				. ' AND ' . TodoyuSql::buildLikeQueryPart(array($value), array('ext_contact_contactinfo.info'))
				. ' AND ext_contact_mm_person_contactinfo.id_contactinfo	= ext_contact_contactinfo.id'
				. ' AND ' . self::TABLE . '.id								= ext_contact_mm_person_contactinfo.id_person';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Filter by system_role
	 *
	 * @param	Array		$roles
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_systemrole($roles, $negate = false) {
		if( empty($roles) ) {
			return false;
		}

		$tables = array('ext_contact_mm_person_role');

		$roleIDs= TodoyuArray::intExplode(',', $roles);
		$where  = TodoyuSql::buildInListQueryPart($roleIDs, 'ext_contact_mm_person_role.id_role', true, $negate)
				. ' AND ext_contact_person.id	= ext_contact_mm_person_role.id_person';

		return array(
			'tables'=> $tables,
			'where'	=> $where
		);
	}



	/**
	 * Filter by jobtype
	 *
	 * @param	Array		$idJobtype
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_jobtype($idJobtype, $negate = false) {
		$idJobtype  = (int) $idJobtype;

		if( $idJobtype === 0 ) {
			return false;
		}

		$tables = array('ext_contact_mm_company_person');
		$compare= $negate ? ' != ' : ' = ';
		$where  = '			ext_contact_mm_company_person.id_jobtype	' . $compare . $idJobtype
				. ' AND ' . self::TABLE . '.id = ext_contact_mm_company_person.id_person';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}



	/**
	 * Filter by country of address
	 *
	 * @param	Array		$idCountry
	 * @param	Boolean		$negate
	 * @return	Array
	 */
	public function Filter_country($idCountry, $negate = false) {
		$idCountry  = (int) $idCountry;

		if( $idCountry === 0 ) {
			return false;
		}

		$tables = array(
			'ext_contact_address',
			'ext_contact_mm_person_address',
			self::TABLE
		);
		$compare= $negate ? ' != ' : ' = ';

		$where  = '			ext_contact_address.id_country '		. $compare . $idCountry
				. ' AND		ext_contact_mm_person_address.id_address	= ext_contact_address.id '
				. ' AND ' . self::TABLE . '.id							= ext_contact_mm_person_address.id_person ';

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
						'ext_contact_mm_person_address',
						self::TABLE
					),
					'where'	=> TodoyuSql::buildLikeQueryPart($valueParts, $fields)
							.  ' AND	ext_contact_mm_person_address.id_address	= ext_contact_address.id '
							. ' AND ' . self::TABLE . '.id							= ext_contact_mm_person_address.id_person '
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

		if( sizeof($valueParts) > 0 ) {
			$fields	= array('ext_contact_address.zip');
			$queryParts	= array(
					'tables'=> array(
						'ext_contact_address',
						'ext_contact_mm_person_address',
						self::TABLE
					),
					'where'	=> TodoyuSql::buildLikeQueryPart($valueParts, $fields)
							.  ' AND	ext_contact_mm_person_address.id_address	= ext_contact_address.id '
							. ' AND ' . self::TABLE . '.id							= ext_contact_mm_person_address.id_person '
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
		$city  = trim($city);

		if( empty($city) ) {
			return false;
		}

		$tables = array(
			'ext_contact_address',
			'ext_contact_mm_person_address',
			self::TABLE
		);
		$compare= $negate ? ' != ' : ' = ';

		$where  = '			ext_contact_address.city '		. $compare . ' "' . $city . '" '
				. ' AND		ext_contact_mm_person_address.id_address	= ext_contact_address.id '
				. ' AND ' . self::TABLE . '.id							= ext_contact_mm_person_address.id_person ';

		return array(
			'tables'=> $tables,
			'where'	=> $where,
		);
	}

}

?>