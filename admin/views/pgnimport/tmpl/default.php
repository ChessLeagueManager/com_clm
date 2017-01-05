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

$pgn = JRequest::getVar ('pgn_file', '', 'default', 'string');
$pgn_file = JRequest::getVar ('pgn_file', '', 'default', 'string');
//$swt_file = JRequest::getVar ('swt_file', '', 'default', 'string');
$task = JRequest::getVar ('task', '', 'default', 'string');

$stask = 0;

jimport( 'joomla.filesystem.file' );
$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;
//echo "<br>vi-pgn:"; var_dump($pgn); //die();		
//echo "<br>vi-pgn_file:"; var_dump($pgn_file); //die();		
//echo "<br>vi-swt_file:"; var_dump($swt_file); die();		

$liga = JRequest::getVar('liga', '', 'default', 'string');
//echo "<br>ca-html-pgnimport: liga $liga "; var_dump($liga); //die();
?>

<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) { 
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            submitform( pressbutton );
            return;
        }
        // do field validation
		if ( getSelectedValue('adminForm','task') == 'update' ) {
			if ( getSelectedValue('adminForm','liga') == 0 ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_00', true ); ?>" );
			} else {
				submitform( pressbutton );
			}
        } else {
            submitform( pressbutton );
        }
    }
	
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<table width="100%" class="admintable"> 
		<tr>
			<td width="35%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'PGN_LEAGUE_OVERWRITE_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'PGN_LEAGUE_OVERWRITE_HINTS_TEXT' ); ?>
				</fieldset>
			</td>
			<td width="65%" style="vertical-align: top;">
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
			</td>
		</tr>		
	</table>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="pgnimport" />
	<input type="hidden" name="controller" value="pgnimport" />
	<input type="hidden" name="task" value="<?php echo $task; ?>" />
	<input type="hidden" name="pgn" value="<?php echo $pgn; ?>" />
	<input type="hidden" name="pgn_file" value="<?php echo $pgn_file; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
