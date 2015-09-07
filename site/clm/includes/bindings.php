<?php
defined('clm') or die('Restricted access');
// Dem direkten Aufruf die GET/POST Parameter Namen zuweisen
// Diese werden in der angegebenen durchgereicht POST wird bevorzugt
$bindings["view_report"]=array("liga","runde","dg","paar");
$bindings["view_tournament_group"]=array("liga");
$bindings["view_logging_new"]=array("ids");
?>
