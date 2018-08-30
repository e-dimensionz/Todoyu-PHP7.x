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
 * config\colors.php
 *
 * Global enumerated colors array.
 *
 * Used to visually differ various elements (e.g list entries/ options, persons, types, etc.),
 * to be used assigned top-down upon elements (repeated from beginning when there are more elements than predifined colors)
 *
 * To evoke (re-)generation of 'cache/css/colors.css' and 'cache/img/colors.png' evoke: Install::generateColorsCSS();
 */

Todoyu::$CONFIG['COLORS'] = array(
		// Based upon "tango"-palette
	'#FCE94F',	// Butter 1
	'#8AE234',	// Chameleon Green 1
	'#E9B96E',	// Chocolate 1
	'#729FCF',	// Sky Blue 1
	'#FCAF3E',	// Orange 1
	'#AD7FA8',	// Plum 1
	'#EF2929',	// Scarlet Red 1
	'#EEEEEC',	// Aluminium 1

	'#EDD400',	// Butter 2
	'#73D216',	// Chameleon Green 2
	'#C17D11',	// Chocolate 2
	'#3465A4',	// Sky Blue 2
	'#F57900',	// Orange 2
	'#75507B',	// Plum 2
	'#CC0000',	// Scarlet Red 2
	'#D3D7CF',	// Aluminium 2

	'#C4A000',	// Butter 3
	'#4E9A06',	// Chameleon Green 3
	'#8F5902',	// Chocolate 3
	'#204A87',	// Sky Blue 3
	'#CE5C00',	// Orange 3
	'#5C3566',	// Plum 3
	'#A40000',	// Scarlet Red 3
	'#BABDB6',	// Aluminium 3
);

?>