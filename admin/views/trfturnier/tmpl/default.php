<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2026 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://chessleaguemanager.org
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$trf_file = clm_core::$load->request_string('trf_file', '');
// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$upload		=$config->upload_swt;
$execute	=$config->execute_swt;

?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<table width="100%" class="admintable"> 
<!---  Teil trf - Import von Sevilla, lichess, ...- Dateien------------------------------------------------------------------------------------------ -->		
		<tr><td><br><br><fieldset><legend>TRF-Import</legend></fieldset></td></tr>
		<tr>
			<td width="45%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_ATTENTION_TAB' ); ?></legend> 
					<?php echo Text::_( 'TRF_ATTENTION_TEXT01' ); ?>
					<?php echo Text::_( 'TRF_ATTENTION_TEXT02' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo Text::_( 'TRF_ACTIVATION_TEXT01' ).' '; ?> <?php if ($upload == 1) { ?><font color="#00ff00"><?php echo Text::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo Text::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo " , ".Text::_( 'TRF_ACTIVATION_TEXT02' ).' '; ?><?php if ($execute == 1) { ?><font color="#00ff00"><?php echo Text::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo Text::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".Text::_( 'TRF_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_HINTS_TAB' ); ?></legend> 
					<?php echo Text::_( 'TRF_HINTS_TEXT01' ); ?>
					<?php echo Text::_( 'TRF_HINTS_TEXT02' ); ?>
					<?php echo Text::_( 'TRF_HINTS_TEXT03' ); ?>
				</fieldset>
			</td>
			<td width="5%" style="vertical-align: top;">
			</td>
			<td width="50%" style="vertical-align: top;">
				<?php if ($upload == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'TRF_UPLOAD_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><input type="file" name="trf_datei" /></td>
							<td width="50%"><?php echo Text::_( 'TRF_UPLOAD_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<br>
				<?php if ($execute == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-weight:bold;font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'TRF_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['trf_files'] ?></td>
							<td width="50%"><?php echo Text::_( 'TRF_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<br>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo Text::_( 'SWT_TOURNAMENT_OVERWRITE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="50%"><?php echo $this->lists['saisons'] ?></td>
							<td width="50%"><?php echo Text::_( 'SWT_TOURNAMENT_OVERWRITE_SEASONS_TEXT' ); ?></td>
						</tr>
						<tr>
							<td width="50%"><?php echo $this->lists['turniere'] ?></td>
							<td width="50%"><?php echo Text::_( 'SWT_TOURNAMENT_OVERWRITE_TOURNAMENT_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<br>
			</td>
		</tr>		
	</table>
	
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="trfturnier" />
	<input type="hidden" name="controller" value="trfturnier" />
<!--	<input type="hidden" name="trf_file" value="<?php echo $trf; ?>" /> -->
	<input type="hidden" name="task" value="" />
	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>