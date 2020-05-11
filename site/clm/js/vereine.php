<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->vereine;
clm_core::$cms->addScriptDeclaration('var clm_vereine_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_vereine_name = "'.html_entity_decode($lang->name_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_vereine_zps = "'.html_entity_decode($lang->zps_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_vereine_sid = "'.html_entity_decode($lang->sid_auswaehlen).'";');
clm_core::$cms->addScript(clm_core::$url."js/vereine.js");
?>
