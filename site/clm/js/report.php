<?php
defined('clm') or die('Restricted access');
clm_core::$cms->addScript(clm_core::$url."js/report.js");
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
?>
