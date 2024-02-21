<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	function clm_function_clm_path() {
		$out = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$out = str_replace('administrator/', '', $out);
		$out = str_replace('index.php', '', $out);
		$ipos = strpos($out,'/component');
		if ($ipos !== false) 
			$out = substr($out,0,$ipos);
		if (substr($out,0,$ipos) !== false) 
			$out = substr($out, 0, -1);
		return $out;
	}
?>
