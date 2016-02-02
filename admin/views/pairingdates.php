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

class CLMViewPairingDates
{

public static function setPairingDatesToolbar($row)
	{
	// Menubilder laden
		clm_core::$load->load_css("icons_images");

	$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
	JArrayHelper::toInteger($cid, array(0));
	if (JRequest::getVar( 'task') == 'edit') { $text = JText::_( 'Edit' );}
		else { $text = JText::_( 'New' );}
	$verein 	= JRequest::getVar( 'verein' );
	JToolBarHelper::title(  JText::_( 'TITLE_PAARUNG').' '.$row->name.': [ '. $text.' ]' ,'clm_settings_2');
	JToolBarHelper::custom( 'save', 'save.png', 'save_f2.png', JText::_( 'SAVE'),false );
	JToolBarHelper::custom( 'apply', 'apply.png', 'apply_f2.png', JText::_( 'APPLY'),false );
	JToolBarHelper::cancel();
	JToolBarHelper::help( 'screen.clm.edit' );
	}
		
public static function pairingdates( &$row, $paarung, $man, $count_man, $option, $cid, &$lists)
	{
	CLMViewPairingDates::setPairingDatesToolbar($row);
	JRequest::setVar( 'hidemainmenu', 1 );
	JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
		?>
<!--- <br> --->

	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="width-50 fltlft">

	<fieldset class="adminform">
<!---	<legend><?php echo JText::_( 'PAARUNG_AKTUELL' ); ?></legend> --->

	<table class="admintable">

<?php $count = 0; 
if ($row->durchgang > 1) { $runden_counter = $row->durchgang * $row->runden; }
  else { $runden_counter = $row->runden; }

	for ($x = 0; $x < $runden_counter; $x++ ) { ?>
		<?php if ($row->runden_modus == 4) $pairings = pow(2,($row->runden - 1 - $x));
		      elseif ($row->runden_modus == 5) { $pairings = pow(2,($row->runden - 2 - $x));
												if ($pairings < 1) $pairings = 1; }
		      else $pairings = $row->teil / 2; ?>
 
	<tr>
	<td class="key" nowrap="nowrap" colspan="7" height="24"><h3><?php echo $paarung[$count]->rname;
		if (isset($paarung[$count]->datum) AND $paarung[$count]->datum > '1900-01-01') {
			echo '  '.JText::_('ON_DAY').' '.JHTML::_('date',  $paarung[$count]->datum, JText::_('DATE_FORMAT_CLM_F')); 
			if(isset($paarung[$count]->enddatum) and $paarung[$count]->enddatum > '1970-01-01' and $paarung[$count]->enddatum != $paarung[$count]->datum) { 
			echo ' - '.JHTML::_('date',  $paarung[$count]->enddatum, JText::_('DATE_FORMAT_CLM_F')); } }?>
	</h3></td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap" height="24"><?php echo JText::_( 'PAARUNG_DG' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_RUNDE' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_PAAR' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_HEIM' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_GAST' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_HEIM' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_GAST' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_DATE' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_TIME' ); ?></td>
	</tr>

	<?php for ($y = 0; $y < $pairings; $y++ ) { ?>
	<tr>
		<td class="key" nowrap="nowrap" height="24"><?php echo $paarung[$count]->dg; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->runde; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->paar; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->tln_nr; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->gegner; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->hname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->gname; ?></td>
		
   		<?php 
   		$ndate = 'D'.$paarung[$count]->dg.'R'.$paarung[$count]->runde.'P'.$paarung[$count]->paar.'Date'; 
   		$ntime = 'D'.$paarung[$count]->dg.'R'.$paarung[$count]->runde.'P'.$paarung[$count]->paar.'Time';
		//echo "<br>ndate:".$ndate."  pdate:".$paarung[$count]->pdate."  datum:".$paarung[$count]->datum;
		if ($paarung[$count]->pdate < '1970-01-02' AND $paarung[$count]->datum > '1970-01-01') {
			$paarung[$count]->pdate = $paarung[$count]->datum;
			$paarung[$count]->ptime = $paarung[$count]->startzeit;
		//echo "<br>Jndate:".$ndate."  pdate:".$paarung[$count]->pdate."  datum:".$paarung[$count]->datum;
		}
		?>
		<td>
            <?php echo JHtml::_('calendar', $paarung[$count]->pdate, $ndate, $ndate, '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'12')); ?>
        </td>
		<td>
			<input class="inputbox" type="time" name="<?php echo $ntime; ?>" id="<?php echo $ntime; ?>" size="6" maxlength="6" value="<?php echo substr($paarung[$count]->ptime,0,5); ?>"  />
		</td>
	</tr>
<?php $count++; }} ?>

	</table>
	</fieldset>
	</div>

	
	<div class="clr"></div>

	<input type="hidden" name="section" value="pairingdates" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}
} ?>
