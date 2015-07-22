<?php
defined('clm') or die('Restricted access');
clm_core::$cms->addStyleSheet(clm_core::$url."css/buttons.css");

if(clm_core::$db->config()->button_style == 0){
	clm_core::$cms->addStyleSheet(clm_core::$url."css/buttons_big.css");
}




?>
