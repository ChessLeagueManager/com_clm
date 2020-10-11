<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2020 CLM Team  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_ecfv2_import() {
	clm_core::$load->load_css("ecfv2_import");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_js("ecfv2_import");
	$lang = clm_core::$lang->ecfv2_import;
	echo $lang->infotext1 . "<br/>" . $lang->infotext2;
?>
<div class="clm_view_ecfv2_import_div">
<?php $fix = clm_core::$api->view_units_form("",clm_core::$db->config()->lv); echo $fix[2]; // array dereferencing fix php 5.3 ?>
<button type="button" class="clm_button" onclick="clm_ecfv2_import_club(this)" title="<?php echo $lang->clubUpdateText; ?>"><?php echo $lang->clubUpdate; ?></button>
<button type="button" class="clm_button" onclick="clm_ecfv2_import_player(this)" title="<?php echo $lang->normalUpdateText; ?>"><?php echo $lang->normalUpdate; ?></button>
</div>

<div class="clm_view_ecfv2_import_update" style="display:none;">

</div>

<?php
} 
?>
