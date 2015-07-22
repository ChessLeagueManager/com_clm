<?php
defined('clm') or die('Restricted access');
clm_core::$load->load_js("qrcode");
$config = clm_core::$db->config();
clm_core::$cms->addScriptDeclaration('var clm_app_info_url = "'.str_replace(array("/components/com_clm/clm/","https://","http://"),"",clm_core::$url).'";');
clm_core::$cms->addScriptDeclaration('var clm_app_info_https = "'.$config->app_security.'";');
$lang = clm_core::$lang->app_info;
clm_core::$cms->addScriptDeclaration('var clm_app_info_empty = "'.$lang->empty.'";');
clm_core::$cms->addScript(clm_core::$url."js/app_info.js");
?>
