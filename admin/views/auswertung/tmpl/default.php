<?php
/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2020 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
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
	<legend><?php echo JText::_('DB_EXPORT_FILES'); ?></legend>
	<?php echo $this->lists['files']; ?>
	</fieldset>
</div>

<div class="width-50 fltlft">
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform"> 
		<legend><?php echo JText::_('DB_EXPORT_LEAGUE'); ?></legend>
			<table width="100%">
				<tr>
					<td width="70%"><?php echo $this->lists['lid'] ?></td>
					<td width="30%"><input type="submit" value="<?php echo JText::_('DB_FILE_CREATE'); ?>"></td>
				</tr>
				<tr>
					<td width="70%"><select id="filter_format" name="filter_format" class="inputbox" size="1" onchange="">
						<option value="0"><?php echo JText::_('DB_FILE_FORMAT_0'); ?></option>
						<?php if ($countryversion == "de") { ?>
							<option value="1"><?php echo JText::_('DB_FILE_FORMAT_1'); ?></option>
							<option value="2"><?php echo JText::_('DB_FILE_FORMAT_2'); ?></option>
						<?php } elseif ($countryversion == "en") { ?>
							<option value="3"><?php echo JText::_('DB_FILE_FORMAT_3'); ?></option>
						<?php } ?>
					</select></td><td width="30%">
				</tr>
				<tr>
					<td width="70%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_FROM_ROUND_HINT'); ?>">
						<label for="lround"><?php echo JText::_('DEWIS_FROM_ROUND'); ?></label>
					</td>
					<td width="30%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_FROM_ROUND_HINT'); ?>">
						<input class="inputbox" type="text" name="lround" id="lround" size="10" maxlength="6" value=""/>
					</td>
				</tr>
				<tr>
					<td width="70%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_AND_PAIRING_HINT'); ?>">
						<label for="lpairing"><?php echo JText::_('DEWIS_AND_PAIRING'); ?></label>
					</td>
					<td width="30%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_AND_PAIRING_HINT'); ?>">
						<input class="inputbox" type="text" name="lpairing" id="lpairing" size="20" maxlength="20" value=""/>
					</td>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_('form.token'); ?>
		</form>
		
	<?php //if ($countryversion =="de") {?>	
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform">
		<legend><?php echo JText::_('DB_EXPORT_TOURNAMENT'); ?></legend> 
			<table width="100%">
				<tr>
					<td width="70%"><select id="filter_format" name="filter_format" class="inputbox" size="1" onchange="">
					<?php if ($countryversion == "de") { ?>
						<option value="2"><?php echo JText::_('DB_FILE_FORMAT_2'); ?></option>
					<?php } elseif ($countryversion == "en") { ?>
						<option value="3"><?php echo JText::_('DB_FILE_FORMAT_3'); ?></option>
					<?php } ?>
						<option value="4"><?php echo JText::_('DB_FILE_FORMAT_4'); ?></option>
					</select></td><td width="30%"></td>
				</tr>
				<tr>
				<td width="70%"><?php echo $this->lists['et_lid'] ?></td>
				<td width="30%"><input type="submit" value="<?php echo JText::_('DB_FILE_CREATE'); ?>"></td>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_('form.token'); ?>
		</form>
	
	<?php //}?>
	<?php //if ($countryversion =="de") {?>		
		<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform">
		<legend><?php echo JText::_('DB_EXPORT_TEAMTOURNAMENT'); ?></legend> 
			<table width="100%">
				<tr>
				<td width="70%"><?php echo $this->lists['mt_lid'] ?></td>
				<td width="30%"><input type="submit" value="<?php echo JText::_('DB_FILE_CREATE'); ?>"></td>	
				</tr>
				<tr>
					<td width="70%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_FROM_ROUND_HINT'); ?>">
						<label for="mround"><?php echo JText::_('DEWIS_FROM_ROUND'); ?></label>
					</td>
					<td width="30%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_FROM_ROUND_HINT'); ?>">
						<input class="inputbox" type="text" name="mround" id="mround" size="10" maxlength="6" value=""/>
					</td>
				</tr>
				<tr>
					<td width="70%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_AND_PAIRING_HINT'); ?>">
						<label for="mpairing"><?php echo JText::_('DEWIS_AND_PAIRING'); ?></label>
					</td>
					<td width="30%" nowrap="nowrap" title="<?php echo JText::_('DEWIS_AND_PAIRING_HINT'); ?>">
						<input class="inputbox" type="text" name="mpairing" id="mpairing" size="20" maxlength="20" value=""/>
					</td>
				</tr>
			</table>
		</fieldset>
		<input type="hidden" name="controller" value="auswertung" />
		<input type="hidden" name="task" value="datei" />
		<?php echo JHTML::_('form.token'); ?>
		</form>
	<?php //}?>
<div class="clr"></div>
</div>
