<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\File;

class CLMModelSWMTurnier extends JModelLegacy {

	var $_saisons;
	var $_turniere;
	var $_swmFiles;
	
	function __construct(){
		parent::__construct();
		
		$filter_saison	= clm_core::$load->request_int( 'filter_saison' , $this->_getAktuelleSaison() );
		
		$this->setState( 'filter_saison' , $filter_saison );
	}
	
	function getSaisons() {
		if (empty( $this->_saisons )) { 
			$query =  ' SELECT id, name 
						FROM #__clm_saison
						WHERE id = '.$this->getState( 'filter_saison' ).'';
			$this->_saisons = $this->_getList( $query );
		} 
		return $this->_saisons;
	}
	
	function getTurniere() {
		$swm_file	= clm_core::$load->request_string( 'swm_file' );
//		if (strtolower(File::getExt($swm_file) ) == 'tumx' OR strtolower(File::getExt($swm_file) ) == 'tutx') {
		if (strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'tumx' OR strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'tutx') {
			$group = true; 
		} else { $group = false; }
		if (empty( $this->_turniere )) { 
			if ($group) {
				$query =  ' SELECT id, name, bem_int FROM #__clm_liga ' 
						.' WHERE sid = '.$this->getState( 'filter_saison' ).'';
			} else {	
				$query =  ' SELECT id, name, bem_int FROM #__clm_turniere ' 
						.' WHERE sid = '.$this->getState( 'filter_saison' ).'';
			}
			$this->_turniere = $this->_getList( $query );
		} 
		return $this->_turniere;
	}
	
	function _getAktuelleSaison() {
		if (empty( $this->_aktuelleSaison )) { 
		
			$query =  ' SELECT 
							id,
							name,
							published
						FROM 
							#__clm_saison 
						WHERE
							published = 1 AND archiv = 0';
			$var = $this->_getList( $query );
		} 
		return $var[0]->id;
	}

	function getSwmFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->swmFiles = Folder::files( $filesDir, '.TUNx$|.tunx$|.TUNX$|.TURx$|.turx$|.TURX$|.TUMx$|.tumx$|.TUMX$|.TUTx$|.tutx$|.TUTX$', false, true );
		
		return $this->swmFiles;
	}
	
	function swm_upload() {
		jimport( 'joomla.filesystem.file' );

		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('swm_datei', null);

		//Dateiname wird bereinigt
		$swm_file = File::makeSafe($file['name']);
		$_POST['swm_file'] = $swm_file;

		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $swm_file;

		//Datei wird auf dem Server gespeichert (abfrage auf .tunx oder turx Endung)
//		if ( strtolower(File::getExt($swm_file) ) == 'tunx' OR strtolower(File::getExt($swm_file) ) == 'turx' OR
//			 strtolower(File::getExt($swm_file) ) == 'tumx' OR strtolower(File::getExt($swm_file) ) == 'tutx') {
		if (strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'tunx' OR strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'turx' OR
			 strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'tumx' OR strtolower(substr(strrchr(basename($swm_file), '.'),1) ) == 'tutx') {
			if ( File::upload($src, $dest) ) {
				$msg = Text::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = Text::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = Text::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' ).'*'.$swm_file.'*';
		}

		return $msg;
	}

	function swm_delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$swm_file = clm_core::$load->request_string('swm', '');
		if ($swm_file == '') {
			$msg = Text::_( 'SWT_FILE_ERROR' ); 
			return $msg;
		}		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( File::delete($path.$swm_file) ) {
			$msg = Text::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = Text::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function swm_import() {

		//Name der zu Datei wird geladen
		$swm_file = clm_core::$load->request_string('swm_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($swm_file!=""&&file_exists($path.$swm_file)) {
			return 1;
		} else {
			return -1;
		}
	}

}

?>