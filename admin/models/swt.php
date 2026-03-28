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

//JLoader::registerAlias('JModelLegacy', '\\Joomla\\CMS\\MVC\\Model\\BaseDatabaseModel', '6.0');
//use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;

class CLMModelSWT extends JModelLegacy {

	var $_swtFiles;

	function __construct(){
		parent::__construct();
	}
	
	function getSwtFiles() { 
		jimport( 'joomla.filesystem.folder' );
		
		$filesDir = 'components'.DS."com_clm".DS.'swt';
		$this->swtFiles = Folder::files( $filesDir, '.SWT$|.swt$', false, true );
		
		return $this->swtFiles;
	}
	
	function upload() {
		jimport( 'joomla.filesystem.file' );
		
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('swt_datei', null);
		
		//Dateiname wird bereinigt
		$filename = File::makeSafe($file['name']);
		$_POST['filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		
		//Datei wird auf dem Server gespeichert (abfrage auf .swt Endung)
//		if ( strtolower(File::getExt($filename) ) == 'swt') {
		if ( strtolower(substr(strrchr(basename($filename), '.'),1) ) == 'swt') {
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
		$filename = clm_core::$load->request_string('swt_file', '');
		if ($filename == '') {
			$msg = Text::_( 'SWT_FILE_ERROR' ); 
			return $msg;
		}
		//SWT-Verzeichnis
		$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;
		
		//Datei löschen
		if ( File::delete($path.$filename) ) {
			$msg = Text::_( 'SWT_DELETE_SUCCESS' ); 
		} else {
			$msg = Text::_( 'SWT_DELETE_ERROR' ); 
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
	

}

?>