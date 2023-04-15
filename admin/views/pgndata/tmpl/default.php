<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

//echo "<br><br>v-pgn_arr:"; var_dump($this->pgn_arr);
$pgn_data = $this->pgn_arr;
$pgn_del = $this->pgn_del;
//echo "<br><br>v-pgn_data:"; var_dump($pgn_data);
$turnier = $this->turnier;
//echo "<br><br>v-turnier:"; var_dump($turnier);
$total = count($pgn_data);
$pgn_error = 0;
for ($p = 0; $p < count($pgn_data); $p++) { 
	if ($pgn_data[$p]['error'] != '') $pgn_error++;
}
if (isset($pgn_data[0]['tid'])) $tid = $pgn_data[0]['tid']; else $tid = 0;
if (isset($pgn_data[0]['tkz'])) $tkz = $pgn_data[0]['tkz']; else $tkz = '';
 
$task = clm_core::$load->request_string('task', '');
$stask = clm_core::$load->request_string('stask', '');
$liga = clm_core::$load->request_string('liga', '');
$pgn_file = clm_core::$load->request_string('pgn_file', '');

?>

<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) { 		
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
            return;
        }
        // do field validation (z.Z. nichts)
           
		Joomla.submitform( pressbutton );
    }

</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="width-100 fltlft">
		<fieldset class="adminform">
			<legend><?php echo $total.' '.JText::_( 'PGN_MAINTAIN_TOTAL' ); ?><br>
				<?php if ($total == 0) echo JText::_( 'PGN_MAINTAIN_CLOSE' ); 
					else echo JText::_( 'PGN_MAINTAIN_OPEN' ); ?></legend>
			<table class="paramlist admintable">
			<?php if ($pgn_error > 0) {  ?>
			<tr>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_ERROR') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_TOURNAMENT') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_DG') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_ROUND') ?></div></th>
					<?php if ($tkz == 't') {  ?>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_PAIR') ?></div></th>
					<?php }  ?>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_BOARD') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_PGNTEXT') ?></div></th>
				</tr>
 			<?php for ($p = 0; $p < count($pgn_data); $p++) {  
				if ($pgn_data[$p]['error'] == '') continue;
				if ($tkz == 't') { $dg = $turnier[0]->durchgang; 
								   $brett = $turnier[0]->stamm;}
				else { $dg = $turnier[0]->dg; 
					   $brett = ($turnier[0]->teil / 2); }
				?>
				<tr>
					<td width="20%" valign="top">
						<textarea name="error<?php echo $p; ?>" id="error<?php echo $p; ?>" cols="40" rows="2" style="width:90%"><?php echo $pgn_data[$p]['error']; // str_replace('&','&amp;',$this->bemerkungen);?></textarea>
					</td>
					<td width="20%" valign="top">
						<?php echo ' '.$turnier[0]->name; ?>
						<input type="hidden" name="pgnnr<?php echo $p; ?>" id="pgnnr<?php echo $p; ?>" value="<?php echo $pgn_data[$p]['pgnnr'] ?>" />
					</td>
					<td width="5%" nowrap="nowrap" valign="top">
						<select name="dg<?php echo $p; ?>" id="dg<?php echo $p; ?>" value="<?php echo $pgn_data[$p]['dg']; ?>" size="1">
							<?php for ($i = 0; $i <= $dg; $i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ($pgn_data[$p]['dg'] == $i) {echo 'selected="selected"';} ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</td>
					<td width="5%" nowrap="nowrap" valign="top">
						<select name="runde<?php echo $p; ?>" id="runde<?php echo $p; ?>" value="<?php echo $pgn_data[$p]['runde']; ?>" size="1">
							<?php for ($i = 0; $i <= $turnier[0]->runden; $i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ($pgn_data[$p]['runde'] == $i) {echo 'selected="selected"';} ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</td>
					<?php if ($tkz == 't') {  ?>
					  <td width="5%" nowrap="nowrap" valign="top">
						<select name="paar<?php echo $p; ?>" id="paar<?php echo $p; ?>" value="<?php echo $pgn_data[$p]['paar']; ?>" size="1">
							<?php for ($i = 0; $i <= ($turnier[0]->teil/2); $i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ($pgn_data[$p]['paar'] == $i) {echo 'selected="selected"';} ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					  </td>
					<?php } ?>
					<td width="5%" nowrap="nowrap" valign="top">
						<select name="brett<?php echo $p; ?>" id="brett<?php echo $p; ?>" value="<?php echo $pgn_data[$p]['brett']; ?>" size="1">
							<?php for ($i = 0; $i <= $brett; $i++) { ?>
								<option value="<?php echo $i; ?>" <?php if ($pgn_data[$p]['brett'] == $i AND $pgn_data[$p]['runde'] > 0) {echo 'selected="selected"';} ?>><?php echo $i; ?></option>
							<?php } ?>
						</select>
					</td>

					<td width="80%" valign="top">
						<textarea class="inputbox" name="text<?php echo $p; ?>" id="text<?php echo $p; ?>" cols="40" rows="8" style="width:90%"><?php echo $pgn_data[$p]['text']; // str_replace('&','&amp;',$this->bemerkungen);?></textarea>
					</td>
				</tr>
			<?php } ?>
			<?php }  ?>
			</table>

	</div>

	<div class="clr"></div>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="pgndata" />
	<input type="hidden" name="controller" value="pgndata" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tkz" value="<?php echo $tkz; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="liga" value="<?php echo $liga; ?>" />
	<input type="hidden" name="pgn_file" value="<?php echo $pgn_file; ?>" />
	<input type="hidden" name="pgn_count" value="<?php echo count($pgn_data); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
