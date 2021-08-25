<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

//$swt = clm_core::$load->request_string ('swt_file', '');
$swt_file = clm_core::$load->request_string ('swt_file', '');
//$sid = clm_core::$load->request_int ('filter_saison', 0);

jimport( 'joomla.filesystem.file' );
$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'swt' . DIRECTORY_SEPARATOR;

$mturnier = 1;
/* $liga_mannschaften = CLMSWT::readInt ($path.$swt, 602);
$modus = CLMSWT::readInt ($path.$swt, 596);
if (empty ($liga_mannschaften) || $modus != 1) { // keine Liga oder nicht vollrundig
	$mturnier = 1;
} */
$noOrgReference = '0';
$noBoardResults = '0';
$liga = clm_core::$load->request_int('liga', 0);
?>

<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) { 
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
            return;
        }
        // do field validation
		if ( pressbutton == 'update' ) {
			// get references to select list and display text box
			var sel = document.getElementById('liga');			
			var opt;
			for ( var i = 0, len = sel.options.length; i < len; i++ ) {
				opt = sel.options[i];
				if ( opt.selected === true ) {
					val = opt.value;
					break;
				}
			}
			if ( val == '' ) {
				alert( "<?php echo JText::_( 'LEAGUE_HINT_00', true ); ?>" ); }
		}
        Joomla.submitform( pressbutton );
    }
	
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" >
	<table width="100%" class="admintable"> 
		<tr>
			<td width="35%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_LEAGUE_OVERWRITE_HINTS_TAB' ); ?></legend> 
					<?php echo JText::_( 'SWT_LEAGUE_OVERWRITE_HINTS_TEXT' ); ?>
				</fieldset>
			</td>
			<td width="65%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_LEAGUE_OVERWRITE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%"><?php echo $this->lists['saisons'] ?></td>
							<td width="60%"><?php echo JText::_( 'SWT_LEAGUE_OVERWRITE_SEASONS_TEXT' ); ?></td>
						</tr>
						<tr>
							<td width="40%"><?php echo $this->lists['ligen'] ?></td>
							<td width="60%"><?php echo JText::_( 'SWT_LEAGUE_OVERWRITE_LEAGUE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'SWT_LEAGUE_MODE_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%">
								<select name="mturnier" id="mturnier" value="0" size="1">
									<option value="0" <?php if ($mturnier != 1) { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_MODE_LEAGUE' );?></option>
									<option value="1" <?php if ($mturnier == 1) { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_MODE_MTURN' );?></option>
								</select>
							</td>
							<td width="60%"><?php echo JText::_( 'SWT_MODE_TEXT' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<br><br><br><br>
				<fieldset class="adminform"> 
					<legend style="font-size:120%;"><?php //echo JText::_( 'SWT_LEAGUE_NOORGREFERENCE_TAB' ); 
						echo JText::_( 'SWT_LEAGUE_OPTIONS' ); ?><span style="font-size:70%";><?php echo JText::_( 'SWT_LEAGUE_OPTIONS_HINT' ); ?></span></legend> 
					<table width="100%">
						<tr>
							<td width="40%">
								<select name="noOrgReference" id="noOrgReference" value="0" size="1">
									<option value="0" <?php if ($noOrgReference == '0') { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_NOORGREFERENCE_0' );?></option>
									<option value="1" <?php if ($noOrgReference == '1') { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_NOORGREFERENCE_1' );?></option>
								</select>
							</td>
							<td width="60%"><?php echo JText::_( 'SWT_NOORGREFERENCE_T0' )."<br>".JText::_( 'SWT_NOORGREFERENCE_T1' ); ?></td>
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform"> 
					<legend><?php //echo JText::_( 'SWT_LEAGUE_NOBOARDRESULTS_TAB' ); ?></legend> 
					<table width="100%">
						<tr>
							<td width="40%">
								<select name="noBoardResults" id="noBoardResults" value="0" size="1">
									<option value="0" <?php if ($noBoardResults == '0') { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_NOBOARDRESULTS_0' );?></option>
									<option value="1" <?php if ($noBoardResults == '1') { echo 'selected="selected"'; } ?>><?php echo JText::_( 'SWT_NOBOARDRESULTS_1' );?></option>
								</select>
							</td>
							<td width="60%"><?php echo JText::_( 'SWT_NOBOARDRESULTS_T0' )."<br>".JText::_( 'SWT_NOBOARDRESULTS_T1' ); ?></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>		
	</table>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtliga" />
	<input type="hidden" name="controller" value="swtliga" />
	<input type="hidden" name="task" value="" />
<!--	<input type="hidden" name="swt" value="<?php echo $swt; ?>" /> -->
	<input type="hidden" name="swt_file" value="<?php echo $swt_file; ?>" />
<!--	<input type="hidden" name="sid" value="<?php echo $sid; ?>" />  -->
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
