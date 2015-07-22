<?php require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'javascript'.DS.'colorPicker.php'); ?>
<link rel="stylesheet" href="<?php echo JURI::base().'components'.DS.'com_clm'.DS.'images'.DS.'colorpicker'.DS.'colorPicker.css'; ?>" type="text/css"></link>

<?php
defined('_JEXEC') or die('Restricted access');

	$config			= &JComponentHelper::getParams( 'com_clm' );
	
    ?>
    
    <table class="noshow">
      <tr>
        <td width="50%">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_TOURN_BASICS' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
						<td valign="top">
							<?php echo $this->params->render( 'params', 'tournaments'); ?>
						</td>
					</tr>
				</tbody>
				</table>
			</fieldset>
			
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'CONFIG_FRONTEND_PGN' ); ?></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top">
                        	<?php echo $this->params->render( 'params', 'fe_pgn'); ?>
                            <br>
							<?php echo JText::_( 'CONFIG_TEMP_PICKER' ); ?><br />
						<input type="text" id="bau" onclick="startColorPicker(this)" onkeyup="maskedHex(this)">
						<input type="text" id="baubau" onclick="startColorPicker(this)" >
						</td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>


        <!-- <td width="50%">
			<fieldset class="adminform">
			<legend></legend>
				<table class="admintable" cellspacing="1">
				<tbody>
					<tr>
       					<td valign="top"></td>
      				</tr>
				</tbody>
				</table>
			</fieldset>
		</td>
        -->
      </tr>
    </table>