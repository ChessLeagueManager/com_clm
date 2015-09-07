<?php
function clm_view_dsb_import() {
	clm_core::$load->load_css("dsb_import");
	clm_core::$load->load_js("dsb_import");
	$lang = clm_core::$lang->dsb_import;
	echo $lang->infotext1 . "<br/>" . $lang->infotext2 . "<br/>" . $lang->infotext3 . "<br/>" . $lang->infotext4;
?>
<div class="clm_view_dsb_import_div">
<input type="file" onchange="clm_dsb_import_check(this)" name="file" />
<button type="button" onclick="clm_dsb_import_club(this,0,0)" title="<?php echo $lang->clubUpdateText; ?>" disabled="disabled"><?php echo $lang->clubUpdate; ?></button>
<button type="button" onclick="clm_dsb_import_player(this,0,0,0)" title="<?php echo $lang->normalUpdateText; ?>" disabled="disabled"><?php echo $lang->normalUpdate; ?></button>
<button type="button" onclick="clm_dsb_import_player(this,0,1,0)" title="<?php echo $lang->normalUpdateText; ?>" disabled="disabled"><?php echo $lang->specialUpdate; ?></button>
</div>

<div class="clm_view_dsb_import_update" style="display:none;">
</div>

<?php
} 
?>
