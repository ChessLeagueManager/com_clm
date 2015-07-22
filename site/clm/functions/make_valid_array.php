<?php
// Diese Funktion werden drei Arrays übergeben
// 1. enthält die eingegangen Parameter
// 2. enthält den Typ des Parameters
// 3. enthält den Standardwert
function clm_function_make_valid_array($input, $type, $standard, $choose = null)
{
		if (!is_array($input)) {
			return $standard;
		}
		for ($i = 0;$i < count($type);$i++) {
			if (!isset($input[$i])) {
				$input[$i] = $standard[$i];
			}else{
				if(isset($choose[$i])){
				$input[$i] = clm_core::$load->make_valid($input[$i],$type[$i],$standard[$i],$choose[$i]);
			   }
			   else{
				$input[$i] = clm_core::$load->make_valid($input[$i],$type[$i],$standard[$i]);
			   }
			}
		}
		return $input;
}