<?php require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'javascript'.DS.'colorPicker.php'); ?>
<link rel="stylesheet" href="<?php echo JURI::base().'components'.DS.'com_clm'.DS.'images'.DS.'colorpicker'.DS.'colorPicker.css'; ?>" type="text/css"></link>

<?php
defined('_JEXEC') or die('Restricted access');

	$config			= JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%" valign="top">
	    <fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_GENEREL' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_generell'); ?>
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TEMP_BACKGROUND' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_background'); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TEMP_UPDOWN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_aufab'); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>


      <td width="50%" valign="top">
	<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TEMP_TEXT' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_text'); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TEMP_MSCH' ); ?></legend>
            <?php echo JText::_( 'CONFIG_TEMP_MSCH_HINT' ); ?><br />
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_mannschaften'); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TEMP_WRONG' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'template_wrong'); ?>
       	 				</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'Color Picker' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        <?php echo JText::_( 'CONFIG_TEMP_PICKER' ); ?><br />
<input type="text" id="bau" onclick="startColorPicker(this)" onkeyup="maskedHex(this)">

<input type="text" id="baubau" onclick="startColorPicker(this)" ></td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
        
		</td>
      </tr>
    </table>