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
 * Core Action Controller
 * About
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuCoreAboutActionController extends TodoyuActionController {

	/**
	 * Render about window
	 *
	 * @param	Array		$params
	 * @return	String
	 */
	public function popupAction(array $params) {
		$data	= array(
			'names'	=> array(
				'Zimmermann'		=> 'Adrian',
				'Ledergerber'		=> 'Andr&eacute;',
				'Oechslin'			=> 'Andr&eacute;',
				'Steiner'			=> 'Andri',
				'Schenker'			=> 'Astrid',
				'Boppart'			=> 'Cornel',
				'Brander'			=> 'Dominic',
				'Erni'				=> 'Fabian',
				'Orlow'				=> 'Joel B.',
				'Stenschke'			=> 'Kay',
				'Rossi'				=> 'Mario',
				'Rohner'			=> 'Markus',
				'Wiederkehr'		=> 'Martin',
				'Karrer'			=> 'Nicolas',
				'Fuchser'			=> 'Pascal',
				'Imboden'			=> 'Thomas',
				'Schl&auml;pfer'	=> 'Thomas'
			),
			'thirdpartycredits'	=> require('core/config/libraries.inc.php')
		);
		ksort($data['names']);

		$tmpl	= 'core/view/about-window.tmpl';

		return Todoyu::render($tmpl, $data);
	}

}

?>