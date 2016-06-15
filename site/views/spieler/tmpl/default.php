<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');
//JHtml::_('behavior.tooltip', '.CLMTooltip', $params);
JHtml::_('behavior.tooltip', '.CLMTooltip');

// Variblen aus URL holen
$sid 			= JRequest::getInt('saison','1');
$lid			= JRequest::getInt('liga','1'); 
$liga 			= JRequest::getInt( 'liga', '1' );
$tln 			= JRequest::getInt('tlnr');
$itemid 		= JRequest::getInt('Itemid','1');
$zps			= JRequest::getVar('zps');
$mgl			= JRequest::getInt('mglnr');
$PKZ			= JRequest::getInt('PKZ');

$erg 			= CLMModelSpieler::getCLMLink();

$spieler		= $this->spieler;
$runden			= $this->runden;
$saisons	 	= $this->saisons;
$vereinsliste 	= $this->vereinsliste;
$spielerliste 	= $this->spielerliste;
$ex = 0;

if (isset($spieler[0]->Spielername)){ 
if ($spieler[0]->dsb_datum  > 0) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_RUN').' '.utf8_decode(JText::_('ON_DAY')).' '.JHTML::_('date',  $spieler[0]->dsb_datum, JText::_('DATE_FORMAT_CLM_F')); 
if (($spieler[0]->dsb_datum == 0) || (!isset($spieler))) $hint_dwzdsb = JText::_('DWZ_DSB_COMMENT_UNCLEAR');  
}
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

echo "<div id=\"clm\"><div id=\"spieler\">";
	
// Browsertitelzeile setzen
$doc =JFactory::getDocument();
if (isset($spieler[0]->Spielername)){ 
	$daten['title'] = $spieler[0]->Spielername;
	if (isset($spieler[0]->Vereinname)){ 
		$daten['title'] .= ' - '.$spieler[0]->Vereinname; }
	$doc->setTitle($daten['title']);
} else {
$daten['title'] = JText::_('PLAYER_UNKNOWN_TITLE');
$doc->setTitle(JText::_('PLAYER_UNKNOWN_TITLE'));
}

// Konfigurationsparameter auslesen
$config = clm_core::$db->config();
$countryversion = $config->countryversion;
?>

<script language="JavaScript">
<!-- Vereinsliste
function goto(form) { var index=form.select.selectedIndex
if (form.select.options[index].value != "0") {
location=form.select.options[index].value;}}
//-->
</script>

<div class="clmbox">
        <span class="left">
            <form name="form1">
                <select name="select" onchange="goto(this.form)" class="selectteam">
                <?php  $cnt = 0;   foreach ($spielerliste as $spielerliste) { $cnt++;?>
				  <?php if ($countryversion =="de") { ?>
                     <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $spielerliste->ZPS; ?>&mglnr=<?php echo $spielerliste->Mgl_Nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                    <?php if ($spielerliste->Mgl_Nr == $mgl) { echo 'selected="selected"'; } ?>><?php echo $spielerliste->Spielername; ?></option>
				  <?php } else { ?>
                     <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $spielerliste->ZPS; ?>&PKZ=<?php echo $spielerliste->PKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                    <?php if ($spielerliste->PKZ == $PKZ) { echo 'selected="selected"'; } ?>><?php echo $spielerliste->Spielername; ?></option>
                  <?php } ?>
                <?php } ?>
                </select>
            </form>
        </span>
        
        <span class="right">
        	<form name="form1">
            	<select name="select" onchange="goto(this.form)" class="selectteam">
                	<?php foreach ($saisons as $saisons) { ?>
                    	<option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=spieler&saison=<?php echo $saisons->id; ?>&zps=<?php echo $zps; ?>&mglnr=<?php echo $mgl; ?>&PKZ=<?php echo $PKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                        <?php if ($saisons->id == $sid) { echo 'selected="selected"'; } ?>><?php echo $saisons->name; ?> </option>
                    <?php } ?>
                </select>
            </form>
        </span>
                    
        <span class="right">
            <form name="form1">
                <select name="select" onchange="goto(this.form)" class="selectteam">
                <?php  $cnt = 0;   foreach ($vereinsliste as $vereinsliste) { $cnt++;?>
                     <option value="<?php echo JURI::base(); ?>index.php?option=com_clm&view=dwz&saison=<?php echo $sid; ?>&zps=<?php echo $vereinsliste->zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"
                    <?php if ($vereinsliste->zps == $zps) { echo 'selected="selected"'; } ?>><?php echo $vereinsliste->name; ?></option>
                <?php } ?>
                </select>
            </form>
        </span>
        
    <div class="clear"></div>
    
