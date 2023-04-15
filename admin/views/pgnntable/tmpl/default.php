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

$pgn_data = $this->pgn_arr;
$pgn_del = $this->pgn_del;
$turnier = $this->turnier;
$sid = $turnier[0]->sid;
$aentries = $this->aentries;
$pgn_error = 0;
$total = 0;
if (!is_null($pgn_data)) {
	$total = count($pgn_data);
	for ($p = 0; $p < count($pgn_data); $p++) { 
		if ($pgn_data[$p]['error'] != '') $pgn_error++;
	}
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
					else echo JText::_( 'PGN_MAINTAIN_TABLE' ); ?></legend>
			<table class="paramlist admintable">
			<?php if ($pgn_error > 0) {  ?>
			<tr>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_ERROR') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_TOURNAMENT') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_PLAYER_ONAME') ?></div></th>
					<th style="text-align:left; color: #ff0000;" ><div><?php echo JText::_('PGN_PLAYER_NNAME') ?></div></th>
					<th style="text-align:left" ><div><?php echo JText::_('PGN_LEAGUE_PGNTEXT') ?></div></th>
				</tr>
			<?php for ($p = 0; $p < count($pgn_data); $p++) {  
				if ($pgn_data[$p]['error'] == '') continue;

				$game = array();
				$pgn_arr = array();
				$total = 0; $pgn_error = 0;
				$ii = 0; $jj = 0; $ij = 0; $ib = 0; 
				$pgn_edata = $pgn_data[$p]['text'];
				$length = strlen($pgn_edata);
				for ($ii = 0; $ii < $length; $ii++) {
					if ($ii < $ij) continue;
					if (substr($pgn_edata, $ii, 1) == '[' AND substr($pgn_edata, $ii+1, 1) != '%') {
					$jj = strpos(substr($pgn_edata, ($ii + 1)), ']');
					if ($jj === false) { echo "<br>Fehler]"; die(); }
					$game_par = substr($pgn_edata, $ii+1, $jj);
					$game_arr = explode(' ', $game_par, 2);
					if (isset($game[$game_arr[0]])) { 
						$game['tkz'] = $tkz;
						$game['tid'] = $tid;
						$game['text'] = substr($pgn_edata, $ib, ($ii - $ib - 1));
						$total++;
						$return_arr = $this->dbgame($game);
						if ($return_arr['error'] != '') $pgn_error++;
							//$pgn_arr[] = $return_arr;
							$game = array();
							$ib = $ii;
					}
					$game[$game_arr[0]] = substr($game_arr[1], 1, (strlen($game_arr[1]) - 2));
					$ij = $ii + $jj;
				}
			}
			if (!isset($game['White'])) $game['White'] = '';		
			if (isset($aentries[$game['White']])) $game_tWhite = $aentries[$game['White']]; else { $game_tWhite = ''; }
			if (!isset($game['Black'])) $game['Black'] = ''; 
			if (isset($aentries[$game['Black']])) $game_tBlack = $aentries[$game['Black']]; else { $game_tBlack = ''; } 
?>
				<tr>
					<td width="20%" valign="top">
						<textarea name="error<?php echo $p; ?>" id="error<?php echo $p; ?>" cols="40" rows="2" style="width:90%"><?php echo $pgn_data[$p]['error']; // str_replace('&','&amp;',$this->bemerkungen);?></textarea>
					</td>
					<td width="20%" valign="top">
						<?php echo ' '.$turnier[0]->name; ?>
						<input type="hidden" name="sid<?php echo $p; ?>" id="sid<?php echo $p; ?>" value="<?php echo $sid;  ?>" size="5" maxlength="5" style="width:100%;">
					</td>
					<td width="20%" valign="top">
						<input type="text" name="woname<?php echo $p; ?>" id="woname<?php echo $p; ?>" value="<?php echo $game['White'];  ?>" size="50" maxlength="150" style="width:100%;">
						<br>
						<input type="text" name="boname<?php echo $p; ?>" id="boname<?php echo $p; ?>" value="<?php echo $game['Black'];  ?>" size="50" maxlength="150" style="width:100%;">
					<td width="20%" valign="top">
						<input type="text" name="wnname<?php echo $p; ?>" id="wnname<?php echo $p; ?>" value="<?php echo $game_tWhite;  ?>" size="50" maxlength="150" style="width:100%;" title="<?php echo JText::_( 'DECODE_HINT' ); ?>">
						<br>
						<input type="text" name="bnname<?php echo $p; ?>" id="bnname<?php echo $p; ?>" value="<?php echo $game_tBlack;  ?>" size="50" maxlength="150" style="width:100%;">
					</td>

					<td width="20%" valign="top">
						<textarea name="text<?php echo $p; ?>" id="text<?php echo $p; ?>" cols="40" rows="8" style="width:90%"><?php echo $pgn_data[$p]['text']; // str_replace('&','&amp;',$this->bemerkungen);?></textarea>
					</td>
				</tr>
			<?php } ?>
			<?php }  ?>
			</table>

	</div>

	<div class="clr"></div>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="pgnntable" />
	<input type="hidden" name="controller" value="pgnntable" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tkz" value="<?php echo $tkz; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="liga" value="<?php echo $liga; ?>" />
	<input type="hidden" name="pgn_file" value="<?php echo $pgn_file; ?>" />
	<input type="hidden" name="pgn_count" value="<?php echo count($pgn_data); ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
