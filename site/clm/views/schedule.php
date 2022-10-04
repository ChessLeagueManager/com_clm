<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2022 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
function clm_view_schedule($out) {
	clm_core::$load->load_css("schedule");
	clm_core::$load->load_css("buttons");
	clm_core::$load->load_css("notification");
	$lang = clm_core::$lang->schedule;
	clm_core::$cms->setTitle(html_entity_decode($lang->title." ".$out["club"][0]->name));

	// Variablen initialisieren
	$paar 		= $out["paar"];
	$club 		= $out["club"];
	$club_list 		= $out["club_list"];

	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
	
	$itemid 	= clm_core::$load->request_int('Itemid',0);
?>
<Script language="JavaScript">
<!-- Vereinsliste
function goto(form) { var index=form.select.selectedIndex
if (form.select.options[index].value != "0") {
location=form.select.options[index].value;}}
//-->
</SCRIPT>
	<div class="clmbox">
<?php	
	if (isset($paar[0])) echo clm_core::$load->create_link($lang->title_club_details, 'verein', array('saison' => $paar[0]->sid, 'zps' => $club[0]->zps))
						.' | '. clm_core::$load->create_link($lang->title_members, 'dwz', array('saison' => $paar[0]->sid, 'zps' => $club[0]->zps))
						.' | '. clm_core::$load->create_link($lang->title_clubs, 'vereinsliste', array('saison' => $paar[0]->sid));
?>	
	<span style="float:right;">
    <form name="form1">
        <select name="select" onchange="goto(this.form)" class="selectteam">
        <option value=""><?php echo JText::_('CLUB_SELECTTEAM') ?></option>
        <?php  $cnt = 0;   foreach ($club_list as $verein) { $cnt++; ?>
         <option value="<?php echo clm_core::$load->create_valuelink('schedule', array('season' => $verein->sid, 'club' => $verein->zps)); ?>"
        <?php if ($verein->zps == $club[0]->zps) { echo 'selected="selected"'; } ?>><?php echo $verein->name; ?></option>
        <?php } ?>
        </select>
    </form>
</span> 
</div> 
<?php	echo '<br />';
		
	echo '<table style="width:100%"><tr><th><h4>'.$club[0]->name." - ".$club[0]->season_name; 
	echo '</h4></th><th style="align:right">';
	if (isset($paar[0])) echo clm_core::$load->create_link_pdf('schedule', $lang->pdf, array('layout' => 'schedule', 'season' => $paar[0]->sid, 'liga' => $paar[0]->lid, 'club' => $club[0]->zps));
	if (isset($paar[0])) echo clm_core::$load->create_link_xls('schedule', $lang->csv, array('layout' => 'schedule', 'season' => $paar[0]->sid, 'liga' => $paar[0]->lid, 'club' => $club[0]->zps));
	echo "</th></tr></table>";
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

<?php	$x = 0;
		foreach ($paar as $paar1) { ?>
<?php //echo "<br>paar1:"; var_dump($paar1);
	$x++;
	if ($x%2 != 0) { $zeilenr = 'zeile1'; }
	else { $zeilenr = 'zeile2'; } ?>
<div class="flex <?php echo $zeilenr; ?>">
<div class="element date"><?php 
							if ($paar1->rdate > "1970-01-01") { echo clm_core::$cms->showDate($paar1->rdate, "d M Y"); 
								if ($paar1->rtime != "00:00:00" AND $paar1->rtime != "24:00:00") echo "<br>".substr($paar1->rtime,0,5); }
							?></div>
<div class="element league">
    <a href="index.php?option=com_clm&view=paarungsliste&saison=<?php echo $paar1->sid; ?>&liga=<?php echo $paar1->lid; ?><?php if ($itemid <> 0) { echo "&Itemid=".$itemid; } ?>">
	<?php echo $paar1->lname; ?></a></div>
<div class="element dg"><?php echo $paar1->dg; ?></div>
<div class="element round"><?php echo $paar1->runde; ?></div>
<div class="element home">
    <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $paar1->sid; ?>&liga=<?php echo $paar1->lid; ?>&tlnr=<?php echo $paar1->htln; ?><?php if ($itemid <> 0) { echo "&Itemid=".$itemid; } ?>">
	<?php echo $paar1->hname; ?></a></div>
<div class="element result"><?php echo $paar1->brettpunkte." : ".$paar1->gbrettpunkte; ?></div>
<div class="element guest">
    <a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $paar1->sid; ?>&liga=<?php echo $paar1->lid; ?>&tlnr=<?php echo $paar1->gtln; ?><?php if ($itemid <> 0) { echo "&Itemid=".$itemid; } ?>">
	<?php echo $paar1->gname; ?></a></div>
</div>

<?php } 

echo '<div class="button_container">';
echo '<button type="button" onclick="javascript:history.back(1);" class="clm_button button_back">'.$lang->button_back.'</button>';
echo '</div><div class="space"></div>';
 } ?>
