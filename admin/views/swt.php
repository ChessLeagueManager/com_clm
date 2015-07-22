<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewSWT
{

function setImport_1Toolbar ($execute,$upload)
	{
		clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(   JText::_( 'SWT_STEP1' ), 'clm_headmenu_manager.png' );
	JToolBarHelper::custom('import_2','apply.png','apply_f2.png','SWT_STEP2',false);
	JToolBarHelper::custom('back','apply.png','apply_f2.png','SWT_BACK',false);
	}


function Import_1 (&$spieler, &$lists,&$man, &$swt, &$man_zps, &$vereine, &$sid, $fileName, &$name_manuell)
	{
	CLMViewSWT::setImport_1Toolbar($execute,$upload);	
	JRequest::setVar( 'hidemainmenu', 1 );
	?>

	<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
	
	<?php
		$liga_name = CLMControllerSWT::give_name( $swt,377,441);
		$veranstaltung = CLMControllerSWT::give_name( $swt,246,310);
	?>
	<div class="width-30 fltlft">
	<div>
	<fieldset>
	<legend>Turnierdaten</legend>
	<table class="admintable">
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_EVENT' ); ?>&nbsp;&nbsp;&nbsp;</th><th class="key" nowrap="nowrap"><?php echo $veranstaltung; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_TYPE' ); ?></th><th class="key" nowrap="nowrap"><?php if ($swt[607] == 255) { echo "Mannschaftsturnier !";} else {echo "Einzelturnier ! ";} ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_NAME' ); ?></th><th class="key" nowrap="nowrap"><?php echo $liga_name; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MANNSCHAFTEN' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[603]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_RUNDEN' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[2]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_AKT_RUNDE' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[4]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_BRETTER' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[605]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_SPIELER' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[8]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_DURCHGAENGE' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[599]; ?></td><tr>
		<tr><th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_AKT_DG' ); ?></th><th class="key" nowrap="nowrap"><?php echo $swt[600]; ?></td><tr>
	</table>
	</fieldset>
	</div>

	
	<div>
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIPS' ); ?></legend>
	<?php echo JText::_( 'SWT_TIP_LINE1' ); ?><br>
	<br><?php echo JText::_( 'SWT_TIP_LINE1A' ); ?><b><?php echo JText::_( 'SWT_TIP_LINE1B' ); ?></b><br>
	<br><?php echo JText::_( 'SWT_TIP_LINE2' ); ?><br>
	<br><?php echo JText::_( 'SWT_TIP_LINE3' ); ?> 

	</fieldset>
	</div>
	</div>


	<div class="width-70 fltrt">
	<div>
		<fieldset>
		<legend><?php echo JText::_( 'SWT_MANNSCHAFTEN' ); ?></legend>
		
		<table class="admintable">
			<tr>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_TLN_NR' ); ?></th>
				<th class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MANNSCHAFT' ); ?></th>
				<th width="6%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_ZPS' ); ?></th>
				<th width="27%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_VEREIN' ); ?></th>
				<th width="27%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_OPT_NAME' ); ?></th>
			</tr>
			<?php 
			$offset = 13384 + $swt[8]*$swt[2]*19 + $swt[2]*$swt[603]*19 + $swt[8]*655;
			for ($man =1; $man < 1+$swt[603]; $man++) { 
				$man_name = CLMControllerSWT::give_name( $swt,1+$offset,($offset+65));
				$offset += 655;
				?> 
			<tr>
				<td width="5%" class="key" nowrap="nowrap"><?php echo $man; ?></td>
				<td class="key" nowrap="nowrap"><?php echo $man_name; ?></td>
					<input type="hidden" name="man_name<?php echo $man; ?>" value="<?php echo $man; ?>" />
				<td width="6%" class="key" nowrap="nowrap"><?php echo $man_zps[$man]; ?></td>
					<input type="hidden" name="man_zps<?php echo $man; ?>" value="<?php echo $man_zps[$man]; ?>" />
				<td width="27%" class="key" nowrap="nowrap">
				  <select size="1" name="<?php echo 'zps'.$man; ?>" id="<?php echo 'zps'.$man; ?>">
					<option value="0"><?php echo JText::_( 'SWT_VEREIN_AUSWAEHLEN' ); ?></option>
					<?php for ($x=0; $x < count($vereine); $x++) { ?>
					 <option value="<?php echo $vereine[$x]->zps; ?>" <?php if ($man_zps[$man] == $vereine[$x]->zps) { ?> selected="selected" <?php } ?>><?php echo $vereine[$x]->name; ?></option> 
					<?php }	?>
				  </select>
				</td>
				<td width="27%" class="key" nowrap="nowrap"><input class="inputbox" type="text" name="manuell_name<?php echo $man; ?>" 
				id="manuell_name<?php echo $man; ?>" size="40" maxlength="60" 
				value="<?php if (!$name_manuell[($man-1)] OR $name_manuell[($man-1)]->name =="") { echo $man_name;} else { echo $name_manuell[($man-1)]->name;} ?>" /></td>
			</tr>
			<?php }	?>
			</table>
		</fieldset>
		</div>
		</div>
	<input type="hidden" name="liga1" value="<?php echo $veranstaltung; ?>" />
	<input type="hidden" name="liga2" value="<?php echo $swt[607]; ?>" />
	<input type="hidden" name="liga3" value="<?php echo $liga_name; ?>" />
	<input type="hidden" name="liga4" value="<?php echo $swt[603]; ?>" />
	<input type="hidden" name="liga5" value="<?php echo $swt[2]; ?>" />
	<input type="hidden" name="liga6" value="<?php echo $swt[4]; ?>" />
	<input type="hidden" name="liga7" value="<?php echo $swt[605]; ?>" />
	<input type="hidden" name="liga8" value="<?php echo $swt[8]; ?>" />
	<input type="hidden" name="liga9" value="<?php echo $swt[599]; ?>" />
	<input type="hidden" name="liga10" value="<?php echo $swt[600]; ?>" />

	<input type="hidden" name="step" value="import_1" />
	<input type="hidden" name="fileName" value="<?php echo $fileName; ?>"/>

	<input type="hidden" name="section" value="swt" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<div class="clr"></div>
	</form>
	<?php
	}


