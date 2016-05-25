<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$turParams = new clm_class_params($this->turnier->params);
$pgnInput = $turParams->get('pgnInput', 1);
?>

<style type="text/css">
#clm .clm input, #clm .clm textarea, #clm .clm .uneditable-input {
    width: auto;
}
</style>

	<script language="javascript" type="text/javascript">
	
	function openPgnRow(id) {
		document.getElementById('pgnSwitch' + id).innerHTML = '';
		
		document.getElementById('pgnHead' + id).innerHTML = 'PGN:';
		
		document.getElementById('pgnTextarea' + id).innerHTML = '<textarea name="pgn[' + id + ']" id="pgnformt' + id + '" cols="75" rows="10"></textarea>';
		document.getElementById('pgnformt' + id).value = document.getElementById('pgnformh' + id).value;
		document.getElementById('pgnHidden' + id).innerHTML = '';
		
		document.getElementById('pgnEnd' + id).innerHTML = '<a href="#" onclick="closePgnRow(' + id + ')">X</a>';
	}
	
	function closePgnRow(id) {
		document.getElementById('pgnSwitch' + id).innerHTML = '<a href="#" onclick="openPgnRow(' + id + ')">' + document.getElementById('pgnformt' + id).value.length + '</a>';
		
		document.getElementById('pgnHead' + id).innerHTML = '';
		
		document.getElementById('pgnHidden' + id).innerHTML = '<input type="hidden" name="pgn[' + id + ']" id="pgnformh' + id + '">';
		document.getElementById('pgnformh' + id).value = document.getElementById('pgnformt' + id).value;
		document.getElementById('pgnTextarea' + id).innerHTML = '';
		
		document.getElementById('pgnEnd' + id).innerHTML = '';
	}
	
	</script>

