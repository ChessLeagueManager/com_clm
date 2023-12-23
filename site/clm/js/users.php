<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');

$lang = clm_core::$lang->users;
$conf_user_member = clm_core::$load->request_string('clm_user_member');
clm_core::$cms->addScriptDeclaration('var clm_users_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_users_name = "'.html_entity_decode($lang->name_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_user = "'.html_entity_decode($lang->user_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_mail = "'.html_entity_decode($lang->mail_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_wrong_mail = "'.html_entity_decode($lang->wrong_mail).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_func = "'.html_entity_decode($lang->func_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_zps = "'.html_entity_decode($lang->zps_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_sid = "'.html_entity_decode($lang->sid_auswaehlen).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_pkz = "'.html_entity_decode($lang->pkz_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_users_bem = "'.html_entity_decode($lang->bem_angeben).'";');
clm_core::$cms->addScriptDeclaration('var clm_user_member = "'.html_entity_decode($conf_user_member).'";');
clm_core::$cms->addScript(clm_core::$url."js/users.js");
?>
