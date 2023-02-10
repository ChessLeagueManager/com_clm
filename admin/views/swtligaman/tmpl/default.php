<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

$swt_file    = clm_core::$load->request_string('swt_file', '');
$update = clm_core::$load->request_int('update', 0);
$swt_id = clm_core::$load->request_int('swt_id', 0);
$man	= clm_core::$load->request_int('man', 0);
$sid	= $this->swt_db_data['sid'];
$mturnier = clm_core::$load->request_int('mturnier', 0);
$noOrgReference = clm_core::$load->request_string('noOrgReference', '0');
$noBoardResults = clm_core::$load->request_string('noBoardResults', '0');
$ungerade = clm_core::$load->request_int('ungerade', 0);

$spielerid = clm_core::$load->request_array_int('spielerid', NULL, true);
$lid = clm_core::$load->request_int('lid', 0);
$dwz_handling   = clm_core::$load->request_string( 'dwz_handling', '0');
$name_land   = clm_core::$load->request_string( 'name_land', '0');
?>

<script language="javascript" type="text/javascript">
    
	Joomla.submitbutton = function (pressbutton) { 		
        var form = document.adminForm;
        if (pressbutton == 'cancel') {
            Joomla.submitform( pressbutton );
            return;
        }
        // do field validation
        if (form.name.value == "") {
            alert( "<?php echo JText::_( 'MANNSCHAFT_NAMEN_ANGEBEN', true ); ?>" );
        } else {
            Joomla.submitform( pressbutton );
        }
    }
	
</script>

<form action="index.php" method="get" name="adminForm" id="adminForm">
    <div class="col width-50">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'SWT_LEAGUE_TEAM_DATA' ); ?></legend>
            <table class="admintable">
                <tr>
                    <td width="20%" nowrap="nowrap">
                        <label for="name"><?php echo JText::_( 'SWT_LEAGUE_TEAM_NAME' ); ?></label>
                    </td>
                    <td colspan="2">
                        <input class="inputbox" type="text" name="name" id="name" size="22" maxlength="32" value="<?php echo htmlspecialchars($this->swt_data['man_name'], ENT_QUOTES); ?>" />
                    </td>
                    <?php if (isset($this->db_man_nr)) { ?>
						<td nowrap="nowrap">
							<label for="man_nr"><?php echo JText::_( 'SWT_LEAGUE_TEAM_ID' ); ?></label>
						</td>
						<td colspan="2">
							<?php if (isset($this->db_man_nr)) echo $this->db_man_nr; else echo ''; ?>
						</td>
					<?php } ?>
                </tr>
                <tr>
				  <?php if ($noOrgReference == '0') { ?>
                    <td nowrap="nowrap">
                        <label for="verein"><?php echo JText::_( 'SWT_LEAGUE_TEAM_CLUB' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php echo $this->lists['vereine']; ?>
                    </td>
                  <?php } else { ?>
					<input type="hidden" name="zps" value="<?php echo '0'; ?>" />
				  <?php } ?>
                    <td nowrap="nowrap">
                        <label for="tln_nr"><?php echo JText::_( 'SWT_LEAGUE_TEAM_NUMBER' ); ?></label>
                    </td>
                    <td colspan="2">
                        <?php echo $this->lists['tln_nr']; ?>
                    </td>
                </tr>
				 <?php if ($noOrgReference == '0') { ?>
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
				 <?php } } ?>

            </table>
        </fieldset>

        <fieldset class="adminform">
		  <?php  if ($noBoardResults == '0') { ?>
            <legend><?php echo JText::_( 'SWT_LEAGUE_PLAYERS_1' ); ?></legend>
	      <?php  } ?>
            <table  class="adminlist">
                <?php echo $this->tables['stammspieler']; ?>
            </table>
        </fieldset>
   </div>
    <div class="col width-50">
        <fieldset class="adminform">
		  <?php  if ($noBoardResults == '0') { ?>
            <legend><?php echo JText::_( 'SWT_LEAGUE_PLAYERS_2' ); ?></legend>
	      <?php  } ?>
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
	<input type="hidden" name="swt_file" value="<?php echo $swt_file; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
    <input type="hidden" name="swt_id" value="<?php echo $swt_id; ?>" />
    <input type="hidden" name="lid" value="<?php echo $lid; ?>" />
   	<input type="hidden" name="man" value="<?php echo $man; ?>" />
   	<input type="hidden" name="sid" value="<?php echo $sid; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="noOrgReference" value="<?php echo $noOrgReference; ?>" />
	<input type="hidden" name="noBoardResults" value="<?php echo $noBoardResults; ?>" />
	<input type="hidden" name="ungerade" value="<?php echo $ungerade; ?>" />
	<input type="hidden" name="dwz_handling" value="<?php echo $dwz_handling; ?>" />
	<input type="hidden" name="name_land" value="<?php echo $name_land; ?>" />
   	<?php
   		for ($i = 1; $i <= $this->swt_db_data['anz_spieler']; $i++) {
			if (!isset($this->swt_data['spieler_'.$i])) continue;
			$brett = $this->swt_data['spieler_'.$i]['brett'];
			$name = $this->swt_data['spieler_'.$i]['name'];
   			if (isset ($spielerid[$brett])) {
   			?>
   	<input type="hidden" name="spielerid_<?php echo $brett; ?>" value="<?php echo $spielerid[$brett]; ?>" />
   	<input type="hidden" name="name_<?php echo $brett; ?>" value="<?php echo addcslashes ($name,'"'); ?>" />
   			<?php
   			}
   		}
   	?>
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
