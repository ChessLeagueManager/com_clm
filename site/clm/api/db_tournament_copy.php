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
	$before_ID = $id;
	
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
	$after_ID = clm_core::$db->insert_id();
	
	if(!$group) return array(true,"");
	
	if ($after_ID !== false AND $after_ID != 0 AND $after_ID != $before_ID) {
		// Runden und Rundentermine für neuen Mannschaftswettbewerb anlegen
		clm_core::$api->db_tournament_genRounds($after_ID,true); 
	
	// copy Rundentermine für Mannschaftswettbewerbe
		$sql = "SELECT * FROM #__clm_runden_termine WHERE liga=".$id
			." ORDER BY nr ASC";
		$runden = clm_core::$db->loadAssocList($sql);
 
		// alle Runden durchgehen
		foreach ($runden as $runde) {	
			$new = "UPDATE #__clm_runden_termine "
				." SET name = '".$runde["name"]."'"
				.", datum = '".$runde["datum"]."'"
				.", startzeit = '".$runde["startzeit"]."'"
				.", deadlineday = '".$runde["deadlineday"]."'"
				.", deadlinetime = '".$runde["deadlinetime"]."'"
				.", published = ".$runde["published"]
				.", ordering = ".$runde["ordering"]
				.", enddatum = '".$runde["enddatum"]."'"
				." WHERE liga = ".$after_ID
				." AND nr = ".$runde["nr"]
				;
			clm_core::$db->query($new);
		}

		// Mannschaften anlegen
		for($x=1; $x< 1+$table[0]['teil']; $x++) {
			$man_name = $lang->LIGEN_STD_TEAM." ".$x;
			if ($x < 10) $man_nr = $after_ID.'0'.$x; else $man_nr = $after_ID.$x;
			$newt = " INSERT INTO #__clm_mannschaften "
				." (`sid`,`name`,`liga`,`zps`,`liste`,`edit_liste`,`man_nr`,`tln_nr`,`mf`,`published`) "
				." VALUES ('".$table[0]['sid']."','$man_name','$after_ID','1','0','0','$man_nr','$x','0','0') "
				;
			clm_core::$db->query($newt);
		}
	}

	return array(true,"before_ID:".$before_ID."  after_ID:".$after_ID);
}
?>
