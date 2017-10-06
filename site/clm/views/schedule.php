<?php
function clm_view_schedule($out) {
	clm_core::$load->load_css("schedule");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_css("notification");
	//clm_core::$load->load_js("report");
	$lang = clm_core::$lang->schedule;
	//clm_core::$cms->setTitle($lang->title.' '.$out["club"][0]->name);
	clm_core::$cms->setTitle(html_entity_decode($lang->title." ".$out["club"][0]->name));

	// Variablen initialisieren
	$paar 		= $out["paar"];
	$club 		= $out["club"];
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;

	echo "<h4>".$club[0]->name." - ".$club[0]->season_name; /*.', '.$lang->round.' '.$runde;
	if ($liga[0]->durchgang > 1) {
		 echo " (".$dg.". ".$lang->dg.")";
	}
	if ($liga[0]->datum != '0000-00-00' && $liga[0]->datum != '1970-01-01' && $liga[0]->datum) { 
		//echo ', am '.	clm_core::$load->date_to_string($liga[0]->datum,false); 
		echo '  '.$lang->date_on.' '.clm_core::$cms->showDate($liga[0]->datum, $lang->date_format_clm_f);
	}
*/	echo "</h4>";

?>
<div class="flex title">
<div class="element date"><?php echo $lang->date; ?></div>
<div class="element league"><?php echo $lang->lname; ?></div>
<div class="element dg"><?php echo $lang->dg; ?></div>
<div class="element round"><?php echo $lang->round; ?></div>
<div class="element home"><?php echo $lang->home; ?></div>
<div class="element result"><?php echo $lang->result; ?></div>
<div class="element guest"><?php echo $lang->guest; ?></div>
</div>

<?php	//for ($i=0; $i<$liga[0]->stamm; $i++){ ?>
<?php	foreach ($paar as $paar1) { ?>
<?php //echo "<br>paar1:"; var_dump($paar1); ?>
<div class="flex">
<div class="element date"><?php 
							if ($paar1->rdate > "1970-01-01") { echo clm_core::$cms->showDate($paar1->rdate, "d M Y"); 
								if ($paar1->rtime != "00:00:00" AND $paar1->rtime != "24:00:00") echo "<br>".substr($paar1->rtime,0,5); }
							?></div>
<div class="element league"><?php echo $paar1->lname; ?></div>
<div class="element dg"><?php echo $paar1->dg; ?></div>
<div class="element round"><?php echo $paar1->runde; ?></div>
<div class="element home"><?php echo $paar1->hname; ?></div>
<div class="element result"><?php echo $paar1->brettpunkte." : ".$paar1->gbrettpunkte; ?></div>
<div class="element guest"><?php echo $paar1->gname; ?></div>
</div>

<?php } 

/*echo '<input type="hidden" class="liga" value="'.$out["input"]["liga"].'">';
echo '<input type="hidden" class="runde" value="'.$out["input"]["runde"].'">';
echo '<input type="hidden" class="dg" value="'.$out["input"]["dg"].'">';
echo '<input type="hidden" class="paar" value="'.$out["input"]["paar"].'">';
echo '<div class="clm_view_notification"><div class="notice"><span>' . $lang->data_needed . '</div></div>';
*/
echo '<div class="button_container">';
echo '<button type="button" onclick="javascript:history.back(1);" class="clm_button button_back">'.$lang->button_back.'</button>';
echo '</div><div class="space"></div>';
 } ?>
