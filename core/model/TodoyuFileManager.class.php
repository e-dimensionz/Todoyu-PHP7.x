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
 * File management functions
 *
 * @package		Todoyu
 * @subpackage	Core
 */
class TodoyuFileManager {

	/**
	 * Remove absolute site path from a path
	 *
	 * @param	String		$path
	 * @return	String
	 */
	public static function removeSitePath($path) {
		return str_replace(PATH, '', $path);
	}



	/**
	 * Get absolute path
	 *
	 * @param	String	$path
	 * @return	String
	 */
	public static function pathAbsolute($path) {
		$path	= trim($path);

			// Replace directory separator according to current system settings
		$path = str_replace(array('\\', '/'), DIR_SEP, $path);

			// If no absolute path
		if( substr($path, 0, 1) !== '/' && substr($path, 0, strlen(PATH)) !== PATH && substr($path, 1, 1) !== ':' ) {
			$path = PATH . DIR_SEP . $path;
		}

			// Remove slash at the end
		if( substr($path, -1, 1) === DIR_SEP ) {
			$path = substr($path, 0, -1);
		}

		return $path;
	}



	/**
	 * Get web path of a file
	 *
	 * @param	String		$absolutePath
	 * @param	Boolean		$prependDomain
	 * @return	String
	 */
	public static function pathWeb($absolutePath, $prependDomain = false) {
		$pathWeb = str_replace('\\', '/', str_replace(PATH . DIR_SEP, '', self::pathAbsolute($absolutePath)));

		if( $prependDomain ) {
			$pathWeb = TODOYU_URL . '/' . $pathWeb;
		}

		return $pathWeb;
	}



	/**
	 * Delete all files inside given folder
	 *
	 * @param	String		$folderPath
	 * @param	Boolean		$deleteHidden	Deletion of all files was successful
	 * @return	Boolean		Success?
	 */
	public static function deleteFolderContents($folderPath, $deleteHidden = false) {
		$folderPath = self::pathAbsolute($folderPath);

			// No folder, no action taken
		if( ! is_dir($folderPath) ) {
			return true;
		}

		$folders	= self::getFoldersInFolder($folderPath, $deleteHidden);
		$files		= self::getFilesInFolder($folderPath, $deleteHidden);
		$success	= true;

			// Delete folders with contents
		foreach($folders as $folderName) {
			$pathFolder	= $folderPath . DIR_SEP . $folderName;

			if( is_dir($pathFolder) ) {
				$successContents = self::deleteFolderContents($pathFolder, $deleteHidden);

				if( !$successContents ) {
					$success = false;
				}

					// Check if there are still elements in the folder
				$elementsInFolder	= self::getFolderContents($pathFolder, true);

					// Only delete the folder if empty
				if( sizeof($elementsInFolder) === 0 ) {
					$successFolder = self::deleteFolder($pathFolder);

					if( !$successFolder ) {
						$success = false;
					}
				}
			}
		}

			// Delete files in folder
		foreach($files as $filename) {
			$pathFile	= $folderPath . DIR_SEP . $filename;

			$success	= self::deleteFile($pathFile);
		}

		return $success;
	}



	/**
	 * Delete given file, return deletion whether succeeded, log failures
	 *
	 * @param	String		$pathFile
	 * @return	Boolean
	 */
	public static function deleteFile($pathFile) {
		$pathFile	= self::pathAbsolute($pathFile);

		if( is_file($pathFile) && file_exists($pathFile) ) {
			if( is_writable($pathFile) ) {
				$success	= unlink($pathFile);
			} else {
				TodoyuLogger::logError('Can\'t delete file. File not writable: ' . $pathFile);
				$success = false;
			}
		} else {
			TodoyuLogger::logError('Can\'t delete file. File not found: ' . $pathFile);
			$success = false;
		}

		return $success;
	}



