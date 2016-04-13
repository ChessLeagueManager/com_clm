<?php
function clm_api_view_tournament_group($liga=2) {
	if(clm_core::$access->access('BE_league_general') == 0 && clm_core::$access->access('BE_teamtournament_general') == 0) {
		return array(false,"e_noRights");
	}
	$table = new clm_class_table("tournament_group","db_tournament_group",15,13);
	
	//Andere Position?
	clm_core::$load->load_js("modal");
  	clm_core::$load->load_css("modal");
  	//Andere Position?
   
	$lang = clm_core::$lang->tournament_group;

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	if(clm_core::$access->access('BE_teamtournament_create') !== false OR clm_core::$access->access('BE_league_create') !== false) {
		$table->add_button($lang->categories,$lang->categories_title,clm_core::$load->gen_url(array("view"=>"catmain")),"clm_table_url clm_button clm_button_big clm_button_big_article");
	}
	if(clm_core::$access->access('BE_league_create') !== false) {
		$table->add_button($lang->newLeague,$lang->newLeague_title,clm_core::$load->gen_url(array("section"=>"ligen"),array("view")),"clm_table_url clm_button clm_button_new clm_button_big clm_button_big_new");
	}
	if(clm_core::$access->access('BE_teamtournament_create') !== false) {
		$table->add_button($lang->newNoneLeague,$lang->newNoneLeague_title,clm_core::$load->gen_url(array("section"=>"mturniere"),array("view")),"clm_table_url clm_button clm_button_new clm_button_big clm_button_big_new");	
	}
	if(clm_core::$access->access('BE_league_create') !== false || clm_core::$access->access('BE_teamtournament_create') !== false) {
		$table->add_button($lang->copy,$lang->copy_title,json_encode(array("db_tournament_copy",array("",true))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_save-copy");	
	}
	if(clm_core::$access->access('BE_league_delete' ) !== false  || clm_core::$access->access('BE_teamtournament_delete') !== false) {
		$table->add_button($lang->del,$lang->del_title,json_encode(array("db_tournament_del",array("",true))),"clm_table_event clm_button clm_button_del clm_table_confirm clm_button_big clm_button_big_delete");	
	}
	if(clm_core::$access->access('BE_league_edit_detail' ) !== false || clm_core::$access->access('BE_teamtournament_edit_detail') !== false) {
/* Erst wenn Ligen und Mannschaftsturniere mit der selben Ansicht bearbeitet werden können, kann dies hier angepasst wieder aktiviert werden.
		$table->add_button($lang->edit,$lang->edit_title,clm_core::$load->gen_url(array("view"=>"ligen","task"=>"edit"),array("id"))."&id=","clm_table_multi clm_button clm_button_edit clm_button_big clm_button_big_edit");
*/
		$table->add_button($lang->ranking,$lang->ranking_title,json_encode(array("db_tournament_ranking",array("",true))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_default");				
		$table->add_button($lang->sort,$lang->sort_title,json_encode(array("db_tournament_sortByTWZ",array("",true))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_default");
	}

	if(clm_core::$access->access('BE_league_edit_round') !== false || clm_core::$access->access('BE_teamtournament_edit_round') !== false) {
		$table->add_button($lang->newRounds,$lang->newRounds_title,json_encode(array("db_tournament_genRounds",array("",true))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_back");
		$table->add_button($lang->delRounds,$lang->delRounds_title,json_encode(array("db_tournament_delRounds",array("",true))),"clm_table_event clm_button clm_button_del clm_table_confirm clm_button_big clm_button_big_cancel");
	  if ($countryversion =="de") {
		$table->add_button($lang->dwzUpdate,$lang->dwzUpdate_title,json_encode(array("db_tournament_updateDWZ",array("",true))),"clm_table_event clm_button clm_button_edit clm_table_confirm clm_button_big clm_button_big_default");
		$table->add_button($lang->dwzGen,$lang->dwzGen_title,json_encode(array("db_tournament_genDWZ",array("",true))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_refresh");
		$table->add_button($lang->dwzDel,$lang->dwzDel_title,json_encode(array("db_tournament_delDWZ",array("",true))),"clm_table_event clm_button clm_button_del clm_button_big clm_button_big_cancel");
	  }
	}
	if(clm_core::$access->access('BE_league_edit_detail') !== false || clm_core::$access->access('BE_teamtournament_edit_detail') !== false) {
		$table->add_button($lang->public,$lang->public_title,json_encode(array("db_tournament_publish",array("",true,true))),"clm_table_event clm_button clm_button_edit clm_button_big clm_button_big_publish");
		$table->add_button($lang->hide,$lang->hide_title,json_encode(array("db_tournament_publish",array("",false,true))),"clm_table_event clm_button clm_button_edit clm_button_big clm_button_big_unpublish");
	}
	// Ordering (besondere Behandlung)
	if(clm_core::$access->access('BE_league_edit_detail') !== false || clm_core::$access->access('BE_teamtournament_edit_detail') !== false) {
		$table->add_button($lang->ordering,$lang->ordering_title,json_encode(array("db_ordering",array("","","liga"))),"clm_table_ordering clm_button clm_button_save_icon");
	}
	$season = array("" => $lang->season);
	$category = array("" => $lang->category);
	$fix = clm_core::$api->db_season_array(1);
	$season += $fix[2];

	list($parentArray, $parentKeys, $parentChilds) = clm_class_category::get();

	$category += $parentArray;

	$table->add_filter_field("catidAlltime:catidEdition",$category);
	$table->add_filter_field("sid",$season,clm_core::$access->getSeason());
	$table->add_filter_field("runden_modus",array("" => $lang->mode0,1 => $lang->mode1,2 => $lang->mode2,3 => $lang->mode3,4 => $lang->mode4,5 => $lang->mode5));
	
	// Freie Auswahl zwischen Ligen und Mannschaftsturniere
	if(clm_core::$access->access('BE_league_general') == 1 && clm_core::$access->access('BE_teamtournament_general') == 1) {
		if($liga==2) {
			$selected = "";
		} elseif($liga==1){
			$selected = 0;
		} else {
			$selected = 1;
		}
		$table->add_filter_field("liga_mt",array("" => $lang->liga_mt0,0 => $lang->liga_mt1,1 => $lang->liga_mt2),$selected);
	}
	// nur Ligen
	else if(clm_core::$access->access('BE_league_general') == 1) {
		$table->add_filter_field("liga_mt",array(0 => $lang->liga_mt1));
	}
	// nur Mannschaftsturniere
	else if(clm_core::$access->access('BE_teamtournament_general') == 1) {
		$table->add_filter_field("liga_mt",array(1 => $lang->liga_mt2));
	}
	$table->add_filter_field("published",array("" => $lang->status0,1 => $lang->status1,0 => $lang->status2));

	$fix= clm_core::$load->load_view("table", $table->result());
	return array(true,"m_tableSuccess","<div class='clm'>".$fix[1]."</div>");
}
?>
