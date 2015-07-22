<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2013 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'controllers'.DIRECTORY_SEPARATOR.'db.php');
?>
	
<?php
	$clubs			  = JRequest::getVar('clubs', 0);
?>		

<?php
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	// Konfigurationsparameter auslesen
	$config		= &JComponentHelper::getParams( 'com_clm' );
	$upload		= $config->get('upload_sql',0);
	$execute	= $config->get('execute_sql',0);
	$version	= $config->get('version',0);
	$verband	= $config->get('lv','G02');

	if($version =="0"){$db_version = "deutsche";}
	if($version =="1"){$db_version = "niederländische";}
?>


<form action="index.php?option=com_clm&view=db" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >
  <table width="100%" class="admintable"> 
  <tr>

  <td width="50%" style="vertical-align: top;">
	<fieldset>
	<legend><?php echo JText::_( 'DB_ATT' ); ?></legend>
	<?php echo JText::_( 'DB_ATT_0' ); ?><br><br>
	<?php echo JText::_( 'DB_ATT_1' ); ?><br>
	<?php echo JText::_( 'DB_ATT_2' ); ?><br>
	<?php echo JText::_( 'DB_ATT_3' ); ?>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_HINT'); ?></legend>
	<?php echo JTEXT::_('DB_HINT_1');
	echo $db_version;
	echo JTEXT::_('DB_HINT_2');
	echo JTEXT::_('DB_HINT_3');
	
	if($version =="1"){
	echo JTEXT::_('DB_HINT_4');
	echo JTEXT::_('DB_HINT_5');
	echo JTEXT::_('DB_HINT_6');
	echo JTEXT::_('DB_HINT_7');
	echo JTEXT::_('DB_HINT_8');
	echo JTEXT::_('DB_HINT_9');
	echo JTEXT::_('DB_HINT_10');
	echo JTEXT::_('DB_HINT_11');
	echo JTEXT::_('DB_HINT_12');
	echo JTEXT::_('DB_HINT_13');
	} ?>
	<?php if($version =="0"){
	echo JTEXT::_('DB_HINT_14');
	echo JTEXT::_('DB_HINT_15');
	} ?>
	<br><br>
	<?php if($version =="0"){
		echo JTEXT::_('DB_HINT_16');
	} ?>

	<?php if($version =="1"){ 
		echo JTEXT::_('DB_HINT_17');
	} ?>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_STATUS_1'); ?></legend>
	<?php echo JTEXT::_('DB_STATUS_2').' '; if ($upload == 1) { ?><font color="#00ff00"><?php echo JTEXT::_('DB_STATUS_AKTIV'); ?></font>
			<?php } else { ?><font color="#ff0000"><?php echo JTEXT::_('DB_STATUS_INAKTIV'); } ?></font>
	<br>
	<?php echo JTEXT::_('DB_STATUS_3').' '; if ($execute == 1) { ?><font color="#00ff00"><?php echo JTEXT::_('DB_STATUS_AKTIV'); ?></font>
			<?php } else { ?><font color="#ff0000"><?php echo JTEXT::_('DB_STATUS_INAKTIV'); } ?></font>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_DWZ_1'); ?></legend>
	<!---?php echo JTEXT::_('DB_DWZ_2'); ?><a href="http://www.schachbund.de/dwz/db/download.html"> http://www.schachbund.de/dwz/db/download.html</a--->
	<?php echo JTEXT::_('DB_DWZ_2'); ?><a href="http://www.schachbund.de/download.html" target="_blank"> http://www.schachbund.de/download.html</a>
	<!---?php echo JTEXT::_('DB_DWZ_3'); ?><a href="http://www.schachbund.de/dwz/db/download/LV-<?php echo substr($lv, 0, 1); ?>-sql.zip"> http://www.schachbund.de/dwz/db/download/LV-<?php echo substr($lv, 0, 1); ?>-sql.zip</a--->
	<?php echo JTEXT::_('DB_DWZ_3'); ?><a href="https://dwz.svw.info/services/files/export/sql/LV-<?php echo substr($verband, 0, 1); ?>-sql.zip"> https://dwz.svw.info/services/files/export/LV-<?php echo substr($verband, 0, 1); ?>-sql.zip</a>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_SQL_1'); ?></legend>
	<?php echo JTEXT::_('DB_SQL_2'); ?>
	<table>
		<?php $datei = CLMControllerDB::files();
		for ($x=0; $x< count($datei); $x++ ) { ?>
			<tr><td><a href="components/com_clm/upload/<?php echo $datei[$x]; ?>" target="_blank"><?php echo $datei[$x]; ?></a></td></tr>
		<?php } ?>
	</table>
	</fieldset>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_EXPORT_1'); ?></legend>
	<?php echo JTEXT::_('DB_EXPORT_2'); ?>
	<table >
		<?php $export_files = CLMControllerDB::export_files();
		for ($x=0; $x< count($export_files); $x++ ) { ?>
			<tr><td><a href="components/com_clm/upload/<?php echo $export_files[$x]; ?>" target="_blank"><?php echo $export_files[$x]; ?></a></td></tr>
		<?php } ?>
	</table>
	</fieldset>

  </td>


  <td width="50%" style="vertical-align: top;">

