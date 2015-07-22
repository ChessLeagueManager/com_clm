<?php
defined('clm') or die('Restricted access');
$lang = clm_core::$lang->submit;
clm_core::$cms->addScriptDeclaration('var clm_submit_pleaseWait = "'.$lang->pleaseWait.'";');
clm_core::$load->load_js("jquery");
clm_core::$cms->addScript(clm_core::$url."js/submit.js");
?>
