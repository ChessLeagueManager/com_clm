<?php
function clm_view_ecf_import() {
	$sid = clm_core::$access->getSeason();
	clm_core::$load->load_css("ecf_import");
	clm_core::$load->load_js("ecf_import");
	$lang = clm_core::$lang->ecf_import;
	echo $lang->infotext1 . "<br/>" . $lang->infotext2 . "<br/>" . $lang->infotext3 . "<br/>" . $lang->infotext4;
	
	$config		= clm_core::$db->config();
	$lv	= $config->lv;
	$sql = " SELECT * FROM #__clm_dwz_verbaende WHERE Verband = '".$lv."'";
	$verband = clm_core::$db->loadObjectList($sql);	
	if (count($verband) > 0) echo "<br>CLM used by ".$verband[0]->Verband." ".$verband[0]->Verbandname;
?>
<div class="clm_view_ecf_import_div">
<input type="file" onchange="clm_ecf_import_check(this)" name="file" />
<button type="button" onclick="clm_ecf_import_org(this,0,0)" title="<?php echo $lang->orgUpdateText; ?>" disabled="disabled"><?php echo $lang->orgUpdate; ?></button>
<button type="button" onclick="clm_ecf_import_club(this,0,0)" title="<?php echo $lang->clubUpdateText; ?>" disabled="disabled"><?php echo $lang->clubUpdate; ?></button>
<button type="button" onclick="clm_ecf_import_player(this,0,1,0)" title="<?php echo $lang->normalUpdateText; ?>" disabled="disabled"><?php echo $lang->specialUpdate; ?></button>
</div>

<div class="clm_view_ecf_import_update" style="display:none;">
</div>

<?php
} 
?>
