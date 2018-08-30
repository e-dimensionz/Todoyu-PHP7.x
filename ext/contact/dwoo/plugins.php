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
 * Contact specific Dwoo plugins
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 */



/**
 * Check whether given ID belongs to the current person
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	Boolean
 */
function Dwoo_Plugin_isPersonID_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'Todoyu::personid() === intval(' . $idPerson . ')';
}



/**
 * Get person ID
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @return	Boolean
 */
function Dwoo_Plugin_personid_compile(Dwoo_Compiler $compiler) {
	return 'Todoyu::personid()';
}



/**
 * Get the name to given person ID
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_name_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactPersonManager::getPerson(' . $idPerson . ')->getFullName(true)';
}



/**
 * Returns a wrapped label tag of a person, evoking person-info tooltip on rollOver
 *
 * @param	Dwoo			$dwoo
 * @param	Integer			$idPerson
 * @param	String			$idPrefix	descriptive string: 'ext'_'recordtype'
 * @param	Integer			$idRecord	record containing the person ID, e.g. task, comment, etc.
 * @param	String			$tag
 * @param	String			$class
 * @return	String
 */
function Dwoo_Plugin_personLabel(Dwoo $dwoo, $idPerson = 0, $idPrefix = 'person', $idRecord = 0, $tag = 'span', $class = '') {
	if( !TodoyuContactPersonRights::isSeeAllowed($idPerson) ) {
		return '';
	}

	$htmlID		= $idPrefix . '-' . $idRecord . '-' . $idPerson;
	$personLabel= TodoyuContactPersonManager::getLabel($idPerson);
	$attributes	= array(
		'id'	=> $htmlID,
		'class'	=> trim('quickInfoPerson ' . $class)
	);

	$personTag		= TodoyuString::buildHtmlTag($tag, $attributes, $personLabel);
	$quickInfoScript= TodoyuString::wrapScript('Todoyu.Ext.contact.QuickInfoPerson.add(\'' . $htmlID . '\');');

	return $personTag . $quickInfoScript;
}



/**
 * Get address label
 *
 * @param	Dwoo_Compiler		$compiler
 * @param	Integer				$idAddress
 * @return	String
 */
function Dwoo_Plugin_addressLabel_compile(Dwoo_Compiler $compiler, $idAddress) {
	return 'TodoyuContactAddressManager::getLabel(' . $idAddress . ')';
}



/**
 * Get person shortname, optionally generate it from first- and lastname
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idPerson
 * @param	Boolean		$truncateFromFullnameIfMissing
 * @return	String
 */
function Dwoo_Plugin_shortname(Dwoo $dwoo, $idPerson = 0, $truncateFromFullnameIfMissing = false) {
	$idPerson	= intval($idPerson);
	$person	= TodoyuContactPersonManager::getPerson($idPerson);

	$shortname	= $person->getShortname();
	if( empty($shortname) && $truncateFromFullnameIfMissing ) {
		$shortname	= strtoupper(substr($person->getFirstName(), 0, 2) . substr($person->getLastName(), 0, 2));
	}

	return $shortname;
}



/**
 * Get name of contact info type
 *
 * @package		Todoyu
 * @subpackage	Template
 *
 * @param	Dwoo		$dwoo
 * @param	Integer		$idContactinfotype
 * @return	String
 */
function Dwoo_Plugin_labelContactinfotype(Dwoo $dwoo, $idContactinfotype) {
	$idContactinfotype = intval($idContactinfotype);

	return TodoyuContactContactInfoManager::getContactInfoTypeName($idContactinfotype);
}



/**
 * Returns the label of the address type with given ID
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idAddressType
 * @return	String
 */
function Dwoo_Plugin_addressType_compile(Dwoo_Compiler $compiler, $idAddressType) {
	return 'TodoyuContactAddressManager::getAddresstypeLabel(' . $idAddressType . ')';
}



/**
 * Returns the salutation Label of a person
 *
 * @param	Dwoo $dwoo
 * @param	Integer	$idPerson
 * @return	String
 */
function Dwoo_Plugin_salutationLabel(Dwoo $dwoo, $idPerson) {
	$idPerson	= intval($idPerson);

	if( $idPerson > 0 ) {
		return TodoyuContactPersonManager::getPerson($idPerson)->getSalutationLabel();
	}

	return '';
}



/**
 * Renders the image of given person
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_personImage_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactImageManager::getContactImage(' . $idPerson . ', \'person\')';
}



/**
 * Renders the image of given person
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_personAvatar_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactImageManager::getAvatarImage(' . $idPerson . ', \'person\')';
}



/**
 * Renders image of given company
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idCompany
 * @return	String
 */
