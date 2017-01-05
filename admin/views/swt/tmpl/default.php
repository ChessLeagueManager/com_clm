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

// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$upload		=$config->upload_swt;
$execute	=$config->execute_swt;
$lv			=$config->lv;
$upload_pgn	=$config->upload_pgn;
$import_pgn	=$config->import_pgn;


?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<table width="100%" class="admintable"> 
		<tr>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_ATTENTION_TAB' ); ?></legend> 
					<?php echo JText::_( 'SWT_ATTENTION_TEXT' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo JText::_( 'SWT_ACTIVATION_TEXT01' ).' '; ?> <?php if ($upload == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo " , ".JText::_( 'SWT_ACTIVATION_TEXT02' ).' '; ?><?php if ($execute == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".JText::_( 'SWT_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'SWT_HINTS_TEXT01' ); ?>
					<?php echo JText::_( 'SWT_HINTS_TEXT02' ); ?>
				</fieldset>
			</td>
			<td width="50%" style="vertical-align: top;">
				<?php if ($upload == 1) { ?>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_UPLOAD_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><input type="file" name="datei" /></td>
							<td width="50%"><?php echo JText::_( 'SWT_UPLOAD_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<?php if ($execute == 1) { ?>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['swt_files'] ?></td>
							<td width="50%"><?php echo JText::_( 'SWT_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
			</td>
		</tr>		
		
		
		<tr>
			<td width="50%" style="vertical-align: top;">
				<br><br><br><br>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_ATTENTION_TAB' ); ?></legend> 
					<?php echo JText::_( 'PGN_ATTENTION_TEXT' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo JText::_( 'PGN_ACTIVATION_TEXT01' ).' '; ?> <?php if ($upload_pgn == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo " , ".JText::_( 'PGN_ACTIVATION_TEXT02' ).' '; ?><?php if ($import_pgn == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".JText::_( 'SWT_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'PGN_HINTS_TEXT01' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT02' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT03' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT04' ); ?>
				</fieldset>
			</td>
			<td width="50%" style="vertical-align: top;">
				<br><br><br><br>
				<?php if ($upload_pgn == 1) { ?>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_UPLOAD_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><input type="file" name="pgn_datei" /></td>
							<td width="50%"><?php echo JText::_( 'PGN_UPLOAD_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<?php if ($import_pgn == 1) { ?>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['pgn_files'] ?></td>
							<td width="50%"><?php echo JText::_( 'PGN_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
			</td>
		</tr>		
	</table>
	
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swt" />
	<input type="hidden" name="controller" value="swt" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
