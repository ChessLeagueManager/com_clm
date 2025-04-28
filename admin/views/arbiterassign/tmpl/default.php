<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2025 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
defined('_JEXEC') or die('Restricted access');

	require_once (JPATH_COMPONENT_SITE . DS . 'includes' . DS . 'tooltip.php');
	$lang = clm_core::$lang->arbiter;
	// Auswahlfelder durchsuchbar machen
	clm_core::$load->load_js("suche_liste");

	$lid = clm_core::$load->request_int('lid');
	$tid = clm_core::$load->request_int('tid');
	$returnview = clm_core::$load->request_string('returnview');

	$turnier = $this->turnier;
	$arbiters  = $this->arbiters;
	$lists  = $this->lists;
	$All = $this->All;
	$paarung = $this->paarung;
	$arbiterlist = $this->arbiterlist;
	$field_search = $this->field_search;
	$array_A00 = $this->array_A00;
	$array_A00U = $this->array_A00U;
	$array_All = $this->array_All;
		
?>

	<script language="javascript" type="text/javascript">

	Joomla.submitbutton = function submitbutton(pressbutton) {
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			Joomla.submitform( pressbutton );
			return;
		}
		
		// do field validation
//		if (form.fideid.value == 0) {
//			alert( jserror['enter_fideid'] );
//		} elseif (form.name.value == "") {
//			alert( jserror['enter_name'] );
//		} else {
			Joomla.submitform( pressbutton );
//		}
	}
	
	</script>