function Dwoo_Plugin_companyImage_compile(Dwoo_Compiler $compiler, $idCompany) {
	return 'TodoyuContactImageManager::getContactImage(' . $idCompany . ', \'company\')';
}



/**
 * Checks if current person is allowed to edit given company
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idCompany
 * @return	String
 */
function Dwoo_Plugin_isCompanyEditAllowed_compile(Dwoo_Compiler $compiler, $idCompany) {
	return 'TodoyuContactCompanyRights::isEditAllowed(' . $idCompany . ')';
}



/**
 * Checks if current person is allowed to see given company
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idCompany
 * @return	String
 */
function Dwoo_Plugin_isCompanySeeAllowed_compile(Dwoo_Compiler $compiler, $idCompany) {
	return 'TodoyuContactCompanyRights::isSeeAllowed(' . $idCompany . ')';
}



/**
 * Checks if current person is allowed to delete given company
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idCompany
 * @return	String
 */
function Dwoo_Plugin_isCompanyDeleteAllowed_compile(Dwoo_Compiler $compiler, $idCompany) {
	return 'TodoyuContactCompanyRights::isDeleteAllowed(' . $idCompany . ')';
}



/**
 * Checks if current person is allowed to edit given persons
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_isPersonEditAllowed_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactPersonRights::isEditAllowed(' . $idPerson . ')';
}



/**
 * Checks if current person is allowed to see given person
 *
 * @param	Dwoo_Compiler	$compiler
 * @param	Integer			$idPerson
 * @return	String
 */
function Dwoo_Plugin_isPersonSeeAllowed_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactPersonRights::isSeeAllowed(' . $idPerson . ')';
}



/**
 * Checks if current person is allowed to delete given person
 *
 * @param	Dwoo_Compiler		$compiler
 * @param	Integer				$idPerson
 * @return	String
 */
function Dwoo_Plugin_isPersonDeleteAllowed_compile(Dwoo_Compiler $compiler, $idPerson) {
	return 'TodoyuContactPersonRights::isDeleteAllowed(' . $idPerson . ')';
}



/**
 * Checks if current Person is internal
 *
 * @param	Dwoo_Compiler	$compiler
 * @return	String
 */
function Dwoo_Plugin_isInternal_compile(Dwoo_Compiler $compiler) {
	return 'TodoyuAuth::isInternal()';
}



/**
 * Checks if current person has access to the addresstype of current record (company / person)
 *
 * @param	Dwoo		$dwoo
 * @param	String		$type
 * @param	Integer		$idRecord
 * @param	Integer		$idAddressType
 * @return	Boolean
 */
function Dwoo_Plugin_isAddressTypeSeeAllowed(Dwoo $dwoo, $type, $idRecord, $idAddressType) {
	$idRecord		= intval($idRecord);
	$idAddressType	= intval($idAddressType);

	if( $type === 'person' ) {
		return TodoyuContactRights::isAddresstypeOfPersonSeeAllowed($idRecord, $idAddressType);
	} else if( $type === 'company' ) {
		return TodoyuContactRights::isAddresstypeOfCompanySeeAllowed($idRecord, $idAddressType);
	}

	return false;
}



/**
 * Checks if current person has access to the contactinfotype of current record (company / person)
 *
 * @param	Dwoo		$dwoo
 * @param	String		$type
 * @param	Integer		$idRecord
 * @param	Integer		$idAddressType
 * @return	Boolean
 */
function Dwoo_Plugin_isContactinfotypeSeeAllowed(Dwoo $dwoo, $type, $idRecord, $idAddressType) {
	$idRecord		= intval($idRecord);
	$idAddressType	= intval($idAddressType);

	if( $type === 'person' ) {
		return TodoyuContactRights::isContactinfotypeOfPersonSeeAllowed($idRecord, $idAddressType);
	} else if( $type === 'company' ) {
		return TodoyuContactRights::isContactinfotypeOfCompanySeeAllowed($idRecord, $idAddressType);
	}

	return false;
}



/**
 * @param	Dwoo	$dwoo
 * @param	Array	$contactInfoData
 * @return	Array
 */
function Dwoo_Plugin_renderContactInfoType(Dwoo $dwoo, $contactInfoData) {
	$contactInfoData = TodoyuHookManager::callHookDataModifier('contact', 'contactinfotype.render', $contactInfoData);

	return $contactInfoData['html'];
}

?>