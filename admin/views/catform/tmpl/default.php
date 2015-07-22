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

	<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
		} else {
			submitform( pressbutton );
		}
	}
	
	</script>


<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'JDETAILS' ); ?></legend>
			<table class="paramlist admintable">
	
			<tr>
				<td width="40%" class="paramlist_key">
					<label for="name"><?php echo JText::_( 'CATEGORY_NAME' ); ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $this->category->name; ?>" />
				</td>
			</tr>

			<tr>
				<td width="40%" class="paramlist_key">
					<label for="name"><?php echo JText::_( 'CATEGORY_PARENT' ); ?>:</label>
				</td>
				<td class="paramlist_value">
						<?php echo $this->form['parent']; ?>
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="paramlist_key">
					<label for="dateStart">
						<?php echo JText::_( 'CATEGORY_DAYSTART' ); ?>:
					</label>
				</td>
				<td class="paramlist_value">
					<?php echo JHtml::_('calendar', $this->category->dateStart, 'dateStart', 'dateStart', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
				</td>
			</tr>
			
			<tr>
				<td width="40%" class="paramlist_key">
					<label for="dateEnd">
						<?php echo JText::_( 'CATEGORY_DAYEND' ); ?>:
					</label>
				</td>
				<td class="paramlist_value">
					<?php echo JHtml::_('calendar', $this->category->dateEnd, 'dateEnd', 'dateEnd', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'32',  'maxlength'=>'19')); ?>
				</td>
			</tr>
			
			
			</table>
	  </fieldset>
		<fieldset class="adminform">
	
			<legend><?php echo JText::_( 'STATUS' ); ?></legend>
		
			<table class="paramlist admintable">
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="published">
							<?php echo JText::_( 'JPUBLISHED' ); ?>:
						</label>
					</td>
					<td class="paramlist_value"><fieldset class="radio">
						<?php echo $this->form['published']; ?>
					</fieldset></td>
				</tr>
			</table>
	
		</fieldset>
		
  </div>
  
	<div class="width-50 fltrt">
		<fieldset class="adminform">
			
			<legend><?php echo JText::_( 'REMARKS' ); ?></legend>
		
			<table class="paramlist admintable">
				<legend><?php echo JText::_( 'REMARKS_PUBLIC' ); ?></legend>
				<br>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->category->bemerkungen);?></textarea>
					</td>
				</tr>
			</table>
		
			<table class="adminlist">
				<legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
				<br>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->category->bem_int);?></textarea>
					</td>
				</tr>
			</table>
		
		</fieldset>
	</div>
  



<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="catform" />
	<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="controller" value="catform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
