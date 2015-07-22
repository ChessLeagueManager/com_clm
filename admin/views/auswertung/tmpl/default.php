<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');
?>

<div class="width-50 fltlft">
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'Dewis Auswertungsdateien' ); ?></legend>
	<?php echo $this->lists['files']; ?>
	</fieldset>
</div>

<div class="width-50 fltlft">
<form action="index.php?option=com_clm&view=auswertung" method="post" id="auswertung_dewis" name="adminForm"  enctype="multipart/form-data" >
		<fieldset class="adminform"> 
		<legend><?php echo JText::_( 'Liga : Auswertungsdatei erstellen' ); ?></legend>
			<table width="100%">
				<tr>
					<td width="50%"><?php echo $this->lists['lid'] ?></td>
					<td width="50%"><a href="#" onclick="Joomla.submitbutton('datei')" class="toolbar">Datei erstellen</a></td>
				</tr>
				<tr>
					<td width="50%"><select id="filter_format" name="filter_format" class="inputbox" size="1" onchange="">
						<option value="0">- Dateiformat ausw&auml;hlen -</option>
						<option value="1">DSB Format (menschenlesbar)</option>
						<option value="2">XML Dewis</option>
					</select>
				</tr>
			</table>
		</fieldset>
	
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Einzelturnier : Auswertungsdatei erstellen' ); ?></legend> 
			<table width="100%">
				<tr>
				<td width="50%"><?php echo $this->lists['et_lid'] ?></td>
				<td width="50%"><a href="#" onclick="Joomla.submitbutton('datei')" class="toolbar">Datei erstellen</a></td>	
				</tr>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Mannschaftsturnier : Auswertungsdatei erstellen' ); ?></legend> 
			<table width="100%">
				<tr>
				<td width="50%"><?php echo $this->lists['mt_lid'] ?></td>
				<td width="50%"><a href="#" onclick="Joomla.submitbutton('datei')" class="toolbar">Datei erstellen</a></td>	
				</tr>
			</table>
		</fieldset>

	<input type="hidden" name="controller" value="auswertung" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>		
<div class="clr"></div>
</div>

