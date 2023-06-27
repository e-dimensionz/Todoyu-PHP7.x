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
 * Render a full page when reloading the whole brower window
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuPage {

	/**
	 * Path to template file
	 *
	 * @var	String
	 */
	private static $template;

	/**
	 * Data array for template rendering
	 *
	 * @var	Array
	 */
	private static $data = array();


	private static $jsInit = array();



	/**
	 * Initialize page object with given template
	 *
	 * @param	String		$template		Path to template
	 */
	public static function init($template) {
		self::setTemplate($template);

			// Load all page configuration provided by extensions
		self::loadExtPageConfig();

			// Ensure cache\js\Config.XX.js to be available
		TodoyuConfigManager::checkJavaScriptConfig();
		self::addJavaScriptConfigAsset();

			// Add core assets
		self::addCoreAssets();
			// Add all assets of allowed extensions
		self::addExtAssets();

		self::addMetatag('', 'text/html; charset=utf-8', 'content-type');
		self::addMetatag('robots', 'noindex,nofollow');

		self::addJsInit('Todoyu.init()', 1);
	}



	/**
	 * Set page template. Normally the page template will be set by the constructor
	 *
	 * @param	String		$template
	 */
	public static function setTemplate($template) {
		self::$template = $template;
	}



	/**
	 * Add JS and CSS files which are used by the core
	 */
	private static function addCoreAssets() {
		$jsFiles	= TodoyuArray::assure(Todoyu::$CONFIG['FE']['PAGE']['assets']['js']);
		$cssFiles	= TodoyuArray::assure(Todoyu::$CONFIG['FE']['PAGE']['assets']['css']);

		foreach($jsFiles as $jsFile) {
			self::addJavascript($jsFile['file'], $jsFile['position'], $jsFile['compress'], $jsFile['merge'], $jsFile['localize']);
		}

		foreach($cssFiles as $cssFile) {
			self::addStylesheet($cssFile['file'], $cssFile['media'], $cssFile['position'], $cssFile['compress'], $cssFile['merge']);
		}
	}



	/**
	 * Add javascript config asset for user
	 *
	 */
	private static function addJavaScriptConfigAsset() {
		$idPerson	= TodoyuAuth::getPersonID();

		self::addJavascript('cache/jsconfig/Config.' . $idPerson . '.js', 51, false, false, false);
	}



	/**
	 * Add all extension assets of allowed extension. If not logged in, don't check
	 */
	private static function addExtAssets() {
		TodoyuExtensions::loadAllAssets();

		$extKeys	= TodoyuExtensions::getInstalledExtKeys();

		foreach($extKeys as $ext) {
			self::addExtJavascript($ext);
			self::addExtStylesheets($ext);
		}
	}



	/**
	 * Load extension CSS files
	 *
	 * @param	String		$ext
	 */
	private static function addExtStylesheets($ext) {
		TodoyuExtensions::loadAllAssets();

		$files	= TodoyuArray::assure(Todoyu::$CONFIG['EXT'][$ext]['assets']['css']);

		foreach($files as $file) {
			self::addStylesheet($file['file'], $file['media'] ?? 'all', $file['position'] ?? 100, $file['compress'] ?? true, $file['merge'] ?? true);
		}
	}



	/**
	 * Load extension JavaScript files
	 *
	 * @param	String		$ext
	 */
	private static function addExtJavascript($ext) {
		$files	= TodoyuArray::assure(Todoyu::$CONFIG['EXT'][$ext]['assets']['js']);
		$area	= Todoyu::getAreaKey();

		foreach($files as $file) {
				// Only check for area, when merging is disabled
			if( $file['merge'] !== true ) {
					// Limit file to areas
				if( isset($file['area']) ) {
						// List of areas
					if( is_array($file['area']) ) {
						if( !in_array($area, $file['area']) ) {
							continue;
						}
					} else {
							// Single area
						if( $area !== $file['area'] ) {
							continue;
						}
					}
				}
			}

			self::addJavascript($file['file'], $file['position'], $file['compress'], $file['merge'], $file['localize']);
		}
	}



	/**
	 * Load all page configuration of the extensions
	 */
	private static function loadExtPageConfig() {
		TodoyuExtensions::loadAllPage();
	}



	/**
	 * Set attribute in data array
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public static function set($name, $value) {
		self::$data[$name] = $value;
	}



	/**
	 * Remove attribute from data array
	 *
	 * @param	String		$name
	 */
	public static function remove($name) {
		unset(self::$data[$name]);
	}



	/**
	 * Append data to an array attribute
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public static function add($name, $value) {
		self::$data[$name][] = $value;
	}



	/**
	 * Prepend data to an array attribute
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public static function prepend($name, $value) {
		$tmp = self::$data[$name];

		self::$data[$name] = array_merge( array($value), $tmp);
	}



	/**
	 * Append a value to an string attribute
	 *
	 * @param	String		$name
	 * @param	Mixed		$value
	 */
	public static function append($name, $value) {
		self::$data[$name] .= $value;
	}



	/**
	 * Set page title
	 *
	 * @param	String		$title
	 */
	public static function setTitle($title) {
		self::set('pagetitle', Todoyu::Label($title) . ' - todoyu');
	}



	/**
	 * Set panelWidgets in page template
	 *
	 * @param	String		$panelWidgets
	 */
	public static function setPanelWidgets($panelWidgets) {
		self::set('panelWidgets', $panelWidgets);
	}



	/**
	 * Set content marker content
	 *
	 * @param	String		$content
	 */
	public static function setContent($content) {
		self::set('content', $content);
	}



	/**
	 * Set tabs marker content
	 *
	 * @param	String		$tabs
	 */
	public static function setTabs($tabs) {
		self::set('tabs', $tabs);
	}



	/**
	 * Set fullContent marker content
	 * tabs and content marker are ignored then
	 *
	 * @param	String		$fullContent
	 */
	public static function setFullContent($fullContent) {
		self::set('fullContent', $fullContent);
	}



	/**
	 * Set body ID
	 *
	 * @param	String		$bodyID
	 */
	public static function setBodyID($bodyID) {
		self::set('bodyID', $bodyID);
	}



	/**
	 * Add a metatag
	 *
	 * @param	String		$name
	 * @param	String		$content
	 * @param	String		$httpEquiv
	 */
	public static function addMetatag($name, $content, $httpEquiv = '') {
		self::add(
			'metatags',
			array(
				'name'		=> $name,
				'content'	=> $content,
				'httpequiv'	=> $httpEquiv
			)
		);
	}



	/**
	 * Add a stylesheet to the current page. The stylesheet will be managed (merged, compressed)
	 *
	 * @param	String		$pathToFile			Path to original file
	 * @param	String		$media				Media type
	 * @param	Integer		$position			File position in loading order
	 * @param	Boolean		$compress			Compress content?
	 * @param	Boolean		$merge				Add content to merge file
	 */
	public static function addStylesheet($pathToFile, $media = 'all', $position = 100, $compress = true, $merge = true) {
		TodoyuPageAssetManager::addStylesheet($pathToFile, $media, $position, $compress, $merge);
	}



	/**
	 * Add JavaScript to the current page. The script will be managed (merged, compressed, localized)
	 *
	 * @param	String		$pathToFile			Path to original file
	 * @param	Integer		$position			File position in loading order
	 * @param	Boolean		$compress			Compress content?
	 * @param	Boolean		$merge				Add content to merge file
	 * @param	Boolean		$localize			Localize content (replace [LLL:xxx] tags
	 */
	public static function addJavascript($pathToFile, $position = 100, $compress = true, $merge = true, $localize = true) {
		TodoyuPageAssetManager::addJavascript($pathToFile, $position, $compress, $merge, $localize);
	}



	/**
	 * Add inline JavaScript code
	 *
	 * @param	String		$jsCode
	 * @param	Integer		$position
	 */
	public static function addJsInline($jsCode, $position = 100) {
		self::add('jsInlines',
			array(
				'position'	=> $position,
				'code'		=> $jsCode
			)
		);
	}



	/**
	 * Add JS functions which shall be called on dom loaded
	 *
	 * @param	String		$function
	 * @param	Integer		$position
	 */
	public static function addJsInit($function, $position = 100) {
		self::$jsInit[] = array(
			'function'	=> $function,
			'position'	=> intval($position)
		);
	}



	/**
	 * All all js init function in one dom loaded callback
	 *
	 */
	private static function addJsInitsAsJsInline() {
		$inits		= TodoyuArray::sortByLabel(self::$jsInit, 'position');
		$functions	= TodoyuArray::getColumn($inits, 'function');

		$code	= "document.on('dom:loaded', function(){\n"; //);'

		foreach($functions as $function) {
			$code .= "\t" . trim($function) . ";\n";
		}

		$code	.= '});';

		self::addJsInline($code, 10);
	}



	/**
	 * Add additional header data. Can be any HTML code
	 *
	 * @param	String		$headerData
	 */
	public static function addAdditionalHeaderData($headerData) {
		self::add('additionalHeaderData', $headerData);
	}



	/**
	 * Add an attribute to the body tag
	 *
	 * @param	String		$name
	 * @param	String		$value
	 */
	public static function addBodyAttribute($name, $value) {
		self::add('bodyAttributes', array(
			'name'	=> $name,
			'value'	=> $value
		));
	}



	/**
	 * Add a class to the body element
	 *
	 * @param	String		$class
	 */
	public static function addBodyClass($class) {
		self::append('bodyClass', ' ' . $class);
	}



	/**
	 * Add an HTML element to the body
	 *
	 * @param	String		$elementHtml
	 */
	public static function addBodyElement($elementHtml) {
		self::add('bodyElements', $elementHtml);
	}



	/**
	 * Sort inline JavaScripts by position (key)
	 */
	public static function sortJSinlines() {
		if( is_array(self::$data['jsInlines']) ) {
			self::$data['jsInlines']	= TodoyuArray::sortByLabel(self::$data['jsInlines'], 'position');
		}
	}



	/**
	 * Add JavaScripts and style sheets to the page object variables
	 */
	private static function addJavascriptAndStyleSheetsToPage() {
		TodoyuPageAssetManager::addAssetsToPage();
	}



	/**
	 * Render all registered headlets
	 */
	private static function renderHead() {
		if( TodoyuAuth::isLoggedIn() ) {
			$head	= TodoyuHeadManager::render();

			self::set('head', $head);
		}
	}



	/**
	 * Render page with template
	 *
	 * @return	String
	 */
	public static function render() {
			// Call hook just before page is rendered
		TodoyuHookManager::callHook('core', 'renderPage');

			// Add headlets
		self::renderHead();

			// Add main navigation
		self::set('navigation', TodoyuRenderer::renderNavigation());

			// Add JavaScripts and stylesheet to page
		self::addJavascriptAndStyleSheetsToPage();
		self::addJsInitsAsJsInline();
		self::sortJSinlines();

		return Todoyu::render(self::$template, self::$data);
	}



	/**
	 * Render page and send output with ECHO
	 */
	public static function display() {
		echo self::render();
	}

}

?>