	/**
	 * Delete given directory and all contained files
	 *
	 * @param	String		$pathFolder
	 * @return	Boolean
	 */
	public static function deleteFolder($pathFolder) {
		$pathFolder	= self::pathAbsolute($pathFolder);

			// Prevent deleting whole todoyu if an empty variable is given
		if( $pathFolder === PATH ) {
			return false;
		}

		$success	= true;

		if( is_dir($pathFolder) ) {
			self::deleteFolderContents($pathFolder, true);

			$result	= rmdir($pathFolder);
			if( !$result ) {
				TodoyuLogger::logNotice('Folder deletion failed: ' . $pathFolder);
				$success = false;
			}
		} else {
			$success = false;
		}

		return $success;
	}



	/**
	 * Replace all not allowed characters of a filename by "_" or another character
	 *
	 * @param	String		$dirtyFilename		Filename (not path!)
	 * @param	String		$replacement
	 * @return	String
	 */
	public static function makeCleanFilename($dirtyFilename, $replacement = '_') {
		$pattern	= '|[^A-Za-z0-9\.\-_\[\]()]|';

		return preg_replace($pattern, $replacement, $dirtyFilename);
	}



	/**
	 * Create multiple sub directories to create a path structure in the file system
	 * The path will be a directory (don't give a file path as parameter!)
	 *
	 * @param	String		$directoryPath		Directory path to create
	 * @param	Integer		$mode				Access rights mode
	 * @return	Boolean
	 */
	public static function makeDirDeep($directoryPath, $mode = null) {
		$directoryPath	= self::pathAbsolute($directoryPath);
		$mode			= is_null($mode) ? Todoyu::$CONFIG['CHMOD']['folder'] : $mode;

			// Check if directory already exists
		if( is_dir($directoryPath) ) {
			return true;
		}

		$success	= mkdir($directoryPath, $mode, true);

			// Make sure
		if( $success ) {
			$last	= null;
			while( $last != $directoryPath && is_dir($directoryPath) ) {
				if( ! @chmod($directoryPath, $mode) ) {
					break;
				}
				$last			= $directoryPath;
				$directoryPath	= dirname($directoryPath);

				if( $directoryPath === PATH ) {
					break;
				}
			}
		}

		return $success;
	}



	/**
	 * Create new randomly named folder inside cache, optionally prefixed as given, return path (or false on failure)
	 *
	 * @param	String		$basePath
	 * @param	Boolean		$more_entropy		Add additional entropy? (making result more unique)
	 * @param	String		$prefix
	 * @return	String|Boolean
	 */
	public static function makeRandomCacheDir($basePath, $more_entropy = false, $prefix = '') {
		$dirName= uniqid($prefix, $more_entropy);
		$path	= self::pathAbsolute(PATH_CACHE . DIR_SEP . $basePath . DIR_SEP . $dirName) ;

		return self::makeDirDeep($path) ? $path : false;
	}



	/**
	 * Get random temp file (path) in cache
	 *
	 * @param	String|Boolean	$ext		File extension
	 * @param	Boolean			$create		Create empty file with (touch)
	 * @return	String
	 */
	public static function getTempFile($ext = false, $create = false) {
		$key	= md5(PATH . NOW . microtime(true) . uniqid());
		$path	= self::pathAbsolute(PATH_CACHE . '/temp/' . $key);

		if( $ext !== false ) {
			$path .= '.' . $ext;
		}

		self::makeDirDeep(dirname($path));

		if( $create ) {
			touch($path);
		}

		return $path;
	}



	/**
	 * Check if file exists. Also relative path from PATH
	 *
	 * @param	String		$path
	 * @return	Boolean
	 */
	public static function isFile($path) {
		$path	= self::pathAbsolute($path);

		return is_file($path);
	}



	/**
	 * Set modification timestamp of file
	 *
	 * @param	String		$filePath
	 * @return	Boolean
	 */
	public static function touch($filePath) {
		$filePath	= self::pathAbsolute($filePath);

		return touch($filePath);
	}



