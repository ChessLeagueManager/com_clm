<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
$rfirst	= JRequest::getVar( 'rfirst',1);
$rcount = count($this->runden);
JRequest::setVar('rcount', $rcount);
$rlast = $rcount;
JRequest::setVar('rfirst', $rfirst);
$pcount = JRequest::getVar('pcount');
if ($pcount < 50) $rrange = 5;
elseif ($pcount < 100) $rrange = 3;
elseif ($pcount < 150) $rrange = 2;
else $rrange = 1;
JRequest::setVar('rrange', $rrange);
?>
<script language="javascript" type="text/javascript">

function showRounds(){
	var runde = document.adminForm.runden_filter.value;
	
	if(runde == 0) {
	<?php 
		foreach($this->runden as $rnd => $runde) {
			echo "document.getElementById('fieldset_runde".$rnd."').style.display = 'block'; \n";
		}
	?>
	} else {
	<?php 
		foreach($this->runden as $rnd => $runde) {
			echo "document.getElementById('fieldset_runde".$rnd."').style.display = 'none'; \n";
		}	
	?>
	document.getElementById('fieldset_runde'+runde).style.display = 'block';
	}
}
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm" >
<!---fieldset class='adminform' >
	<legend><?php echo JText::_('SWT_CHOOSE_ROUND'); ?></legend>
	<table class='admintable' width='50%'>
		<tbody>
			<tr>
				<td class='key' width='20%' nowrap='nowrap'><?php echo JText::_('SWT_CHOOSE_ROUND_HINT'); ?></td>
				<td><?php echo JHtml::_('select.genericlist', $this->runden_options, 'runden_filter', 'class="inputbox" autocomplete="off" onchange="showRounds()"', 'value', 'text', 1, false); ?></td>
			</tr>
		</tbody>
	</table>
</fieldset>
<br/--->
<?php

