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

// Variablen holen
$sid 		= JRequest::getInt('saison','1');
$lid 		= JRequest::getInt('liga');
$runde 		= JRequest::getInt('runde');
$paarung	= JRequest::getInt('paar');
$dg 		= JRequest::getInt('dg');
$tln_nr		= JRequest::getInt('tln');
$itemid		= JRequest::getInt('Itemid');

// Variablen initialisieren
$liga 		= $this->liga;
$paar 		= $this->paar;
$oldresult 	= $this->oldresult;
$heim 		= $this->heim;
$countheim 	= $this->countheim;
$gast 		= $this->gast;
$countgast 	= $this->countgast;
$ergebnis 	= $this->ergebnis;
$finish		= $this->finish;
//$meldung	= $this->meldung;

	$mainframe	= JFactory::getApplication();
	$option 	= JRequest::getCmd( 'option' );
	
// Datum der Meldung
$now = date('Y-m-d H:i:s'); 
// Stylesheet laden
require_once(JPATH_COMPONENT.DS.'includes'.DS.'css_path.php');

	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$conf_ergebnisse=$config->get('conf_ergebnisse',1);
	$meldung_verein	= $config->get('meldung_verein',1);
	$meldung_heim	= $config->get('meldung_heim',1);

if ($conf_ergebnisse != 1) {
	$msg = JText::_( 'RESULT_DATA_DISABLED');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
			}
	// Login Status prüfen
	$clmuser = $this->clmuser;
	$user 	=& JFactory::getUser();
	$jid	= $user->get('id');

if (!$jid) {
	$msg = JText::_( 'RESULT_DATA_LOGIN');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
			}
if ($clmuser[0]->published < 1) {
	$msg = JText::_( 'RESULT_DATA_ACCOUNT');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
				}

// Auf falsche Eingaben in der URL reagieren !
if ($clmuser[0]->zps != $paar[0]->hzps AND $clmuser[0]->zps != $paar[0]->gzps AND strpos($paar[0]->hsgzps,$clmuser[0]->zps) === false AND strpos($paar[0]->gsgzps,$clmuser[0]->zps) === false) {
	$link = "index.php?option=com_clm&view=info";
	$msg = JText::_( 'RESULT_DATA_FALSE' );
	$mainframe->redirect( $link, $msg );
				}

if ($paar[0]->gast_mf != $jid AND $paar[0]->heim_mf != $jid AND $meldung_verein == 0) {
	$msg = JText::_( 'RESULT_DATA_TEAM_LEADER');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
				}
if ($clmuser[0]->zps != $heim[0]->zps AND $meldung_heim == 0) {
	$msg = JText::_( 'RESULT_DATA_HOME');
	$link = "index.php?option=com_clm&view=info";
	$mainframe->redirect( $link, $msg );
				}

if (!isset($heim[0]->zps)) {
	$msg = JText::_( 'RESULT_DATA_NO_LIST_HOME');
	//$link = "index.php?option=com_clm&view=info";
	$link = "index.php?option=com_clm&view=runde&saison=".$sid."&liga=".$lid."&runde=".$runde."&dg=".$dg;
	$mainframe->redirect( $link, $msg );
				}

if (!isset($gast[0]->zps)) {
	$msg = JText::_( 'RESULT_DATA_NO_LIST_GUEST');
	//$link = "index.php?option=com_clm&view=info";
	$link = "index.php?option=com_clm&view=runde&saison=".$sid."&liga=".$lid."&runde=".$runde."&dg=".$dg;
	$mainframe->redirect( $link, $msg );
				}

