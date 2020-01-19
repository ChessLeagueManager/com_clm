<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2019 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_db_tournament() {
	$table = '#__clm_turniere';
	$primaryKey = 'id';
	$columns = array(
		array( 'db' => 'id', 'dt' => 0),
		array( 'db' => 'name', 'dt' => 1),
		array( 'db' => 'sid',  'dt' => 2),
		array( 'db' => 'dateStart',   'dt' => 3),
		array( 'db' => 'invitationText', 'dt' => 4),
		array('db' => 'vereinZPS','dt' => 5),
		array('db' => 'typ','dt'=> 6),
		array( 'db' => 'runden',   'dt' => 7),
		array( 'db' => 'teil', 'dt' => 8),
		array('db' => 'tl','dt' => 9),
		array('db' => 'id','dt' => 10),
		array('db' => 'rnd','dt'=> 11),
		array( 'db' => 'published',   'dt' => 12),
		array( 'db' => 'ordering', 'dt' => 13),
		array( 'db' => 'id', 'dt' => 14)
	);

	$allowed = array("typ" => "i","published" => "i","sid" => "i", "catidAlltime:catidEdition" => "i");
	$out = clm_class_DataTables::simple($_POST, $table, $primaryKey, $columns, $allowed,clm_core::$db);
	$lang = clm_core::$lang->tournament;
	$clmAccess = clm_core::$access; 
	for($i=0;$i<count($out["data"]);$i++) {
		$out["data"][$i][0] = $i+1;
		
		$out["data"][$i][1] = clm_class_category::name($out["data"][$i][14],false);
		if (!(($out["data"][$i][9] != $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true ) OR ($clmAccess->access('BE_tournament_edit_detail') === false))) {
			$out["data"][$i][1] = '<a href="'.clm_core::$load->gen_url(array("view"=>"turform","task"=>"edit","id"=>$out["data"][$i][14])).'">'.$out["data"][$i][1].'</a>';
		}		
		// Saisonname statt sid Anzeigen
		$sid = intval($out["data"][$i][2]);		
		$out["data"][$i][2] = clm_core::$db->saison->get($sid)->name;
		// dateStart durch dateStart und dateEnd ersetzen
		if (clm_core::$db->turniere->get($out["data"][$i][14])->dateStart != '0000-00-00' AND
                    clm_core::$db->turniere->get($out["data"][$i][14])->dateStart != '1970-01-01') {
			$out["data"][$i][3] = clm_core::$load->date_to_string(clm_core::$db->turniere->get($out["data"][$i][14])->dateStart,false,true);
			if (clm_core::$db->turniere->get($out["data"][$i][14])->dateEnd != '0000-00-00' AND
                            clm_core::$db->turniere->get($out["data"][$i][14])->dateEnd != '1970-01-01' AND
                            clm_core::$db->turniere->get($out["data"][$i][14])->dateStart != clm_core::$db->turniere->get($out["data"][$i][14])->dateEnd) {
				$out["data"][$i][3] .= " ".$lang->until." ". clm_core::$load->date_to_string(clm_core::$db->turniere->get($out["data"][$i][14])->dateEnd,false,true);
			}
		} else {
			$out["data"][$i][3] = $lang->unknownDate;
		}
		// Veranstalter / Ausrichter
		$out["data"][$i][5]=clm_core::$load->zps_to_district($out["data"][$i][5]);
		if (clm_core::$db->turniere->get($out["data"][$i][14])->bezirkTur == 1) { 
			$out["data"][$i][5]=$lang->districtEvent.($out["data"][$i][5] != "" ? ("<br />".$out["data"][$i][5]) : "");
		}
		// Ausschreibung
		if($out["data"][$i][4]=="") {
			$out["data"][$i][4] = $lang->column3_no;
		} else {
			$out["data"][$i][4] = $lang->column3_yes;
		}
		if (!(($out["data"][$i][9] != $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true ) OR ($clmAccess->access('BE_tournament_edit_detail') === false))) {
			$out["data"][$i][4] = '<a href="'.clm_core::$load->gen_url(array("view"=>"turinvite","task"=>"edit","id"=>$out["data"][$i][14])).'">'.$out["data"][$i][4].'</a>';
		}	
		// Modus ID in Name umsetzen
		$out["data"][$i][6] = clm_core::$load->mode_to_name(intval($out["data"][$i][6]),false);
		// eingetragene Teilnehmer
		$query = 'SELECT COUNT(id)'
					. ' FROM #__clm_turniere_tlnr'
					. ' WHERE turnier = '.$out["data"][$i][14]
					;
		if (!(($out["data"][$i][9] != $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true ) OR ($clmAccess->access('BE_tournament_edit_detail') === false))) {
			$out["data"][$i][8] = '<a href="'.clm_core::$load->gen_url(array("view"=>"turplayers","task"=>"edit","id"=>$out["data"][$i][14])).'">' . $out["data"][$i][8] . " " . $lang->player.'</a>'. "<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->registered.$lang->close;
		} else {
			$out["data"][$i][8] = $out["data"][$i][8] . " " .$lang->player. "<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->registered.$lang->close;
		}
		// Runden mit Bestätigung/tl_ok
		// eingetragene Teilnehmer
		$query = 'SELECT COUNT(id)'
						. ' FROM #__clm_turniere_rnd_termine'
						. ' WHERE turnier = '.$out["data"][$i][14].' AND tl_ok = \'1\''
						;
						
		// Durchläufe Anzeigen
		if(clm_core::$db->turniere->get($out["data"][$i][14])->dg>1) {
					$out["data"][$i][7]=clm_core::$db->turniere->get($out["data"][$i][14])->dg." x ".$out["data"][$i][7];
		}
		
		if (!(($out["data"][$i][9] != $clmAccess->getJid() AND $clmAccess->access('BE_tournament_edit_detail') !== true ) OR ($clmAccess->access('BE_tournament_edit_detail') === false))) {
			$out["data"][$i][7] = '<a href="'.clm_core::$load->gen_url(array("view"=>"turrounds","task"=>"edit","id"=>$out["data"][$i][14])).'">' . $out["data"][$i][7] . " " .$lang->rounds.'</a>'."<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->confirmed.$lang->close;
		} else {
			$out["data"][$i][7] = $out["data"][$i][7] . " " .$lang->rounds. "<br/>".$lang->open.clm_core::$db->count($query)." ".$lang->confirmed.$lang->close;
		}		
		// Turnierleiter
		$query = 'SELECT name'
				. ' FROM #__clm_user'
				. ' WHERE jid = '.$out["data"][$i][9]
				. ' AND sid = '.$sid
				;
		$result = clm_core::$db->loadAssocList($query);
		if(count($result)==1) {
			$out["data"][$i][9] = $result[0]["name"];
		} else {
			$out["data"][$i][9] = "-";
		}
		// DWZ berechnet
		$params = clm_core::$db->turniere->get($out["data"][$i][10])->params;
		$params = new clm_class_params($params);
		if ($params->get("inofDWZ","0") == "1") { 
			$out["data"][$i][10] = '<button class="clm_table_image" value=\'["db_tournament_delDWZ",['.$out["data"][$i][14].',false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][10] = '<button class="clm_table_image" value=\'["db_tournament_genDWZ",['.$out["data"][$i][14].',false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}
		// Runden freigegeben
		if ($out["data"][$i][11] == 1) { 
			$out["data"][$i][11] = '<button class="clm_table_image clm_button_del_danger" value=\'["db_tournament_delRounds",['.$out["data"][$i][14].',false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][11] = '<button class="clm_table_image" value=\'["db_tournament_genRounds",['.$out["data"][$i][14].',false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}
		// Veröffentlicht
		if ($out["data"][$i][12] == 1) { 
			$out["data"][$i][12] = '<button class="clm_table_image " value=\'["db_tournament_publish",['.$out["data"][$i][14].',false,false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/apply").'" /></button>';
		} else {
			$out["data"][$i][12] = '<button class="clm_table_image" value=\'["db_tournament_publish",['.$out["data"][$i][14].',true,false]]\'><img width="16" height="16" src="'.clm_core::$load->gen_image_url("table/cancel").'" /></button>';
		}
		$out["data"][$i][13] = '<input class="clm_table_orderingBox" onkeypress="return clm_isChangeNumber(event);" value="'.$out["data"][$i][13].'" type="text">';
		$out["data"][$i][13] .= '<input class="clm_table_orderingId" value="'.$out["data"][$i][14].'" type="hidden">';
	}
	
	return array(true,"m_tableSuccess",$out);
}
?>
