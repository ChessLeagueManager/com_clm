/*
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/

$(document).ready(function() {
	$('.js-example-basic-single').each(function () {
		$("#" + this.id).select2();
	});
});
