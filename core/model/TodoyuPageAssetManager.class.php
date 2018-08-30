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
 * Manage style sheets and javaScripts which are added to the page
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuPageAssetManager {

	/**
	 * JavaScripts to be added to the page
	 *
	 * @var	Array
	 */
	private static $javaScripts = array();

	/**
	 * Stylesheets to added to the page
	 *
	 * @var	Array
	 */
	private static $styleSheets = array();



	/**
	 * Add a JavaScript file to the page (it will be processed as configured)
	 *
	 * @param	String		$pathToFile			Path to original file
	 * @param	Integer		$position			Position in files list
	 * @param	Boolean		$compress			Compress content?
	 * @param	Boolean		$merge				Include file into merge file?
	 * @param	Boolean		$localize			Parse locale labels
	 * @return	Void|Boolean
	 */
	public static function addJavascript($pathToFile, $position = 100, $compress = true, $merge = true, $localize = true) {
		$absPathToFile	= TodoyuFileManager::pathAbsolute($pathToFile);

		$compress	= $compress === false ? false : true;
		$merge		= $merge 	=== false ? false : true;
		$localize	= $localize === false ? false : true;

		$position		= (int) $position;
		if( $position === 0) {
			$position = 100;
		}

			// Break if file not found
		if( !$absPathToFile || !is_file($absPathToFile) ) {
			TodoyuDebug::printHtml($pathToFile, 'File not found');
			return false;
		}

			// Add file, if not already in list
		if( !isset(self::$javaScripts[$absPathToFile]) ) {
			self::$javaScripts[$absPathToFile] = array(
				'file'		=> $absPathToFile,
				'position'	=> $position,
				'compress'	=> $compress,
				'merge'		=> $merge,
				'localize'	=> $localize,
				'lib'		=> strstr($absPathToFile, PATH_LIB) !== false
			);

			return true;
		}

		return false;
	}



	/**
	 * Add a stylesheet file to the page (it will be processed as configured)
	 *
	 * @param	String		$pathToFile			Path to original file
	 * @param	String		$media				Media type
	 * @param	Integer		$position			Position in files list
	 * @param	Boolean		$compress			Compress content?
	 * @param	Boolean		$merge				Include file into merge file?
	 */
	public static function addStylesheet($pathToFile, $media = 'all', $position = 100, $compress = true, $merge = true) {
		$pathToFile	= TodoyuFileManager::pathAbsolute($pathToFile);
		$media		= empty($media) ? 'all' : $media;

		$position	= (int) $position;
		if( $position === 0) {
			$position	= 100;
		}

		if( ! is_file($pathToFile) ) {
			TodoyuDebug::printHtml($pathToFile, 'File not found');
		}

		if( ! array_key_exists($pathToFile, self::$styleSheets) ) {
			self::$styleSheets[$pathToFile] = array(
				'file'		=> $pathToFile,
				'position'	=> $position,
				'compress'	=> $compress,
				'merge'		=> $merge,
				'media'		=> $media
			);
		}
	}



	/**
	 * Make cache folders (js, css)
	 */
	public static function makeCacheFolders() {
		TodoyuFileManager::makeDirDeep(PATH_CACHE . '/js');
		TodoyuFileManager::makeDirDeep(PATH_CACHE . '/css');
	}



	/**
	 * Add processed assets (JS + CSS) to the page
	 */
	public static function addAssetsToPage() {
		self::makeCacheFolders();

			// Add javaScripts
		$jsFiles	= self::getJavascripts();

			// Add all JS files
		foreach($jsFiles as $jsFile) {
			TodoyuPage::add('jsFiles', array(
				'file' => $jsFile
			));
		}

			// Add style sheets
		$cssFiles	= self::getStyleSheets();

			// Add all CSS files
		foreach($cssFiles as $cssFile) {
			TodoyuPage::add('cssFiles', array(
				'file'	=> $cssFile['file'],
				'media'	=> $cssFile['media']
			));
		}
	}








	### GLOBAL METHODS ###


	/**
	 * Build a unique merge file name
	 * The md5 hash is based on the content of the configuration and the
	 * modification times of all included files
	 *
	 * @param	Array		$fileConfigs
	 * @param	String		$fileExt
	 * @return	String
	 */
	private static function buildMergefileName(array $fileConfigs, $fileExt) {
		$files		= TodoyuArray::getColumn($fileConfigs, 'file');
		$md5hash	= md5(implode('', $files));

		return $md5hash . '.' . $fileExt;
	}










	### JAVASCRIPT ###


	/**
	 * Get all JavaScripts which have to be included in the page
	 *
	 * @return	Array
	 */
	private static function getJavascripts() {
		$files	= array();
		$single	= array();

			// Arrays of core / 3rd party merge file configs
		$merge		= array();
		$libsMerge	= array();

		$javaScripts= TodoyuArray::sortByLabel(self::$javaScripts, 'position');
		$doMerging	= Todoyu::$CONFIG['CACHE']['JS']['merge'];

		foreach( $javaScripts as $fileConfig ) {
			if( $doMerging && $fileConfig['merge'] ) {
					// If file is a third party library, add to a separate merge file
				if( $fileConfig['lib'] ) {
					$libsMerge[]	= $fileConfig;
				} else {
					$merge[]	= $fileConfig;
				}
			} else {
				$single[]	= $fileConfig;
			}
		}

			// Add single files to list
		if( sizeof($single) ) {
			$files = self::getSingleJavascriptFiles($single);
		}

			// Add library merge file to list
		if( sizeof($libsMerge) > 0 ) {
			$files[] = self::getMergedJavascriptFile($libsMerge);
		}

			// Add merge-file to list
		if( sizeof($merge) > 0 ) {
			$files[] = self::getMergedJavascriptFile($merge);
		}

		return $files;
	}



	/**
	 * Get the JavaScript merge file
	 *
	 * @param	Array		$fileConfigs		Configs for all files which have merging enabled
	 * @return	String		Web path to merge file in cache
	 */
	private static function getMergedJavascriptFile(array $fileConfigs) {
		$locale			= Todoyu::getLocale();
		$mergeFileName	= self::buildMergefileName($fileConfigs, $locale . '.js');
		$mergeFilePath	= PATH_CACHE . DIR_SEP . 'js' . DIR_SEP . $mergeFileName;

			// If merge file doesn't exist yet, create it
		if( ! is_file($mergeFilePath) ) {
			$mergeCode	= '';

			$doLocalize	= Todoyu::$CONFIG['CACHE']['JS']['localize'];
			$doCompress	= Todoyu::$CONFIG['CACHE']['JS']['compress'];

			foreach($fileConfigs as $fileConfig) {
				$fileCode	= file_get_contents($fileConfig['file']);

				if( $doLocalize && $fileConfig['localize'] ) {
					$fileCode = self::localizeJavascript($fileCode);
				}
				if( $doCompress && $fileConfig['compress'] ) {
					$fileCode = self::compressJavaScript($fileCode);
				}

					// If not compressed, add file information at the top of the code
				if( !$doCompress ) {
					$fileCode = "\n\n/* " . TodoyuFileManager::pathWeb($fileConfig['file']) . "\n" . str_repeat('=', 50) . "*/\n" . $fileCode;
				}

				$mergeCode .= $fileCode;
			}

			TodoyuFileManager::saveFileContent($mergeFilePath, $mergeCode);
		}

		return TodoyuFileManager::pathWeb($mergeFilePath);
	}



	/**
	 * Get paths to JavaScript files which are not merged (but possibly compressed and localized)
	 *
	 * @param	Array		$fileConfigs
	 * @return	Array
	 */
	private static function getSingleJavascriptFiles(array $fileConfigs) {
		$fileConfigs= TodoyuArray::sortByLabel($fileConfigs);
		$files		= array();
		$doLocalize	= Todoyu::$CONFIG['CACHE']['JS']['localize'];
		$doCompress	= Todoyu::$CONFIG['CACHE']['JS']['compress'];

		foreach($fileConfigs as $fileConfig) {
			if( ($doLocalize && $fileConfig['localize']) || ($doCompress && $fileConfig['compress']) ) {

				$localized	= $doLocalize && $fileConfig['localize'];
				$compressed	= $doCompress && $fileConfig['compress'];

					// Get file path
				$filePath	= self::getSingleJavascriptPath($fileConfig['file'], $compressed, $localized);

				if( ! is_file($filePath) ) {
					$fileCode	= file_get_contents($fileConfig['file']);

					if( $localized ) {
						$fileCode	= self::localizeJavascript($fileCode);
						$localized	= true;
					}
					if( $compressed ) {
						$fileCode	= self::compressJavaScript($fileCode);
						$compressed	= true;
					}

						// Save content in this file
					TodoyuFileManager::saveFileContent($filePath, $fileCode);
				}
			} else {
				$filePath	= $fileConfig['file'];
			}

			$files[] = TodoyuFileManager::pathWeb($filePath);
		}

		return $files;
	}



	/**
	 * Path to single JavaScript file
	 *
	 * @param	String		$pathToFile
	 * @param	Boolean		$compressed
	 * @param	Boolean		$localized
	 * @return	String
	 */
	private static function getSingleJavascriptPath($pathToFile, $compressed = false, $localized = false) {
		$pathToFile	= TodoyuFileManager::pathAbsolute($pathToFile);
		$dirHash	= TodoyuString::md5short(dirname($pathToFile));
		$pathInfo	= pathinfo($pathToFile);

		$postfix	= ($compressed ? '-min' : '') . ($localized ? '-' . TodoyuLabelManager::getLocale() : '');

		$storagePath= PATH_CACHE . DIR_SEP . 'js' . DIR_SEP . $dirHash . '.' . $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];

		return $storagePath;
	}



	/**
	 * Compress JavaScript code
	 *
	 * @param	String		$javaScriptCode
	 * @return	String
	 */
	public static function compressJavaScript($javaScriptCode) {
		require_once( PATH_LIB . '/php/jsmin.php' );

		try {
			return JSMin::minify($javaScriptCode);
		} catch(JSMinException $e) {
			ob_end_clean();
//			TodoyuDebug::printHtml($e->getTrace(), $e->getMessage());
			TodoyuDebug::printHtml($javaScriptCode, 'JSMin Error: ' . $e->getMessage());
			exit();
		}
	}



	/**
	 * Localize a JavaScript
	 *
	 * @param	String	$javascriptCode
	 * @return	String
	 */
	public static function localizeJavascript($javascriptCode) {
		return preg_replace_callback(Todoyu::$CONFIG['CACHE']['JS']['localePattern'], array('TodoyuPageAssetManager', 'localizeJavascriptCallback'), $javascriptCode);
	}



	/**
	 * Callback for javascript localization
	 *
	 * @param	Array		$match		Regex matching data
	 * @return	String
	 */
	private static function localizeJavascriptCallback(array $match) {
		return str_replace('\'', '\\\'', Todoyu::Label($match[1]));
	}











	### STYLESHEETS ###


	/**
	 * Get style sheets which have to be included in the page
	 * The files are merged and compressed as configured in the asset array
	 *
	 * @param   Array   $styleSheets	optional
	 * @return	Array
	 */
	public static function getStyleSheets($styleSheets = array()) {
		$files	= array();
		$merge	= array();
		$single	= array();

		if( count($styleSheets) === 0 ) {
			$styleSheets= TodoyuArray::sortByLabel(self::$styleSheets, 'position');
		}

		$doMerging	= Todoyu::$CONFIG['CACHE']['CSS']['merge'];

			// Parse SCSS files
		foreach($styleSheets as $index => $styleSheet) {
			$stylesheetFile	= $styleSheet['file'];
			if( TodoyuString::endsWith($stylesheetFile, '.scss') ) {
					// Parse SCSS and store resulting CSS in cache to be included in page
				$styleSheets[$index]['file']	= self::parseScssStylesheet($stylesheetFile);
			}
		}

			// Group stylesheets to be merged / added separately
		foreach( $styleSheets as $fileConfig ) {
			if( $doMerging && $fileConfig['merge'] !== false ) {
				$merge[]	= $fileConfig;
			} else {
				$single[]	= $fileConfig;
			}
		}
			// Process non-merge files
		if( sizeof($single) ) {
			$files = self::getSingleCssFiles($single);
		}
			// Process merge files
		if( sizeof($merge) > 0 ) {
			$files = array_merge($files, self::getMergedCssFiles($merge));
		}

		return $files;
	}



	/**
	 * Parse given SCSS file into cache/css/ and return the new file path
	 *
	 * @param	String			$pathScss
	 * @return	String|Boolean	Path of created CSS file in cache / false if failed
	 */
	public static function parseScssStylesheet($pathScss) {
			// Create unique filename for parsed file
		$filenameCss= str_replace('.scss', '.css', basename($pathScss));
		$pathWeb	= TodoyuFileManager::pathWeb($pathScss);

		$pathCssDir	= TodoyuFileManager::pathAbsolute('cache/css/' . dirname($pathWeb));
		$pathCssFile= $pathCssDir. '/' . $filenameCss;

			// Parse if not yet
		if( ! file_exists($pathCssFile) ) {
			TodoyuFileManager::makeDirDeep($pathCssDir);
			$sassParser	= self::getSassParser($pathScss); /*(TodoyuFileManager::getFileName($pathScss));*/

			$cssCode	= $sassParser->toCss($pathScss, true);
			$cssCode	= self::rewriteRelativePaths($cssCode, $pathScss, true);

			return TodoyuFileManager::saveFileContent($pathCssFile, $cssCode) ? $pathCssFile : false;
		}

		return $pathCssFile;
	}



	/**
	 * Get SassParser instance
	 *
	 * @param	String		$pathScss
	 * @return	SassParser
	 */
	public static function getSassParser($pathScss) {
		require_once( PATH_LIB . '/php/phpsass/SassParser.php');

		$options = array(
			'filename' 		=> array(
					'dirname' 	=> dirname($pathScss),
					'basename'	=> TodoyuFileManager::getFileName($pathScss)
			),
			'style'			=> SassRenderer::STYLE_NESTED,
			'cache'			=> false,
			'syntax'		=> SassFile::SCSS,
			'debug'			=> true,
			'callbacks'	=> array(
					'warn'	=> false,	//'cb_warn',
					'debug' => false	//'cb_debug'
			),
		);

		return new SassParser($options);
	}



	/**
	 * Get single CSS files (non-merge)
	 *
	 * @param	Array		$fileConfigs
	 * @return	Array		List of file paths with media type attribute
	 */
	private static function getSingleCssFiles(array $fileConfigs) {
		$fileConfigs= TodoyuArray::sortByLabel($fileConfigs, 'position');
		$files		= array();
		$doCompress	= Todoyu::$CONFIG['CACHE']['CSS']['compress'];

			// Make sure CSS cache folder exists
		if( $doCompress ) {
			TodoyuFileManager::makeDirDeep( PATH_CACHE . DIR_SEP . 'css');
		}

			// Collect file paths and create compressed version if configured
		foreach($fileConfigs as $fileConfig) {
			if( $doCompress && $fileConfig['compress'] ) {
					// Get file path (absolute)
				$filePath	= self::getSingleStylesheetPath($fileConfig['file'], true);

					// If file doesn't exist, create a compressed version
				if( ! is_file($filePath) ) {
						// Get content
					$fileCode	= file_get_contents($fileConfig['file']);
						// Compress
					$fileCode	= self::compressStylesheet($fileCode);
						// Rewrite external media paths (url())
					$fileCode	= self::rewriteRelativePaths($fileCode, $fileConfig['file']);
						// Save content in this file
					TodoyuFileManager::saveFileContent($filePath, $fileCode);
				}
			} else {
					// No compression, get normal path
				$filePath	= $fileConfig['file'];
			}

			$files[] = array(
				'file'	=> TodoyuFileManager::pathWeb($filePath),
				'media'	=> $fileConfig['media']
			);
		}

		return $files;
	}



	/**
	 * Get path to cached CSS file
	 *
	 * @param	String		$pathToFile			Path to uncached file
	 * @param	Boolean		$compressed			Compress content with cssMin?
	 * @return	String							Absolute path for the cache file
	 */
	private static function getSingleStylesheetPath($pathToFile, $compressed = false) {
		$pathToFile	= TodoyuFileManager::pathAbsolute($pathToFile);
		$dirHash	= TodoyuString::md5short(dirname($pathToFile));
		$pathInfo	= pathinfo($pathToFile);

		$postfix	= $compressed ? '-min' : '' ;

		$storagePath= PATH_CACHE . DIR_SEP . 'css' . DIR_SEP. $dirHash . '.' . $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];

		return $storagePath;
	}



	/**
	 * Get merged CSS files (one for each media type)
	 *
	 * @param	Array		$fileConfigs
	 * @return	Array
	 */
	private static function getMergedCssFiles(array $fileConfigs) {
		$files		= array();
		$media		= array();
		$doCompress	= Todoyu::$CONFIG['CACHE']['CSS']['compress'];

			// Split in to the different media types
		foreach($fileConfigs as $fileConfig) {
			$media[$fileConfig['media']][] = $fileConfig;
		}

			// Process all files of a media type
		foreach($media as $mediaType => $mediaFileConfigs) {
			$mergeFileName	= self::buildMergefileName($mediaFileConfigs, 'css');
			$mergeFilePath	= PATH_CACHE . DIR_SEP . 'css' . DIR_SEP .$mergeFileName;

				// If merge file doesn't exist yet, create it
			if( ! is_file($mergeFilePath) ) {
				$mergeCode	= '';

				foreach($mediaFileConfigs as $fileConfig) {
					if( is_file($fileConfig['file']) ) {
							// Load file content
						$fileCode	= file_get_contents($fileConfig['file']);
							// Rewrite external media paths (url())
						$fileCode	= self::rewriteRelativePaths($fileCode, $fileConfig['file']);

						if( $doCompress && $fileConfig['compress'] ) {
							$fileCode = self::compressStylesheet($fileCode);
						}

							// If not compressed, add file information at the top of the code
						if( !$doCompress ) {
							$fileCode = "\n\n/* " . TodoyuFileManager::pathWeb($fileConfig['file']) . "\n" . str_repeat('=', 50) . "*/\n" . $fileCode;
						}

						$mergeCode .= $fileCode;
					}
				}

					// Write content into file
				TodoyuFileManager::saveFileContent($mergeFilePath, $mergeCode);
			}

			$files[] = array(
				'file'	=> TodoyuFileManager::pathWeb($mergeFilePath),
				'media'	=> $mediaType
			);
		}

		return $files;
	}



	/**
	 * Compress CSS code
	 *
	 * @param	String		$cssCode
	 * @return	String
	 */
	private static function compressStylesheet($cssCode) {
		require_once( PATH_LIB . '/php/cssmin.php' );

		return cssmin::minify($cssCode);
	}



	/**
	 *  Rewrite relative CSS paths in files
	 *
	 * @param	String		$cssCode				CSS code
	 * @param	String		$pathSourceFile			Absolute path to source file
	 * @param	Boolean		$withSubFolders			Prefix dirUp (../) for single cache files
	 * @return	String
	 */
	private static function rewriteRelativePaths($cssCode, $pathSourceFile, $withSubFolders = false) {
			// Remove quotes in url() elements
		$pattern	= '|url\([\'"]{1}([^\'")]+?)[\'"]{1}\)|';
		$replace	= 'url($1)';
		$cssCode	= preg_replace($pattern, $replace, $cssCode);

			// Web path
		$webDirName	= dirname(TodoyuFileManager::pathWeb($pathSourceFile));
			// Up dir levels
		$levels		= 2; // cache folder levels
		if( $withSubFolders ) {
			$levels += substr_count($webDirName, '/') + 1; // + subfolder levels (needs +1)
		}

			// Rewrite paths
		$search		= 'url(';
		$replace	= 'url(' . str_repeat('../', $levels) . $webDirName . '/';
		$cssCode	= str_replace($search, $replace, $cssCode);

			// Make a real path
		$search		= '|url\((.*?)\)|';
		$cssCode	= preg_replace_callback($search, array('TodoyuPageAssetManager','callbackRealpath'), $cssCode);

		return $cssCode;
	}



	/**
	 * Callback for make a nicer and shorter path in the CSS file url attributes
	 *
	 * @param	Array		$match		Matching data array
	 * @return	String
	 */
	private static function callbackRealpath(array $match) {
		$realpath	= realpath(PATH_CACHE . '/css/' . $match[1]);

		if( $realpath !== false ) {
			$realpath = '../../' . TodoyuFileManager::pathWeb($realpath);
		} else {
			$realpath = $match[1];
		}

		return 'url(' . $realpath . ')';
	}



	/**
	 * Add IE custom scripts to the browser (if its an IE)
	 */
	function addInternetExplorerAssets() {
		if( TodoyuBrowserInfo::isIE() ) {
			self::addStylesheet('core/asset/css/compatibility/ie.scss', 'all', 1000);

			if( TodoyuBrowserInfo::getMajorVersion() === 8 ) {
				self::addStylesheet('core/asset/css/compatibility/ie8.scss', 'all', 1000);
			}
		}
	}

}

?>