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

use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;

class CLMModelTRFTurnier extends JModelLegacy {

	var $_saisons;
	var $_turniere;
	var $_trfFiles;
	
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
		if (empty( $this->_turniere )) { 
			$query =  ' SELECT id, name, bem_int
						FROM 
							#__clm_turniere 
						WHERE 
							sid = '.$this->getState( 'filter_saison' ).'';
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

	function getTrfFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->trfFiles = Folder::files( $filesDir, '.TRF$|.trf$|.TRFX$|.trfx$', false, true );
		
		return $this->trfFiles;
	}
	
	function trf_upload() {
		jimport( 'joomla.filesystem.file' );

		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('trf_datei', null);

		//Dateiname wird bereinigt
		$trf_file = File::makeSafe($file['name']);
		$_POST['trf_file'] = $trf_file;

		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $trf_file;

		//Datei wird auf dem Server gespeichert (abfrage auf .tunx oder turx Endung)
//		if ( strtolower(File::getExt($trf_file) ) == 'trf' OR strtolower(File::getExt($trf_file) ) == 'trfx' ) {
		if (strtolower(substr(strrchr(basename($trf_file), '.'),1) ) == 'trf' OR strtolower(substr(strrchr(basename($trf_file), '.'),1) ) == 'trfx') {
			if ( File::upload($src, $dest) ) {
				$msg = Text::_( 'SWT_UPLOAD_SUCCESS' ); 
			} else {
				$msg = Text::_( 'SWT_UPLOAD_ERROR' );
			}
		} else {
			$msg = Text::_( 'SWT_UPLOAD_ERROR_WRONG_EXT' ).'*'.$trf_file.'*';
		}

		return $msg;
	}

	function trf_delete() {
		jimport( 'joomla.filesystem.file' );
		
		//Name der zu löschenden Datei wird geladen
		$trf_file = clm_core::$load->request_string('trf_file', '');
		if ($trf_file == '') {
			$msg = Text::_( 'SWT_FILE_ERROR' ); 
			return $msg;
		}		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( File::delete($path.$trf_file) ) {
			$msg = Text::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = Text::_( 'SWT_DELETE_ERROR' ); 
		}
		return $msg;
	}
	
	function trf_import() {

		//Name der zu Datei wird geladen
		$trf_file = clm_core::$load->request_string('trf_file', '');
		
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DS . "swt" . DS;

		if($trf_file!=""&&file_exists($path.$filename)) {
			return 1;
		} else {
			return -1;
		}
	}

}

?>