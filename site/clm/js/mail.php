<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
$lang = clm_core::$lang->mail;
clm_core::$cms->addScriptDeclaration('var clm_mail_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_button_block = "'.$lang->button_block.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_button_unblock = "'.$lang->button_unblock.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_result_error0 = "'.$lang->result_error0.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_result_error1 = "'.$lang->result_error1.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_result_error2 = "'.$lang->result_error2.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_result_login = "'.$lang->result_login.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_result_success = "'.$lang->result_success.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_data_needed = "'.$lang->data_needed.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_data_filled = "'.$lang->data_filled.'";');
clm_core::$cms->addScriptDeclaration('var clm_mail_data_ready = "'.$lang->data_ready.'";');
clm_core::$cms->addScript(clm_core::$url."js/mail.js");
?>
