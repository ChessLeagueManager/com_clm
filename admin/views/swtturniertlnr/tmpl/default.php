<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
$pfirst	= clm_core::$load->request_int( 'pfirst',1);
$pcount = count($this->teilnehmer);
$_GET['pcount'] = $pcount;
$plast = $pcount;
$_GET['pfirst'] = $pfirst;
$prange = 50;
$_GET['prange'] = $prange;

/*
$params = clm_core::$load->request_string('params');
if(isset($params['useAsTWZ'])) {
	$useAsTWZ = $params['useAsTWZ'];
} else {
	$useAsTWZ = 0;
}
*/  
$useAsTWZ = clm_core::$load->request_string('useAsTWZ', '0');
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" >

	<div id="editcell"> 
		<table class="adminlist">
			<thead>
				<tr>
					<th width="10">
						<?php echo JText::_('CLM_NUMBER_ABB'); ?>
					</th>
					<th width="50">		
						<?php echo JText::_('PLAYER_TITLE'); ?>
					</th>
					<th width="" class="title">
						<?php echo JText::_('PLAYER_NAME'); ?>
					</th>
					<th width="" >
						<?php echo JText::_('PLAYER_SEX'); ?>
					</th>
					<th width="" >
						<?php echo JText::_('SWT_ZPS'); ?>
					</th>
					<th width="50" >
						<?php echo JText::_('SWT_MGL_NR'); ?>
					</th>
					<th width="" >
						<?php echo JText::_('CLUB'); ?>
					</th>
					<th width="50">
						<?php echo JText::_('RATING'); ?>
					</th>
					<th width="50" >
						<?php echo JText::_('FIDE_ELO'); ?>
					</th>
					<th width="50" >
						<?php echo JText::_('PLAYER_BIRTH_YEAR'); ?>
					</th>					
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="10">
						<?php //echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php 
					
					$k = 0;
					foreach($this->teilnehmer as $i => $spieler) {
						if ($spieler->name == 'spielfrei') continue;
						if ($i < $pfirst) continue;
						if ($i > ($pfirst + $prange - 1)) { $plast = ($i - 1); break;}
						echo "<tr class='row".$k."'>";
							echo "<td align='center'>".$i."<input type='hidden' name='snr[".$i."]' 	value='".$i."' /></td>";
							echo "<td align='center'><input class='inputbox' type='text' name='title[".$i."]' id='title".$i."' size='3' maxlength='3' value='".$spieler->title."' /></td>";
							echo "<td><input class='inputbox' type='text' name='name[".$i."]' id='name' size='40' maxlength='60' value='".htmlspecialchars($spieler->name, ENT_QUOTES)."' /></td>";
							echo "<td>".JHtml::_('select.genericlist', $this->geschlechter, 'geschlecht['.$i.']', 'class="inputbox" autocomplete="off"', 'value', 'text', $spieler->geschlecht, false)."</td>";
							echo "<td>".CLMForm::selectVereinZPS('zps['.$i.']',$spieler->zps);
                          				echo "<input type='hidden' name='tlnrStatus[".$i."]' value='".$spieler->tlnrStatus."' /><input type='hidden' name='zps_z[".$i."]' value='".$spieler->zps."' />"."</td>";
							echo "<td align='center'><input class='inputbox' type='text' name='mgl_nr[".$i."]' id='mgl_nr".$i."' size='4' maxlength='4' value='".$spieler->mgl_nr."' /></td>";
							echo "<td><input class='inputbox' type='text' name='verein[".$i."]' id='verein".$i."' size='40' maxlength='60' value='".htmlspecialchars($spieler->verein, ENT_QUOTES)."' /></td>";
							echo "<td align='center'><input class='inputbox' type='text' name='start_dwz[".$i."]' id='start_dwz".$i."' size='4' maxlength='4' value='".$spieler->start_dwz."' /></td>";
							echo "<td align='center'><input class='inputbox' type='text' name='FIDEelo[".$i."]' id='FIDEelo".$i."' size='4' maxlength='4' value='".$spieler->FIDEelo."' /><input type='hidden' name='twz[".$i."]' id='twz'  value='".$spieler->twz."' /></td>";
							echo "<td align='center'><input class='inputbox' type='text' name='birthYear[".$i."]' id='birthYear".$i."' size='4' maxlength='4' value='".$spieler->birthYear."' /></td>";
							echo "<input type='hidden' name='FIDEcco[".$i."]' id='FIDEcco".$i."' value='".$spieler->FIDEcco."' />";
							echo "<input type='hidden' name='FIDEid[".$i."]' id='FIDEid".$i."' value='".$spieler->FIDEid."' />";
							echo "<input type='hidden' name='s_punkte[".$i."]' id='s_punkte".$i."' value='".$spieler->s_punkte."' />";
						echo "</tr>";
					$k = 1 - $k;
					}
				?>
			</tbody>
		</table>
	</div> 
	
	<div class="clr"></div>
	
	<input type="hidden" name="rnd" 	value="<?php echo clm_core::$load->request_int('rnd'); ?>" />
	<input type="hidden" name="typ" 	value="<?php echo clm_core::$load->request_int('typ'); ?>" />
	
	<input type="hidden" name="swt_file" 	value="<?php echo clm_core::$load->request_string('swt_file'); ?>" />
<!--	<input type="hidden" name="swt" 	value="<?php echo clm_core::$load->request_string('swt'); ?>" /> -->
	<input type="hidden" name="update" 	value="<?php echo clm_core::$load->request_int('update'); ?>" />
	<input type="hidden" name="tid" 	value="<?php echo clm_core::$load->request_int('tid'); ?>" />
	<input type="hidden" name="swt_tid" value="<?php echo clm_core::$load->request_int('swt_tid'); ?>" />
	<input type="hidden" name="sid" 	value="<?php echo clm_core::$load->request_int('sid'); ?>" />
	
	<input type="hidden" name="pfirst" 	value="<?php echo $pfirst; ?>" />
	<input type="hidden" name="plast" 	value="<?php echo $plast; ?>" />
	<input type="hidden" name="pcount" 	value="<?php echo $pcount; ?>" />
	<input type="hidden" name="useAsTWZ"    value="<?php echo $useAsTWZ; ?>" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtturniertlnr" />
	<input type="hidden" name="controller" value="swtturniertlnr" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
