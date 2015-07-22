<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );
$mainframe	= JFactory::getApplication();
// Variablen holen
$sid 	= JRequest::getInt('saison','1');
$lid 	= JRequest::getInt('liga');
$zps 	= JRequest::getVar('zps');
$gid 	= JRequest::getInt('gid');
$count 	= JRequest::getInt('count');

	$option = JRequest::getCmd( 'option' );
	$db	= JFactory::getDBO();

$user 		=JFactory::getUser();
$meldung 	= $user->get('id');

// Prüfen ob Datensatz schon vorhanden ist
	$query	= "SELECT id "
		." FROM #__clm_rangliste_id "
		." WHERE sid = $sid AND zps = '$zps' AND gid = $gid ";
	$db->setQuery( $query );
	$abgabe=$db->loadObjectList();
//if ($abgabe[0]->id != "") {
/* if (count($abgabe) > 0) {

	$link = 'index.php?option=com_clm&view=info';
	$msg = JText::_( '<h2>Diese Rangliste wurde bereits abgegeben ! </h2>Bitte schauen Sie in die entsprechende Mannschaftsübersicht' );
	$mainframe->redirect( $link, $msg);
 			}
*/
	// evtl. vorhandene Daten in der Tabelle löschen
	$query	=" DELETE FROM #__clm_rangliste_id "
		." WHERE gid = ".$gid
		." AND zps = '$zps'"
		;
	$db->setQuery($query);
	$db->query();

	$query	=" DELETE FROM #__clm_rangliste_spieler "
		." WHERE Gruppe = ".$gid
		." AND ZPS = '$zps'"
		;
	$db->setQuery($query);
	$db->query();

	$query	=" DELETE FROM #__clm_meldeliste_spieler "
		." WHERE status = ".$gid
		." AND ZPS = '$zps'"
		;
	$db->setQuery($query);
	$db->query();

	// Liganummer ermitteln
	$query	=" SELECT liga FROM #__clm_mannschaften "
		." WHERE zps = '$zps'"
		." GROUP BY man_nr ASC "
		;
	$db->setQuery($query);
	$lid_rang	= $db->loadObjectList();

	// Datum und Uhrzeit für Meldung
	$date =JFactory::getDate();
	$now = $date->toSQL();

	// Datensätze schreiben
	$liga_count	= 0;
	$liga		= $lid_rang[0]->liga;
	$change		= JRequest::getVar('MA0');

	for ($y=0; $y < $count; $y++) {
	$mgl	= JRequest::getVar('MGL'.$y);
	$pkz	= JRequest::getVar('PKZ'.$y);
	$mnr	= JRequest::getVar('MA'.$y);
	$rang	= JRequest::getVar('RA'.$y);

	if ($y !="0" AND $mnr > $change) {
		$liga_count++;
		$liga	= $lid_rang[$liga_count]->liga;
		 }
	$change	= $mnr;

	if ($mnr !=="99" AND $mnr !=="0" AND $mnr !=="") {
	$query = " INSERT INTO #__clm_rangliste_spieler "
		." (`Gruppe`, `ZPS`, `Mgl_Nr`, `PKZ`, `Rang`, `man_nr`, `sid`) "
		." VALUES ('$gid','$zps','$mgl','$pkz','$rang','$mnr','$sid') "
		;
	$db->setQuery($query);
	$db->query();

	$query = " INSERT INTO #__clm_meldeliste_spieler "
		." (`sid`, `lid`, `mnr`, `snr`, `mgl_nr`, `zps`,`status`) "
		." VALUES ('$sid','$liga','$mnr','$rang','$mgl','$zps','$gid') "
		;
	$db->setQuery($query);
	$db->query();
	}}

	$query = " INSERT INTO #__clm_rangliste_id "
		." (`gid`, `sid`, `zps`, `rang`, `published`) "
		." VALUES ('$gid','$sid','$zps','0','0') "
		;
	$db->setQuery($query);
	$db->query();

	$msg = JText::_( '<h2>Die Rangliste wurde gespeichert !</h2>' );
	$mainframe->redirect( 'index.php?option='. $option.'&view=info', $msg );
?>