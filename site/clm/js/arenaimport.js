/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

	function insertCode () {
		if (arena_code.value == '') {
			var selValue = tid.value;
			var code_index = 'turnier'+tid.value;
			var code_value = document.getElementById(code_index);			
			arena_code.value = code_value.value;
		}
	}
		
