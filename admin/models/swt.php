<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSWT extends JModelLegacy {

	var $_swtFiles;
	var $_swmFiles;
	var $_pgnFiles;
	var $_trfFiles;

	function __construct(){
		parent::__construct();
	}
	
	function getSwtFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->swtFiles = JFolder::files( $filesDir, '.SWT$|.swt$', false, true );
		
		return $this->swtFiles;
	}
	
	function upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('swt_datei', null);
		
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		$_POST['filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		
		//Datei wird auf dem Server gespeichert (abfrage auf .swt Endung)
		if ( strtolower(JFile::getExt($filename) ) == 'swt') {
			if ( JFile::upload($src, $dest) ) {
				$msg = JText::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = JText::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = JText::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' );
		}
		return $msg;
	}
	
	function delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('swt_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei l�schen
		if ( JFile::delete($path.$filename) ) {
			$msg = JText::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = JText::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function import() {
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('swt_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($filename!=""&&file_exists($path.$filename)) {
			return CLMSWT::readInt($path.$filename,606,1);
		} else {
			return -1;
		}
	}
	
	function getPgnFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->pgnFiles = JFolder::files( $filesDir, '.PGN$|.pgn$', false, true );
		
		return $this->pgnFiles;
	}
	
	function pgn_upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('pgn_datei', null);
		
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		$_POST['pgn_filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		
		//Datei wird auf dem Server gespeichert (abfrage auf .pgn Endung)
		if ( strtolower(JFile::getExt($filename) ) == 'pgn') {
			if ( JFile::upload($src, $dest) ) {
				$msg = JText::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = JText::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = JText::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' );
		}
		return $msg;
	}
	
	function pgn_delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('pgn_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( JFile::delete($path.$filename) ) {
			$msg = JText::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = JText::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function pgn_import() {
		//Name der zu Datei wird geladen
		$filename = clm_core::$load->request_string('pgn_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($filename!=""&&file_exists($path.$filename)) {
			return CLMSWT::readInt($path.$filename,606,1);
		} else {
			return -1;
		}
	}

	function getSwmFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->swmFiles = JFolder::files( $filesDir, '.TUNx$|.tunx$|.TUNX$|.TURx$|.turx$|.TURX$|.TUMx$|.tumx$|.TUMX$|.TUTx$|.tutx$|.TUTX$', false, true );
		
		return $this->swmFiles;
	}
	
	function swm_upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('swm_datei', null);
		
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		$_POST['swm_filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		//Datei wird auf dem Server gespeichert (abfrage auf .tunx oder turx Endung)
		if ( strtolower(JFile::getExt($filename) ) == 'tunx' OR strtolower(JFile::getExt($filename) ) == 'turx' OR
			 strtolower(JFile::getExt($filename) ) == 'tumx' OR strtolower(JFile::getExt($filename) ) == 'tutx') {
			if ( JFile::upload($src, $dest) ) {
				$msg = JText::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = JText::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = JText::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' );
		}
		return $msg;
	}
	
	function swm_delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('swm_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( JFile::delete($path.$filename) ) {
			$msg = JText::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = JText::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function swm_import() {
		//Name der zu Datei wird geladen
		$filename = clm_core::$load->request_string('swm_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($filename!=""&&file_exists($path.$filename)) {
			return 1;
		} else {
			return -1;
		}
	}

	function getTrfFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->trfFiles = JFolder::files( $filesDir, '.TRF$|.trf$|.TRFX$|.trfx$', false, true );
		
		return $this->trfFiles;
	}
	
	function trf_upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('trf_datei', null);
		
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		$_POST['trf_filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		//Datei wird auf dem Server gespeichert (abfrage auf .tunx oder turx Endung)
		if ( strtolower(JFile::getExt($filename) ) == 'trf' OR strtolower(JFile::getExt($filename) ) == 'trfx' ) {
			if ( JFile::upload($src, $dest) ) {
				$msg = JText::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = JText::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = JText::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' ).'*'.$filename.'*';
		}
		return $msg;
	}
	
	function trf_delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$filename = clm_core::$load->request_string('trf_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( JFile::delete($path.$filename) ) {
			$msg = JText::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = JText::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function trf_import() {
		//Name der zu Datei wird geladen
		$filename = clm_core::$load->request_string('trf_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($filename!=""&&file_exists($path.$filename)) {
			return 1;
		} else {
			return -1;
		}
	}

	function arena_import() {
		//Name der zu Datei wird geladen
		$arena_code = clm_core::$load->request_string('arena_code', '');
		
		return $arena_code;
	}

}

?>