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

require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_clm'.DS.'controllers'.DS.'db.php');
$clmAccess = clm_core::$access;


?>


<form action="index.php?option=com_clm&view=db" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data" >

	<?php ob_start(); ?>
	<?php echo JText::_( 'DB_ATT_0' ); ?><br><br>
	<?php echo JText::_( 'DB_ATT_2' ); ?><br>
	<?php echo JText::_( 'DB_ATT_3' ); ?>
 	<?php 
 	$content = ob_get_contents();
 	ob_end_clean(); 
 	$fix = clm_core::$load->load_view("spoiler",array(JText::_( 'DB_ATT' ),$content,true));
	echo $fix[1]; // array dereferencing fix php 5.3
 	?>
 	
 	
	<?php ob_start();
	echo JTEXT::_('DB_UPLOAD_1') .'<br/><input type="file" name="datei" /><br/>'; 
		$files = CLMControllerDB::files();
		echo "<br/>".JTEXT::_('DB_EXPORT_1');
		for ($x=0; $x< count($files); $x++ ) { ?>
			<br/><a href="components/com_clm/upload/<?php echo $files[$x]; ?>" target="_blank"><?php echo $files[$x]; ?></a>
		<?php } 
		echo "<br/><br/>".JTEXT::_('DB_LIGA_EXPORT_11')."<br/>";
		?>
		<select size="1" name="delete_export">
			<option value="0"><?php echo JTEXT::_('DB_LIGA_EXPORT_12'); ?></option>
			<option value="all"><?php echo JTEXT::_('DB_LIGA_EXPORT_13'); ?></option>
			<?php if(isset($files)) for ($x=0; $x < count($files); $x++) { ?>
				<option value="<?php echo $files[$x]; ?>"><?php echo $files[$x]; ?></option> 
			<?php } ?>
		  </select> 
	<?php

 	$content = ob_get_contents();
 	ob_end_clean(); 
 	$fix = clm_core::$load->load_view("spoiler",array(JTEXT::_('DB_TOOLS'),$content));
	echo $fix[1]; // array dereferencing fix php 5.3
 	?>
 
	<?php ob_start(); ?>
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
 	<?php 
 	$content = ob_get_contents();
 	ob_end_clean(); 
 	$fix = clm_core::$load->load_view("spoiler",array(JTEXT::_('DB_LIGA_EXPORT'),$content));
	echo $fix[1]; // array dereferencing fix php 5.3
 	?>
 	
 	<?php ob_start(); ?>
	<?php $saison_import = CLMControllerDB::saison(); ?>
	  <table>
		<tr>
			<td class="key" nowrap="nowrap">
			  <select size="1" name="import">
				<option value="0"><?php echo JTEXT::_('DB_LIGA_IMPORT_1'); ?></option>
				<?php for ($x=0; $x < count($files); $x++) { ?>
					<option value="<?php echo $files[$x]; ?>"><?php echo $files[$x]; ?></option> 
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
	 <?php 
 	$content = ob_get_contents();
 	ob_end_clean(); 
 	$fix = clm_core::$load->load_view("spoiler",array(JTEXT::_('DB_LIGA_IMPORT'),$content));
	echo $fix[1]; // array dereferencing fix php 5.3
 	?>
	
	<?php 
	$fix = clm_core::$api->view_database();
	echo $fix[2]; // array dereferencing fix php 5.3
	?>

	<input type="hidden" name="controller" value="db" />		
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
