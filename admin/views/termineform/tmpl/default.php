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
$today = date("Y-m-d");


?>

	<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
		} else if (form.startdate.value == "0000-00-00") {
			alert( jserror['enter_startdate'] );
		} else if (form.startdate.value == "0000-00-00" && form.starttime.value != "00:00") {
			alert( jserror['dont_starttime'] );
		} else if (form.startdate.value == "0000-00-00" && form.enddate.value != "0000-00-00") {
			alert( jserror['dont_enddate'] );
		} else if (form.startdate.value > form.enddate.value) {
			alert( jserror['enddate_wrong'] );
		} else if (form.starttime.value == "00:00" && form.endtime.value != "00:00") {
			alert( jserror['dont_endtime'] );
		} else if (form.endtime.value != "00:00" && form.allday.checked == true) {
			alert( jserror['dont_allday'] );
		} else if (form.starttime.value == "00:00" && form.noendtime.checked == true) {
			alert( jserror['dont_noendtime'] );
		} else {
			submitform( pressbutton );
		}
	}
		  
		</script>

			
<form action="index.php" method="post" name="adminForm" id="adminForm">
  <div class="width-60 fltlft">
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'JDETAILS' ); ?></legend>

		<table class="paramlist admintable">
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'TERMINE_TASK' ); ?>:</label>
			</td>
			<td colspan="2" >
			<input class="inputbox" type="text" name="name" id="name" size="50" maxlength="100" value="<?php echo $this->termine->name; ?>" />
			</td>
		</tr>

		<tr>
			<td class="key" width="20%" nowrap="nowrap">
			<label for="name"><?php echo JText::_( 'TERMINE_HOST' ); ?>:</label>
			</td>
			<td colspan="2" >
			<?php echo $this->form['vereinZPS']; ?>
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			<label for="adresse">
			<?php echo JText::_( 'TERMINE_ADRESS' ); ?>:
			</label>
			</td>
			<td colspan="2" >
			<input class="inputbox" type="text" name="address" id="address" size="50" maxlength="100" value="<?php echo $this->termine->address; ?>" />
			</td>
		</tr>

		<tr>
			<td width="100" align="right" class="key">
			<label for="event_link">
			<?php echo JText::_( 'TERMINE_EVENT_LINK' ); ?>:
			</label>
			</td>
			<td colspan="2" >
			<input class="inputbox" type="text" name="event_link" id="event_link" size="60" maxlength="500" value="<?php echo $this->termine->event_link; ?>" />
			</td>
		</tr>

	<?php
	// Kategorien
	list($parentArray, $parentKeys) = CLMCategoryTree::getTree();
	if (count($parentArray) > 0)  { // nur, wenn Kategorien existieren
		$parentlist[]	= JHtml::_('select.option',  '0', CLMText::selectOpener(JText::_( 'NO_PARENT' )), 'id', 'name' );
		foreach ($parentArray as $key => $value) {
			$parentlist[]	= JHtml::_('select.option',  $key, $value, 'id', 'name' );
		}
		$catidAlltime = JHtml::_('select.genericlist', $parentlist, 'catidAlltime', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($this->termine->catidAlltime));
		$catidEdition = JHtml::_('select.genericlist', $parentlist, 'catidEdition', 'class="inputbox" size="1" style="max-width: 250px;"', 'id', 'name', intval($this->termine->catidEdition));
	}
	if (isset($catidAlltime)) { ?>
		<tr>
			<td colspan="1" class="paramlist_key">
				<label for="category">
					<?php echo JText::_( 'CATEGORY_ALLTIME' ); ?>:
				</label>
			</td>
			<td colspan="2" class="paramlist_value">
				<?php echo $catidAlltime; ?>
			</td>
			<td colspan="1" class="paramlist_key">
				<label for="category">
					<?php echo JText::_( 'CATEGORY_EDITION' ); ?>:
				</label>
			</td>
			<td colspan="2" class="paramlist_value">
				<?php echo $catidEdition; ?>
			</td>
		</tr>
	<?php } ?>

	<?php if (isset($this->termine->category) AND $this->termine->category != '') { ?>
		<tr>
			<td class="key" width="20%" nowrap="nowrap">
				<label for="name">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'TERMINE_KATEGORIE_HINT' );?>">
				<?php echo JText::_( 'TERMINE_KATEGORIE' ); ?></span></label>
			</td>
			<td colspan="2" >
			<input class="inputbox" type="text" name="category" id="category" size="32" maxlength="60" value="<?php echo $this->termine->category; ?>" />
			</td>
		</tr>
	<?php } ?>
        
		<tr>
			<td width="100" align="right" class="key">
			<label for="startdate">
				<?php echo JText::_( 'TERMINE_STARTDATE' ); ?>:
			</label>
			</td>
			<td>
				<?php if ($this->termine->startdate < '1970-01-01') $this->termine->startdate = $today;
				echo JHtml::_('calendar', $this->termine->startdate, 'startdate', 'startdate', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
				<span >  </span>
			</td><td>
				<input class="inputbox" type="time" name="starttime" id="starttime" size="6" maxlength="6" value="<?php echo substr($this->termine->starttime,0,5); ?>"  />
				<span > <?php echo JText::_( 'TERMINE_UHR' ); ?> </span>
			</td><td>
				<span><input type="checkbox" id='allday' name='allday' <?php if ($this->termine->allday == 1) echo " checked='checked' "; ?> value="<?php echo $this->termine->allday; ?>" /></span>
				<span ><?php echo JText::_( 'TERMINE_ALLDAY' ); ?></span>
			</td>
		</tr>
        
		<tr>
			<td width="100" align="right" class="key">
			<label for="enddate">
			<?php echo JText::_( 'TERMINE_ENDDATE' ); ?>:
			</label>
			</td>
			<td>
				<?php if ($this->termine->enddate < '1970-01-01') $this->termine->enddate = $today; 
				echo JHtml::_('calendar', $this->termine->enddate, 'enddate', 'enddate', '%Y-%m-%d', array('class'=>'text_area', 'size'=>'12',  'maxlength'=>'19')); ?>
				<span >  </span>
			</td><td>
				<input class="inputbox" type="time" name="endtime" id="endtime" size="6" maxlength="6" value="<?php echo substr($this->termine->endtime,0,5); ?>"  />
				<span > <?php echo JText::_( 'TERMINE_UHR' ); ?> </span>
			</td><td>
				<span><input type="checkbox" id='noendtime' name='noendtime' <?php if ($this->termine->noendtime == 1) echo " checked='checked' "; ?> value="<?php echo $this->termine->noendtime; ?>" /></span>
				<span ><?php echo JText::_( 'TERMINE_NOENDTIME' ); ?></span>
			</td>
		</tr>

		<tr>
			<td class="key" nowrap="nowrap"><label for="published"><?php echo JText::_( 'JPUBLISHED' ); ?></label>
			</td>
			<td><fieldset class="radio">
			<?php echo $this->form['published']; ?>
			</fieldset></td>
		</tr>
		</table>
	</fieldset>
	
    <fieldset class="adminform">
		<legend><?php echo JText::_( 'TERMINE_DESCRIPTION' ); ?></legend>
		<textarea class="inputbox" name="beschreibung" id="beschreibung" cols="50" rows="10" style="width:99%"><?php echo str_replace('&','&amp;',$this->termine->beschreibung);?></textarea>
	</fieldset>
	</div>

	<div class="clr"></div>

	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="termineform" />
	<input type="hidden" name="id" value="<?php echo $this->termine->id; ?>" />
	<input type="hidden" name="controller" value="termineform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
