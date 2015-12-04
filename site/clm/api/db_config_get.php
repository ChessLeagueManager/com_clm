<?php
function clm_api_db_config_get($array,$base=false) {
	$out = array();
	if(!is_array($array) || count($array) < 1)
	{
		return array(true,"",$out);
	}
	$config = clm_core::$db->config()->getConfig();

	if(!$base){
		$out[0] = $array[0];
		$out[1] = $array[1];
	}
	$p = 0;
	for ($i = 0;$i < count($array);$i++) {
		if($i==0 && !$base)
		{
			$p++;$p++;
			$i++;$i++;
		}
		if (!is_array($array[$i]) && isset($config[$array[$i]])) {
			$out[$p][0] = $config[$array[$i]][1];
			$out[$p][1] = $array[$i];
			$out[$p][2] = clm_core::$db->config()->{$array[$i]};
			if ($out[$p][0] == 11) {
				$out[$p][3] = clm_core::$api->direct($config[$array[$i]][3][0],$config[$array[$i]][3][1]);
			} else {
				$out[$p][3] = null;
			}
			$p++;
		} else if (is_array($array[$i])) {
			$fix = clm_api_db_config_get($array[$i]);
			$out[$p][0] = $fix[2]; // array dereferencing fix php 5.3
			$p++;
		}
	}

	return array(true, "", $out);
}
?>
