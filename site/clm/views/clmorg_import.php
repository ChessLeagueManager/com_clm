<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
function clm_view_clmorg_import() {
	clm_core::$load->load_css("clmorg_import");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_js("clmorg_import");
	$lang = clm_core::$lang->clmorg_import;
	echo $lang->infotext1 . "<br/>" . $lang->infotext2;
?>
<div class="clm_view_clmorg_import_div">
<?php $fix = clm_core::$api->view_units_form("",clm_core::$db->config()->lv); echo $fix[2]; // array dereferencing fix php 5.3 ?>
<button type="button" class="clm_button" onclick="clm_clmorg_import_club(this)" title="<?php echo $lang->clubUpdateText; ?>"><?php echo $lang->clubUpdate; ?></button>
<button type="button" class="clm_button" onclick="clm_clmorg_import_player(this,0)" title="<?php echo $lang->normalUpdateText; ?>"><?php echo $lang->normalUpdate; ?></button>
<button type="button" class="clm_button" onclick="clm_clmorg_import_player(this,1)" title="<?php echo $lang->specialUpdateText; ?>"><?php echo $lang->specialUpdate; ?></button>
</div>

<div class="clm_view_clmorg_import_update" style="display:none;">

</div>

<?php
//var_dump(clm_core::$api->db_dewis_club("636"));
//var_dump(clm_core::$api->db_dewis_player("63629"));
} 
?>
