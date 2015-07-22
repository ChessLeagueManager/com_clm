<?php
defined('clm') or die('Restricted access');
clm_core::$cms->addScriptDeclaration('var clm_config_element_url = "'.clm_core::$url.clm_core::$load->gen_url().'";');
$lang = clm_core::$lang->config;
clm_core::$cms->addScriptDeclaration('var clm_config_element_save_error_JSON = "'.$lang->save_error_JSON.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_save_error_HTTP = "'.$lang->save_error_HTTP.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_save_error_CONTENT = "'.$lang->save_error_CONTENT.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_save_success = "'.$lang->save_success.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_save_partial = "'.$lang->save_partial.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_working = "'.$lang->working.'";');
clm_core::$cms->addScriptDeclaration('var clm_config_element_reset_request = "'.$lang->reset_request.'";');
clm_core::$cms->addScript(clm_core::$url."js/config_element.js");
?>
