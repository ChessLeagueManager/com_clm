<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_table($lang,$destination,$count,$buttons_name=array(),$buttons_title=array(),$buttons_value=array(),$buttons_class=array(),$select=array(),$order='',$orderType='') {
clm_core::$load->load_css("DataTables");
clm_core::$load->load_css("buttons");
clm_core::$load->load_css("notification");
clm_core::$load->load_js("is");
clm_core::$load->load_js("table");
$lang = clm_core::$lang->$lang;
$langT = clm_core::$lang->table;
?>
			<div class="special-table">
				<div class="clm_view_notification"></div>
				<div class="special-table-inner">
				<div class="clm_buttons">
				<div class="clm_table_title <?php echo $lang->title_class; ?>"><p><?php echo $lang->title; ?></p></div>
<?php
for($i=0;$i<count($buttons_name);$i++) {
	echo "<button type='button' title='".$buttons_title[$i]."' value='".$buttons_value[$i]."' class='".$buttons_class[$i]."'><span></span>".$buttons_name[$i]."</button>";
}
?>
				</div>
				<div class="clm_table_filter">
				<button type="button" class="clm_button clm_table_check clm_left" ><?php echo $langT->check; ?></button>
				<div class="dataTables_category clm_left"><b><?php echo $lang->filter; ?> </b><input class="clm_table_filter_box" type="search"></div><?php
					foreach($select as $value) {
						echo '<div class="dataTables_category"><select class="special-table-custom-input" name="'.$value[0].'">';
							foreach($value[1] as $key => $valueb) {
								echo '<option value="'.$key.'"'.(count($value)==3 && $key==$value[2] ? ' selected="selected"': "").'>'.$valueb.'</option>';
							}
						echo '</select></div>';
					}
				?></div>
				<table class="special-table-main display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<?php for($i=0;$i<$count;$i++) { 
								$column = "column".$i;
								$columnTip = "columnTip".$i;
								echo "<th title='".$lang->$columnTip."'>".$lang->$column."</th>";
							} ?>
						</tr>
					</thead>
				</table>
				</div>
				<div class="destination" style="display:none;"><?php echo htmlentities($destination, ENT_QUOTES, "UTF-8"); ?></div>
				<div class="order" style="display:none;"><?php echo htmlentities('[[ '.$order.', "'.$orderType.'" ]]', ENT_QUOTES, "UTF-8"); ?></div>
			</div>
<?php } ?>
