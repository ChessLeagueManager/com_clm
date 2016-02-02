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

class CLMViewPaarung
{

public static function setPaarungToolbar($row)
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
		
public static function paarung( &$row, $paarung, $man, $count_man, $option, $cid, &$lists)
	{
	CLMViewPaarung::setPaarungToolbar($row);
	JRequest::setVar( 'hidemainmenu', 1 );
	JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
	//Liga-Parameter aufbereiten
	$paramsStringArray = explode("\n", $row->params);
	$lparams = array();
	foreach ($paramsStringArray as $value) {
		$ipos = strpos ($value, '=');
		if ($ipos !==false) {
			$key = substr($value,0,$ipos);
			if (substr($key,0,2) == "\'") $key = substr($key,2,strlen($key)-4);
			if (substr($key,0,1) == "'") $key = substr($key,1,strlen($key)-2);
			$lparams[$key] = substr($value,$ipos+1);
		}
	}	
	if (!isset($lparams['round_date']))  {   //Standardbelegung
		$lparams['round_date'] = '0'; }
		?>
	<h1><?php echo JText::_( 'PAARUNG_WARNING_LINE1' ); ?></h1>
	<h1><?php echo JText::_( 'PAARUNG_WARNING_LINE2' ); ?></h1>
	<br>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE3' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE4' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE5' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE6' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE7' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE8' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE9' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE10' ); ?></h3>
	<h3><?php echo JText::_( 'PAARUNG_WARNING_LINE11' ); ?></h3>
	<br>

	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="width-40 fltlft">

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'PAARUNG_AKTUELL' ); ?></legend>

	<table class="paramlist admintable">

<?php $count = 0; 
if ($row->durchgang > 1) { $runden_counter = $row->durchgang * $row->runden; }
  else { $runden_counter = $row->runden; }

	for ($x = 0; $x < $runden_counter; $x++ ) { ?>
		<?php if ($row->runden_modus == 4) $pairings = pow(2,($row->runden - 1 - $x));
		      elseif ($row->runden_modus == 5) { $pairings = pow(2,($row->runden - 2 - $x));
												if ($pairings < 1) $pairings = 1; }
		      else $pairings = $row->teil / 2; ?>
 
	<tr>
		<td class="key" nowrap="nowrap" colspan="7" height="24"><b><br><?php echo $paarung[$count]->rname; ?></b></td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap" height="24"><?php echo JText::_( 'PAARUNG_DG' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_RUNDE' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_PAAR' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_HEIM' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_GAST' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_HEIM' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_GAST' ); ?></td>
		<?php if ($lparams['round_date'] == '1') { ?>
			<td class="key" nowrap="nowrap">&nbsp;&nbsp;<?php echo JText::_( 'PAARUNG_DATE' ); ?></td>
			<td class="key" nowrap="nowrap">&nbsp;&nbsp;<?php echo JText::_( 'PAARUNG_TIME' ); ?></td>
		<?php } ?>
	</tr>

	<?php for ($y = 0; $y < $pairings; $y++ ) { ?>
	<tr>
		<td class="key" nowrap="nowrap" height="37"><?php echo $paarung[$count]->dg; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->runde; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->paar; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->tln_nr; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->gegner; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->hname; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $paarung[$count]->gname; ?></td>
		<?php if ($lparams['round_date'] == '1') { ?>
			<td class="key" nowrap="nowrap">&nbsp;&nbsp;<?php echo $paarung[$count]->pdate; ?></td>
			<td class="key" nowrap="nowrap">&nbsp;&nbsp;<?php echo substr($paarung[$count]->ptime,0,5); ?></td>
		<?php } ?>
	</tr>
<?php $count++; }} ?>

	</table>
	</fieldset>
	</div>

	<div class="width-60 fltrt">

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'PAARUNG_CHANGED' ); ?></legend>

	<table class="paramlist admintable">

