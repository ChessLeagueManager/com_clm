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
	jimport( 'joomla.filesystem.file' );

	$mainframe	= JFactory::getApplication();

// Include the AddressHandler class
require_once JPATH_COMPONENT_ADMINISTRATOR. '/helpers/addresshandler.php';

// Variablen holen
$sid 		= clm_core::$load->request_int('saison');
$zps 		= clm_core::$load->request_string('zps');
$name 		= clm_core::$load->request_string('name');
$new 		= clm_core::$load->request_string('new');
$config = clm_core::$db->config();

// Variablen initialisieren
$clmuser 	= $this->clmuser;
$row 		= $this->row;

$user =JFactory::getUser();
	$link = JURI::base() .'index.php?option=com_clm&view=verein&saison='. $sid .'&zps='. $zps;
// Login Status prüfen
if (!$user->get('id')) {
	$msg = JText::_( 'CLUB_DATA_SENT_LOGIN' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
 			}
if ($clmuser[0]->published < 1) { 
	$msg = JText::_( 'CLUB_DATA_SENT_ACCOUNT' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
			}
if ($clmuser[0]->zps <> $zps  OR $clmuser[0]->usertype == "spl") {
	$msg = JText::_( 'CLUB_DATA_SENT_FALSE' );
	$mainframe->enqueueMessage( $msg );
	$mainframe->redirect( $link );
 			}
// Login Status prüfen
if ($user->get('id') > 0 AND  $clmuser[0]->published > 0 AND $clmuser[0]->zps == $zps OR $clmuser[0]->usertype == "admin")
	{
// Prüfen ob Datensatz schon vorhanden ist
$db	=JFactory::getDBO();

	$msg = '';
	if ($msg == '') {
		//Datei wird hochgeladen
		$file = clm_core::$load->request_file('logo_file', null);
		//Dateiname wird bereinigt
		$filename = JFile::makeSafe($file['name']);
		$_POST['filename'] = $filename;
		//Temporärer Name und Ziel werden festgesetzt
		$src = $file['tmp_name'];
		$dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;
		//Datei wird auf dem Server gespeichert (Abfrage auf .png Endung)
		$ext = strtolower(JFile::getExt($filename));
		if ( $ext != 'png') {
			$msg = JText::_( 'Falscher Dateityp - ist nicht .png' );
		}
	}
	if ($msg == '') {
		// eigentliches Hochgeladen
		if ( !JFile::upload($src, $dest) ) {
			$msg = JText::_( 'Upload-Fehler' );
		}
	} 
	
	if ($msg == '') {
		// eigentliches Hochgeladen
		$size = getimagesize( $dest);
		if ( $size[0] > 256 ) {
			$msg = JText::_( 'Logo ist zu breit, max. 256 x 256 px' );
		} elseif ( $size[1] > 256 ) {
			$msg = JText::_( 'Logo ist zu hoch, max. 256 x 256 px' );
		}
	} 

	if ($msg == '') {
		// Get the image and convert into string
		$img = file_get_contents($dest);

		// Encode the image string data into base64
		$ndata = base64_encode($img);
		$ndata = "data:image/".$ext.";base64,".$ndata;
		if ( strlen($ndata) > 65535 ) { // max. Länge für ein DB-Feld vom Typ TEXT
			$msg = JText::_( 'Bilddatei zu groß (base46-Code > 65535 Byte)' );
		}
	}
	
	// nach encode kann Datei gelöscht werden
	unlink($dest);
	
	if ($msg == '') {
		// Save the image to the database
		$query = " INSERT INTO #__clm_images "
			." (typ, key1, key2, image, width, height ) "
			." VALUES ('club', '".$zps."', '".$sid."', '".$ndata."',$size[0], $size[1])"
			." ON DUPLICATE KEY UPDATE image = '".$ndata."', width = $size[0], height = $size[1]";
		if (!clm_core::$db->query($query)) $msg = 'Speichern in die DB nicht möglich';
		$anz = clm_core::$db->affected_rows();
	}
	
	if ($msg == '') {
		$msg = 'Logo wurde hochgeladen';
		$mtype = 'message';
		// Log schreiben
		$aktion = "Logo hochgeladen";
		$callid = uniqid ( "", false );
		$userid = clm_core::$access->getId ();	
		$parray = array('sid' => $sid, 'zps' => $zps, 'filename' => $filename);
		$query	= "INSERT INTO #__clm_logging "
			." ( `callid`, `userid`, `timestamp` , `type` ,`name`, `content`) "
			." VALUES ('".$callid."','".$userid."',".time().",5,'".$aktion."','".json_encode($parray)."') "
			;
		clm_core::$db->query($query);
	} else {
		$mtype = 'warning';
	}
	
	$mainframe->enqueueMessage( $msg, $mtype );
	$mainframe->redirect( $link );
}
?>


