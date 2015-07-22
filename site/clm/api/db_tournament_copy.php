<?php
function clm_api_db_tournament_copy($id, $group=true) {
	$id = clm_core::$load->make_valid($id, 0, -1);
	if($group) {
		$table_list = "#__clm_liga";
	} else {
		$table_list = "#__clm_turniere";
	}

	$sql = "SELECT * FROM ".$table_list." WHERE id=".$id;
	$table = clm_core::$db->loadAssocList($sql);
	
	$lang = clm_core::$lang->tournament;
	$table[0]["name"]=$lang->copy2." ".$table[0]["name"];
	$table[0]["rnd"]=0;
	$table[0]["published"]=0;
	$out = new clm_class_params($table[0]["params"]);
	$out->set("dwz_date","");
	$out->set("inofDWZ","");
	$table[0]["params"]=$out->params();

	$keyS="";
	$valueS="";

	foreach($table[0] as $key => $value) {
		if(is_numeric($key) || $key == "id") {
			continue;
		}
		if($keyS!="") {
			$keyS.=", ";
		}
		$keyS .= "`".$key."`";
		if($valueS!="") {
			$valueS.=", ";
		}
		$valueS .= "'".clm_core::$db->escape($value)."'";
	}
	$new = "INSERT INTO ".$table_list." (".$keyS.") VALUES (".$valueS.")";

	clm_core::$db->query($new);
	return array(true,"");
}
?>
