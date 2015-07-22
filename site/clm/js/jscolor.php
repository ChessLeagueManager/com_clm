<?php
defined('clm') or die('Restricted access');
clm_core::$cms->addScriptDeclaration('jscolor.dir = "'.clm_core::$url.'images/jscolor/";');
clm_core::$cms->addScript(clm_core::$url."js/jscolor.js");
?>
