<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

// Joomla eigenes JQuery nutzen
// $document = JFactory::getDocument();
// JHtml::_('jquery.framework');

// Vom CLM bereitgestelltes JQuery nutzen
// ACHTUNG : Es wird von Störungen (nicht CLM) bei gleichzeitig aktiviertem Joomla JQuery berichtet
clm_core::$cms->addScript(clm_core::$url."includes/jquery-3.7.1.min.js");

// Select2 Jquery Bibliothek einbinden
clm_core::$cms->addScript(clm_core::$url."js/select2.min.js");
clm_core::$load->load_css("suche_liste");
clm_core::$cms->addScript(clm_core::$url."js/suche_liste.js");
?>
