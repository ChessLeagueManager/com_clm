<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_api_view_tournament() {
	$lang = clm_core::$lang->tournament;
	$table = new clm_class_table("tournament","db_tournament",15,13);

	if(clm_core::$access->access('BE_tournament_create') !== false) {
		$table->add_button($lang->categories,$lang->categories_title,clm_core::$load->gen_url(array("view"=>"catmain")),"clm_table_url clm_button clm_button_big clm_button_big_article");
		$table->add_button($lang->special,$lang->special_title,clm_core::$load->gen_url(array("view"=>"sonderranglistenmain")),"clm_table_url clm_button clm_button_big clm_button_big_article");
		$table->add_button($lang->new,$lang->new_title,clm_core::$load->gen_url(array("view"=>"turform")),"clm_table_url clm_button clm_button_new clm_button_big clm_button_big_new");
	}
	if(clm_core::$access->access('BE_tournament_create') !== false) {
		$table->add_button($lang->copy,$lang->copy_title,json_encode(array("db_tournament_copy",array("",false))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_save-copy");
	}
	if(clm_core::$access->access('BE_tournament_delete') !== false) {
		$table->add_button($lang->del,$lang->del_title,json_encode(array("db_tournament_del",array("",false))),"clm_table_event clm_button clm_button_del clm_table_confirm clm_button_big clm_button_big_delete");
	}
	if(clm_core::$access->access('BE_tournament_edit_detail') !== false) {
		$table->add_button($lang->edit,$lang->edit_title,clm_core::$load->gen_url(array("view"=>"turform","task"=>"edit"),array("id"))."&id=","clm_table_multi clm_button clm_button_edit clm_button_big clm_button_big_edit");
	}
	if(clm_core::$access->access('BE_tournament_edit_round') !== false) {
		$table->add_button($lang->newRounds,$lang->newRounds_title,json_encode(array("db_tournament_genRounds",array("",false))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_back");
		$table->add_button($lang->delRounds,$lang->delRounds_title,json_encode(array("db_tournament_delRounds",array("",false))),"clm_table_event clm_button clm_button_del clm_table_confirm clm_button_big clm_button_big_cancel");
		$table->add_button($lang->dwzUpdate,$lang->dwzUpdate_title,json_encode(array("db_tournament_updateDWZ",array("",false))),"clm_table_event clm_button clm_button_edit clm_table_confirm clm_button_big clm_button_big_default");
		$table->add_button($lang->dwzGen,$lang->dwzGen_title,json_encode(array("db_tournament_genDWZ",array("",false))),"clm_table_event clm_button clm_button_new clm_button_big clm_button_big_refresh");
		$table->add_button($lang->dwzDel,$lang->dwzDel_title,json_encode(array("db_tournament_delDWZ",array("",false))),"clm_table_event clm_button clm_button_del clm_button_big clm_button_big_cancel");
	}
	if(clm_core::$access->access('BE_tournament_edit_detail') !== false) {
		$table->add_button($lang->public,$lang->public_title,json_encode(array("db_tournament_publish",array("",true,false))),"clm_table_event clm_button clm_button_edit clm_button_big clm_button_big_publish");
		$table->add_button($lang->hide,$lang->hide_title,json_encode(array("db_tournament_publish",array("",false,false))),"clm_table_event clm_button clm_button_edit clm_button_big clm_button_big_unpublish");
	}
	// Ordering (besondere Behandlung)
	if(clm_core::$access->access('BE_tournament_edit_detail') !== false) {
		$table->add_button($lang->ordering,$lang->ordering_title,json_encode(array("db_ordering",array("","","turniere"))),"clm_table_ordering clm_button clm_button_save_icon");
	}

	$season = array("" => $lang->season);
	$category = array("" => $lang->category);
	$fix = clm_core::$api->db_season_array(1);
	$season += $fix[2];
	list($parentArray, $parentKeys, $parentChilds) = clm_class_category::get();
	$category += $parentArray;

	$table->add_filter_field("catidAlltime:catidEdition",$category);
	$table->add_filter_field("sid",$season,clm_core::$access->getSeason());
//	$table->add_filter_field("typ",array("" => $lang->mode0,1 => $lang->mode1,2 => $lang->mode2,3 => $lang->mode3,4 => $lang->mode4,5 => $lang->mode5,6 => $lang->mode6));
	$table->add_filter_field("typ",array("" => $lang->mode0,1 => $lang->mode1,2 => $lang->mode2,3 => $lang->mode3,5 => $lang->mode5,6 => $lang->mode6));
	$table->add_filter_field("published",array("" => $lang->status0,1 => $lang->status1,0 => $lang->status2));

	$fix= clm_core::$load->load_view("table", $table->result());
	return array(true,"m_tableSuccess","<div class='clm'>".$fix[1]."</div>");
}
?>
