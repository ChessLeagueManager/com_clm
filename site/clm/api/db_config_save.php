<?php
function clm_api_db_config_save($in) {
	$out = array();
	for($i=0;$i<count($in);$i++) {
		if(count($in[$i])==2) {
			clm_core::$db->config()->{$in[$i][0]}=$in[$i][1];
			$out[$i]=clm_core::$db->config()->{$in[$i][0]};
		}
	}
	return array(true, "m_configSaveSuccess", $out);
}
?>
