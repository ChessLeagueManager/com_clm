<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/


defined('_JEXEC') or die('Restricted access'); 

JRequest::checkToken() or die( 'Invalid Token' );

	$mainframe	= JFactory::getApplication();

$user =& JFactory::getUser();
if (!$user->get('id')) {
	$msg = JText::_('RESULT_DATA_LOGIN');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
		}
else {

// Stylesheet laden

require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

	$db	=&JFactory::getDBO();
	$sid 	= JRequest::getVar('sid');
	$lid 	= JRequest::getVar('lid');
	$rnd 	= JRequest::getVar('runde');
	$paarung= JRequest::getVar('paarung');
	$stamm 	= JRequest::getVar('stamm');
	$htln 	= JRequest::getVar('htln');
	$gtln 	= JRequest::getVar('gtln');
	$dg 	= JRequest::getVar('dg');
	$tln_nr	= JRequest::getInt('tln');
	$itemid	= JRequest::getInt('Itemid');
	$mail 	= JRequest::getVar('mail');
	
	$heim_name	= JRequest::getVar('heimname');
	$gast_name	= JRequest::getVar('gastname');
	$liga_name	= JRequest::getVar('liganame');
	$datum 		= JRequest::getVar('datum');
	$ko_decision = JRequest::getVar( 'ko_decision'); //mtmt
	$comment = JRequest::getVar( 'comment'); //mtmt
 
	// Variablen initialisieren
	$finish		= $this->finish;
	$liga 		= $this->liga;
	$mdt = $finish[($rnd+(($dg-1)*$liga[0]->runden)-1)]->deadlineday.' ';
	$mdt .= $finish[($rnd+(($dg-1)*$liga[0]->runden)-1)]->deadlinetime; 
	$mdt1 = substr($finish[($rnd+(($dg-1)*$liga[0]->runden)-1)]->deadlinetime,0,5).' Uhr';
	
	// Datum der Meldung
	$now = date('Y-m-d H:i:s');	

	// Punktemodus aus #__clm_liga holen
	$query = " SELECT a.stamm, a.sieg, a.remis, a.nieder, a.antritt, a.runden_modus, a.durchgang, a.runden, "
		." a.man_sieg, a.man_remis, a.man_nieder, a.man_antritt, a.sieg_bed "
		." FROM #__clm_liga as a"
		." WHERE a.id = ".$lid
		;
	$db->setQuery($query);
	$liga = $db->loadObjectList();
		$stamm 		= $liga[0]->stamm;
		$sieg_bed	= $liga[0]->sieg_bed;
		$sieg 		= $liga[0]->sieg;
		$remis 		= $liga[0]->remis;
		$nieder		= $liga[0]->nieder;
		$antritt	= $liga[0]->antritt;
		$durchgang	= $liga[0]->durchgang;

	$hmpunkte = "";
	$gmpunkte = "";
		
//Test Spieler mehrfach 
	for ($i=0; $i<$stamm; $i++) {
		$heim_i 		= JRequest::getVar('heim'.($i+1));
		$hspiel_i 	= explode(";", $heim_i);
		$gast_i 		= JRequest::getVar('gast'.($i+1));
		$gspiel_i 	= explode(";", $gast_i);
		for ($j=($i+1); $j<$stamm; $j++) {
			$heim_j 		= JRequest::getVar('heim'.($j+1));
			$gast_j 		= JRequest::getVar('gast'.($j+1));
			if ($hspiel_i[2] !="ZZZZZ" AND $heim_i == $heim_j ) {
				$msg = JText::_('RESULT_DATA_HOME_DOUBLE').$hspiel_i[1].' '.$hspiel_i[2].'-'.$hspiel_i[0];
				$link = "index.php?option=com_clm&view=meldung&saison=".$sid."&liga=".$lid."&runde=".$rnd."&tln=".$tln_nr."&paar=".$paarung."&dg=".$dg."&Itemid=".$itemid;
				$mainframe->redirect( $link, $msg );
			}
			if ($gspiel_i[2] !="ZZZZZ" AND $gast_i == $gast_j ) {
				$msg = JText::_('RESULT_DATA_GUEST_DOUBLE').$gspiel_i[1].' '.$gspiel_i[2].'-'.$gspiel_i[0];
				$link = "index.php?option=com_clm&view=meldung&saison=".$sid."&liga=".$lid."&runde=".$rnd."&tln=".$tln_nr."&paar=".$paarung."&dg=".$dg."&Itemid=".$itemid;
				$mainframe->redirect( $link, $msg );
			}
		}
	}

for ($y=1; $y< (1+$stamm) ; $y++){
	$gesamt_ergebnis	= JRequest::getVar( 'ergebnis'.$y);
	$data_ergebnis 		= explode(";", $gesamt_ergebnis);
	$ergebnis		= $data_ergebnis[0];
	
	if ($ergebnis == 0)
		{ 	$erg_h = $nieder+$antritt;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 1)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = $nieder+$antritt;
		}
	if ($ergebnis == 2)
		{ 	$erg_h = $remis+$antritt;
			$erg_g = $remis+$antritt;
		}
	if ($ergebnis == 3)
		{ 	$erg_h = $antritt;
			$erg_g = $antritt;
		}
	if ($ergebnis == 4)
		{ 	$erg_h = 0;
			$erg_g = $sieg+$antritt;
		}
	if ($ergebnis == 5)
		{ 	$erg_h = $sieg+$antritt;
			$erg_g = 0;
		}
	if ($ergebnis == 6)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 7)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	if ($ergebnis == 8)
		{ 	$erg_h = 0;
			$erg_g = 0;
		}
	$hmpunkte = $hmpunkte + $erg_h;
	$gmpunkte = $gmpunkte + $erg_g;
	}
