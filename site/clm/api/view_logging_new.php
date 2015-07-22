<?php
function clm_api_view_logging_new($ids) {
	$out = clm_core::$api->db_logging_new($ids);

	return array(true,"",implode("\n", $out[2]),array("Content-disposition: attachment; filename=CLM_LOG-".$out[2][0].".txt","Content-type: text/plain"));
}
?>