<?php
$config		= &JComponentHelper::getParams( 'com_clm' );
$verband	= $config->get('lv','G02');
$form['verband'] = CLMForm::selectVerband('verband', $verband);
$doc = JFactory::getDocument();
$doc->addScript('http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
$doc->addScriptDeclaration('jQuery.noConflict();');
?>

<script type="text/javascript">
jQuery(document).ready(function() {
		
jQuery("#btnSun").click(function(){
        //jQuery("#data_zps").html( " X " );
    jQuery("#update_div").toggle();
    }); 

jQuery("#update_progress").click(function(){
	jQuery("#update_ende").hide();
	jQuery("#update_error").hide();
    jQuery("#update_div").fadeIn(700);
    soap_data('1');
    });

jQuery("#update_progress_p").click(function(){
	jQuery("#update_ende").hide();
	jQuery("#update_error").hide();
    jQuery("#update_div").fadeIn(700);
    soap_data('0');
    });

function soap_data(p_passiv) {
	jQuery("#counter").html( "Daten werden geladen !" );
	
	var input_status = jQuery("#status").val()
	var input_start	= jQuery("#start").val()
	var input_verband = jQuery("#verband").val()
	var input_jpath_admin = jQuery("#jpath_admin").val()
	var input_provide = jQuery("#provide").val()
	var input_update = jQuery("#update").val()
	var input_insert = jQuery("#insert").val()
	var input_beginn = jQuery("#beginn").val()
	var input_incl_pd = p_passiv
	var input_msg = jQuery("#msg").val()
	var input_error = jQuery("#error").val()
		//jQuery("#debug").html( input_status + '--' + input_start + '--' + input_verband +  '--<' + input_incl_pd + '--<');
    	jQuery.ajax({
	    	type:"GET",
				//url: 'http://clm.fishpoke.de/clm_test/administrator/index.php?option=com_clm&view=dewis&format=raw',
			url: input_jpath_admin+'index.php?option=com_clm&view=db&format=raw',
			data: '&status='+input_status+'&start='+input_start+'&verband='+input_verband+'&provide='+input_provide+'&update='+input_update+'&insert='+input_insert+'&beginn='+input_beginn+'&incl_pd='+input_incl_pd+'&msg='+input_msg+'&error='+input_error+'&jpath_admin='+input_jpath_admin+'&ende=55',
			success: function(data) {
				ajax = eval('(' + data + ')');
				var soap_status = ajax.status;
				var soap_error = ajax.error;
				var soap_incl_pd = ajax.incl_pd;
		    	// Aktuell abgearbeiteter Verband
				jQuery("#data_zps").html( ajax.verband );
				// Fortschritt
				jQuery("#percentage").html( soap_status );
			
				jQuery("#start").val( ajax.start );
				jQuery("#provide").val( ajax.provide );
				jQuery("#update").val( ajax.update );
				jQuery("#insert").val( ajax.insert );
				jQuery("#beginn").val( ajax.beginn );
				jQuery("#incl_pd").val( ajax.incl_pd );
				jQuery("#msg").val( ajax.msg );
				//jQuery("#zufall").html( ajax.zufall + '--' + ajax.start + '--' + ajax.verband + '--' + ajax_incl_pd);
				if(soap_status<100) { soap_data(soap_incl_pd); }
				else{
					if(soap_error>0) {
						jQuery("#msg_error").html( ajax.msg );
						jQuery("#update_div").fadeOut(1200);
						jQuery("#update_error").fadeIn(1200);
					}
					else{
						jQuery("#counter").html( "UPDATE ABGESCHLOSSEN !!!" );
						jQuery("#msg").html( ajax.msg );
						jQuery("#start").val( "0" );
						jQuery("#update_div").fadeOut(1200);
						jQuery("#update_ende").fadeIn(1200);
					}
				}
			}
		});
	    
	}
 });
  </script>
	<?php echo JTEXT::_('DB_UPDATE_DIRECT01').'<br>'.JTEXT::_('DB_UPDATE_DIRECT02').'<br>'.JTEXT::_('DB_UPDATE_DIRECT03'); ?>
		<fieldset class="adminform">
		<legend><?php echo JTEXT::_('DB_UPDATE_VERBAND'); ?></legend>
		<table width="100%">
		  <tr>
			<td><?php echo $form['verband']; ?></td>
			<td>  </td>
			<td>  </td>
		 </tr>
		 <tr>
			<td><input type="button" name="update_progress" id="update_progress" value="Update!" style="background-color:#CCCCCC; border-color:#333333; border-width:1px;" 
				title="<?php echo JTEXT::_('DB_UPDATE_INCL_P_HINT_1'); ?>"></td>
			<td><input type="button" name="update_progress_p" id="update_progress_p" value="Update+P!" style="background-color:#CCCCCC; border-color:#333333; border-width:1px;"
				title="<?php echo JTEXT::_('DB_UPDATE_INCL_P_HINT_0'); ?>"></td>
			<td><input type="button" name="btnSun" id="btnSun" value="Einblenden / Ausblenden" ></td>	
		 </tr>
		</table>
		
		<div id="update_div" style="display:none;">
		<!--<br><b>Debug 1 : </b><span id="debug"></span>-->
		<!--<br><b>Debug 2 : </b><span id="zufall"></span>-->
		<br><b> </b><span id="jpath_admin"></span>
		<br><b>Status : </b><span id="counter">inaktiv</span>
		<br><b>Aktuell verarbeiteter Verein: <span id="data_zps" style="color:#ff0000;"> bitte etwas Geduld</span></b>
		<br><font style="font-weight:bold; font-size:150%">Fortschritt : </font>
		<font style="font-weight:bold; font-size:200%"><span id="percentage">0</span> %</font>
		</div>

		<div id="update_ende" style="display:none;">
		<font style="font-weight:bold; font-size:200%; color:#ff0000">Update beendet !</font>
		<br><span style="font-weight:bold" id="msg"> </span>
		</div>
		
		<div id="update_error" style="display:none;">
		<font style="font-weight:bold; font-size:200%; color:#ff0000">Fehler !</font>
		<br><span style="font-weight:bold" id="msg_error"> </span>
		</div>
 
		<input type="hidden" id="start" value="0">
		<input type="hidden" id="status" value="0">
		<input type="hidden" id="provide" value="0">
		<input type="hidden" id="update" value="0">
		<input type="hidden" id="insert" value="0">
		<input type="hidden" id="beginn" value="">
		<input type="hidden" id="msg" value="">
		<input type="hidden" id="e_nr" value="0">
		<?php //echo "<br>jpa:".JURI::base(); die(''); //http://localhost/sg/administrator/ ?> 
		<input type="hidden" id="jpath_admin" value="<?php echo JURI::base(); ?> ">
		</fieldset>

		<br>
  <?php echo JTEXT::_('DB_UPDATE_CLASSIC01').'<br>'.JTEXT::_('DB_UPDATE_CLASSIC02').'<br>'.JTEXT::_('DB_UPDATE_CLASSIC03'); ?>
	<?php if ($upload == 1) { ?>
		<fieldset>
		<legend><?php echo JTEXT::_('DB_UPLOAD_1'); ?></legend>
			 <input type="file" name="datei" />
		</fieldset>
	<?php } ?>


	<?php if ($execute == 1) { ?>
		<fieldset>
		<legend><?php echo JTEXT::_('DB_SQL_DEL_UP'); ?></legend>
		<div>
		  <table>
		  <tr>
			<td class="key" nowrap="nowrap">
			<select size="1" name="sql_datei">
				<option value="0"><?php echo JTEXT::_('DB_SQL_DEL_UP_1'); ?></option>
				<option value="all"><?php echo JTEXT::_('DB_SQL_DEL_UP_2'); ?></option>
				<?php for ($x=0; $x < count($datei); $x++) { ?>
					<option value="<?php echo $datei[$x]; ?>"><?php echo $datei[$x]; ?></option> 
				<?php }	?>
			</select>
			</td>
		  </tr>
		  <tr><td colspan="3">  </td></tr>
		  <tr><td colspan="3">  </td></tr>
		  <tr>
			<td colspan="3"><?php echo JTEXT::_('DB_DEL_OLD_DATA_PAR'); ?></td>
		  </tr>
		  <tr>
			<td>
			<input type="checkbox" id="sql_del" name="sql_del" value="1" /><?php echo JTEXT::_('DB_DEL_OLD_DATA'); ?></td>
			<td>     </td>
			<td><?php echo JTEXT::_('DB_DEL_OLD_DATA_HINT'); ?></td>
		  </tr>
		  <tr> </tr>
		  </table>
		</div>
		</fieldset>
	<?php } ?>

	<fieldset>
	<legend><?php echo JTEXT::_('DB_UPDATE'); ?></legend>
	<div>
		<table>
		  <tr>
			<td>
			<input type="checkbox" id="incl_p" name="incl_p" value="1" /><?php echo JTEXT::_('DB_UPDATE_INCL_P'); ?></td>
			<td>     </td>
			<td><?php echo JTEXT::_('DB_UPDATE_INCL_P_HINT'); ?></td>
		  </tr>
		  <tr> </tr>
		</table>
	</div>
	</fieldset>
 
	<?php 	$clmAccess->accesspoint = 'BE_database_general';
		if ( $clmAccess->access() ) { ?>
<br>
	<?php echo JTEXT::_('DB_EXPORT_IMPORT'); ?>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_EXPORT'); ?></legend>
	  <?php $liga_export = CLMControllerDB::liga(); ?>
		<div>
		<table>
		  <tr>
			<td class="key" nowrap="nowrap">
			  <select size="1" name="liga_export">
				<option value="0"><?php echo JTEXT::_('DB_LIGA_EXPORT_1'); ?></option>
				<option value="all"><?php echo JTEXT::_('DB_LIGA_EXPORT_2'); ?></option>
				<?php if (isset($liga_export)) for ($x=0; $x < count($liga_export); $x++) { ?>
					<option value="<?php echo $liga_export[$x]->id; ?>"><?php echo $liga_export[$x]->name; ?></option>
				<?php } ?>
			  </select>
			</td>
			<td><?php echo JTEXT::_('DB_LIGA_EXPORT_3'); ?></td>
		  </tr>
		  <tr>
			<td>
				<input type="checkbox" id="cb1" name="clm_user_exp" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_4'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_EXPORT_5'); ?></td>
		  </tr>
		  <tr>
			<td>
				<input type="checkbox" id="cb2" name="clm_joomla_exp" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_6'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_EXPORT_7'); ?></td>
		  </tr>
		  <tr>
			<td>
				<input type="checkbox" id="cb3" name="clm_sql" value="1" /><?php echo JTEXT::_('DB_LIGA_EXPORT_8'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_EXPORT_9'); ?></td>
		  </tr>
		  <tr>
			<td>
				<input class="inputbox" type="text" name="bem" id="bem" cols="15" rows="1" maxlength="50" style="width:100%"><?php if (isset($row)) echo str_replace('&','&amp;',$row->bem_int);?></input>
			</td>
			<td><?php echo JTEXT::_('DB_LIGA_EXPORT_10'); ?></td>
		  </tr>
		</table>
	</div>
	</fieldset>
<br>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_EXPORT_11'); ?></legend>
	<table>
	  <tr>
		<td class="key" nowrap="nowrap">
		  <select size="1" name="delete_export">
			<option value="0"><?php echo JTEXT::_('DB_LIGA_EXPORT_12'); ?></option>
			<option value="all"><?php echo JTEXT::_('DB_LIGA_EXPORT_13'); ?></option>
			<?php if(isset($export_files)) for ($x=0; $x < count($export_files); $x++) { ?>
				<option value="<?php echo $export_files[$x]; ?>"><?php echo $export_files[$x]; ?></option> 
			<?php }	?>
		  </select>
		</td>
	  </tr>
	</table>
	</fieldset>
<br>
	<?php $saison_import = CLMControllerDB::saison(); ?>
	<fieldset>
	<legend><?php echo JTEXT::_('DB_LIGA_IMPORT'); ?></legend>
	  <table>
		<tr>
			<td class="key" nowrap="nowrap">
			  <select size="1" name="import">
				<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_1'); ?></option>
				<?php for ($x=0; $x < count($export_files); $x++) { ?>
					<option value="<?php echo $export_files[$x]; ?>"><?php echo $export_files[$x]; ?></option> 
				<?php }	?>
			  </select>
			</td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_2'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			  <select size="1" name="liga_import">
				<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_3'); ?></option>
				<option value="new"><?php echo JTEXT::_('DB_LIGA_IMPORT_4'); ?></option>
				<?php for ($x=0; $x < count($liga_export); $x++) { ?>
					<option value="<?php echo $liga_export[$x]->id; ?>"><?php echo $liga_export[$x]->name; ?></option>
				<?php } ?>
			  </select>
			</td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_5'); ?></td>
		</tr>
		<tr>
			<td class="key" nowrap="nowrap">
			  <select size="1" name="saison_import">
				<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_6'); ?></option>
				<?php for ($x=0; $x < count($saison_import); $x++) { ?>
					<option value="<?php echo $saison_import[$x]->id; ?>"><?php echo $saison_import[$x]->name; ?></option>
				<?php } ?>
			  </select>
			</td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_7'); ?></td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="cb_imp1" name="imp_user" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_8'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_9'); ?></td>
		</tr>
		<tr>		
			<td>
				<input type="checkbox" id="cb_pub" name="imp_pub" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_10'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_11'); ?></td>
		</tr>
		<tr>
			<td>
				<input type="checkbox" id="override" name="override" value="1" /><?php echo JTEXT::_('DB_LIGA_IMPORT_12'); ?></td>
			<td><?php echo JTEXT::_('DB_LIGA_IMPORT_13'); ?></td>
		</tr>
	  </table>
	</fieldset>
	<?php  } ?>
	</td>
	</tr>		
	</table>

	<input type="hidden" name="controller" value="db" />		
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