	/**
	 * Save file content based on a template
	 *
	 * @param	String		$savePath		Path where the file is saved
	 * @param	String		$templateFile	Path to the template file
	 * @param	Array		$data			Template data
	 * @param	Boolean		$wrapAsPhp		Wrap content with PHP start and end tags
	 * @return	Integer|Boolean				Number of bytes written to file / false
	 */
	public static function saveTemplatedFile($savePath, $templateFile, array $data = array(), $wrapAsPhp = true) {
		$savePath		= self::pathAbsolute($savePath);
		$templateFile	= self::pathWeb($templateFile);

			// Render file content
		$content= Todoyu::render($templateFile, $data);

		if( $wrapAsPhp ) {
				// Add PHP start and end tag
			$content= TodoyuString::wrap($content, '<?php|?>');
		}

		return TodoyuFileManager::saveFileContent($savePath, $content) !== false;
	}



	/**
	 * Move a file to the folder structure
	 *
	 * @param	String			$storagePath			Path of the storage directory
	 * @param	String			$sourceFile				Path to source file
	 * @param	String			$realFileName			
	 * @param	Boolean			$prependTimestamp
	 * @return	String|Boolean	New file path or FALSE
	 */
	public static function addFileToStorage($storagePath, $sourceFile, $realFileName, $prependTimestamp = true) {
		$sourceFile	= self::pathAbsolute($sourceFile);
		$fileName	= self::makeCleanFilename($realFileName);

		if( !self::isFile($sourceFile) ) {
			TodoyuLogger::logError('Tried to add not existing file to storage <' . $sourceFile . '>');
			return false;
		}

		if( $prependTimestamp ) {
			$fileName	= NOW . '_' . $fileName;
		}

		$targetFile	= self::pathAbsolute($storagePath . DIR_SEP . $fileName);
		$fileMoved	= rename($sourceFile, $targetFile);

		return $fileMoved ? $targetFile : false;
	}



	/**
	 * Set default access rights to folder or file
	 *
	 * @param	String		$path
	 * @return	Boolean
	 */
	public static function setDefaultAccessRights($path) {
		$path	= self::pathAbsolute($path);

		if( is_file($path) ) {
			return self::setDefaultFileAccess($path);
		} elseif( is_dir($path) ) {
			return self::setDefaultFolderAccess($path);
		}

		return false; // a special element we don't handle here
	}



	/**
	 * Set default file access
	 *
	 * @param	String		$pathToFile
	 * @return	Boolean
	 */
	public static function setDefaultFileAccess($pathToFile) {
		$pathToFile	= self::pathAbsolute($pathToFile);

		return @chmod($pathToFile, Todoyu::$CONFIG['CHMOD']['file']);
	}



	/**
	 * Set default file access
	 *
	 * @param	String		$pathToFolder
	 * @return	Boolean
	 */
	public static function setDefaultFolderAccess($pathToFolder) {
		$pathToFolder	= self::pathAbsolute($pathToFolder);

		return @chmod($pathToFolder, Todoyu::$CONFIG['CHMOD']['folder']);
	}



	/**
	 * Save content in file
	 *
	 * @param	String		$pathFile
	 * @param	String		$content
	 * @return	Integer|Boolean			Number of bytes written / false
	 */
	public static function saveFileContent($pathFile, $content) {
		$pathFile	= self::pathAbsolute($pathFile);
		self::makeDirDeep(dirname($pathFile));

		return file_put_contents($pathFile, $content);
	}



	/**
	 * Get file content
	 *
	 * @param	String		$pathFile
	 * @return	String
	 */
	public static function getFileContent($pathFile) {
		$pathFile	= self::pathAbsolute($pathFile);

		if( is_file($pathFile) && is_readable($pathFile) ) {
			return file_get_contents($pathFile);
		} else {
			TodoyuLogger::logError('Can\'t open file! File: ' . $pathFile);
			return '';
		}
	}



