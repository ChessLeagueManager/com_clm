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

defined('clm') or die('Restricted access');

$mainframe = JFactory::getApplication();

// Variablen holen
$sid		= clm_core::$load->request_int('saison');
$zps 		= clm_core::$load->request_string('zps');
$mgl		= clm_core::$load->request_int('mglnr');

$clmuser 	= $this->clmuser;
$spieler	= $this->spieler;
$verein		= $this->verein;

$user 		= JFactory::getUser();
$link = JURI::base() . 'index.php?option=com_clm&view=mitglieder_details&saison='. $sid .'&zps='. $zps .'&mglnr='. $mgl;

if ($clmuser[0]->zps <> $zps) {
    $msg = JText::_('Sie sind nicht berechtigt, Aenderungen vorzunehmen.');
    $mainframe->enqueueMessage($msg);
    $mainframe->redirect($link);
}

// Login Status prüfen
if ($user->get('id') > 0 and  $clmuser[0]->published > 0 and $clmuser[0]->zps == $zps) {

    // Prüfen ob Datensatz schon vorhanden ist
    $db	= JFactory::getDBO();

    // Variablen holen
    $sid		= clm_core::$load->request_int('saison');
    $name 		= clm_core::$load->request_string('name');
    $mglnr		= clm_core::$load->request_int('mglnr');
    $dwz 		= clm_core::$load->request_int('dwz');
    $dwz_index 	= clm_core::$load->request_int('dwz_index');
    $geschlecht	= clm_core::$load->request_string('geschlecht');
    $geburtsjahr = clm_core::$load->request_int('geburtsjahr');
    $zps		= clm_core::$load->request_string('zps');

    if ($new < 1) {
        // Datensatz updaten
        $query	= "UPDATE #__clm_dwz_spieler "
            ." SET Spielername = '$name' "
            ." , Mgl_Nr = '$mglnr' "
            ." , DWZ = '$dwz' "
            ." , DWZ_Index = '$dwz_index' "
            ." , Geschlecht = '$geschlecht' "
            ." , Geburtsjahr = '$geburtsjahr' "
            ." WHERE ZPS = '$zps' "
            ." AND sid = '$sid'"
            ." AND Mgl_Nr = '$mglnr'"
        ;

        $db->setQuery($query);
        clm_core::$db->query($query);

    }
    // Neuer Spieler
    else {
        $query	= "INSERT INTO #__clm_dwz_spieler"
            ." ( `sid`,`ZPS`, `Mgl_Nr`, `Status`, `Spielername`, `Geschlecht`, `Geburtsjahr`, `DWZ`, `DWZ_Index`) "
            ." VALUES ('$sid', '$zps','$mglnr','N','$name','$geschlecht', '$geburtsjahr','$dwz','$dwz_index')"
        ;
        $db->setQuery($query);
        clm_core::$db->query($query);
    }

    // Log
    $date = JFactory::getDate();
    $now = $date->toSQL();
    $user 		= JFactory::getUser();
    $jid_aktion =  ($user->get('id'));
    $aktion = "Spielerdaten FE";


    $msg = JText::_('Spielerdaten geändert');
    $mainframe->enqueueMessage($msg);
    $linkback = JURI::base() . 'index.php?option=com_clm&view=mitglieder&saison='. $sid .'&zps='. $zps;
    $mainframe->redirect($linkback);

}
