<?php
// $published=0 => Nur Unveröffentlichte
// $published=1 => Nur Veröffentlichte
// $published=2 => Kein Filter
function clm_api_db_season_array($published=1) {
	if($published==0) {
		$sql = " WHERE published=0";
	} else if ($published==1) {
		$sql = " WHERE published=1";
	} else {
		$sql = "";
	}
	$query = 'SELECT id, name FROM #__clm_saison'.$sql;
	$result = clm_core::$db->loadAssocList($query);
	
	$out = array();
	for($i=0;$i<count($result);$i++) {
		$out[$result[$i]["id"]]=$result[$i]["name"];
	}
	return array(true,'',$out);
}
?>
