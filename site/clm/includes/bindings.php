<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2018 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
// Dem direkten Aufruf die GET/POST Parameter Namen zuweisen
// Diese werden in der angegebenen durchgereicht POST wird bevorzugt
$bindings["view_report"]=array("liga","runde","dg","paar");
$bindings["view_tournament_group"]=array("liga");
$bindings["view_logging_new"]=array("ids");
$bindings["view_schedule"]=array("season","club");
$bindings["view_schedule_pdf"]=array("season","club");
$bindings["view_schedule_xls"]=array("season","club");
$bindings["view_paarungsliste_xls"]=array("liga");
?>