	/**
	 * Check if a file is in allowed download paths
	 * By default, no download path is allowed (except PATH_FILES)
	 * You can allow paths in Todoyu::$CONFIG['sendFile']['allow'] or disallow paths in Todoyu::$CONFIG['sendFile']['disallow']
	 * Disallow tasks precedence before allow
	 *
	 * @param	String		$absoluteFilePath		Absolute path to file
	 * @return	Boolean
	 */
	public static function isFileInAllowedDownloadPath($absoluteFilePath) {
		$absoluteFilePath	= realpath($absoluteFilePath);
		$disallowedPaths	= Todoyu::$CONFIG['sendFile']['disallow'];
		$allowedPaths		= Todoyu::$CONFIG['sendFile']['allow'];

			// If file exists
		if( $absoluteFilePath !== false ) {
				// Check if file is in an explicitly disallowed path
			if( is_array($disallowedPaths) ) {
				foreach($disallowedPaths as $disallowedPath) {
					if( strpos($absoluteFilePath, $disallowedPath) !== false ) {

						return false;
					}
				}
			}
				// Check if file is in an allowed path
			if( is_array($allowedPaths) ) {
				foreach($allowedPaths as $allowedPath) {
					if( strpos($absoluteFilePath, $allowedPath) !== false ) {
						return true;
					}
				}
			}
		}

			// If file not found, or no allowing config available, disallow download
		return false;
	}



	/**
	 * Read a file from hard disk and send it to the browser (with echo)
	 * Reads file in small parts (1024 B)
	 *
	 * @throws	TodoyuExceptionFileDownload
	 * @param	String		$pathFile
	 * @param	String		$mimeType			Mime type of the file
	 * @param	String		$fileName			Name of the downloaded file shown in the browser
	 * @param	Boolean		$asAttachment
	 * @return	Boolean		File was allowed to download and sent to browser
	 */
	public static function sendFile($pathFile, $mimeType = null, $fileName = null, $asAttachment = true) {
			// Get real path
		$pathFile	= self::pathAbsolute($pathFile);
		$status		= self::canSendFile($pathFile);

			// Problem detected?
		if( $status !== true ) {
			throw new TodoyuExceptionFileDownload($pathFile, $status);
		}

			// Clear file information cache
		clearstatcache();
			// Send download headers
		$fileSize	= filesize($pathFile);
		$fileName	= is_null($fileName) ? basename($pathFile) : $fileName;
		$fileModTime= filemtime($pathFile);

		if( is_null($mimeType) ) {
			$mimeType = TodoyuFileManager::getMimeType($pathFile);
		}

			// Clear output buffer to prevent invalid file content
		ob_clean();
			// Send headers, file data
		TodoyuHeader::sendDownloadHeaders($mimeType, $fileName, $fileSize, $fileModTime, $asAttachment);

		return readfile($pathFile) !== false;
	}



	/**
	 * Check whether a file can get sent to the browser
	 *
	 * @param	String			$pathFile
	 * @return	Boolean|String	True or an error message
	 */
	public static function canSendFile($pathFile) {
		$pathFile	= self::pathAbsolute($pathFile);

		if( !is_file($pathFile) ) {
			return Todoyu::Label('core.file.error.notFound');
		}

		if( !is_readable($pathFile) ) {
			return Todoyu::Label('core.file.error.notReadable');
		}

		if( !self::isFileInAllowedDownloadPath($pathFile) ) {
			return Todoyu::Label('core.file.error.notInDownloadPath');
		}

		return true;
	}



	/**
	 * Append string to filename, preserving path delimiter and file extension
	 *
	 * @param	String	$filename
	 * @param	String	$append
	 * @return	String
	 */
	public static function appendToFilename($filename, $append) {
		$pathInfo	= pathinfo($filename);
		$dir		= ( $pathInfo['dirname'] == '.' ) ? '' : $pathInfo['dirname'] . DIR_SEP;

		return $dir . $pathInfo['filename'] . $append . '.' . $pathInfo['extension'];
	}



