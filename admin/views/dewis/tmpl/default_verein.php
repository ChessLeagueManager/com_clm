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
?>
<pre><?php //print_r($this->data);
?></pre>

<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data" >
<h1><?php echo $this->name.' ('.$this->zps.')'; ?></h1>
	<table width="100%" class="adminlist">
	<thead>
	<tr>
		<th style="background-color:#ff0;" colspan="9">DeWiS Daten</th>
		<th>&nbsp;</th>
		<th style="background-color:#ff0;" colspan="5">CLM Daten</th>
	</tr>
		<tr>
			<th width="10">
			<?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?>
			</th>
			<th width="10">
			<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo 1+count( $this->data ); ?>);" />
			</th>
			<th width="80">ID</th><th>Name</th><th width="35">Geschl.</th>
			<th width="40">Status</th><th width="50">Mgl.Nr.</th><th width="70">DWZ</th><th width="40">DSB</th>
			
			<th width="20">&nbsp;</th>
			<th>Name</th><th width="40">Status</th><th width="50">Mgl.Nr.</th><th width="70">DWZ</th>
			<th width="40">Spieler löschen</th>
		</tr>
	</thead>
			<!--<td width="50%" style="vertical-align: top;">-->
		<?php $cnt = 1; $dclass=""; $cclass=""; $dwzclass="";
		
		foreach ($this->data as $data ) { 

			if($data->dpkz ==""){ $dclass = "background-color:#EBF58C;"; }
			if($data->sname ==""){ $cclass = "background-color:#EBF58C;"; }
			if(($data->dindex != $data->sindex AND $data->dindex > 0 AND $data->sname !="") 
				OR ($data->sdwz != $data->ddwz)
			)
				{ $dwzclass = "background-color:#ceffff;";}

			
//			if($data->sdwz != $data->ddwz){ $dwzclass = "background-color:#ceffff;"; echo "***".$cnt;}
			
		//	if($cnt < 2){ echo "->".$cnt.'<-->'.$data->sdwz.'<-->'.$data->ddwz;}
			//if($data->sname ==""){ $value_cb = $data->dpkz; }
			if($data->dpkz ==""){ $value_cb = $this->zps.$data->smgl;}
					else{ $value_cb = $this->zps.$data->dmgl;}
			?>
			<tr>
				<td align="center">
					<?php echo $cnt;?>
				</td>
				<td>
			<input type="checkbox" id="cb<?php echo $cnt;?>" name="cid[]" value="<?php echo $value_cb; ?>" />	
				</td>
				<td style="text-align:center;<?php echo $dclass;?>"><?php echo $data->dpkz; ?></td>
				<td style="padding-left:5px;<?php echo $dclass;?>"><?php echo $data->dname; ?></td>
				<td style="text-align:center;<?php echo $dclass;?>"><?php echo $data->dgeschlecht; ?></td>
				<td style="text-align:center;<?php echo $dclass;?>"><?php echo $data->dstatus; ?></td>
				<td style="padding-left:5px;<?php echo $dclass;?>"><?php echo $data->dmgl; ?></td>
				<td style="padding-left:5px;<?php echo $dclass;?>"><?php echo $data->ddwz.'-'.$data->dindex; ?></td>
				<td style="text-align:center;<?php echo $dclass;?>"><?php if($data->dpkz){?><a href="http://www.schachbund.de/spieler.html?pkz=<?php echo $data->dpkz; ?>" target="_blank">LINK</a><?php } ?></td>
				
				<td width="20"></td>
				<td style="padding-left:5px;<?php echo $cclass;?>"><?php echo $data->sname; ?></td>
				<td style="text-align:center;<?php echo $cclass;?>"><?php echo $data->sstatus; ?></td>
				<td style="padding-left:5px;<?php echo $cclass;?>"><?php echo $data->smgl; ?></td>
				<td style="padding-left:5px;<?php echo $cclass; echo $dwzclass; ?>"><?php echo $data->sdwz.'-'.$data->sindex; ?></td>
				<td style="padding-left:5px;">DEL</td>
			</tr>
		<?php $cnt++; $dclass=""; $cclass=""; $dwzclass=""; }
		?>
	</table>
	
<pre><?php  //print_r($this->data);?></pre>
		
	<input type="hidden" name="zps" value="<?php echo $this->zps; ?>" />	
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="dewis" />
	<input type="hidden" name="controller" value="dewis" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
