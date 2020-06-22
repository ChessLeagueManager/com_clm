<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->mannschaft;
$lists_noOrgReference = clm_core::$load->request_string('clm_noOrgReference');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_name = "'.html_entity_decode($lang->name_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_mnr = "'.html_entity_decode($lang->mnr_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_tnr = "'.html_entity_decode($lang->tnr_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_sid = "'.html_entity_decode($lang->sid_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_lid = "'.html_entity_decode($lang->lid_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_zps = "'.html_entity_decode($lang->zps_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_mannschaft_noOrg = "'.html_entity_decode($lists_noOrgReference).'";');
clm_core::$cms->addScript(clm_core::$url."js/mannschaft.js");
?>
