<?php
defined('clm') or die('Restricted access');
// **** Datenbank Verbindung **** //
//
// Eigene Verbindungsdaten:
// $database = array('host','db-name','name','pas','db-prefix');
//
// Joomla Verbindungsdaten übernehmen
// $database = clm_class_cms::joomla_config($joomla_path);

$database = clm_class_cms::joomla_config();
?>