	/**
	 * Get folder contents
	 *
	 * @param	String		$pathFolder
	 * @param	Boolean		$showHidden
	 * @param	Boolean		$getFileStats		Get also statistics of the files?
	 * @return	Array
	 */
	public static function getFolderContents($pathFolder, $showHidden = false, $getFileStats = false) {
		$pathFolder	= self::pathAbsolute($pathFolder);
		$items		= array();

		if( is_dir($pathFolder) ) {
			$elements		= scandir($pathFolder);

			foreach($elements as $element) {
				if( $element === '.' || $element === '..' ) {
						// Ignore parent and self references
					continue;
				}
					// Also get hidden folders (starting with a dot)?
				if( substr($element, 0, 1) !== '.' || $showHidden ) {
					if( $getFileStats ) {
							// Get file statistics
						$items[$element] = stat($pathFolder . DIR_SEP . $element);
					} else {
							// Get only file name
						$items[] = $element;
					}
				}
			}
		}

		return $items;
	}



	/**
	 * Get listing of files inside given folder
	 *
	 * @param	String		$pathFolder
	 * @param	Boolean		$showHidden
	 * @param	Array		$filters			strings needed to be contained in files looking for
	 * @return	Array
	 */
	public static function getFilesInFolder($pathFolder, $showHidden = false, array $filters = array()) {
		$pathFolder	= self::pathAbsolute($pathFolder);
		$elements	= self::getFolderContents($pathFolder, $showHidden);
		$files		= array();
		$hasFilters	= sizeof($filters) > 0;

		foreach($elements as $element) {
			if( is_file($pathFolder . DIR_SEP . $element) ) {
					// No filters defined: add file to results array
				if( ! $hasFilters ) {
					$files[] = $element;
				} else {
						// Check string filters
					foreach($filters as $filterString) {
						if( strpos($element, $filterString) !== false ) {
							$files[] = $element;
							break;
						}
					}
				}
			}
		}

		return $files;
	}



	/**
	 * Get sub folders in given path
	 *
	 * @param	String	$pathToFolder
	 * @param	Boolean	$showHidden
	 * @return	Array
	 */
	public static function getFoldersInFolder($pathToFolder, $showHidden = false) {
		$pathToFolder	= self::pathAbsolute($pathToFolder);
		$elements		= self::getFolderContents($pathToFolder, $showHidden);
		$folders		= array();

		foreach($elements as $element) {
			if( is_dir($pathToFolder . DIR_SEP . $element) ) {
				$folders[] = $element;
			}
		}

		return $folders;
	}



	/**
	 * Get file name (w/o extension)
	 *
	 * @param	String	$filename
	 * @return	String					filename
	 */
	public static function getFileName($filename) {
		return pathinfo($filename, PATHINFO_FILENAME);
	}



	/**
	 * Get file extension
	 *
	 * @param	String	$filename
	 * @return	String					file extension (without dot)
	 */
	public static function getFileExtension($filename) {
		return pathinfo($filename, PATHINFO_EXTENSION);
	}



	/**
	 * Download a file from an external server and return the content
	 * Use the options parameters to specify special options
	 *
	 * @todo	Implement other transfer methods. See t3lib_div::getURL() function
	 * @param	String		$url		URL to resource. Should be as complete as possible. Ex: http://www.todoyu.com/archive.zip
	 * @param	Array		$options	Several options
	 * @return	String|Array|Boolean	String if download succeeded, FALSE if download failed, Array for special options config (ex: headers)
	 */
	public static function downloadFile($url, array $options = array()) {
		if( function_exists('curl_init') ) {
			$content	= self::downloadFile_CURL($url, $options);
		} elseif( function_exists('fsockopen') ) {
			$content	= self::downloadFile_SOCKET($url, $options);
		} else {
			$content	= @file_get_contents($url);
		}

		return $content;
	}



