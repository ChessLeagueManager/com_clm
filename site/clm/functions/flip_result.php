<?php
// 0 -> 0 - 1
// 1 -> 1 - 0
// 2 -> 0.5 - 0.5
// 3 -> 0 - 0
// 4 -> - / +
// 5 -> + / -
// 6 -> - / -
// 7 -> ---
// 8 -> spielfrei
// Invertieren des Spielergebnis (Nummer) 
function clm_function_flip_result($result) {
	switch ($result) {
		case 0:
			return 1;
		case 1:
			return 0;
		case 2:
			return 2;
		case 3:
			return 3;
		case 4:
			return 5;
		case 5:
			return 4;
		case 6:
			return 6;
		case 7:
			return 7;
		case 8:
			return 8;
	}
}
?>
