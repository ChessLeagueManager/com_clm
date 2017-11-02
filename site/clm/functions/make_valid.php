<?php
// Diese Funktion filtert eingehende Variablen nach gewählten Kriterien
// und sollte bei korrekter Verwendung sql Injection vollständig und XSS größtenteils verhindern
// input enthält den erhaltenen Wert, type erhält den jeweiligen Typ der Veriable,
// standard enthält den Standardwert falls die übergebene Variable nicht gleich dem entsprechenden Typ ist
// Liste aller Typen
// 0 -> Ganzzahl
// 1 -> keine Ganzzahl
// 2 -> Alle Zahlen
// 3 -> gültiger Timestamp
// 4 -> Farben für CSS wie fff oder af24df ohne #
// 5 -> Opacity für CSS (Zahl zwischen 0 und 1, auch Kommazahlen)
// 6 -> escaping der jeweiligen Datenbank (nur Standard falls null)
// 7 -> XSS Filter, HTML Tags filtern
// 8 -> zusätzlich Sonderzeichen mit HTML-Entsprechung ersetzen
// 9 -> ist eines der Elemente der Auswahl (Vordefiniert)
//10 -> Datum für die Datenbank
//11 -> ist eines der Elemente der Auswahl (SQL)
//12 -> E-Mail
//13 -> Länge
//14 -> URL
// Bei ungültigen Typ wird stets der Standardwert zurückgegeben!
function clm_function_make_valid($input, $type, $standard, $choose = null) {
	if (is_null($input)) {
		return $standard;
	}
	switch ($type) {
		case 0: // integer
			if (!clm_core::$load->is_whole_number($input)) {
				return $standard;
			}
			$input = intval($input);
		break;
		case 1: // float
			if (!is_numeric($input) || clm_core::$load->is_whole_number($input)) {
				return $standard;
			}
			$input = floatval($input);
		break;
		case 2: // number
			if (!is_numeric($input)) {
				return $standard;
			}
			if (clm_core::$load->is_whole_number($input)) {
				$input = intval($input);
			} else {
				$input = floatval($input);
			}
		break;
		case 3: // timestamp
			if (!clm_core::$load->is_timestamp($input)) {
				return $standard;
			}
			$input = intval($input);
		break;
		case 4: // color
			if (!clm_core::$load->is_color($input)) {
				return $standard;
			}
		break;
		case 5: // opacity
			if (!clm_core::$load->is_opacity($input)) {
				return $standard;
			}
			$input = floatval($input);
		break;
		case 6: // sql-string
			return clm_core::$db->escape($input);
		break;
		case 7: // xss-prevention -> not perfect
			if (!is_bool($input) && !is_float($input) && !is_int($input) && !is_string($input)) {
				return $standard;
			}
			return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
		break;
		case 8: // xss-prevention + all specialchars
			if (!is_bool($input) && !is_float($input) && !is_int($input) && !is_string($input)) {
				return $standard;
			}
			return htmlentities($input, ENT_QUOTES, 'UTF-8');
		break;
		case 9: // is $input one of $choose
			if (!clm_core::$load->is_one_element($input, $choose)) {
				return $standard;
			}
		break;
		case 10: // date for mysql
			if (strlen($input) == 10) {
				if (!clm_core::$load->is_date($input,'Y-m-d')) {
					return $standard;
				}
			} else {	
				if (!clm_core::$load->is_date($input)) {
					return $standard;
				}
			}
		break;
		case 11: // is $input one of $choose
			$array = clm_core::$api->direct($choose[0],$choose[1]);
			if (isset($array[$input])) {
				return $standard;
			}
		break;
		case 12: // is $input a valid email
			if (!clm_core::$load->is_email($input)) {
				return $standard;
			}
		break;
		case 13: // is $input a length with a unit
			if (!clm_core::$load->is_length($input)) {
				return $standard;
			}
		break;
		case 14: // is $input a valid url
			if (!clm_core::$load->is_url($input)) {
				return $standard;
			}
			return  str_replace(array('"',"'","\\"), '',$input);
		break;
		default:
			return $standard; // falsche Nummer wird abgefangen
			
	}
	return $input;
}
?>
