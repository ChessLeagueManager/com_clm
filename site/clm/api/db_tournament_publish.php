<?php
function clm_api_db_tournament_publish($id,$published=true,$group=true,$special=null) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table = "liga";
	} else {
		$table = "turniere";	
	}
	
	// Besondere Felder
	if($group) {
		switch ($special) {
   		case "mail":
   			$column="mail";
      		break;
			default:
				$column="published";
		}
	} else {
		$column="published";
	}
	
	if($published) {
		clm_core::$db->$table->get($id)->$column=1;
	} else {
		clm_core::$db->$table->get($id)->$column=0;
	}
	return array(true, "");
}
?>