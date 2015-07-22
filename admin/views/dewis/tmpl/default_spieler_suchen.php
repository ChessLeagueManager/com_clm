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
<?php if(!empty($this->data->members)){ ?>
<h1>Spieler Suche : <?php echo $this->name;?></h1>
<?php if(count($this->data->members) > 99 ){?><h3>Es werden maximal 100 Treffer ausgegeben !
Spezifizieren Sie ihre Suche durch die Angabe von z.B. dem ersten Buchstaben des Nachnamens</h3><?php } ?>

<table width="100%" class="adminlist">
	<thead>
	<tr>
			<th width="10"><?php echo JText::_( 'JGRID_HEADING_ROW_NUMBER' ); ?></th>
			<th width="60">ID</th>
			<th>Name</th>
			<th>Verein</th>
			<th width="30">Titel</th>
			<th width="40">DWZ</th>
			<th width="40">Index</th>
			<th width="35">Geschl.</th>
			<th width="40">Mgl.Nr.</th>
			<th width="50">Geb.Jahr</th>
			<th width="40">Status</th>
			<th width="50">FIDE ID</th>
			<th width="35">ELO</th>
			<th width="80">letztes Turnier</th>
			<th width="70">beendet am</th>
			<th width="40">DSB</th>
	</tr>
	</thead>
			<!--<td width="50%" style="vertical-align: top;">-->
		<?php $cnt = 1; foreach ($this->data->members as $data ) { 
			$value_cb = $data->vkz.$data->club;
			?>
			<tr>
			<td align="center"><?php echo $cnt;?></td>
			<td style="text-align:center;"><?php echo $data->pid; ?></td>
			<td style="padding-left:10px;"><a href="index.php?option=com_clm&view=dewis&task=spieler_detail&name=<?php echo $data->surname;?>&pkz=<?php echo $data->pid; ?>"><?php echo $data->surname.','.$data->firstname; ?></a></td>
			<td ><a href="index.php?option=com_clm&view=dewis&task=verein_detail&zps=<?php echo $data->vkz; ?>"><?php echo $data->club; ?></a></td>
			<td style="text-align:center;"><?php echo $data->title; ?></td>
			<td style="text-align:center;"><?php echo $data->rating; ?></td>
			<td style="text-align:center;"><?php echo $data->ratingIndex; ?></td>
			<td style="text-align:center;"><?php echo $data->gender; ?></td>
			<td style="text-align:center;"><?php echo $data->membership; ?></td>
			<td style="text-align:center;"><?php echo $data->yearOfBirth; ?></td>
			<td style="padding-left:5px;"><?php echo $data->state; ?></td>
			<td style="text-align:right;"><a href="http://ratings.fide.com/card.phtml?event=<?php echo $data->idfide; ?>" target="_blank"><?php echo $data->idfide; ?></a></td>
			<td style="text-align:center;"><?php echo $data->elo; ?></td>
			<td style="padding-left:5px;"><a href="index.php?option=com_clm&view=dewis&task=turnier_detail&turnier=<?php echo $data->tcode; ?>"><?php echo $data->tcode; ?></a></td>
			<td style="text-align:center;"><?php echo $data->finishedOn; ?></td>
			<td style="text-align:center;"><?php if($data->pid){?><a href="http://www.schachbund.de/spieler.html?pkz=<?php echo $data->pid; ?>" target="_blank">LINK</a><?php } ?></td>
			</tr>
		<?php $cnt++; }
		?>
	</table>

<?php } else { echo '<h1>Kein Eintrag in der DSB Datenbank gefunden !</h1>'; }  ?>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="dewis" />
	<input type="hidden" name="controller" value="dewis" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
