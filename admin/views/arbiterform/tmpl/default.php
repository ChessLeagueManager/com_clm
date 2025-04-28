<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

	$lid = clm_core::$load->request_int('lid');
	$tid = clm_core::$load->request_int('tid');
	$returnview = clm_core::$load->request_string('returnview');
	if (is_null($this->arbiter->bemerkungen)) $this->arbiter->bemerkungen = '';
	if (is_null($this->arbiter->bem_int)) $this->arbiter->bem_int = '';

	$lang = clm_core::$lang->arbiter;
?>

	<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.fideid.value == 0) {
			alert( jserror['enter_fideid'] );
		} elseif (form.name.value == "") {
			alert( jserror['enter_name'] );
		} else {
			Joomla.submitform( pressbutton );
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
					<label for="fide"><?php echo $lang->title; ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="title" id="title" size="8" maxlength="8" value="<?php echo $this->arbiter->title; ?>" />
				</td>
			</tr>

			<tr>
				<td width="40%" class="paramlist_key">
					<label for="name"><?php echo $lang->name; ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="name" id="name" size="40" maxlength="60" value="<?php echo $this->arbiter->name; ?>" />
				</td>
			</tr>

			<tr>
				<td width="40%" class="paramlist_key">
					<label for="vorname"><?php echo $lang->vorname; ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="vorname" id="vorname" size="40" maxlength="60" value="<?php echo $this->arbiter->vorname; ?>" />
				</td>
			</tr>

			<tr>
				<td width="40%" class="paramlist_key">
					<label for="fide"><?php echo $lang->fideid; ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="fideid" id="fideid" size="8" maxlength="8" value="<?php echo $this->arbiter->fideid; ?>" />
				</td>
			</tr>
			<tr>
				<td width="40%" class="paramlist_key">
					<label for="fidecco"><?php echo $lang->fidefed; ?>:</label>
				</td>
				<td class="paramlist_value">
					<input class="inputbox" type="text" name="fidefed" id="fidefed" size="3" maxlength="3" value="<?php echo $this->arbiter->fidefed; ?>" />
				</td>
			</tr>
			
			
			</table>
	  </fieldset>
		<fieldset class="adminform">
	
			<legend><?php echo $lang->status; ?></legend>
		
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
						<textarea class="inputbox" name="bemerkungen" id="bemerkungen" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->arbiter->bemerkungen);?></textarea>
					</td>
				</tr>
			</table>
		
			<table class="adminlist">
				<legend><?php echo JText::_( 'REMARKS_INTERNAL' ); ?></legend>
				<br>
				<tr>
					<td width="100%" valign="top">
						<textarea class="inputbox" name="bem_int" id="bem_int" cols="40" rows="5" style="width:90%"><?php echo str_replace('&','&amp;',$this->arbiter->bem_int);?></textarea>
					</td>
				</tr>
			</table>
		
		</fieldset>
	</div>
  



<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="arbiterform" />
	<input type="hidden" name="id" value="<?php echo $this->arbiter->id; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="returnview" value="<?php echo $returnview; ?>" />
	<input type="hidden" name="controller" value="arbiterform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
