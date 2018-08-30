<?php

/**
 * Truncates a string at the given length
 * <pre>
 *  * value : text to truncate
 *  * length : the maximum length for the string
 *  * etc : the characters that are added to show that the string was cut off
 *  * break : if true, the string will be cut off at the exact length, instead of cutting at the nearest space
 *  * middle : if true, the string will contain the beginning and the end, and the extra characters will be removed from the middle
 * </pre>
 * This software is provided 'as-is', without any express or implied warranty.
 * In no event will the authors be held liable for any damages arising from the use of this software.
 *
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  Copyright (c) 2008, Jordi Boggiano
 * @license    http://dwoo.org/LICENSE   Modified BSD License
 * @link       http://dwoo.org/
 * @version    1.1.0
 * @date       2009-07-18
 * @package    Dwoo
 */
function Dwoo_Plugin_truncate(Dwoo $dwoo, $value, $length=80, $etc='...', $break=false, $middle=false)
{
	if ($length == 0) {
		return '';
	}

	$value = (string) $value;
	$etc = (string) $etc;
	$length = (int) $length;

	if (mb_strlen($value, 'utf-8') < $length) {
		return $value;
	}

	$length = max($length - strlen($etc), 0);
	if ($break === false && $middle === false) {
		$value = preg_replace('#\s+(\S*)?$#', '', mb_substr($value, 0, $length+1, 'utf-8'));
	}
	if ($middle === false) {
		return mb_substr($value, 0, $length, 'utf-8') . $etc;
	}
	return mb_substr($value, 0, ceil($length/2), 'utf-8') . $etc . mb_substr($value, -floor($length/2), 'utf-8');
}
