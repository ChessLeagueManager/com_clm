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

$pgn_file = clm_core::$load->request_string ('pgn_file', '');
$task = clm_core::$load->request_string('task', '');
// Konfigurationsparameter auslesen
$config		= clm_core::$db->config();
$upload_pgn	= $config->upload_pgn;
$import_pgn	= $config->import_pgn;

$stask = 0;

jimport( 'joomla.filesystem.file' );
$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
//echo "<br>vi-pgn:"; var_dump($pgn); //die();		
//echo "<br>vi-pgn_file:"; var_dump($pgn_file); //die();		
//echo "<br>vi-swt_file:"; var_dump($swt_file); die();		

$liga = clm_core::$load->request_string('liga', '');
//echo "<br>ca-html-pgnimport: liga $liga "; var_dump($liga); //die();
?>

<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) { 
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
        }
        // do field validation (z.Z. nichts)
           
		Joomla.submitform( pressbutton );
    }
	
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<table width="100%" class="admintable"> 
<!---  Teil pgn - Import von Partie-Notationen  ------------------------------------------------------------------------------------------- -->		
		<tr><td><br><br><fieldset><legend>PGN-Import</legend></fieldset></td></tr>
		<tr>
			<td width="45%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'PGN_ATTENTION_TAB' ); ?></legend> 
					<?php echo JText::_( 'PGN_ATTENTION_TEXT' ); ?>
					<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'SWT_ACTIVATION_TAB' ); ?></legend> 
						<?php echo JText::_( 'PGN_ACTIVATION_TEXT01' ).' '; ?> <?php if ($upload_pgn == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo " , ".JText::_( 'PGN_ACTIVATION_TEXT02' ).' '; ?><?php if ($import_pgn == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
						else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
						<?php echo "<br>".JText::_( 'SWT_ACTIVATION_TEXT03' ); ?>
						<br><br>
				</fieldset>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'PGN_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'PGN_HINTS_TEXT01' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT02' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT03' ); ?>
					<?php echo JText::_( 'PGN_HINTS_TEXT04' ); ?>
				</fieldset>
			</td>
			<td width="5%" style="vertical-align: top;">
			</td>
			<td width="50%" style="vertical-align: top;">
				<?php if ($upload_pgn == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'PGN_UPLOAD_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%"><input type="file" name="pgn_datei" /></td>
							<td width="60%"><?php echo JText::_( 'PGN_UPLOAD_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<br>
				<?php if ($import_pgn == 1) { ?>
				<fieldset class="adminform"> 
					<legend style="font-size:130%;line-height:100%;margin-bottom:10px;"><?php echo JText::_( 'PGN_EXECUTE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%"><?php echo $this->lists['pgn_files'] ?></td>
							<td width="60%"><?php echo JText::_( 'PGN_EXECUTE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<br>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_LEAGUE_OVERWRITE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%"><?php echo $this->lists['saisons'] ?></td>
							<td width="60%"><?php echo JText::_( 'PGN_LEAGUE_OVERWRITE_SEASONS_TEXT' ); ?></td>
						</tr>
						<tr>
							<td width="40%"><?php echo $this->lists['ligen'] ?></td>
							<td width="60%"><?php echo JText::_( 'PGN_LEAGUE_OVERWRITE_LEAGUE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<?php } ?>
				<br>
			</td>
		</tr>		
	</table>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="pgnimport" />
	<input type="hidden" name="controller" value="pgnimport" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
<!--	<input type="hidden" name="pgn_file" value="<?php echo $pgn_file; ?>" /> -->
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