?>
<div id="clm">
<div id="check">
<!--- <div class="componentheading"></div> --->
<h4><?php echo $liga_name.', '.JText::_('RESULT_DATA_ROUND').' '.$rnd;
if ($durchgang > 1) { echo " (".$dg.". ".JText::_('RESULT_DATA_DG').")";}
if (!isset($datum) OR $datum == '0000-00-00') {  }
else { echo ', am '.JHTML::_('date', $datum, JText::_('DATE_FORMAT_CLM')); }?>
</h4>

<div id="desc"><h4><?php echo JText::_('RESULT_DATA_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('RESULT_DATA_CHECK_NOTE1') ?></li>
<li><?php echo JText::_('RESULT_DATA_CHECK_NOTE2') ?></li>
	<?php if (substr($mdt,0,4) == '0000' OR $mdt < $now) { ?>
		<li><?php echo JText::_('RESULT_DATA_CHECK_NOTE3') ?></li>
	<?php } else { ?>
		<li><?php echo JText::_('RESULT_DATA_CHECK_NOTE3A').JHTML::_('date', $mdt, JText::_('DATE_FORMAT_CLM')).' '.$mdt1.JText::_('RESULT_DATA_CHECK_NOTE3B') ?></li>
	<?php } ?>
<?php if ($mail == 1) { ?>
<li><?php echo JText::_('RESULT_DATA_CHECK_NOTE4') ?></li>
<?php } ?>
</ol>
</div>
<br>

<form action="index.php?option=com_clm&amp;view=meldung&amp;layout=sent" method="post" name="adminForm">
<center>
<table cellpadding="0" cellspacing="0" class="meldung">

	<tr>
		<th class="anfang"><?php echo JText::_('RESULT_DATA_BOARD') ?></th>
		<th class="anfang"><?php echo JText::_('RESULT_DATA_HOME') ?></th>
		<th class="anfang"> <?php echo JText::_('RESULT_DATA_SEPARATOR') ?> </th>
		<th class="anfang"><?php echo JText::_('RESULT_DATA_GUEST') ?></th>
		<th class="anfang"><?php echo JText::_('RESULT_DATA_RESULT') ?></th>
	</tr>

	<tr>
		<td></td>
		<td align="center"><?php echo $heim_name; ?></td>
		<td align="center"> - </td>
		<td align="center"><?php echo $gast_name; ?></td>
		<td align="center"><?php echo $hmpunkte.' : '.$gmpunkte; ?></td>
	</tr>
<?php	for ($i=0; $i<$stamm; $i++){

$heim 		= JRequest::getVar('heim'.($i+1));
$gast 		= JRequest::getVar('gast'.($i+1));
$ergebnis	= JRequest::getVar('ergebnis'.($i+1));
	if($heim !=""){
	$teil_heim 	= explode(";", $heim);
	$heim 		= $teil_heim[1];
	}
	if($gast !=""){
	$teil_gast 	= explode(";", $gast);
	$gast 		= $teil_gast[1];
	}
	if($ergebnis !=""){
	$teil_ergebnis 	= explode(";", $ergebnis);
	$ergebnis 	= $teil_ergebnis[1];
	}
?>
	<tr>
		<td align="center" class="key" nowrap="nowrap">
			<?php echo JText::_( '<b>'.($i+1).'</b>'); ?>
		</td>
		<td>
			<?php 	if($heim !=""){ echo $heim; ?>
			<input type="hidden" name="heim<?php echo $i+1; ?>" value="<?php echo $teil_heim[0].'-'.$teil_heim[2]; ?>" /><?php } ?>
		</td>
	<td align="center"> - </td>
		<td>
			<?php if($gast !=""){ echo $gast; ?>
			<input type="hidden" name="gast<?php echo $i+1; ?>" value="<?php echo $teil_gast[0].'-'.$teil_gast[2]; ?>" /><?php } ?>
		</td>
		<td align="center">
			<?php if($ergebnis !="NULL"){ echo $ergebnis; } ?>
			<input type="hidden" name="ergebnis<?php echo $i+1; ?>" value="<?php echo $teil_ergebnis[0]; ?>" />
		</td>
	</tr>
<?php } ?> 
	</table>
<br>
<?php if (($liga[0]->runden_modus == 4 OR $liga[0]->runden_modus == 5) AND ($hmpunkte == $gmpunkte)) { ?>
	<tr><td colspan ="6"><?php  if (isset($paar[$y])) {
									if ($paar[$y]->ko_decision == 1) {
										if ($paar[$y]->hrank > $paar[$y]->grank) echo JText::_('ROUND_DECISION_WP_HEIM'); 
										else echo JText::_('ROUND_DECISION_WP_GAST'); }
									if ($paar[$y]->ko_decision == 2) echo JText::_('ROUND_DECISION_BLITZ_HEIM');
									if ($paar[$y]->ko_decision == 3) echo JText::_('ROUND_DECISION_BLITZ_GAST'); 
									if ($paar[$y]->ko_decision == 4) echo JText::_('ROUND_DECISION_LOS_HEIM');
									if ($paar[$y]->ko_decision == 5) echo JText::_('ROUND_DECISION_LOS_GAST'); }?>
		</td></tr>
<?php }  ?>
<br>
<?php if ($liga[0]->runden_modus == 4 OR $liga[0]->runden_modus == 5) {    // KO System ?>	
	<div class="col width-60">
	<fieldset class="adminform">
	<legend><?php 
		echo JText::_( 'RESULTS_MT_KO_LEGEND' ); //" KO-System: Feinwertung ";
	 ?>
	</legend>
	<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="ko_decision"><?php echo JText::_( 'RESULTS_MT_KO_DECISION' ); ?></label>
			</td><td class="key" nowrap="nowrap">
			<select name="ko_decision" id="ko_decision" value="<?php echo $ko_decision; ?>" size="1">
			<!--<option>- wählen -</option>-->
			<option value="1" <?php if ($ko_decision == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BW' );?></option>
			<option value="2" <?php if ($ko_decision == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$heim_name;?></option>
			<option value="3" <?php if ($ko_decision == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$gast_name;?></option>
			<option value="4" <?php if ($ko_decision == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$heim_name;?></option>
			<option value="5" <?php if ($ko_decision == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$gast_name;?></option>
			</select>
			</td>
		</tr>
	</table>
	</fieldset>
	</div>		
<?php } ?> 

<?php // Konfigurationsparameter auslesen
	$config	= &JComponentHelper::getParams( 'com_clm' );
	$pcomment = $config->get('kommentarfeld',0);
	if (($pcomment == 1) OR ($pcomment == 2 AND ($liga[0]->runden_modus == 4 OR $liga[0]->runden_modus == 5))) {    // Kommentarfeld ?>			
	<div class="col width-60">
	  <fieldset class="adminform">
		<legend><?php echo JText::_( 'RESULTS_COMMENT_LEGEND' ); ?></legend>
		<table class="admintable">
		<tr>
			<td class="key" nowrap="nowrap">
			<label for="comment"><?php echo JText::_( 'RESULTS_COMMENT' ); ?></label>
			</td>
			<td class="inputbox" nowrap="nowrap" width="100%" valign="top" size="1">
			<textarea name="comment" id="comment" cols="30" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$comment);?></textarea>
			</td>
		</tr>
		</table>
	  </fieldset>	
	</div>	
<?php } ?> 

<input type="submit" value=" <?php echo JText::_('RESULT_DATA_BUTTON_SEND') ?> ">
		<input type="hidden" name="layout" value="sent" />
		<input type="hidden" name="view" value="meldung" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="sid" value="<?php echo $sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
		<input type="hidden" name="runde" value="<?php echo $rnd; ?>" />
		<input type="hidden" name="paarung" value="<?php echo $paarung; ?>" />
		<input type="hidden" name="dg" value="<?php echo $dg; ?>" />
		<input type="hidden" name="htln" value="<?php echo $htln; ?>" />
		<input type="hidden" name="gtln" value="<?php echo $gtln; ?>" />
		<input type="hidden" name="stamm" value="<?php echo $stamm; ?>" />
		<input type="hidden" name="ko_decision" value="<?php echo $ko_decision; ?>"  />
		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
<?php }

echo '</center><br></div></div>';
	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
?>