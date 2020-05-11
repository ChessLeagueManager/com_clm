<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->ranglisten;
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_verein = "'.html_entity_decode($lang->verein_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_saison = "'.html_entity_decode($lang->saison_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_gruppe = "'.html_entity_decode($lang->gruppe_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_check = "'.html_entity_decode($lang->team_check).'";');
clm_core::$cms->addScriptDeclaration('var clm_ranglisten_usepkz = 1 ;');
clm_core::$cms->addScript(clm_core::$url."js/ranglisten.js");
?>