if ($jid > 0 AND  $clmuser[0]->published > 0 ){
// Prüfen ob vorherige Runde gemeldet wurde und SL_OK
//if ($finish[($runde-1)]->meldung == 0 ) {
if ($finish[($runde+(($dg-1)*$liga[0]->runden)-1)]->meldung == 0 ) {  //klkl
	$msg = JText::_( 'RESULT_DATA_ROUND_UNPUBLISHED');
	$link = "index.php?option=com_clm&view=paarungsliste&saison=$sid&liga=$lid";
	$mainframe->redirect( $link, $msg );
}
// Prüfen ob Datensatz schon vorhanden ist
$access	= $this->access;
$mdt = $finish[($runde+(($dg-1)*$liga[0]->runden)-1)]->deadlineday.' ';
if ($finish[($runde+(($dg-1)*$liga[0]->runden)-1)]->deadlinetime != '00:00:00') $mdt .= $finish[($runde+(($dg-1)*$liga[0]->runden)-1)]->deadlinetime; else $mdt .= '24:00:00';
if ($access[0]->gemeldet > 0 AND $mdt < $now) {
	$msg = JText::_( 'RESULT_DATA_ALREADY_EXISTS');
	$link = "index.php?option=com_clm&view=runde&saison=$sid&liga=$lid&runde=$runde&dg=$dg";
	$mainframe->redirect( $link, $msg );
}
// NICHT vorhanden --> Meldung bzw. vorhanden aber Korrekturmeldung noch möglich
else {
$datum =& JFactory::getDate($liga[0]->datum);

// Ergebnistext für flexibele Punktevergabe holen
$erg_text = CLMModelMeldung::punkte_text($liga[0]->id);

	// Browsertitelzeile setzen
	$doc =& JFactory::getDocument();
	$daten['title'] = JText::_('RESULT_DATA_RESULT').' '.$liga[0]->name;
	$doc->setHeadData($daten);

?>
<div id="clm">
<div id="meldung">
<div class="componentheading"><?php echo JText::_('RESULT_DATA_RESULT') ?> </div>

<h4><?php echo $liga[0]->name.', ';?><?php echo JText::_('RESULT_DATA_ROUND') ?> <?php echo $runde;
if ($liga[0]->durchgang > 1) { echo " (".$dg.". ".JText::_('RESULT_DATA_DG').")";} ?>
<?php if ($liga[0]->datum == '0000-00-00' or !$liga[0]->datum) {  }
else { echo ', am '.JHTML::_('date', $liga[0]->datum, JText::_('DATE_FORMAT_CLM')); }?>
</h4>

<div id="desc">
<h4><?php echo JText::_('RESULT_DATA_NOTE') ?></h4>
<ol>
<li><?php echo JText::_('RESULT_DATA_NOTE1') ?></li>
<li><?php echo JText::_('RESULT_DATA_NOTE2') ?></li>
</ol>
</div>
<br>

<form action="index.php?option=com_clm&amp;view=meldung&amp;layout=check" method="post" name="adminForm">
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
<td align="center"><b><?php echo $paar[0]->hname; ?></b></td>
<td> - </td>
<td align="center"><b><?php echo $paar[0]->gname; ?></b></td>
<td></td>
</tr>
<?php	for ($i=0; $i<$liga[0]->stamm; $i++){
?>
	<tr>
		<td class="key" nowrap="nowrap">
		  <label for="sid">
			<?php echo JText::_( '<b>&nbsp;&nbsp;'.($i+1).'</b>'); ?>
		  </label>
		</td>
		<td>
		  <select size="1" name="<?php echo "heim".($i+1); ?>" id="<?php echo "heim".($i+1); ?>">
			<option value="0"><?php echo JText::_('RESULT_DATA_LIST_PLAYER') ?></option>
			<?php for ($x=0; $x < ($countheim[0]->count); $x++) {
			if ($liga[0]->rang !="0") {?>
			  <option value="<?php echo $heim[$x]->mgl_nr.';'.$heim[$x]->name.';'.$heim[$x]->zps; ?>"<?php if (isset($oldresult[$i]) AND $heim[$x]->mgl_nr == $oldresult[$i]->spieler AND $heim[$x]->zps == $oldresult[$i]->zps) echo ' selected="selected" '; ?>><?php echo $heim[$x]->rmnr.' - '.$heim[$x]->rang.' &nbsp;&nbsp;';if($heim[$x]->rang < 1000) { echo "&nbsp;&nbsp;&nbsp;&nbsp;";};if($heim[$x]->rang < 10) { echo "&nbsp;&nbsp;";}; echo $heim[$x]->name; ?></option> 
			<?php }
			else { ?>
			  <option value="<?php echo $heim[$x]->mgl_nr.';'.$heim[$x]->name.';'.$heim[$x]->zps; ?>"<?php if (isset($oldresult[$i]) AND $heim[$x]->mgl_nr == $oldresult[$i]->spieler AND $heim[$x]->zps == $oldresult[$i]->zps) echo ' selected="selected" '; ?>><?php echo $heim[$x]->snr;if($heim[$x]->snr < 10) {echo "&nbsp;&nbsp";} echo ' - '.$heim[$x]->name; ?></option> 
			<?php }} ?>
			<option value="<?php echo '99999'.';'.JText::_('RESULTS_DETAILS_NOT_NOMINATED').';'.'ZZZZZ'; ?>"<?php if ($heim[$i]->zps =="ZZZZZ"){ ?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('RESULTS_DETAILS_NOT_NOMINATED'); ?></option>
		  </select>
		</td>
		<td> - </td>
		<td>
		  <select size="1" name="<?php echo "gast".($i+1); ?>" id="<?php echo "gast".($i+1); ?>">
			<option value="0"><?php echo JText::_('RESULT_DATA_LIST_PLAYER') ?></option>
			<?php for ($x=0; $x < ($countgast[0]->count); $x++) {
			if ($liga[0]->rang !="0") {?>
			 <option value="<?php echo $gast[$x]->mgl_nr.';'.$gast[$x]->name.';'.$gast[$x]->zps; ?>"<?php if (isset($oldresult[$i]) AND $gast[$x]->mgl_nr == $oldresult[$i]->gegner AND $gast[$x]->zps == $oldresult[$i]->gzps) echo ' selected="selected" '; ?>><?php echo $gast[$x]->rmnr.' - '.$gast[$x]->rang.' &nbsp;&nbsp;';if($gast[$x]->rang < 1000) { echo "&nbsp;&nbsp;&nbsp;&nbsp;";};if($gast[$x]->rang < 10) { echo "&nbsp;&nbsp;";}; echo $gast[$x]->name; ?></option> 
			<?php }
			else { ?>
			 <option value="<?php echo $gast[$x]->mgl_nr.';'.$gast[$x]->name.';'.$gast[$x]->zps; ?>"<?php if (isset($oldresult[$i]) AND $gast[$x]->mgl_nr == $oldresult[$i]->gegner AND $gast[$x]->zps == $oldresult[$i]->gzps) echo ' selected="selected" '; ?>><?php echo $gast[$x]->snr;if($gast[$x]->snr <10) {echo "&nbsp;&nbsp;";} echo ' - '.$gast[$x]->name; ?></option> 
			<?php }} ?>
			<option value="<?php echo '99999'.';'.JText::_('RESULTS_DETAILS_NOT_NOMINATED').';'.'ZZZZZ'; ?>"<?php if ($gast[$i]->zps =="ZZZZZ"){ ?> selected="selected"<?php } ?>>&nbsp;&nbsp;&nbsp;---&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_('RESULTS_DETAILS_NOT_NOMINATED'); ?></option>
		  </select>
		</td>

		<td>
		  <select size="1" name="<?php echo "ergebnis".($i+1); ?>" id="<?php echo "ergebnis".($i+1); ?>">
			<option value="7"><?php echo JText::_('RESULT_DATA_LIST_RESULT') ?></option>
			<?php for ($x=0; $x < 9; $x++) { ?>
			 <option value="<?php echo $ergebnis[$x]->eid.';'.$erg_text[$x]->erg_text; ?>"<?php if (isset($oldresult[$i]) AND $ergebnis[$x]->eid == $oldresult[$i]->ergebnis) echo ' selected="selected" '; ?>><?php echo $erg_text[$x]->erg_text ; ?></option> 
			<?php } ?>
		  </select>
		</td>
	</tr>
<?php } ?> 
  </table>
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
			<select name="ko_decision" id="ko_decision" value="<?php echo $paar[0]->ko_decision; ?>" size="1">
			<!--<option>- wählen -</option>-->
			<option value="1" <?php if ($paar[0]->ko_decision == 1) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BW' );?></option>
			<option value="2" <?php if ($paar[0]->ko_decision == 2) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$paar[0]->hname;?></option>
			<option value="3" <?php if ($paar[0]->ko_decision == 3) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_BLITZ' ).$paar[0]->gname;?></option>
			<option value="4" <?php if ($paar[0]->ko_decision == 4) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$paar[0]->hname;?></option>
			<option value="5" <?php if ($paar[0]->ko_decision == 5) {echo 'selected="selected"';} ?>><?php echo JText::_( 'RESULTS_MT_KO_DECISION_LOS' ).$paar[0]->gname;?></option>
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
			<textarea name="comment" id="comment" cols="30" rows="2" style="width:90%"><?php echo str_replace('&','&amp;',$paar[0]->comment);?></textarea>
			</td>
		</tr>
		</table>
	  </fieldset>	
	</div>	
<?php } ?> 

<input type="submit" value=" <?php echo JText::_('RESULT_DATA_BUTTON_NEXT') ?> ">
		<input type="hidden" name="layout" value="check" />
		<input type="hidden" name="view" value="meldung" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="sid" value="<?php echo $liga[0]->sid; ?>" />
		<input type="hidden" name="saison" value="<?php echo $liga[0]->sid; ?>" />
		<input type="hidden" name="lid" value="<?php echo $liga[0]->id; ?>" />
		<input type="hidden" name="liga" value="<?php echo $liga[0]->id; ?>" />
		<input type="hidden" name="runde" value="<?php echo $runde; ?>" />
		<input type="hidden" name="paarung" value="<?php echo $paarung; ?>" />
		<input type="hidden" name="dg" value="<?php echo $dg; ?>" />
		<input type="hidden" name="tln" value="<?php echo $tln_nr; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $itemid; ?>" />
		
		<input type="hidden" name="htln" value="<?php echo $paar[0]->htln; ?>" />
		<input type="hidden" name="hzps" value="<?php echo $paar[0]->hzps; ?>" />

		<input type="hidden" name="gtln" value="<?php echo $paar[0]->gtln; ?>" />
		<input type="hidden" name="gzps" value="<?php echo $paar[0]->gzps; ?>" />

		<input type="hidden" name="heimname" value="<?php echo $paar[0]->hname; ?>" />
		<input type="hidden" name="gastname" value="<?php echo $paar[0]->gname; ?>" />
		<input type="hidden" name="mail" value="<?php echo $liga[0]->mail; ?>" />		

		<input type="hidden" name="stamm" value="<?php echo $liga[0]->stamm; ?>" />
		<input type="hidden" name="ersatz" value="<?php echo $liga[0]->ersatz; ?>" />
		<input type="hidden" name="liganame" value="<?php echo $liga[0]->name; ?>" />
		<input type="hidden" name="datum" value="<?php echo $liga[0]->datum; ?>" />

		<input type="hidden" name="max" value="<?php echo $max[0]->max; ?>" />

		<input type="hidden" name="task" value="" />
		<?php echo JHTML::_( 'form.token' ); ?>
</form>
</center>
<br />

<?php }} 

echo '</div></div>';
	
require_once(JPATH_COMPONENT.DS.'includes'.DS.'copy.php'); 
?>