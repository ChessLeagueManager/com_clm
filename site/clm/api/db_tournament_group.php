<?php
function clm_api_db_tournament_group() {
	$table = '#__clm_liga';
	$primaryKey = 'id';
	$columns = array(
		array( 'db' => 'id', 'dt' => 0),
		array( 'db' => 'name', 'dt' => 1),
		array( 'db' => 'sid',  'dt' => 2),
		array( 'db' => 'runden_modus',   'dt' => 3),
		array( 'db' => 'runden', 'dt' => 4),
		array('db' => 'teil','dt' => 5),
		array('db' => 'stamm','dt'=> 6),
		array( 'db' => 'sl', 'dt' => 7),
		array('db' => 'bemerkungen','dt' => 8),
		array('db' => 'mail','dt' => 9),
		array('db' => 'id','dt' => 10),
		array( 'db' => 'rnd',   'dt' => 11),
		array( 'db' => 'published',   'dt' => 12),
		array( 'db' => 'ordering', 'dt' => 13),
		array( 'db' => 'id', 'dt' => 14)
	);

	// Erzwinge Filter bei unzureichenden Berechtigungen
	if(clm_core::$access->access('BE_league_general') == 1 && clm_core::$access->access('BE_teamtournament_general') == 0) {
		if (in_array('liga_mt', $_POST["names"])) {
			$_POST["values"][array_search("liga_mt",$_POST["names"])] = "0";
		} else {
			$_POST["names"][] = "liga_mt";
			$_POST["values"][] = "0";
		}
	}else if(clm_core::$access->access('BE_league_general') == 0 && clm_core::$access->access('BE_teamtournament_general') == 1) {
		if (in_array('liga_mt', $_POST["names"])) {
			$_POST["values"][array_search("liga_mt",$_POST["names"])] = "1";
		} else {
			$_POST["names"][] = "liga_mt";
			$_POST["values"][] = "1";
		}
	}
	
	$allowed = array("runden_modus" => "i","published" => "i","sid" => "i", "catidAlltime:catidEdition" => "i", "liga_mt" => "i");

	$out = clm_class_DataTables::simple($_POST, $table, $primaryKey, $columns, $allowed,clm_core::$db);
	$lang = clm_core::$lang->tournament_group;
	$clmAccess = clm_core::$access; 
	for($i=0;$i<count($out["data"]);$i++) {	
			if(clm_core::$db->liga->get($out["data"][$i][0])->liga_mt == 0) {
				$right = "league";
				$section = "ligen";
			} else {
				$right = "teamtournament";	
				$section = "mturniere";
			}	
	
	
		$out["data"][$i][0] = $i+1;
		
		$out["data"][$i][1] = clm_class_category::name($out["data"][$i][14],true);
		if (!(($out["data"][$i][7] != $clmAccess->getJid() AND $clmAccess->access('BE_'.$right.'_edit_detail') !== true ) OR ($clmAccess->access('BE_'.$right.'_edit_detail') === false))) {
			//$out["data"][$i][1] = '<a href="'.clm_core::$load->gen_url(array("section"=>"ligen","task"=>"edit","cid[]"=>$out["data"][$i][14]),array("view")).'">'.$out["data"][$i][1].'</a>';
			$out["data"][$i][1] = '<a href="'.clm_core::$load->gen_url(array("section"=>$section,"task"=>"edit","cid[]"=>$out["data"][$i][14]),array("view")).'">'.$out["data"][$i][1].'</a>';
		}
		
		// Saisonname statt sid Anzeigen
		$sid = intval($out["data"][$i][2]);		
		$out["data"][$i][2] = clm_core::$db->saison->get($sid)->name;
		
		// Modus ID in Name umsetzen
		$out["data"][$i][3] = clm_core::$load->mode_to_name(intval($out["data"][$i][3]),true);		
		

		// Runden mit Bestätigung/sl_ok
		// eingetragene Teilnehmer
		$query = 'SELECT COUNT(id)'
						. ' FROM #__clm_runden_termine'
						. ' WHERE liga = '.$out["data"][$i][14].' AND sl_ok = \'1\''
						;
						
		// Durchläufe Anzeigen
		if(clm_core::$db->liga->get($out["data"][$i][14])->durchgang>1) {
					$out["data"][$i][4]=clm_core::$db->liga->get($out["data"][$i][14])->durchgang." x ".$out["data"][$i][4];
		}
		
		if (!(($out["data"][$i][7] != $clmAccess->getJid() AND $clmAccess->access('BE_'.$right.'_edit_detail') !== true ) 
			OR ($clmAccess->access('BE_'.$right.'_edit_detail') === false)) AND ($out["data"][$i][11] == 1)) {
			$out["data"][$i][4] = '<a href="'.clm_core::$load->gen_url(array("section"=>"runden","liga"=>$out["data"][$i][14]),array("view")).'">' . $out["data"][$i][4] . " " .$lang->rounds.'</a>'."<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->confirmed.$lang->close;
		} else {
			$out["data"][$i][4] = $out["data"][$i][4] . " " .$lang->rounds. "<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->confirmed.$lang->close;
		}	
		
		// Stammspieler + Ersatzspieler
		$out["data"][$i][6]=$out["data"][$i][6] . " ".$lang->open.$lang->plus." ".clm_core::$db->liga->get($out["data"][$i][14])->ersatz.$lang->close;
		
		// Turnierleiter
		$query = 'SELECT name'
				. ' FROM #__clm_user'
				. ' WHERE jid = '.$out["data"][$i][7]
				. ' AND sid = '.$sid
				;
		$result = clm_core::$db->loadAssocList($query);
		if(count($result)==1) {
			$out["data"][$i][7] = $result[0]["name"];
		} else {
			$out["data"][$i][7] = "-";
		}
		
		if($out["data"][$i][8]=="") {
				$out["data"][$i][8] = $lang->column9_no;
		} else {
				$out["data"][$i][8] = '<a href="javascript:void(0);" onclick=\'clm_modal_display("'.htmlspecialchars($out["data"][$i][8],ENT_QUOTES, "UTF-8").'")\' href="javascript:;" >'.$lang->column9_yes.'</a>';
		}
		// Mail
		if ($out["data"][$i][9] == 1) { 
			$out["data"][$i][9] = '<button class="clm_table_image " value=\'["db_tournament_publish",['.$out["data"][$i][14].',false,true,"mail"]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][9] = '<button class="clm_table_image" value=\'["db_tournament_publish",['.$out["data"][$i][14].',true,true,"mail"]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}		
		// DWZ berechnet
		$params = clm_core::$db->liga->get($out["data"][$i][10])->params;
		$params = new clm_class_params($params);
		if ($params->get("inofDWZ","0") == "1") { 
			$out["data"][$i][10] = '<button class="clm_table_image" value=\'["db_tournament_delDWZ",['.$out["data"][$i][14].',true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][10] = '<button class="clm_table_image" value=\'["db_tournament_genDWZ",['.$out["data"][$i][14].',true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}
		// Runden
		if ($out["data"][$i][11] == 1) { 
			$out["data"][$i][11] = '<button class="clm_table_image " value=\'["db_tournament_delRounds",['.$out["data"][$i][14].',true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][11] = '<button class="clm_table_image" value=\'["db_tournament_genRounds",['.$out["data"][$i][14].',true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}
		
		// Veröffentlicht
		if ($out["data"][$i][12] == 1) { 
			$out["data"][$i][12] = '<button class="clm_table_image " value=\'["db_tournament_publish",['.$out["data"][$i][14].',false,true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][12] = '<button class="clm_table_image" value=\'["db_tournament_publish",['.$out["data"][$i][14].',true,true]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}		
		
		$out["data"][$i][13] = '<input class="clm_table_orderingBox" onkeypress="return clm_isChangeNumber(event);" value="'.$out["data"][$i][13].'" type="text">';
		$out["data"][$i][13] .= '<input class="clm_table_orderingId" value="'.$out["data"][$i][14].'" type="hidden">';
	}
	
	return array(true,"m_tableSuccess",$out);
}
?>
