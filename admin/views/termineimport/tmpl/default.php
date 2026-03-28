<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

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
<!---  Import von CLM-Termine Dateien ------------------------------------------------------------------------------------------ -->		
		<tr><td><fieldset><legend><?php echo Text::_( 'TERM_IMPORT_HEADLINE' ); ?></legend></fieldset></td></tr>
		<tr>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_ATTENTION_TAB' ); ?></legend> 
					<?php echo Text::_( 'TERM_ATTENTION_TEXT' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo Text::_( 'TERM_ACTIVATION_TEXT01' ).' '; ?> <?php if ($upload == 1) { ?><font color="#00ff00"><?php echo Text::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo Text::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo " , ".Text::_( 'TERM_ACTIVATION_TEXT02' ).' '; ?><?php if ($execute == 1) { ?><font color="#00ff00"><?php echo Text::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo Text::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".Text::_( 'SWT_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_HINTS_TAB' ); ?></legend> 
					<?php echo Text::_( 'TERM_HINTS_TEXT01' ); ?>
				</fieldset>
			</td>
			<td width="50%" style="vertical-align: top;">
				<?php if ($upload == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'TERM_UPLOAD_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><input type="file" name="termine_datei" /></td>
							<td width="50%"><?php echo Text::_( 'TERM_UPLOAD_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<?php if ($execute == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'TERM_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['termine_files'] ?></td>
							<td width="50%"><?php echo Text::_( 'TERM_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
			</td>
		</tr>		

	</table>
	
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="termineimport" />
	<input type="hidden" name="controller" value="termineimport" />
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
