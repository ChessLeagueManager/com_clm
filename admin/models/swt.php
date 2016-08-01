<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelSWT extends JModelLegacy {

	var $_swtFiles;

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
		$file = JRequest::getVar('datei', null, 'files', 'array');
		
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		JRequest::setVar('filename',$filename);	
		//Tempor�rer Name und Ziel werden festgesetzt
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
		
		//Name der zu l�schenden Datei wird geladen
		$filename = JRequest::getVar('swt_file', '', 'post', 'string');
		
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
		//Name der zu l�schenden Datei wird geladen
		$filename = JRequest::getVar('swt_file', '', 'post', 'string');
		
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