<?php $count = 0;
if ($row->durchgang > 1) { $runden_counter = $row->durchgang * $row->runden; }
  else { $runden_counter = $row->runden; }

	for ($x = 0; $x < $runden_counter; $x++ ) {
		if ($row->runden_modus == 4) $pairings = pow(2,($row->runden - 1 - $x));
		elseif ($row->runden_modus == 5) { $pairings = pow(2,($row->runden - 2 - $x));
				if ($pairings < 1) $pairings = 1; } 							
		else $pairings = $row->teil / 2; 
		if ($x+1 > (3 * $row->runden)) { 
			$dg = 4;
			$cnt = $x - (3 * $row->runden);
		} elseif ($x+1 > (2 * $row->runden)) { 
			$dg = 3;
			$cnt = $x - (2 * $row->runden);
		} elseif ($x+1 > $row->runden) { 
			$dg = 2;
			$cnt = $x - $row->runden;
		} else { 
			$dg = 1; 
			$cnt = $x;
			}
	?>

	<tr>
		<td class="key" nowrap="nowrap" colspan="7" height="24"><b><br><?php echo $paarung[$count]->rname; ?></b></td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap" height="24"><?php echo JText::_( 'PAARUNG_HEIM' ); ?></td>
		<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_GAST' ); ?></td>
		<?php if ($lparams['round_date'] == '1') { ?>
			<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_DATE' ); ?></td>
			<td class="key" nowrap="nowrap"><?php echo JText::_( 'PAARUNG_TIME' ); ?></td>
		<?php } ?>
	</tr>

	<?php for ($y = 0; $y < $pairings; $y++ ) { ?>
	<tr>
		<td class="key" nowrap="nowrap" height="24">
		  <select size="1" name="<?php echo 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Heim'; ?>" id="<?php echo 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Heim'; ?>">
			<option value="0"><?php echo JText::_( 'PAARUNG_HEIM_WAEHLEN' ); ?></option>
			<?php for ($z=0; $z < $count_man[0]->tln_nr; $z++){ 
				if (($row->runden_modus != 4 AND $row->runden_modus != 5) OR 
				    ($row->runden_modus == 4 AND ($man[$z]->rankingpos >= ($paarung[$count]->runde - 1))) OR
				    ($row->runden_modus == 5 AND (($x < ($row->runden-1) AND $man[$z]->rankingpos >= ($paarung[$count]->runde - 1)) 
											 OR (($x == ($row->runden-1) AND $man[$z]->rankingpos == ($paarung[$count]->runde - 3)))))) { //mtmt ?>
			 <option value="<?php echo $man[$z]->tln_nr; ?>" <?php if (((int)$paarung[$count]->tln_nr) == ((int)$z+1)) { ?> selected="selected" <?php } ?>><?php echo $man[$z]->name; ?></option> 
			<?php }}	?>
		  </select>
		</td>

		<td class="key" nowrap="nowrap">
		  <select size="1" name="<?php echo 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Gast'; ?>" id="<?php echo 'D'.$dg.'R'.($cnt+1).'P'.($y+1).'Gast'; ?>">
			<option value="0"><?php echo JText::_( 'PAARUNG_GAST_WAEHLEN' ); ?></option>
			<?php for ($z=0; $z < $count_man[0]->tln_nr; $z++){ 
				if (($row->runden_modus != 4 AND $row->runden_modus != 5) OR 
				    ($row->runden_modus == 4 AND ($man[$z]->rankingpos >= ($paarung[$count]->runde - 1))) OR
				    ($row->runden_modus == 5 AND (($x < ($row->runden-1) AND $man[$z]->rankingpos >= ($paarung[$count]->runde - 1)) 
											 OR (($x == ($row->runden-1) AND $man[$z]->rankingpos == ($paarung[$count]->runde - 3)))))) { //mtmt ?>
			 <option value="<?php echo $man[$z]->tln_nr; ?>" <?php if (((int)$paarung[$count]->gegner) == ((int)$z+1)) { ?> selected="selected" <?php } ?>><?php echo $man[$z]->name; ?></option> 
			<?php }}	?>
		  </select>
		</td>
		<?php if ($lparams['round_date'] == '1') { 
			$ndate = 'D'.$paarung[$count]->dg.'R'.$paarung[$count]->runde.'P'.$paarung[$count]->paar.'Date'; 
			$ntime = 'D'.$paarung[$count]->dg.'R'.$paarung[$count]->runde.'P'.$paarung[$count]->paar.'Time';
			if ($paarung[$count]->pdate < '1970-01-02' AND $paarung[$count]->datum > '1970-01-01') {
				$paarung[$count]->pdate = $paarung[$count]->datum;
				$paarung[$count]->ptime = $paarung[$count]->startzeit;
			} ?>
			<td>
				<?php echo JHtml::_('calendar', $paarung[$count]->pdate, $ndate, $ndate, '%Y-%m-%d', array('class'=>'text_area', 'size'=>'8',  'maxlength'=>'12')); ?>
			</td>
			<td>
				<input class="inputbox" type="time" name="<?php echo $ntime; ?>" id="<?php echo $ntime; ?>" size="3" maxlength="5" value="<?php echo substr($paarung[$count]->ptime,0,5); ?>"  />
			</td>
		<?php } ?>

	</tr>
<?php $count++; }} ?>

	</table>
	</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="section" value="paarung" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="id" value="<?php echo $row->id; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHtml::_( 'form.token' ); ?>
	</form>
	<?php
	}
} ?>