</div>
<br>
<?php
// Überprüfen ob Spieler existiert
if (!isset($spieler[0]->Spielername)){ 
echo CLMContent::clmWarning(JText::_('PLAYER_UNKNOWN'))."<br>";
}
else {  ?>

<div class="componentheading"><?php echo $spieler[0]->Spielername; ?><?php if (isset($spieler[0]->Vereinname)) echo ' - '.$spieler[0]->Vereinname; ?></div>

<!-- /// Spielerdetails /// -->
<br>

<div class="desc">
<table cellpadding="0" cellspacing="0" class="details">
  <tr>
  
    <!-- Erste Spalte -->
    <td valign="top" class="det_coltop1">
    	<table cellpadding="0" cellspacing="0" class="details">
            <tr>
            <td class="det_col1"><?php echo JText::_('PLAYER_NAME') ?></td>
            <td class="det_col2"><?php echo $spieler[0]->Spielername; ?></td>
            </tr>
            <tr>
          	<?php if ($countryversion =="de") { ?>
				<td class="det_col1"><?php echo JText::_('PLAYER_DWZ') ?></td>
            <?php } else { ?>
				<td class="det_col1"><?php echo JText::_('PLAYER_GRADE') ?></td>
            <?php } ?>			
        	<?php if ($countryversion =="out") { ?>
				<?php  $mgl4 = ''.$mgl; while (strlen($mgl4) < 4) { $mgl4 = '0'.$mgl4; } ?>
				<td class="det_col2"><a href="http://schachbund.de/spieler.html?zps=<?php echo $zps; ?>-<?php echo $mgl4; ?>" target="_blank"><?php echo $spieler[0]->dsbDWZ; ?></a> - <?php echo $spieler[0]->DWZ_Index; ?></td>
            <?php } else { ?>
				<td class="det_col2"><?php echo $spieler[0]->dsbDWZ; ?></td>
            <?php } ?>			
            </tr>
            <tr>
            <td class="det_col1"><?php if ($spieler[0]->FIDE_ELO > 0) { ?><?php echo JText::_('PLAYER_ELO') ?><?php } ?></td>
            <td class="det_col2"><a href="http://ratings.fide.com/card.phtml?event=<?php echo $spieler[0]->FIDE_ID;?>" target="_blank"><?php if ($spieler[0]->FIDE_ELO > 0) { ?><?php echo $spieler[0]->FIDE_ELO; ?><?php } ?></a></td>
        	</tr>
        </table>
    </td>
    <!-- Erste Spalte Ende -->
    
    <!-- Zweite Spalte -->
    <td valign="top" class="det_coltop2">
    	<table cellpadding="0" cellspacing="0" class="details">
            <tr>
            <td class="det_col3"><?php echo JText::_('PLAYER_CLUB') ?></td>
            <?php if (isset($spieler[0]->Vereinname)) { ?>
				<td class="det_col4"><a href="index.php?option=com_clm&view=verein&saison=<?php echo $sid; ?>&zps=<?php echo $zps; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $spieler[0]->Vereinname; ?></a></td>
            <?php } ?>
            </tr>
            <tr>
            <td class="det_col3" valign="top"><?php echo JText::_('PLAYER_TEAMS') ?></td>
            <td class="det_col4" valign="top">
                <?php if (count($erg) > 0) {
                $c = 0;
            foreach ($erg as $erg) {
                if ( $c == 0 ) { ?>
                <ul><li><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $erg->lid; ?>&tlnr=<?php echo $erg->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $erg->name; ?></a> - <a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $erg->lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $erg->liga_name; ?><br></a></li>  
                <?php $c++; } else  {?>
        <li><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $erg->lid; ?>&tlnr=<?php echo $erg->tln_nr; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $erg->name; ?></a>
             - <a href="index.php?option=com_clm&view=rangliste&saison=<?php echo $sid; ?>&liga=<?php echo $erg->lid; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $erg->liga_name; ?><br></a></li><?php }} ?></ul>
            <?php } ?>
            </td>
        	</tr>
        </table>
	</td>
    <!-- Zweite Spalte Ende -->
    
  </tr>
</table>
</div>

<!-- /// gespielte Partien /// -->
<div class="title"><?php echo JText::_('PLAYER_GAMES') ?></div>

<?php if (!$runden ) {
echo CLMContent::clmWarning(JText::_('PLAYER_NO_GAMES'))."<br>";
} 
else { $sum_ea = 0; $sum_punkte = 0; $sum_partien = 0; $ex = 0; ?>
    <table cellpadding="0" cellspacing="0" class="spielerverlauf">
    
    <tr>
        <th class="gsp"><?php echo JText::_('PLAYER_ROUND') ?></th>
        <th class="gsp"><?php echo JText::_('PLAYER_LOCATION') ?></th>
        <th class="gsp"><?php echo JText::_('PLAYER_BOARD') ?></th>
        <th><?php echo JText::_('PLAYER_OPONENT') ?></th>
        <th class="gsp"><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('PLAYER_RATING') ?></a></th>
        <th><?php echo JText::_('PLAYER_TEAM') ?></th>
        <th class="gsp2"><?php echo JText::_('DWZ_WE').' &sup1;'; ?></th>
        <th class="gsp2"><?php echo JText::_('PLAYER_RESULT') ?></th>
    </tr>
    <?php 
	$lid = -1;
	foreach ($runden as $runden) {
		if($lid!=$runden->lid) {
			echo "<tr><td class='noBorder' colspan='8'></td></tr><tr><td class='anfang' colspan='8'>".$runden->league."</td></tr>";
			$lid=$runden->lid;
		}
 ?> 
    <tr>
        <td class="gsp"><a href="index.php?option=com_clm&view=runde&saison=<?php echo $sid; ?>&liga=<?php echo $runden->lid; ?>&runde=<?php echo $runden->runde; ?>&dg=<?php echo $runden->dg; ?>"><?php echo $runden->runde ?></a></td>
    <?php if ($runden->heim > 0) { ?>
        <td class="gsp"><?php echo JText::_('PLAYER_HOME') ?></td>
    <?php } else { ?>
        <td class="gsp"><?php echo JText::_('PLAYER_AWAY') ?></td>
    <?php } ?>
        <td class="gsp"><?php echo $runden->brett; ?></td>
        <td align="left"><?php if($runden->gzps=="ZZZZZ"){ ?>N.N.<?php } else { ?><a href="index.php?option=com_clm&view=spieler&saison=<?php echo $sid; ?>&zps=<?php echo $runden->gzps; ?>&mglnr=<?php echo $runden->Mgl_Nr; ?>&PKZ=<?php echo $runden->gPKZ; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $runden->Spielername ?></a><?php } ?></td>
       <?php if ($runden->start_dwz < 1) {    // start_dwz des gegners
			$runden->start_dwz = $runden->DWZ;
        } 
        if ($spieler[0]->start_dwz < 1) {    // start_dwz des spielers
			$spieler[0]->start_dwz = $spieler[0]->dsbDWZ;
        } ?>
        <td class="gsp2"><?php echo $runden->start_dwz ?></td>
        <td align="left"><a href="index.php?option=com_clm&view=mannschaft&saison=<?php echo $sid; ?>&liga=<?php echo $runden->lid; ?>&tlnr=<?php echo $runden->tln; ?><?php if ($itemid <>'') { echo "&Itemid=".$itemid; } ?>"><?php echo $runden->name ?></a></td>
    <?php	
		//$ea = clm_class_dwz_rechner::P($spieler[0]->dsbDWZ, $runden->DWZ);
		$ea = clm_class_dwz_rechner::P($spieler[0]->start_dwz, $runden->start_dwz);
        if ($runden->kampflos == 0) { $sum_ea = $sum_ea + $ea; }
    ?>
        <td class="gsp2"><?php if ($runden->kampflos == 0) { echo $ea;} else { echo "-";} ?></td>
            
    <?php 
    
		$search = array ('.0', '0.5');
		$replace = array ('', '&frac12;');
		$punkte_text = str_replace ($search, $replace, $runden->punkte);
		
		if ($runden->kampflos == 0) {
			$erg_text = $punkte_text;
    			$sum_partien++;
		}
		else {
			if ($config->fe_display_lose_by_default == 0) {
				if($runden->punkte == 0) {
					$erg_text = "-";
				} else {
					$erg_text = "+";
				}
			} elseif ($config->fe_display_lose_by_default == 1) {
				$erg_text =  $punkte_text.' (kl)';
			} else {
				$erg_text = $punkte_text;
			}
		}
             
		if ($runden->weiss > 0) {
           	?>
            <td class="gsp2_w"><?php echo $erg_text; ?></td>
            <?php
		}
		else {
            ?>
            <td class="gsp2_b"><?php echo $erg_text; ?></td> 
    		<?php
    	}
    ?>
    </tr>
    <?php $sum_punkte=$sum_punkte + $runden->punkte; 
         } ?>
    <tr><td class='noBorder' colspan='8'></td></tr>
    <tr class="ende">
        <td colspan="6"><?php echo JText::_('PLAYER_SUM') ?></td>
        <?php  $Pkt = explode (".", $sum_punkte); ?>
        <td align="center"><?php echo $sum_ea; ?></td>
        <td align="center"><?php echo $sum_punkte.'  /  '.$sum_partien;?></td>
    </tr>
    <!-- -->
    
    </table>
<div class="hint"><?php echo JText::_('PLAYER_EA_HINT') ?></div>
<br>

<?php }
	// DWZ Parameter auslesen
	//$config = clm_core::$db->config();
	//$dwz	= $config->dwz_wertung;
?>
<!-- /// DWZ Auswertung /// -->
<br>
<div class="title"><?php echo JText::_('PLAYER_DWZ_EVAL') ?></div>

<?php //if($dwz =="0") {
// Einzelauswertung der Ligen
	foreach ($spieler as $spielerl) {
	if ($spielerl->Punkte == 0 AND $spielerl->Partien == 0) {
	echo CLMContent::clmWarning($spielerl->liga_name . '-' . JText::_('PLAYER_NO_EVAL_GAMES')).'<br>';
	} else { ?>
    <table cellpadding="0" cellspacing="0" class="spielerdwzneu">
    <tr>
        <th class="anfang" colspan="9"><?php echo $spielerl->liga_name; ?></th>
    </tr>
    <tr>
        <td><a title="<?php echo $hint_dwzdsb; ?>" class="CLMTooltip"><?php echo JText::_('PLAYER_RATING_OLD') ?></a></td>
		<td><?php echo JText::_('PLAYER_W') ?></td>
        <td><?php echo JText::_('PLAYER_WE') ?></td>
        <td><?php echo JText::_('PLAYER_EF') ?></td>
        <td><?php echo JText::_('PLAYER_PERFORMANCE') ?></td>
        <td><?php echo JText::_('PLAYER_LEVEL') ?></td>
        <td><?php echo JText::_('PLAYER_POINTS') ?></td>
        <td><?php echo JText::_('PLAYER_DWZ_NEW') ?></td>
        <td><?php echo JText::_('PLAYER_DIFFERENZ') ?></td>
	</tr>
    
    <tr>
       <?php if ($spielerl->start_dwz < 1) { 
			$spielerl->start_dwz = $spielerl->dsbDWZ;
			$spielerl->start_I0  = $spielerl->DWZ_Index;
        } ?>
		<td><?php echo $spielerl->start_dwz.'-'.$spielerl->start_I0;?></td>
        <td><?php echo $spielerl->Punkte;?></td>
        <td><?php echo number_format($spielerl->We,2);?></td>
        <td><?php echo $spielerl->EFaktor;?></td>
        <td><?php  if($spielerl->Punkte == $spielerl->Partien AND $spielerl->Niveau == $spielerl->Leistung AND $spielerl->Punkte != 0) { echo 667+$spielerl->Leistung.' &sup2;'; $ex=1;$pt=$spielerl->liga_name;} else { if ( $spielerl->Leistung == 0 ) { echo "-";} else { echo $spielerl->Leistung; } } ?></td>
        <td><?php echo $spielerl->Niveau;?></td>
        
        <?php  $Pkt = explode (".", $spielerl->Punkte);
            if ($Pkt[1] != "0") {
                if ($Pkt[0] != "0") { ?>
                <td><?php echo $Pkt[0].'&frac12  /  '.$spielerl->Partien;?></td>
                <?php } else { ?>
                <td><?php echo '&frac12  /  '.$spielerl->Partien;?></td>
                <?php }}
            else { ?>
        <td><?php echo $Pkt[0].'  /  '.$spielerl->Partien;?></td>
         <?php } ?>
        <?php if ($spielerl->DWZ > 0) { ?>
        <td><?php echo $spielerl->DWZ.'-'.$spielerl->I0;?></td>
        <?php }
           if ($spielerl->start_dwz >0 AND $spielerl->DWZ == 0) { ?>
                <td><?php echo $spieler->start_dwz.'-'.$spieler->start_I0;?></td>
                <?php }
            if ($spielerl->start_dwz  == 0 AND $spielerl->DWZ == 0) { ?>
                <td><?php echo JText::_('PLAYER_REST') ?></td>
                <?php } ?>
        <td><?php $dwzdifferenz = $spielerl->DWZ - $spielerl->start_dwz; 
		 if ( $dwzdifferenz > 0 ) { echo "+&nbsp;" . $dwzdifferenz; } else { echo $dwzdifferenz; } ?></td>
    </tr>
    </table>
<?php 
}}}
?>

<?php 
if (isset($spieler[0]->Spielername)){ 
echo '<div class="hint">'.$hint_dwzdsb.'</div>'; 
}
?>

<?php require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php');

echo '<div class="clr"></div>';
echo '</div>';
echo '</div>';

?>