if (isset($this->runden) AND count($this->runden) > 0) {
foreach($this->runden as $rnd => $runde) {
	if ($runde->nr < $rfirst) continue;
	if ($runde->nr > ($rfirst + $rrange - 1)) { $rlast = ($runde->nr - 1); break;}
	echo "
	<fieldset class='adminform' id='fieldset_runde".$rnd."' style='display:block;'>
	<legend>".JText::_('ROUND')." ".$runde->runde.", ".JText::_('DG')." ".$runde->dg." (".$runde->name.")</legend>
		<table width='100%' class='admintable'> 
			<tr>";

	//
	// Rundendetails
	//
	
	echo"
				<td width='50%' style='vertical-align: top;'>
					<fieldset class='adminform'>
						<legend>".JText::_('JDETAILS')."</legend>
						<input type='hidden' name='dg[".$rnd."]' 	value='".$runde->dg."' />
						<input type='hidden' name='runde[".$rnd."]' 	value='".$runde->runde."' />
						<input type='hidden' name='nr[".$rnd."]' 	value='".$runde->nr."' />
						<table class='admintable'>
							<tr>
								<td class='key' width='20%' nowrap='nowrap'>
									<label for='name'>".JText::_( 'ROUND_NAME' ).":</label>
								</td>
								<td>
									<input class='inputbox' type='text' name='name[".$rnd."]' id='name[".$rnd."]' size='50' maxlength='60' value='".$runde->name."' />
								</td>
							</tr>

							<tr>
								<td width='100' class='key'>
									<label for='datum'>".JText::_( 'JDATE' ).":</label>
								</td>
								<td>
									".JHtml::_('calendar', $runde->datum, 'datum['.$rnd.']', 'datum['.$rnd.']', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19'))."
								</td>
							</tr>

							<tr>
								<td class='key' nowrap='nowrap'>
									<label for='startzeit'>".JText::_( 'RUNDE_STARTTIME' ).":</label>
								</td>
								<td>
									<input class='inputbox' type='time' name='startzeit[".$rnd."]' id='startzeit[".$rnd."]' value='".substr($runde->startzeit,0,5)."'  />
								</td>
							</tr>

							<tr>
								<td class='key' nowrap='nowrap'>
									<label for='published'>".JText::_( 'CLM_PUBLISHED' ).":</label>
								</td>
								<td><fieldset class='radio'>
									".CLMForm::radioPublished ('published['.$rnd.']', $runde->published)."
								</fieldset></td>
							</tr>
							
							<tr>
								<td class='key' nowrap='nowrap'>
									<label for='verein'>".JText::_( 'ENTRY_ENABLED' )."</label>
								</td>
								<td><fieldset class='radio'>
									".JHtml::_('select.booleanlist',  'abgeschlossen['.$rnd.']', "class='inputbox'", $runde->abgeschlossen )."
								</fieldset></td>
							</tr>

							<tr>
								<td class='key' nowrap='nowrap'>
									<label for='mf'>".JText::_('TOURNAMENT_DIRECTOR').' '.JText::_( 'APPROVAL' ).":</label>
								</td>
								<td><fieldset class='radio'>
									".JHtml::_('select.booleanlist',  'tl_ok['.$rnd.']', "class='inputbox'", $runde->tl_ok )."
								</fieldset></td>
							</tr>
						</table>
					</fieldset>";

	//
	// Bemerkungen
	//
	
	echo"
					<fieldset class='adminform'>
						<legend>".JText::_( 'REMARKS' )."</legend>
						
						<table class='admintable'>
							<tr>
								<td width='20%' class='key' nowrap='nowrap'>
									<label for='mf'>".JText::_( 'REMARKS_PUBLIC' ).":</label>
								</td>
								<td valign='top'>
									<textarea class='inputbox' name='bemerkungen[".$rnd."]' id='bemerkungen[".$rnd."]' cols='40' rows='5' style='width:90%'>".str_replace('&','&amp;',$runde->bemerkungen)."</textarea>
								</td>
							</tr>
							
							<tr>
								<td width='20%' class='key' nowrap='nowrap'>
									<label for='mf'>".JText::_( 'REMARKS_INTERNAL' ).":</label>
								</td>
								<td valign='top'>
									<textarea class='inputbox' name='bem_int[".$rnd."]' id='bem_int[".$rnd."]' cols='40' rows='5' style='width:90%'>".str_replace('&','&amp;',$runde->bem_int)."</textarea>
								</td>
							</tr>
						</table>							
					</fieldset>
				</td>";

	//
	// Paarungsdaten
	//
	
	echo"
				<td width='50%' style='vertical-align: top;'>
					<fieldset class='adminform'>
						<legend>".JText::_('PAIRINGS')."</legend>
						<div id='editcell'> 
							<table class='adminlist'>
								<thead>
									<tr>
										<th width='10'>".JText::_('PAIRING')."</th>
										<th width=''>".JText::_('WHITE')."</th>
										<th width=''>".JText::_('BLACK')."</th>
										<th width='100'>".JText::_('RESULT')."</th>		
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td colspan='10'></td>
									</tr>
								</tfoot>
								<tbody>";

								$k = 0;
								foreach($this->matches[$rnd] as $brett => $match) {
									echo "<tr class='row".$k."'>";
										echo "<td align='center'>".$brett."<input type='hidden' name='brett[".$rnd."][".$brett."]' 	value='".$match->brett."' /></td>";
										echo "<td>".JHtml::_('select.genericlist', $this->teilnehmer_options, 'spieler['.$rnd.']['.$brett.']', 'class="inputbox" autocomplete="off"', 'value', 'text', $match->spieler, false)."</td>";
										echo "<td>".JHtml::_('select.genericlist', $this->teilnehmer_options, 'gegner['.$rnd.']['.$brett.']', 'class="inputbox" autocomplete="off"', 'value', 'text', $match->gegner, false)."</td>";
										echo "<td>".JHtml::_('select.genericlist', $this->ergebnis_options, 'ergebnisWhite['.$rnd.']['.$brett.']', 'class="inputbox" autocomplete="off"', 'value', 'text', $match->ergebnisWhite, false)."
												<input type='hidden' name='ergebnisBlack[".$rnd."][".$brett."]' 	value='".$match->ergebnisBlack."' /></td>";
									echo "</tr>";
								$k = 1 - $k;
								}
	
	
	echo"
									</tbody>
								</table>
							</div> 
						</fieldset>
					</td>
				</tr>
			</table>
		</fieldset>";
	
	}
	} else echo "<br>Es liegen keine Ergebnisse vor!";
	?>
	

	<!---script>
		showRounds();
	</script--->
	
	<div class="clr"></div>
	
	<input type="hidden" name="rnd" 	value="<?php echo JRequest::getVar('rnd'); ?>" />
	
	<input type="hidden" name="swt" 	value="<?php echo JRequest::getVar('swt'); ?>" />
	<input type="hidden" name="update" 	value="<?php echo JRequest::getVar('update'); ?>" />
	<input type="hidden" name="tid" 	value="<?php echo JRequest::getVar('tid'); ?>" />
	<input type="hidden" name="swt_tid" value="<?php echo JRequest::getVar('swt_tid'); ?>" />
	<input type="hidden" name="sid" 	value="<?php echo JRequest::getVar('sid'); ?>" />
	
	<input type="hidden" name="rfirst" 	value="<?php echo $rfirst; ?>" />
	<input type="hidden" name="rlast" 	value="<?php echo $rlast; ?>" />
	<input type="hidden" name="rcount" 	value="<?php echo $rcount; ?>" />
	<input type="hidden" name="pcount" 	value="<?php echo $pcount; ?>" />

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtturniererg" />
	<input type="hidden" name="controller" value="swtturniererg" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
