<?php
// TWZ aus Wertungsmodus, DWZ und ELO ermitteln
// $mode = 0 --> höhere Wertung
// $mode = 1 --> dwz vor elo
// $mode = 2 --> elo vor dwz
function clm_function_gen_twz($mode = 0, $dwz = 0, $elo = 0) {
		$twz = 0;
		if ($mode == 0) {
			$twz = max(array($dwz, $elo));
		} elseif ($mode == 1) {
			$twz = $dwz;
			if ($twz == 0) {
				$twz = $elo;
			}
		} else {
			$twz = $elo;
			if ($twz == 0) {
				$twz = $dwz;
			}
		}
		return $twz;
}
?>
