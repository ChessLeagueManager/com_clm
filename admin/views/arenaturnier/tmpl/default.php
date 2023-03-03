<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

$arena_code = clm_core::$load->request_string('arena_code', '');
$sid = clm_core::$load->request_string('sid', '');
$tid = clm_core::$load->request_string('tid', '');
$turnier_codes = $this->turnier_codes;

// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$execute	= $config->execute_swt;

clm_core::$load->load_js("arenaimport");
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<table width="100%" class="admintable"> 
<!---  Teil arena - Import aus lichess ------------------------------------------------------------------------------------------ -->		
		<tr><td><br><br><fieldset><legend>Arena-Import aus lichess</legend></fieldset></td></tr>
		<tr>
			<td width="45%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'SWT_ATTENTION_TAB' ); ?></legend> 
					<?php echo JText::_( 'ARENA_ATTENTION_TEXT01' ); ?>
					<?php echo JText::_( 'ARENA_ATTENTION_TEXT02' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo JText::_( 'ARENA_ACTIVATION_TEXT02' ).' '; ?><?php if ($execute == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".JText::_( 'SWT_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'SWT_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'ARENA_HINTS_TEXT01' ); ?>
					<?php echo JText::_( 'ARENA_HINTS_TEXT02' ); ?>
					<?php echo JText::_( 'ARENA_HINTS_TEXT03' ); ?>
				</fieldset>
			</td>
			<td width="5%" style="vertical-align: top;">
			</td>
			<td width="50%" style="vertical-align: top;">
				<?php if ($execute == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'ARENA_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><input class="inputbox" type="text" name="arena_code" id="arena_code" size="20" maxlength="20" value="<?php echo $arena_code; ?>" ></td>
							<td width="50%"><?php echo JText::_( 'ARENA_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<br>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;" ><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['saisons'] ?></td>
							<td width="50%"><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_SEASONS_TEXT' ); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->lists['turniere'] ?></td>
							<td width="50%"><?php echo JText::_( 'SWT_TOURNAMENT_OVERWRITE_TOURNAMENT_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<br>
			</td>
		</tr>		
	</table>
	
	<?php foreach ($turnier_codes as $key => $turnier_code) { ?>
		<input type="hidden" name="turnier<?php echo $key; ?>" id="turnier<?php echo $key; ?>" value="<?php echo $turnier_code; ?>" />	
	<?php } ?>
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="arenaturnier" />
	<input type="hidden" name="controller" value="arenaturnier" />
<!--	<input type="hidden" name="arena_code" value="<?php echo $arena; ?>" />
	<input type="hidden" name="arena" value="<?php echo $arena; ?>" />  -->
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>