function setImport_2Toolbar ($execute,$upload)
	{
		clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(   JText::_( 'SWT_STEP2A' ), 'clm_headmenu_manager.png' );
	JToolBarHelper::custom('import_3','apply.png','apply_f2.png','SWT_STEP3',false);
	JToolBarHelper::custom('back','apply.png','apply_f2.png','SWT_BACK',false);
	}


function Import_2 (&$lists, &$swt, &$name_manuell, &$zps_db, &$vereine, &$sid, &$data, &$liga_swt, &$name_man_spl, $fileName, $sg_zps)
	{
	CLMViewSWT::setImport_2Toolbar($execute,$upload);	
	JRequest::setVar( 'hidemainmenu', 1 );
	?>

	<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
		
	<div class="col width-100">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIPS' ); ?></legend>
	<?php echo JText::_( 'SWT_TIP_LINE11' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE12' ); ?>
	<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE12A' ); ?>
	<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE12B' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE13' ); ?><img width="16" height="16" src="images/cancel_f2.png" /><?php echo JText::_( 'SWT_TIP_LINE13A' ); ?>
	<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE13B' ); ?>
	<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE13C' ); ?>
	<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE13D' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE14' ); ?>
	</fieldset>
	</div>

	<div class="col width-100">
	<div class="col width-100">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_MANNSCHAFT' ).' 1'; ?></legend>
	<table class="admintable">
	<tr>
		<!--<th class="key" nowrap="nowrap">Name</th>
		<th width="5%" class="key" nowrap="nowrap">Tlnr</th>
		<th width="2%" class="key" nowrap="nowrap">G</th>
		<th width="6%" class="key" nowrap="nowrap">ZPS</th>
		<th width="5%" class="key" nowrap="nowrap">Mgl</th>
		<th width="17%" class="key" nowrap="nowrap">Verein</th>
		<th width="20%" class="key" nowrap="nowrap">manueller Name</th>
		-->
		
		<th class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_NAME' ); ?></th>
		<th width="3%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_TLNR' ); ?></th>
		<th width="2%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_G' ); ?></th>
		<th width="6%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_ZPS' ); ?></th>
		<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MGL' ); ?></th>
		<th width="20%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_VEREIN' ); ?></th>
		<th width="20%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MAN_NAME' ); ?></th>

	</tr>
	<?php $info=0; $zps_cnt = 1;
	for($y=1; $y < 1+count($data); $y++) { ?>
	<tr>
		<td class="key" nowrap="nowrap"><?php echo $data[$y][2]; ?></td>
		<td width="5%" class="key" nowrap="nowrap"><?php echo $data[$y][0]; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $data[$y][3]; ?></td>
		<td width="6%" class="key" nowrap="nowrap"><?php if($data[$y][6] !="") { ?><img width="16" height="16" src="images/cancel_f2.png" />&nbsp;&nbsp;<?php } else { echo $data[$y][4];} ?></td>
		<td width="5%" class="key" nowrap="nowrap" id="mgl<?php echo $data[$y][5]; ?>" name="mgl<?php echo $data[$y][5]; ?>"><?php if($data[$y][5] =="") { ?><img width="16" height="16" src="images/cancel_f2.png" />&nbsp;<?php } else { echo $data[$y][5];}?></td>
		<td width="17%" class="key" nowrap="nowrap">
			<select size="1" name="<?php echo 'zps'.$data[$y][0]; ?>" id="<?php echo 'zps'.$data[$y][0]; ?>">
				<option value="0"><?php echo JText::_( 'SWT_VEREIN_AUSWAEHLEN' ); ?></option>
				<?php for ($z=0; $z < count($vereine); $z++) { ?>
			<?php if($vereine[$z]->zps == $sg_zps[1][$zps_cnt] OR $vereine[$z]->zps == $sg_zps[2][$zps_cnt] ) { ?> 
				<option value="<?php echo $vereine[$z]->zps; ?>" 
				 <?php if ($data[$y][4] == $vereine[$z]->zps) { ?> 
				 selected="selected" <?php } ?>><?php echo $vereine[$z]->name; ?></option>
			<?php }	?>
				<?php }	?>
			</select>
		</td>
		<td width="20%" class="key" nowrap="nowrap"><input class="inputbox" type="text" name="manuell_name<?php echo $data[$y][0]; ?>" 
		id="manuell_name<?php echo $data[$y][0]; ?>" size="45" maxlength="60" 
		value="<?php if (!$name_man_spl[$data[$y][0]]) { echo $data[$y][2];} 
		else { echo $name_man_spl[$data[$y][0]];} ?>" /></td>
	</tr>
	<?php  
	if($data[$y+1][1] > $data[$y][1]) { $zps_cnt ++;?>
		</table></legend></fieldset></div>
		<?php /*
		if($data[$y][1] > 2 AND $data[$y][1] %2 ==0 ) {?></div><?php }
		if($data[$y][1] %2 ==0 ) {?><div class="col width-100"><br><br></div><?php }
		if($data[$y][1] > 2 AND $data[$y][1] %2 ==0 ) {?><div class="col width-100"><?php } */?>		
		<div class="col width-100">
		<fieldset>
		<legend><?php echo JText::_( 'SWT_MANNSCHAFT' ).' '; ?> <?php echo 1+$data[$y][1];?></legend>
			<table class="admintable">
			<tr>
				<th class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_NAME' ); ?></th>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_TLNR' ); ?></th>
				<th width="2%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_G' ); ?></th>
				<th width="6%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_ZPS' ); ?></th>
				<th width="5%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MGL' ); ?></th>
				<th width="17%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_VEREIN' ); ?></th>
				<th width="17%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MAN_NAME' ); ?></th>
			</tr>
	<?php }
	} ?>
	</table></legend></fieldset></div></div>

	<input type="hidden" name="liga_swt" value="<?php echo $liga_swt;?>" />
	<input type="hidden" name="step" value="import_2" />
	<input type="hidden" name="section" value="swt" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="fileName" value="<?php echo $fileName; ?>"/>
	<?php echo JHtml::_( 'form.token' ); ?>
	<div class="clr"></div>
	</form>
	<?php
	}

	
	
