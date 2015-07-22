<?php
function clm_api_db_logging_del($id) {
	if($id==null) {
		$sql =  "DELETE FROM #__clm_logging";
		clm_core::$db->query($sql);
	}
	if(is_numeric($id)) {
		$sql =  "DELETE FROM #__clm_logging WHERE id=".$id;
		clm_core::$db->query($sql);
	}
	return array(true, "");
}
?>