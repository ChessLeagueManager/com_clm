<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
	//CLM parameter auslesen
	$config = clm_core::$db->config();
	$countryversion = $config->countryversion;
?>

<style type="text/css">
#auswertung_dewis input[type="submit"] {
	margin-bottom: 9px;
}
</style>

<div class="width-50 fltlft">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'DB_EXPORT_FILES' ); ?></legend>
	<?php echo $this->lists['files']; ?>
	</fieldset>
</div>

<div class="width-50 fltlft">
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform"> 
		<legend><?php echo JText::_( 'DB_EXPORT_LEAGUE' ); ?></legend>
			<table width="100%">
				<tr>
					<td width="50%"><?php echo $this->lists['lid'] ?></td>
					<td width="50%"><input type="submit" value="<?php echo JText::_( 'DB_FILE_CREATE' ); ?>"></td>
				</tr>
				<tr>
					<td width="50%"><select id="filter_format" name="filter_format" class="inputbox" size="1" onchange="">
						<option value="0"><?php echo JText::_( 'DB_FILE_FORMAT_0' ); ?></option>
						<?php if ($countryversion =="de") { ?>
							<option value="1"><?php echo JText::_( 'DB_FILE_FORMAT_1' ); ?></option>
							<option value="2"><?php echo JText::_( 'DB_FILE_FORMAT_2' ); ?></option>
						<?php } elseif ($countryversion =="en") { ?>
							<option value="3"><?php echo JText::_( 'DB_FILE_FORMAT_3' ); ?></option>
						<?php } ?>
					</select>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
		
	<?php if ($countryversion =="de") { ?>	
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'DB_EXPORT_TOURNAMENT' ); ?></legend> 
			<table width="100%">
				<tr>
				<td width="50%"><?php echo $this->lists['et_lid'] ?></td>
				<td width="50%"><input type="submit" value=" Datei erstellen "></td>	
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	
	<?php } ?>
	<?php if ($countryversion =="de") { ?>		
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'DB_EXPORT_TEAMTOURNAMENT' ); ?></legend> 
			<table width="100%">
				<tr>
				<td width="50%"><?php echo $this->lists['mt_lid'] ?></td>
				<td width="50%"><input type="submit" value=" Datei erstellen "></td>	
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_( 'form.token' ); ?>
		</form>
	<?php } ?>
<div class="clr"></div>
</div>