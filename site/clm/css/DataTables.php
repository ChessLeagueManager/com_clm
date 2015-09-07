<?php
defined('clm') or die('Restricted access');
clm_core::$cms->addStyleSheet(clm_core::$url."css/DataTables.css");


clm_core::$cms->addStyleDeclaration("
	#clm .clm .clm_view_table * {
    		font-size: ".clm_core::$db->config()->table_fontSize.";
	}");
?>