function setImport_3Toolbar ($execute,$upload)
	{
		clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(   JText::_( 'SWT_STEP3A' ), 'clm_headmenu_manager.png' );
	JToolBarHelper::custom('import_4','apply.png','apply_f2.png','SWT_STEP4',false);
	JToolBarHelper::custom('back','apply.png','apply_f2.png','SWT_BACK',false);
	}


function Import_3 (&$lists, &$swt, &$zps_db, &$sid, &$data, &$liga_swt, &$man_zps, $fileName, $sg_zps)
	{
	CLMViewSWT::setImport_3Toolbar($execute,$upload);	
	JRequest::setVar( 'hidemainmenu', 1 );
	?>
	<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
	
	<?php if(count($zps_db) > 0) { ?>	
	<div class="width-40 fltlft">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIPS' ); ?></legend>
	<?php echo JText::_( 'SWT_TIP_LINE21' ); ?>
	<br>&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE21A' ); ?>
	<br><br><?php echo JText::_( 'SWT_TIP_LINE22' ); ?>
	<br><br><?php echo JText::_( 'SWT_TIP_LINE23' ); ?>
	<b><?php echo JText::_( 'SWT_TIP_LINE23A' ); ?></b>
	<br><br><?php echo JText::_( 'SWT_TIP_LINE24'); ?>
	<br><br><?php echo JText::_( 'SWT_TIP_LINE25' ); ?>
	</fieldset>
	</div>

	<div class="width-60 fltrt">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_AUT_NACHM' ); ?></legend>

	<table class="admintable">
	<tr>
		<th width="8%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_TLNR' ); ?></th>
		<th width="8%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_MGL_NR' ); ?></th>
		<th width="8%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_ZPS' ); ?></th>
		<th class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_NAME' ); ?></th>
		<th width="2%" class="key" nowrap="nowrap">#</th>
		<th width="50%" class="key" nowrap="nowrap"><?php echo JText::_( 'SWT_ADVICE' ); ?></th>
	</tr>
	
	<?php $id=0; foreach ($zps_db as $search) { ?>
	<tr>
		<td class="key" nowrap="nowrap"><?php echo $search->Nr; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $search->mgl_nr; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $search->clm_zps; ?></td>
		<td class="key" nowrap="nowrap"><?php echo $search->Name; ?></td>
		<td width="2%" nowrap="nowrap"><?php echo count($man_zps[$search->Nr]); ?></td>
		<td width="50%" nowrap="nowrap">
			<select size="1" name="<?php echo 'data_'.$id; ?>" id="<?php echo 'data_'.$id; ?>">
				<?php for ($z=0; $z < count($man_zps[$search->Nr]); $z++) { ?>
			<?php /*if(($man_zps[$search->Nr][$z][0] == $sg_zps[1][$search->mnr]) OR ($man_zps[$search->Nr][$z][0] == $sg_zps[2][$search->mnr])) { */?>
				 <option value="<?php echo $man_zps[$search->Nr][$z][0].'-'.$man_zps[$search->Nr][$z][1].'-'.$man_zps[$search->Nr][$z][2].'-'.($search->Nr); ?>">
				 <?php if ($man_zps[$search->Nr][$z][3] =="N") { echo "** ";} echo $man_zps[$search->Nr][$z][0].'-'.$man_zps[$search->Nr][$z][1].'-'.$man_zps[$search->Nr][$z][2]; ?></option> 
				<?php } ?>
			</select>
		</td>
	</tr>
	<?php $id++; } ?>
	</table>
	</fieldset>
	</div>
	<?php } else { ?>
		<br><h1><?php echo JText::_( 'SWT_TIP_LINE31' ); ?></h1> 
		<h2><?php echo JText::_( 'SWT_TIP_LINE32' ); ?></h2>
	<?php } ?>
	<input type="hidden" name="liga_swt" value="<?php echo $liga_swt;?>" />
	<input type="hidden" name="step" value="import_3" />
	<input type="hidden" name="anzahl_meldungen" value="<?php echo count($zps_db); ?>" />
	<input type="hidden" name="fileName" value="<?php echo $fileName; ?>"/>
	<input type="hidden" name="section" value="swt" />
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
	<div class="clr"></div>
	</form>
	<?php
	}

	

	

