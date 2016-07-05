<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access'); 

//$liga		= $this->liga;
$rnd_dg = CLMModelAktuell_Runde::Runden();
$runde	= $rnd_dg[0];
$dg	= $rnd_dg[1];
$itemid		= JRequest::getInt('Itemid','1');
$sid		= JRequest::getInt( 'saison','1');
$lid		= JRequest::getInt('liga','1');
 
$mainframe	= JFactory::getApplication();
$link = JURI::base() . 'index.php/component/clm/?view=runde&saison='.$sid.'&liga='.$lid.'&runde='.$runde.'&dg='.$dg.'&Itemid='.$itemid;
$mainframe->redirect( $link, '' );
