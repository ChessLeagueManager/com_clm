<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->gruppen;
clm_core::$cms->addScriptDeclaration('var clm_gruppen_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_gruppen_name = "'.html_entity_decode($lang->name_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_gruppen_date = "'.html_entity_decode($lang->date_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_gruppen_geschlecht = "'.html_entity_decode($lang->geschlecht_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_gruppen_sid = "'.html_entity_decode($lang->sid_auswaehlen).'";');
clm_core::$cms->addScript(clm_core::$url."js/gruppen.js");
?>
