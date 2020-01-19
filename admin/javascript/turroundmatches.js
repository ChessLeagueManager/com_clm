/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
	
	function openPgnRow(id) {
		document.getElementById('pgnSwitch' + id).innerHTML = '';
		
		document.getElementById('pgnHead' + id).innerHTML = 'PGN:';
		
		document.getElementById('pgnTextarea' + id).innerHTML = '<textarea name="pgn[' + id + ']" id="pgnformt' + id + '" cols="75" rows="10"></textarea>';
		document.getElementById('pgnformt' + id).value = document.getElementById('pgnformh' + id).value;
		document.getElementById('pgnHidden' + id).innerHTML = '';
		
		document.getElementById('pgnEnd' + id).innerHTML = '<a href="#" onclick="closePgnRow(' + id + ')">X</a>';
	}
	
	function closePgnRow(id) {
		document.getElementById('pgnSwitch' + id).innerHTML = '<a href="#" onclick="openPgnRow(' + id + ')">' + document.getElementById('pgnformt' + id).value.length + '</a>';
		
		document.getElementById('pgnHead' + id).innerHTML = '';
		
		document.getElementById('pgnHidden' + id).innerHTML = '<input type="hidden" name="pgn[' + id + ']" id="pgnformh' + id + '">';
		document.getElementById('pgnformh' + id).value = document.getElementById('pgnformt' + id).value;
		document.getElementById('pgnTextarea' + id).innerHTML = '';
		
		document.getElementById('pgnEnd' + id).innerHTML = '';
	}
	
