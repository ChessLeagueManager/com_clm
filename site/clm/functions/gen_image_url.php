<?php
// diese Funktion könnte zum Laden von User Inhalten genutzt werden (anderer Pfad)
function clm_function_gen_image_url($image,$sufix = "png") {
		return clm_core::$url . "images/" . $image . '.' . $sufix;
}
?>
