<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die('Restricted access');

$swt    = JRequest::getVar('swt', '', 'default', 'string');
$update = JRequest::getVar('update', 0);
$lid = JRequest::getVar('lid', 0);
$swt_id = JRequest::getVar('swt_id', 0, 'default', 'int');
$sid = JRequest::getVar('sid', 0, 'default', 'int');
$par	= JRequest::getVar('par', 0, 'default', 'int');
$runde	= JRequest::getVar('runde', 0, 'default', 'int');
$dgang	= JRequest::getVar('dgang', 0, 'default', 'int');
$mturnier = JRequest::getVar('mturnier', 0, 'default', 'int');
$ungerade = JRequest::getVar('ungerade', false, 'default', 'bool');
$noOrgReference = JRequest::getVar('noOrgReference', '0', 'default', 'string');
$noBoardResults = JRequest::getVar('noBoardResults', '0', 'default', 'string');


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

<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div class="col width-50">
    	<?php $sw_prelims = false;
    		for ($p = 1; $p <= $this->anz_paarungen; $p++) { 
				if (!isset($this->swt_data[$p])) break;
				if (($this->swt_data[$p]['gast_mannschaft'] != "spielfrei") AND (isset($this->swt_data[$p]['hemsum'])) AND ($this->swt_data[$p]['hemsum'] != $this->swt_data[$p]['hmmsum']))  $sw_prelims = true;
				if (($this->swt_data[$p]['heim_mannschaft'] != "spielfrei") AND (isset($this->swt_data[$p]['gemsum'])) AND ($this->swt_data[$p]['gemsum'] != $this->swt_data[$p]['gmmsum']))  $sw_prelims = true;
    		}	
			if 	($sw_prelims) {   ?>
				<fieldset class="adminform">
				    <legend><?php echo $this->swt_db_data['liga_name'] . ' ' . JText::_( 'SWT_LEAGUE_DG' ) . ' ' . ($dgang+1) . ' ' . JText::_( 'SWT_LEAGUE_ROUND' ) . ' ' . ($runde+1) . ', ' . JText::_( 'SWT_LEAGUE_PRELIMS' ) ; ?></legend>
				    <table  class="adminlist">
						<?php for ($p = 1; $p <= $this->anz_paarungen; $p++) { if (!isset($this->swt_data[$p])) break; ?>
							<?php if ($this->swt_data[$p]['hemsum'] != $this->swt_data[$p]['hmmsum'])  { ?>
							<tr>
								<th widhth="90%" nowrap="nowrap">
			    			<label><?php echo JText::_( 'SWT_LEAGUE_PAIRING' ).' '.$p.' '.$this->swt_data[$p]['heim_mannschaft'].': '.JText::_( 'SWT_LEAGUE_PRELIM01' ).'('.$this->swt_data[$p]['hmmsum'].') '.JText::_( 'SWT_LEAGUE_PRELIM02' ); ?></label>
								</th>
							</tr>	
							<?php } ?>
							<?php if ($this->swt_data[$p]['gemsum'] != $this->swt_data[$p]['gmmsum'])  { if (!isset($this->swt_data[$p])) break; ?>
							<tr>
								<th widhth="90%" nowrap="nowrap">
				    			<label><?php echo JText::_( 'SWT_LEAGUE_PAIRING' ).' '.$p.' '.$this->swt_data[$p]['gast_mannschaft'].':'.JText::_( 'SWT_LEAGUE_PRELIM01' ).'('.$this->swt_data[$p]['gmmsum'].') '.JText::_( 'SWT_LEAGUE_PRELIM02' ); ?></label>
								</th>
							</tr>	
							<?php } ?>
						<?php } ?>
				    </table>
				</fieldset>
			<?php }	
    		for ($p = 1; $p <= $this->anz_paarungen; $p++) { if (!isset($this->swt_data[$p])) break;
    			?>
				<fieldset class="adminform">
				    <legend <?php if ($noBoardResults == '1') { echo 'style="font-size:120%;"'; } ?>><?php echo $this->swt_db_data['liga_name'] . ' ' . JText::_( 'SWT_LEAGUE_DG' ) . ' ' . ($dgang+1) . ' ' . JText::_( 'SWT_LEAGUE_ROUND' ) . ' ' . ($runde+1) . ', ' . JText::_( 'SWT_LEAGUE_PAIRING' ) . ' ' . $p; ?></legend>
				    <table  class="adminlist">
					  <?php if ($noBoardResults == '0') { ?>  
				    	<tr>
				    		<th width="10%" nowrap="nowrap">
				    			<label><?php echo JText::_( 'SWT_LEAGUE_BOARD' ); ?></label>
				    		</th>
				    		<th nowrap="nowrap">
				    			<label><?php echo $this->swt_data[$p]['heim_mannschaft']; ?></label>
				    		</th>
				    		<th nowrap="nowrap">
				    			<label><?php echo $this->swt_data[$p]['gast_mannschaft']; ?></label>
				    		</th>
				    		<th nowrap="nowrap">
				    			<label><?php echo JText::_( 'SWT_LEAGUE_RESULT' ); ?></label>
				    		</th>
				    		<th nowrap="nowrap">
				    			<label><?php echo JText::_( 'SWT_LEAGUE_RESULT_KORR' ); ?></label>
				    		</th>
				   		</tr>
						<?php echo $this->tables['auswahl'][$p]; ?>
					  <?php } elseif ($noBoardResults == '1') { ?>  
						<tr>
				    		<th width="20%" nowrap="nowrap">
				    			<label><?php echo $this->swt_data[$p]['heim_mannschaft']; ?></label>
				    		</th>
				    		<th width="20%" nowrap="nowrap">
				    			<label><?php echo $this->swt_data[$p]['gast_mannschaft']; ?></label>
				    		</th>
				    		<th width="20%" nowrap="nowrap">
				    			<label><?php echo $this->swt_data[$p]['hmmsum']." : ".$this->swt_data[$p]['gmmsum']; ?></label>
				    		</th>
				   		</tr>
					  <?php } ?>  
				    </table>
				</fieldset>
				<?php
			}
		?>
    </div>
    <!-- ggf. hier noch irgendwas anzeigen (z.B. Bemerkungen, interne Bemerkungen, ...) -->
    
    <!--<div class="col width-50">
        <fieldset class="adminform">
            <legend><?php echo JText::_( 'SWT_LEAGUE_PLAYERS_2' ); ?></legend>
            <table class="adminlist">
                <?php echo $this->tables['ersatzspieler']; ?>
            </table>
        </fieldset>
    </div>
	-->
	<div class="clr"></div>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="swtligaerg" />
	<input type="hidden" name="controller" value="swtligaerg" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="swt" value="<?php echo $swt; ?>" />
	<input type="hidden" name="update" value="<?php echo $update; ?>" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
    <input type="hidden" name="swt_id" value="<?php echo $swt_id; ?>" />
    <input type="hidden" name="sid" value="<?php echo $sid; ?>" />
    <input type="hidden" name="runde" value="<?php echo $runde; ?>" />
    <input type="hidden" name="dgang" value="<?php echo $dgang; ?>" />
	<input type="hidden" name="mturnier" value="<?php echo $mturnier; ?>" />
	<input type="hidden" name="noOrgReference" value="<?php echo $noOrgReference; ?>" />
	<input type="hidden" name="noBoardResults" value="<?php echo $noBoardResults; ?>" />
	<input type="hidden" name="ungerade" value="<?php echo $ungerade; ?>" />
    <?php echo $this->hidden['farbe']; ?>
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
