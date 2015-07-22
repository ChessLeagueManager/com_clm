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

$swt    = JRequest::getVar('swt', '', 'default', 'string');
$update = JRequest::getVar('update', 0);
$swt_id = JRequest::getVar('swt_id', 0, 'default', 'int');
$man	= JRequest::getVar('man', 0, 'default', 'int');
$sid	= $this->swt_db_data['sid'];
$mturnier = JRequest::getVar('mturnier', 0, 'default', 'int');
$ungerade = JRequest::getVar('ungerade', false, 'default', 'bool');

$spielerid = JRequest::getVar ('spielerid');
$lid = JRequest::getVar('lid', 0, 'default', 'int');
?>

<script language="javascript" type="text/javascript">
    <!--
    function submitbutton(pressbutton) {
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            submitform( pressbutton );
            return;
        }
        // do field validation
        /*if (form.name.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_1', true ); ?>" );
        } else if ( getSelectedValue('adminForm','sid') == 0 ) {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_2', true ); ?>" );
        } else if (form.stamm.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_3', true ); ?>" );
        } else if (form.ersatz.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_4', true ); ?>" );
        } else if (form.teil.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_5', true ); ?>" );
        } else if (form.runden.value == "") {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_6', true ); ?>" );
        } else if ( getSelectedValue('adminForm','durchgang') == "" ) {
            alert( "<?php echo JText::_( 'LEAGUE_HINT_7', true ); ?>" );
        } else {*/
            submitform( pressbutton );
        //}
    }
	//-->
</script>

<form action="index.php" method="get" name="adminForm" id="adminForm">
    <div class="col width-50">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'SWT_LEAGUE_TEAM_DATA' ); ?></legend>
            <table class="adminlist">
                <tr>
                    <td width="20%" nowrap="nowrap">
                        <label for="name"><?php echo JText::_( 'SWT_LEAGUE_TEAM_NAME' ); ?></label>
                    </td>
                    <td colspan="2">
                        <input class="inputbox" type="text" name="name" id="name" size="32" maxlength="32" value="<?php echo $this->swt_data['man_name']; ?>" />
                    </td>
                    <td nowrap="nowrap">
                        <label for="man_nr"><?php echo JText::_( 'SWT_LEAGUE_TEAM_ID' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php if (isset($this->db_man_nr)) echo $this->db_man_nr; else echo ''; ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap="nowrap">
                        <label for="verein"><?php echo JText::_( 'SWT_LEAGUE_TEAM_CLUB' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php echo $this->lists['vereine']; ?>
                    </td>
                    <td nowrap="nowrap">
                        <label for="tln_nr"><?php echo JText::_( 'SWT_LEAGUE_TEAM_NUMBER' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php echo $this->lists['tln_nr']; ?>
                    </td>
                </tr>
				<?php for ($i = 0; $i < $this->swt_db_data['anz_sgp']; $i++) { ?>
				<tr>
					<td class="key" nowrap="nowrap"><label for="<?php echo 'sg_zps'.$i; ?>"><?php echo JText::_( 'SWT_LEAGUE_TEAM_SG_CLUB' )." : "; ?></label>
					</td>
                    <td colspan="2">
						<?php echo $this->lists['sg'.$i]; ?>
					</td>
                    <td colspan="3">
                    </td>
                </tr>
				<?php } ?>
            </table>
        </fieldset>

        <fieldset class="adminform">
            <legend><?php echo JText::_( 'SWT_LEAGUE_PLAYERS_1' ); ?></legend>
            <table  class="adminlist">
                <?php echo $this->tables['stammspieler']; ?>
            </table>
        </fieldset>
    </div>
    <div class="col width-50">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'SWT_LEAGUE_PLAYERS_2' ); ?></legend>
            <table class="adminlist">
                <?php echo $this->tables['ersatzspieler']; ?>
            </table>
        </fieldset>
    </div>

	<div class="clr"></div>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtligaman" />
	<input type="hidden" name="controller" value="swtligaman" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="swt" value="<?php echo $swt; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
    <input type="hidden" name="swt_id" value="<?php echo $swt_id; ?>" />
    <input type="hidden" name="lid" value="<?php echo $lid; ?>" />
   	<input type="hidden" name="man" value="<?php echo $man; ?>" />
   	<input type="hidden" name="sid" value="<?php echo $sid; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="ungerade" value="<?php echo $ungerade; ?>" />
   	<?php
   		for ($i = 1; $i <= $this->swt_db_data['anz_spieler']; $i++) {
			if (!isset($this->swt_data['spieler_'.$i])) continue;
			$dwzid = $this->swt_data['spieler_'.$i]['dwzid'];
   			if (isset ($spielerid[$dwzid])) {
   			?>
   	<input type="hidden" name="spielerid_<?php echo $dwzid; ?>" value="<?php echo $spielerid[$dwzid]; ?>" />
   			<?php
   			}
   		}
   	?>
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
