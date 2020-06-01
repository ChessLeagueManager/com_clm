<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
$number = clm_core::$load->request_int( 'clm_number');
$lang = clm_core::$lang->meldelisten;
clm_core::$cms->addScriptDeclaration('var clm_meldelisten_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_meldelisten_double = "'.html_entity_decode($lang->double_position).'";');
clm_core::$cms->addScriptDeclaration('var clm_meldelisten_last = "'.html_entity_decode($lang->last_position).'";');
clm_core::$cms->addScriptDeclaration('var clm_meldelisten_number = "'.html_entity_decode($number).'";');
clm_core::$cms->addScript(clm_core::$url."js/meldelisten.js");
?>
