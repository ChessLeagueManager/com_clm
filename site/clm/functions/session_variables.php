<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
// Spam-Check
// z.B. für Online-Turnieranmeldung
function clm_function_session_variables($string = '') {
	if ($string == '') {
		return true;		
	}
	if ($string == 'o') {
		// Funktion um zufälligen String zu generieren //
		function rand_number($length=2) {
			$letters = array_merge(range(0, 9)); 
			$lettersCount = count($letters) - 1;		
			$result = '';
			for ($i = 0; $i < $length; $i++) {
				$result .= $letters[mt_rand(0, $lettersCount)];
			}
			return $result;
		}
		$zahl02 = rand_number(2);
		if ($zahl02 < 10) $zahl02 += 11;	
		$zahl01 = rand_number(1); 
		if ($zahl01 < 1) $zahl01 += 1;	
		$zahl03	= $zahl02 + $zahl01;
		$session = JFactory::getSession();
		$session->set('reg_wert',$zahl03);
		return array(true, $zahl02, $zahl01);		
	}

}
?>