<form action="index.php" method="post" id="adminForm" name="adminForm">

	<div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend>
			<?php 
			echo JText::_('ROUND')." ".$this->round->nr." (".$this->round->name.")";
			// wenn mehr als ein Durchgang, auch Nummer des Durchgangs angeben
			if ($this->turnier->dg > 1) {
				echo ", ".JText::_('STAGE')." ".$this->matches[0]->dg;
				// leider wurde dg früher nicht in Runde-Table gespeichert - daher aus einem der Matches extrahiert
			}
			?>
		</legend>

		<table class="adminlist">
	
			<tr>
				<th nowrap="nowrap"><?php echo JText::_('MATCH') ?></th>
				<th nowrap="nowrap"><?php echo JText::_('WHITE') ?></th>
				<th nowrap="nowrap"><?php echo JText::_('BLACK') ?></th>
				<th nowrap="nowrap"><?php echo JText::_('RESULT') ?></th>
				<?php
				if ($this->turnier->typ == 3 or $this->turnier->typ == 5) { // KO?
					echo '<th nowrap="nowrap">'.JText::_('DECISION_MATCHES').'</th>';
					$pgnColspan = 4;
				} else {
					$pgnColspan = 3;
				}
				if ($pgnInput == 1) {
					echo '<th nowrap="nowrap" align="center">'.JText::_('PGN').'</th>';
				}
				?>
			</tr>

			<?php 	
				// alle Matches durchgehen
				foreach ($this->matches as $key => $value) {
					// Zeilen färben
					if ($this->turnier->typ != 3 and $this->turnier->typ != 5) { // nicht KO?
						// abwechselnd einfärben
						if ($key%2 == 0) { // gerade
							$color = '#FFFACD';
						} else {
							$color = '#D3D3D3';
						}
					} else {
						// pärchenweise einfärben
						$temp = $key+1;
						if ($temp%2 != 0) $temp++; // auf geraden Grundwert bringen
						$temp = $temp/2;
						if ($temp%2 == 0) { // gerade
							$color = '#FFFACD';
						} else {
							$color = '#D3D3D3';
						}
					}
					?>
					
					<!-- Datenzeile -->
					<tr>
						<td align="center" nowrap="nowrap" style="background-color:<?php echo $color; ?>;">
							<?php echo ($key+1); ?>
						</td>
						<td nowrap="nowrap" style="background-color:<?php echo $color; ?>;">
							<?php
								echo CLMForm::selectMatchPlayer('w['.$value->id.']', $value->spieler, $this->players);
							?>
						</td>
				
						<td nowrap="nowrap" style="background-color:<?php echo $color; ?>;">
							<?php
								echo CLMForm::selectMatchPlayer('b['.$value->id.']', $value->gegner, $this->players);
							?>
						</td>
				
						<td nowrap="nowrap" style="background-color:<?php echo $color; ?>;">
							<?php
								echo CLMForm::selectMatchResult('res['.$value->id.']', $value->ergebnis);
							?>
						</td>
				
						<?php
						if ($this->turnier->typ == 3 or $this->turnier->typ == 5) { // KO?
							echo '<td align="center" nowrap="nowrap" style="background-color:'.$color.';">';
								echo '<input class="inputbox" type="text" name="tiebrS['.$value->id.']" id="tiebrS'.$value->id.'" size="3" maxlength="3" value="'.$value->tiebrS.'" />&nbsp;:&nbsp;';
								echo '<input class="inputbox" type="text" name="tiebrG['.$value->id.']" id="tiebrG'.$value->id.'" size="3" maxlength="3" value="'.$value->tiebrG.'" />';
							echo '</td>';
						}
						if ($pgnInput == 1) {
						?>
							<td nowrap="nowrap" style="background-color:<?php echo $color; ?>;" align="center">
								<span id="pgnSwitch<?php echo $value->id; ?>">
								<?php
									echo '<a href="#" onclick="openPgnRow('.$value->id.')">'.strlen($value->pgn).'</a>';
								?>
								</span>
							</td>
						<?php
						}
						?>
					</tr>
					
					<?php
					if ($pgnInput == 1) {
					?>

						<!-- pgnzeile -->
						<tr>
							<td valign="top" align="center" colspan="1" style="height:0px; background-color:<?php echo $color; ?>; padding: 0px;">
								<span id="pgnHead<?php echo $value->id; ?>">
								</span>
							</td>
							<td align="center" colspan="<?php echo $pgnColspan; ?>" style="height:0px; background-color:<?php echo $color; ?>; padding: 0px;">
								<span id="pgnTextarea<?php echo $value->id; ?>">
								</span>
								<span id="pgnHidden<?php echo $value->id; ?>">
									<input type='hidden' name='pgn[<?php echo $value->id; ?>]' id='pgnformh<?php echo $value->id; ?>' value='<?php echo $value->pgn; ?>'>
								</span>
							</td>
							<td valign="top" align="center" colspan="1" style="height:0px; background-color:<?php echo $color; ?>; padding: 0px;">
								<span id="pgnEnd<?php echo $value->id; ?>">
								</span>
							</td>
						</tr>
					
					<?php
					}
					?>
					
			<?php 
				} 
			?> 
		
		</table>
	</fieldset>
	</div>

	<div class="width-40 fltrt">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'JDETAILS' ); ?></legend>

		<table class="admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'ENTERED_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$this->round->gemeldet) { echo "---"; }
				else { echo $this->round->gname; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'DATE_AT' ); ?>:</label>
			</td>
			<td>
			<?php if ($this->round->zeit != "0000-00-00 00:00:00") {echo JHtml::_('date',  $this->round->zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo "---"; } ?>

			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'EDITED_BY' ); ?></label>
			</td>
			<td>
			<?php if (!$this->round->editor) { echo "---"; }
				else { echo $this->round->ename; }?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'DATE_AT' ); ?>:</label>
			</td>
			<td>
			<?php if ($this->round->edit_zeit != "0000-00-00 00:00:00") {echo JHtml::_('date',  $this->round->edit_zeit, JText::_('DATE_FORMAT_LC2'));} 
			else { echo "---"; } ?>
			</td>
		</tr>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
				<label for="name"><?php echo JText::_( 'APPROVAL' ); ?>:</label>
			</td>
			<td>
				<?php 
					if ($this->round->tl_ok == 1) {
						echo JText::_('JYES');
					} else {
						echo JText::_('JNO');
					}
				?>
			</td>
		</tr>

		</table>
		</fieldset>
		</div>

	<div class="clr"></div>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="turroundmatches" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="controller" value="turroundmatches" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="turnierid" value="<?php echo $this->param['turnierid']; ?>" />
	<input type="hidden" name="roundid" value="<?php echo $this->param['roundid']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
