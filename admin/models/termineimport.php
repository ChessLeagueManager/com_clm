<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

class CLMModeltermineimport extends JModelLegacy {

	var $_TermineFiles;

	function __construct(){
		parent::__construct();
	}
	
	function getTermineFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "pgn" . DIRECTORY_SEPARATOR;
		if (!file_exists($filesDir)) mkdir($filesDir);
		$this->TermineFiles = Folder::files( $filesDir, '.CSV$|.csv$|.ics$', false, true );
		
		return $this->TermineFiles;
	}
	
	function upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('termine_datei', null);
		
		//Dateiname wird bereinigt
		$filename = File::makeSafe($file['name']);
		$_POST['filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "pgn" . DIRECTORY_SEPARATOR . $filename;
		//Datei wird auf dem Server gespeichert (abfrage auf .csv Endung)
//		if ( strtolower(File::getExt($filename) ) == 'csv' OR strtolower(File::getExt($filename) ) == 'ics') {
		if (strtolower(substr(strrchr(basename($filename), '.'),1) ) == 'csv' OR strtolower(substr(strrchr(basename($filename), '.'),1) ) == 'isc') {
			if ( File::upload($src, $dest) ) {
				$msg = Text::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = Text::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = Text::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' );
		}
		return $msg;
	}
	
	function delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('termine_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "pgn" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( File::delete($path.$filename) ) {
			$msg = Text::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = Text::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function import1() {
		//Name der zu importierenden Datei wird geladen
		$filename = clm_core::$load->request_string('termine_file', '');
		//pgn-Verzeichnis
		$path = JPATH_COMPONENT . DS . "pgn" . DS;
		$result = clm_core::$api->db_term_import($path.$filename,false);		
		//pgn-Verzeichnis
		$path = JPATH_COMPONENT . DS . "pgn" . DS;

		if($filename!=""&&file_exists($path.$filename)) {
			return CLMSWT::readInt($path.$filename,606,1);
		} else {
			return -1;
		}
	}
	
}

?>