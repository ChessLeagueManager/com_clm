<?php
/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('clm') or die('Restricted access');
$lang = clm_core::$lang->report;
clm_core::$cms->addScriptDeclaration('var clm_report_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
clm_core::$cms->addScriptDeclaration('var clm_report_button_block = "'.$lang->button_block.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_button_unblock = "'.$lang->button_unblock.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_result_error0 = "'.$lang->result_error0.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_result_error1 = "'.$lang->result_error1.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_result_error2 = "'.$lang->result_error2.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_result_login = "'.$lang->result_login.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_result_success = "'.$lang->result_success.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_data_needed = "'.$lang->data_needed.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_data_filled = "'.$lang->data_filled.'";');
clm_core::$cms->addScriptDeclaration('var clm_report_data_ready = "'.$lang->data_ready.'";');
$clm_p_sieg = clm_core::$load->request_string('clm_p_sieg');
clm_core::$cms->addScriptDeclaration('var clm_p_sieg = "'.$clm_p_sieg.'";');
$clm_p_remis = clm_core::$load->request_string('clm_p_remis');
clm_core::$cms->addScriptDeclaration('var clm_p_remis = "'.$clm_p_remis.'";');
$clm_p_nieder = clm_core::$load->request_string('clm_p_nieder');
clm_core::$cms->addScriptDeclaration('var clm_p_nieder = "'.$clm_p_nieder.'";');
$clm_p_antritt = clm_core::$load->request_string('clm_p_antritt');
clm_core::$cms->addScriptDeclaration('var clm_p_antritt = "'.$clm_p_antritt.'";');
clm_core::$cms->addScript(clm_core::$url."js/report.js");
?>