function setSWTToolbar($execute,$upload)
	{
		clm_core::$load->load_css("icons_images");
	JToolBarHelper::title(   JText::_( 'TITLE_SWT' ), 'clm_headmenu_manager.png' );	
	
	$clmAccess = clm_core::$access;
	if ( $execute == 1 AND $clmAccess->access('BE_swt_general') ) {
		JToolBarHelper::custom('import_1','apply.png','apply_f2.png','SWT_DATEI_IMPORT',false);
		JToolBarHelper::custom('swt_dat_del','delete.png','apply_f2.png','SWT_DATEN_DEL',false);
		JToolBarHelper::custom('swt_dat_nach','delete.png','apply_f2.png','SWT_NACHM_DEL',false);
	}
	if ( $upload == 1 AND $clmAccess->access('BE_swt_general') ) {
		JToolBarHelper::custom('upload_jfile','upload.png','send_f2.png','SWT_DATEI_LOAD',false);
	}

	JToolBarHelper::help( 'screen.clm.info' );
	}
	
	
function SWT ( &$rows, &$lists, &$pageNav)
	{
	// Konfigurationsparameter auslesen
	$config		= clm_core::$db->config();
	$upload		=$config->upload_swt;
	$execute	=$config->execute_swt;
	$lv		=$config->lv;

	CLMViewSWT::setSWTToolbar($execute,$upload);
?>
<div>
<div class="width-50 fltlft">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIP_LINE40' ); ?></legend>
	<!---
	<font color="#ff0000"><b><?php echo JText::_( 'SWT_TIP_LINE41' ); ?></b></font><br>
	--->
	<?php echo JText::_( 'SWT_TIP_LINE42' ); ?><br><?php echo JText::_( 'SWT_TIP_LINE43' ); ?>
	</fieldset>

<form action="index.php" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIP_LINE50' ); ?></legend>
	<b><?php echo JText::_( 'SWT_TIP_LINE51' ).' '; ?> <?php if ($upload == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
				 else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font>
	
	<?php echo " , ".JText::_( 'SWT_TIP_LINE52' ).' '; ?><?php if ($execute == 1) { ?><font color="#00ff00"><?php echo JText::_( 'SWT_ACTIVE' ); } 
	else { ?><font color="#ff0000"><?php echo JText::_( 'SWT_DEACTIVE' ); } ?></font></b>
	</fieldset>

	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIPS' ); ?></legend>
	<b><font color="#ff0000"><?php echo JText::_( 'SWT_TIP_LINE40' ); ?></font><?php echo JText::_( 'SWT_TIP_LINE61' ); ?></b>
	<br><?php echo JText::_( 'SWT_TIP_LINE62' ); ?>
	<!---
	<br><?php echo JText::_( 'SWT_TIP_LINE63' ); ?>
	<br><b><?php echo JText::_( 'SWT_TIP_LINE64' ); ?></b>
	--->
	<br><br><b><?php echo JText::_( 'SWT_TIP_LINE65' ); ?></b>
	<?php echo JText::_( 'SWT_TIP_LINE66' ); ?>
	<br><br><?php echo JText::_( 'SWT_TIP_LINE67' ).' '; ?> <b><?php echo JText::_( 'SWT_TIP_LINE68' ); ?></b> <?php echo JText::_( 'SWT_TIP_LINE69' ); ?>
	<br>
	<br><b><?php echo JText::_( 'SWT_TIP_LINE70' ); ?></b>
	<br><?php echo JText::_( 'SWT_TIP_LINE71' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE72' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE73' ); ?>
	<br><?php echo JText::_( 'SWT_TIP_LINE74' ); ?>
	</fieldset>
</div>


<div class="width-50 fltrt">

<?php if ($execute == 1) { ?>
	<?php $liga_import = CLMControllerSWT::liga(); ?>
	<?php $datei = CLMControllerSWT::files(); ?>
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIP_LINE80' ); ?></legend>
	<table>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="sql_datei">
		<option value="0"><?php echo JText::_( 'SWT_TIP_LINE81' ); ?></option>
		<?php for ($x=0; $x < count($datei); $x++) { ?>
		 <option value="<?php echo $datei[$x]; ?>"><?php echo $datei[$x]; ?></option> 
		<?php } ?>
	  </select>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<b><?php echo JText::_( 'SWT_TIP_LINE82' ); ?></b></td>
	</tr>
<!--	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="liga_import">
		<option value="0">-- Liga wählen --</option>
		<option value="new">-- Keine, neu erstellen --</option>
		<?php /*for ($x=0; $x < count($liga_import); $x++) { */?>
		 <option value="<?php /*echo $liga_import[$x]->id; */?>"><?php /*echo $liga_import[$x]->name; */?></option>
		<?php /*} */?>
	  </select><br><br><br>
	</td>
	<td><b>W&auml;hlen Sie die Liga aus in die importiert werden soll</b>
		<br>- "Keine Auswahl" erstellt eine neue Liga
		<br>- Bei Auswahl werden alle Daten gel&ouml;scht wenn Sie im n&auml;chsten Menue kein Update ausw&auml;hlen !
		<br><br>
	</td>
	</tr>
	<tr>
		<td class="key" nowrap="nowrap">
	  <select size="1" name="liga_import">
		<option value="0">-- Update w&auml;hlen --</option>
		<?php /*for ($x=0; $x < count($liga_export); $x++) { */?>
		 <option value="<?php /*echo $liga_export[$x]->id; */?>"><?php /*echo $liga_export[$x]->name; */?></option>
		<?php /*} */?>
	  </select><br><br><br>
	</td>
	<td><b>Hier k&ouml;nnen Sie ein Update aus bereits importierten Daten w&auml;hlen</b>
		<br>- Es werden die Grunddaten der Liga sowie Mannschaftsnamen, ZPS etc. &uuml;bernommen.
		<br>- Die Rundendaten werden komplett neu eingelesen und alte &uuml;berschrieben.
		<br>- Dieser Import beginnt mit dem 2. Importschritt.
	</td>
	</tr>

	<tr>
	<td>
		<input type="checkbox" id="override" name="override" value="1" />Sicherheitswarnungen ignorieren</td>
		<td>Auf eigene Gefahr : Ermöglicht das ignorieren von Sicherheitswarnungen beim Import ! (Wenn z.B. die Importdatei manipuliert / geändert wurde)</td>
	</tr>
-->

	</table>
	</fieldset>
<?php } ?>
	<input type="hidden" name="section" value="swt" />
	<input type="hidden" name="option" value="com_clm" />
	<?php echo JHtml::_( 'form.token' ); ?>

	
	<?php $data = CLMControllerSWT::delete_data(); ?>
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIP_LINE90' ); ?></legend>
	<table>
	<tr>
	<td class="key" nowrap="nowrap">
	  <select size="1" name="swt_delete">
		<option value="0"><?php echo JText::_( 'SWT_TIP_LINE91' ); ?></option>
		<?php for ($x=0; $x < count($data); $x++) { ?>
		 <option value="<?php echo $data[$x]->swt_id; ?>"><?php echo $data[$x]->Liga; ?></option> 
		<?php } ?>
	  </select>
	</td>
	<td>&nbsp;&nbsp;&nbsp;<b><?php echo JText::_( 'SWT_TIP_LINE92' ); ?></b>
	<br>&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE93' ); ?>
	<br>&nbsp;&nbsp;&nbsp;<?php echo JText::_( 'SWT_TIP_LINE94' ); ?></td>
	</tr>
	</table>
	</fieldset>


<?php if ($upload == 1) { ?>

		<fieldset>
		<legend><?php echo JText::_( 'SWT_TIP_LINE100' ); ?></legend>
			 <input type="file" name="datei" />
		</fieldset>
<?php } ?>




<form action="<?php echo JURI::base(); ?>index.php?option=com_clm&amp;task=sql_db" id="execute" method="post" name="sql_execute">
	<fieldset>
	<legend><?php echo JText::_( 'SWT_TIP_LINE101' ); ?></legend>

	<table >
<?php if (isset($datei)) $count_datei = count($datei); else $count_datei = 0;
for ($x=0; $x< $count_datei; $x++ ) { ?>
	<tr><td><?php echo $datei[$x]; ?></td></tr>
<?php } ?>
	</table>
	</fieldset>

</div>
</div>
		<input type="hidden" name="section" value="swt" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		<div class="clr"></div>
	</form>
<?php }} ?>
