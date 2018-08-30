<?php
 
 /**
 * Flat Todoyu PHP config file
 * Config files always should be included into the global namespace
 *
 * @package		Todoyu
 * @subpackage	Project						(extension key)
 */


	// Set a config variable for project extension
Todoyu::$CONFIG['EXT']['project']['somevar'] = 345;

	// Register a form hook (if you have a lot of form hooks, register them in your special config file hooks.php)
TodoyuFormHook::registerBuildForm('path/to/the/form/file.xml', 'SomeClass::myHandlerFunction', 120);

	// Constants should be defined in constants.php
	// Format: TC_EXTKEY_WHAT_EVER 			(TC = Todoyu constant)
define('TC_PROJECT_STATUS_PLANNING', 1);

	// Rootlevel key UPPERCASE, everything else lowercase
	// Array keys start on a new line and are indented by one tab, => is indented by tabs, value by a space.
Todoyu::$CONFIG['EXT']['project']['status']['task'] = array(
	'key1'	=> 'planning',
	'key2'	=> 'open',
	'key3'	=> 'progress'
);

?>