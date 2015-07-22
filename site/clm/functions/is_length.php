<?php
function clm_function_is_length($length) {
	if($length=="0"){
		return true;
	}
	$units = array("em", "cm", "mm", "in", "pt", "pc", "%", "px", "ex");
	for($i=0;$i<count($units);$i++)
	{
		if(clm_core::$load->ends_with($length, $units[$i]))
		{
			$split = explode($units[$i] , $length);
			if(count($split)==2 && is_numeric($split[0]))
			{
				return true;
			} else { 
				return false;
			}
		}
	}
	return false;
}
?>