<?php
	echo '<br><h3>'.$lang->arbiter_assign .' &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;"></span> '.$this->turnier[0]->name.' &nbsp; &nbsp; &nbsp; <span style="font-weight:normal;">Saison:</span> '.$this->turnier[0]->sname.'</h3><br>';
	// Auswahlfelder durchsuchbar machen
	clm_core::$load->load_js("suche_liste");
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="width-50 fltlft">
		<fieldset class="adminform">
		<legend><?php echo $lang->tournament_roles; ?></legend>
			<table class="paramlist admintable">
			<tr>
				<td width="42%" class="paramlist_key">
					<label for="aca"><?php echo $lang->roleACA; ?></label>
				</td>
				<td class="paramlist_value">
					<?php echo $lists['ACA']; ?>
					<?php if ($lists['ACAU'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
				</td>
			</tr>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['ADCA'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="adca<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleADCA; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['ADCA'.$i]; ?>
						<?php if ($lists['ADCA'.$i.'U'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['APO'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="apo<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleAPO; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['APO'.$i]; ?>
						<?php if ($lists['APO'.$i.'U'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['ASA'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="asa<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleASA; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['ASA'.$i]; ?>
						<?php if ($lists['ASA'.$i.'U'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['AASA'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="aasa<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleAASA; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['AASA'.$i]; ?>
						<?php if ($lists['AASA'.$i.'U'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
					</td>
				</tr>
			<?php } ?>
			<?php for ($i = 0; $i <= 10; $i++) { 
				if (!isset($lists['AACA'.$i])) break; ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="aaca<?php echo $i; ?>"><?php if ($i == 0) echo $lang->roleAACA; ?></label>
					</td>
					<td class="paramlist_value">
						<?php echo $lists['AACA'.$i]; ?>
						<?php if ($lists['AACA'.$i.'U'] == 1) echo '<span style="color:green;font-size:100%;font-weight:bold;"> *</span>'; ?>
					</td>
				</tr>
			<?php } ?>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<?php if ($lid > 0) { ?>
				<tr>
					<td width="40%" class="paramlist_key">
						<label for="htext"><?php echo $lang->tip_assign; ?></label>
					</td>
					<td class="paramlist_value">
						<pre>
Die hier oben zugeordneten Schiedsrichter
sind für diesen Wettbewerb fachlich zuständig.
Systemtechnisch können diese Personen 
die Brettergebnisse auf der Webseite dieser 
CLM-Anwendung selbst eingeben.
Voraussetzung ist ein Benutzerkonto mit FIDE-Id. 
Für die mit <span style="color:green;font-size:100%;font-weight:bold;"> *</span> markierten Schiedsrichter 
wurde bereits ein solches angelegt.
						</pre>			
					</td>
				</tr>
			<?php } ?>

			</table>
	  </fieldset>
  </div>
		
  <div class="width-50 fltlft">
	  <?php if ($lid > 0) { ?>
	  <fieldset class="adminform">
		<legend><?php echo $lang->timetable; ?></legend>
			<table class="paramlist admintable">
			<?php $vdg = 0; $vrunde = 0; $i = 0; $ha00 = 0; //$sf = array();
				$harbiterlist[]	= JHTML::_('select.option',  '0', $lang->select_arbiter , 'fideid', 'fname' );
				$harbiterlist	= array_merge( $harbiterlist, $All );
				for ($p = 0; $p < count($paarung); $p++) {
					If ($vdg != $paarung[$p]->dg OR $vrunde != $paarung[$p]->runde) {
						// neue Runde 
						$i++;
						$vdg = $paarung[$p]->dg; $vrunde = $paarung[$p]->runde;
						echo '<tr><td title="'.clm_core::$cms->showDate($paarung[$p]->datum, "d M Y").'">'.$lang->spieltag.'  '.$lang->runde.' '.$paarung[$p]->runde.'  ';
						echo '<input type="hidden" name="dg['.$i.']" id="dg['.$i.']" value="'.$paarung[$p]->dg.'" />';
						echo '<input type="hidden" name="runde['.$i.']" id="runde['.$i.']" value="'.$paarung[$p]->runde.'" />';
						echo '<input type="hidden" name="paar['.$i.']" id="paar['.$i.']" value="0" />';
						echo "</td>";
						if (isset($array_A00[$paarung[$p]->dg][$paarung[$p]->runde][0])) $ha00 = $array_A00[$paarung[$p]->dg][$paarung[$p]->runde][0]; else $ha00 = 0;
						$hlist = JHTML::_('select.genericlist',   $harbiterlist, 'sf['.$i.']', 'class="'.$field_search.'" style="width:300px" size="1"',
							'fideid', 'fname', $ha00);
						echo "<td>".$hlist;
						if ($ha00 > 0 AND isset($array_A00U[$paarung[$p]->dg][$paarung[$p]->runde][0]) 
								AND $array_A00U[$paarung[$p]->dg][$paarung[$p]->runde][0] == 1 ) echo " * </td></tr>"; else echo "</td></tr>";
					}
			
					$i++;
					echo '<tr><td title="'.$paarung[$p]->hname.' - '.$paarung[$p]->gname.'">'.' - '.$lang->paar.' '.$paarung[$p]->paar.'  ';
					echo '<input type="hidden" name="dg['.$i.']" id="dg['.$i.']" value="'.$paarung[$p]->dg.'" />';
					echo '<input type="hidden" name="runde['.$i.']" id="runde['.$i.']" value="'.$paarung[$p]->runde.'" />';
					echo '<input type="hidden" name="paar['.$i.']" id="paar['.$i.']" value="'.$paarung[$p]->paar.'" />';
					echo "</td>";

					if (isset($array_A00[$paarung[$p]->dg][$paarung[$p]->runde][$paarung[$p]->paar])) $ha00 = $array_A00[$paarung[$p]->dg][$paarung[$p]->runde][$paarung[$p]->paar]; else $ha00 = 0;
					$hlist = JHTML::_('select.genericlist',   $harbiterlist, 'sf['.$i.']', 'class="'.$field_search.'" style="width:300px" size="1"',
						'fideid', 'fname', $ha00);
					echo "<td>".$hlist;
					if ($ha00 > 0 AND isset($array_A00U[$paarung[$p]->dg][$paarung[$p]->runde][$paarung[$p]->paar]) 
							AND $array_A00U[$paarung[$p]->dg][$paarung[$p]->runde][$paarung[$p]->paar] == 1 ) echo " * </td></tr>"; else echo "</td></tr>";
				} ?>
			</table>
	  </fieldset>
	  <?php } ?>
  </div>
  
 



<div class="clr"></div>


	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="arbiterassign" />
	<input type="hidden" name="lid" value="<?php echo $lid; ?>" />
	<input type="hidden" name="tid" value="<?php echo $tid; ?>" />
	<input type="hidden" name="id" value="<?php echo $tid; ?>" />
	<input type="hidden" name="returnview" value="<?php echo $returnview; ?>" />
	<input type="hidden" name="controller" value="arbiterassign" />
	<input type="hidden" name="task" value="apply" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