	/**
	 * Download a file from given URL via CURL
	 *
	 * @param	String	$url
	 * @param	Array	$options
	 * @return	Array|Boolean|mixed$
	 */
	private static function downloadFile_CURL($url, array $options = array()) {
		$ch	= curl_init();

		if( !$ch ) {
			TodoyuLogger::logFatal('Failed to init curl');
			return false;
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

			// Only set curl options if safe mode is not enabled
		if( ((int) (ini_get('safe_mode'))) !== 1 ) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}

		if( $options['fullRequest'] || $options['onlyHeaders'] ) {
			curl_setopt($ch, CURLOPT_HEADER, true);
		}

		if( !empty($options['requestHeaders'])) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $options['requestHeaders']);
		}

		if( !empty($options['curl']) ) {
			curl_setopt_array($ch, $options['curl']);
		}

		$content = curl_exec($ch);

		curl_close($ch);

		if( $options['onlyHeaders'] ) {
			$content	= TodoyuString::extractHttpHeaders($content);
		}

		return $content;
	}




	/**
	 * Download a file via socket connection
	 *
	 * @param	String	$url
	 * @param	Array	$options
	 * @return	Array|Boolean|String
	 */
	private static function downloadFile_SOCKET($url, array $options = array()) {
		$parsedURL	= parse_url($url);
		$port		= (int) $parsedURL['port'];
		$path		= trim($parsedURL['path']);
		$query		= trim($parsedURL['query']);

		if( $parsedURL['scheme'] === 'https' ) {
			$scheme	= 'ssl://';
		} else {
			$scheme	= 'tcp://';
		}

		if( $port === 0 ) {
			if( $parsedURL['scheme'] === 'http' ) {
				$port	= 80;
			} elseif( $parsedURL['scheme'] === 'https' ) {
				$port	= 443;
			}
		}

		if( $path === '' ) {
			$path = '/';
		}

		if( $query !== '' ) {
			$query = '?' . $query;
		}

		$fp = @fsockopen($scheme . $parsedURL['host'], $port, $errno, $errstr, 2.0);

			// Connection failed
		if( !$fp ) {
			TodoyuLogger::logError('File download with socket failed. URL=' . $url . ' - ' . $errno . ' - ' . $errstr);
			return false;
		}

		$requestHeaders	= array();

		$requestHeaders[]	= 'GET ' . $path . $query . ' HTTP/1.0';
		$requestHeaders[]	= 'Host: ' . $parsedURL['host'];
		$requestHeaders[]	= 'Connection: close';

		if( sizeof($options['requestHeaders']) > 0 ) {
			$requestHeaders = array_merge($requestHeaders, $options['requestHeaders']);
		}

		$requestHead	= implode($requestHeaders, "\r\n") . "\r\n\r\n";

		fputs($fp, $requestHead);

		$content	= '';

		while( ! feof($fp) ) {
			$line = fgets($fp, 2048);

			$content .= $line;
		}

		fclose($fp);

			// Get response headers
		$httpHeaders	= TodoyuString::extractHttpHeaders($content);

			// If a redirect header was sent, download redirection URL
		if( $httpHeaders['statusCode'] >= 300 && $httpHeaders['statusCode'] < 400 ) {
			if( isset($httpHeaders['Location']) ) {
					// Download from redirection URL
				return self::downloadFile_SOCKET($httpHeaders['Location'], $options);
			}
		}

		if( $options['fullResponse'] ) {
			// Do nothing
		} else {
			if( $options['onlyHeaders'] ) {
				$content		= $httpHeaders;
			} else {
				$requestParts	= explode("\r\n\r\n", $content, 2);
				$content		= $requestParts[1];
			}
		}

		return $content;
	}



	/**
	 * Save a local copy of a file from an external server
	 *
	 * @param	String			$url
	 * @param	String|Boolean	$targetPath			Path to locale file or FALSE for temp file
	 * @param	Array			$options
	 * @return	String|Boolean	Path to local file or FALSE
	 */
	public static function saveLocalCopy($url, $targetPath = false, array $options = array()) {
		$content	= self::downloadFile($url, $options);

		if( is_string($content) ) {
			if( !$targetPath || $targetPath === '' ) {
				$targetPath	= self::pathAbsolute(PATH_CACHE . '/temp/' . md5($url . NOW));
			} else {
				$targetPath	= self::pathAbsolute($targetPath);
			}

			self::saveFileContent($targetPath, $content);

			return $targetPath;
		} else {
			TodoyuLogger::logError('saveLocalCopy of ' . $url . ' failed');
			return false;
		}
	}



	/**
	 * Copy a folder recursive to another folder
	 * If move is set, all files are moved instead of copied
	 *
	 * @param	String		$sourceFolder
	 * @param	String		$destinationFolder
	 * @param	Array		$exclude
	 * @param	Boolean		$move					Move instead copy
	 * @param	Boolean		$hiddenFiles
	 */
	public static function copyRecursive($sourceFolder, $destinationFolder, array $exclude = array(), $move = false, $hiddenFiles = false) {
		$sourceFolder		= self::pathAbsolute($sourceFolder);
		$destinationFolder	= self::pathAbsolute($destinationFolder);
		$removeFolders		= array();

		foreach($exclude as $index => $item) {
			$exclude[$index] = TodoyuFileManager::pathAbsolute($item);
		}

		self::makeDirDeep($destinationFolder);

		$folderElements	= self::getFolderContents($sourceFolder, $hiddenFiles);

		foreach($folderElements as $element) {
			$pathElement	= self::pathAbsolute($sourceFolder . '/' . $element);
			$pathDestElement= self::pathAbsolute($destinationFolder . '/' . $element);

				// Skip excluded files
			if( in_array($pathElement, $exclude) ) {
				continue;
			}

			if( is_dir($pathElement) ) {
					// Folder
				if( ! is_dir($pathDestElement) ) {
					self::makeDirDeep($pathDestElement);
				}
				self::copyRecursive($pathElement, $pathDestElement, $exclude, $move, $hiddenFiles);
				if( $move ) {
					$removeFolders[] = $pathElement;
				}
			} else {
					// File
				if( is_file($pathDestElement) ) {
					self::deleteFile($pathDestElement);
				}
				if( $move ) {
					self::rename($pathElement, $pathDestElement);
				} else {
					self::copy($pathElement, $pathDestElement);
				}
			}
		}

		foreach($removeFolders as $folder) {
			rmdir($folder);
		}
	}



	/**
	 * Move folders and files recursive
	 *
	 * @param	String		$sourceFolder
	 * @param	String		$destinationFolder
	 * @param	Boolean		$hiddenFiles
	 */
	public static function moveRecursive($sourceFolder, $destinationFolder, $hiddenFiles = false) {
		self::copyRecursive($sourceFolder, $destinationFolder, true, $hiddenFiles);
	}



	/**
	 * Copy file. Wrapper with path fixes
	 *
	 * @param	String	$sourceFile
	 * @param	String	$targetFile
	 * @return	Boolean
	 */
	public static function copy($sourceFile, $targetFile) {
		$sourceFile	= self::pathAbsolute($sourceFile);
		$targetFile	= self::pathAbsolute($targetFile);

		return copy($sourceFile, $targetFile);
	}



	/**
	 * Rename file/folder. Wrapper with path fixes
	 *
	 * @param	String	$sourceFile
	 * @param	String	$targetFile
	 * @return	Boolean
	 */
	public static function rename($sourceFile, $targetFile) {
		$sourceFile	= self::pathAbsolute($sourceFile);
		$targetFile	= self::pathAbsolute($targetFile);

		return rename($sourceFile, $targetFile);
	}



	/**
	 * Get list of version files from a directory. Limit by min and max version and extension
	 *
	 * @param	String			$pathToFolder
	 * @param	String|Boolean	$extension
	 * @param	String			$minVersion			Min version will NOT be included
	 * @param	String			$maxVersion			Max version will be included
	 * @return	Array
	 */
	public static function getVersionFiles($pathToFolder, $extension = false, $minVersion = '0.0.0', $maxVersion = '999.999.999') {
		$pathToFolder	= TodoyuFileManager::pathAbsolute($pathToFolder);
		$files			= TodoyuFileManager::getFilesInFolder($pathToFolder);
		$updateFiles	= array();
		$version2File	= array();

			// Map version numbers to real file names (without extension)
		foreach($files as $filename) {
			$version2File[pathinfo($filename, PATHINFO_FILENAME)] = $filename;
		}

			// Get list of versions
		$versions		= array_keys($version2File);

			// Sort the versions
		usort($versions, 'version_compare');

			// Check all files if they are necessary for the update
		foreach($versions as $version) {
			$filename	= $version2File[$version];
			$info		= pathinfo($filename);

				// Only use file with the requested extension
			if( $extension !== false && $info['extension'] !== $extension ) {
				continue;
			}

				// Get all version which are in the required version range
			if( version_compare($version, $minVersion) === 1 && version_compare($version, $maxVersion) !== 1 ) {
					// Add version file to list
				$updateFiles[] = $version2File[$version];
			}
		}

		return $updateFiles;
	}



	/**
	 * Get a recursive file list of all elements
	 *
	 * @param	String		$pathFolder
	 * @return	Array
	 */
	public static function getRecursiveFileList($pathFolder) {
		$pathFolder	= self::pathAbsolute($pathFolder);
		$files		= self::getFilesInFolder($pathFolder);
		$folders	= self::getFoldersInFolder($pathFolder);
		$elements	= array();

			// Add files
		foreach($files as $filename) {
			$elements[] = self::pathAbsolute($pathFolder . '/' . $filename);
		}

		foreach($folders as $folder) {
			$pathSubfolder	= self::pathAbsolute($pathFolder . '/' . $folder);
			$elements[]		= $pathSubfolder;
			$subElements	= self::getRecursiveFileList($pathSubfolder);

			$elements		= array_merge($elements, $subElements);
		}

		return $elements;
	}



	/**
	 * Detect mime type of a file
	 * If mime_content_type() is not available, try to guess it by file extension
	 *
	 * @param	String			$pathFile
	 * @param	String|null		$fileName
	 * @return	String			Empty if nothing found
	 */
	public static function getMimeType($pathFile, $fileName = null) {
		$pathFile	= self::pathAbsolute($pathFile);

		if( function_exists('mime_content_type') ) {
			return mime_content_type($pathFile);
		} elseif( !is_null($fileName) ) {
			$extension = pathinfo($fileName, PATHINFO_EXTENSION);

			return self::getMimeTypeByFileExtension($extension);
		} elseif( strstr($pathFile, '.') !== false ) {
			$extension = pathinfo($pathFile, PATHINFO_EXTENSION);

			return self::getMimeTypeByFileExtension($extension);
		} else {
			return '';
		}
	}



	/**
	 * Try to guess the mime type by extension
	 *
	 * @param	String		$extension
	 * @return	String		Mime type
	 */
	private static function getMimeTypeByFileExtension($extension) {
		require_once( PATH_CONFIG . '/mime.php' );

		$extension	= trim(strtolower(str_replace('.', '', $extension)));

		if( isset(Todoyu::$CONFIG['mimeTypes'][$extension]) ) {
			return Todoyu::$CONFIG['mimeTypes'][$extension];
		} else {
			return '';
		}
	}



	/**
	 * Include a file
	 * Wrapper for include()
	 *
	 * @param	String		$pathFile
	 * @param	Boolean		$includeOnce	use include_once() instead of include()
	 * @param	Boolean		$silent			Ignore missing file
	 * @return	Boolean
	 */
	public static function includeFile($pathFile, $includeOnce = false, $silent = false) {
		$pathFile	= self::pathAbsolute($pathFile);

		if( is_file($pathFile) ) {
			$includeOnce ? include_once($pathFile) : include($pathFile);
			return true;
		} else {
			if( !$silent ) {
				TodoyuLogger::logError('Include file <' . $pathFile . '> not found');
			}
			return false;
		}
	}

}

?>