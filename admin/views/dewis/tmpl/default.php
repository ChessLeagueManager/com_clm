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
<table width="100%" class="admintable"> 
		<tr>
<form action="index.php?option=com_clm&view=dewis&layout=verein&task=verein" method="post" id="vereine_form" name="adminForm"  enctype="multipart/form-data" >
		<td width="50%" style="vertical-align: top;">
		<fieldset class="adminform"> 
		<legend><?php echo JText::_( 'Verein aus Dewis auslesen' ); ?></legend> 
			<table width="100%">
				<tr>
					<td width="50%"><?php echo $this->lists['vid'] ?></td>
					<td width="50%"><a href="#" onclick="Joomla.submitbutton('verein')" class="toolbar">Verein auslesen</a>
					</td>	
				</tr>
			</table>
		</fieldset>
				
		<br>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Spieler suchen' ); ?></legend>
			<table width="100%">
				<tr>
				<td width="50%"><input name="name" value="Name,Vorname" onfocus="if(this.value== 'Name,Vorname'){this.value=''};" /></td>
				<td width="50%"><a href="#" onclick="Joomla.submitbutton('spieler_suchen')" class="toolbar">Spieler suchen</a>
				</td>	
				</tr>
			</table>
		</fieldset>
		
		</td>


	<td width="50%" style="vertical-align: top;">
	
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Turnier suchen' ); ?></legend>
			<table width="100%">
				<tr>
				<td width="30%">Start</td>
				<td width="40%">
				<?php echo JHTML::_('calendar', '', 'sdatum', 'sdatum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'10',  'maxlength'=>'19')); ?>
				</td>
				<td width="30%">&nbsp;</td>
				</tr>
				
				<tr>
				<td width="30%">Ende</td>
				<td width="40%">
				<?php echo JHTML::_('calendar', '', 'edatum', 'edatum', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'10',  'maxlength'=>'19')); ?>
				</td>
				<td width="30%">
				<a href="#" onclick="Joomla.submitbutton('turnier_suchen')" class="toolbar">Turnier suchen</a>
				</td>
				</tr>

				<tr>
				<td width="30%">Suchbegriff (optional)</td>
				<td width="40%">
				<input name="turnier" value=""  />
				</td>
				<td width="30%">&nbsp;</td>
				</tr>

			</table>
		</fieldset>

		
	</td>
	</tr>		
	</table>

	<input type="hidden" name="controller" value="dewis" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>		


