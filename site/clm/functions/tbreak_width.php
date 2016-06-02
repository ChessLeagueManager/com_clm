<?php
// Festlegen der Spaltenbreite 
// z.B. von Feinwertungen in pdf-Listen
function clm_function_tbreak_width($tbreak, $group) {
	if ($group == 0) {  // single tournament - pdf
		switch ($tbreak) {
			default: return 10;
		}
	} elseif ($group == 1) {  // team tournament - pdf
		switch ($tbreak) {
			case 1: return 11;	// Buchholz
			case 2: return 12;	// Buchholzsumme
			case 3: return 14;	// Sonneborn-Berger alt
			case 4: return 8;	// Siege
			case 5: return 12;	// Brettpunkte
			case 6: return 10;	// Berliner Wertung
			case 11: return 11;	// Buchholz-1
			case 12: return 12;	// Buchholzsumme-1
			case 23: return 14;	// Sonneborn-Berger
			case 25: return 9;	// Direkter Vergleich
			default: return 10;
		}
	} else return 10;
}
?>
