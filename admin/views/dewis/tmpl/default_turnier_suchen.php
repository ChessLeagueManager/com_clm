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

<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data" >
<h1>Turnier Suche.&nbsp;&nbsp;
<?php
	if(!empty($this->sdatum)){ echo "&nbsp;&nbsp;&nbsp;<u>Start</u> : ".$this->sdatum; }
	if(!empty($this->edatum)){ echo "&nbsp;&nbsp;&nbsp;<u>Ende</u> : ".$this->edatum; }
	if(!empty($this->turnier)){ echo "&nbsp;&nbsp;&nbsp;<u>Suchbegriff</u> : ".$this->turnier; }
?></h1>
	
<?php if(!empty($this->data->tournaments)){ ?>
<table width="100%" class="adminlist">

		<tr>
		<thead>
			<th>#</th>
			<th>Turniercode</th><th>Name</th><th>Runden</th><th>Spieler</th><th>beendet</th>
			<th>ausgewertet</th><th>erneut ausgewertet</th>
			<th>Auswerter</th><th>Auswerter 2</th>
		</thead>
		</tr>


	<?php $cnt=1; foreach ($this->data->tournaments as $turnier) { ?>
	<tr>
		<td style="text-align:center;"><?php echo $cnt; ?></td>
		<td style="text-align:center;"><?php echo $turnier->tcode; ?></td>
		<td style="text-align:center;"><a href="index.php?option=com_clm&view=dewis&task=turnier_detail&turnier=<?php echo $turnier->tcode; ?>"><?php echo $turnier->tname; ?></a></td>
		<td style="text-align:center;"><?php echo $turnier->rounds; ?></td>
		<td style="text-align:center;"><?php echo $turnier->cntPlayer; ?></td>
		<td style="text-align:center;"><?php echo $turnier->finishedOn; ?></td>
		<td style="text-align:center;"><a href="index.php?option=com_clm&view=dewis&task=turnier_auswertung&turnier=<?php echo $turnier->tcode; ?>"><?php echo $turnier->computedOn; ?></a></td>
		<td style="text-align:center;"><?php echo $turnier->recomputedOn; ?></td>
		<td style="text-align:center;"><?php echo $turnier->assessor1; ?></td>
		<td style="text-align:center;"><?php echo $turnier->assessor2; ?></td>
	</tr>
	<?php $cnt++; } ?>
</table>

<?php } else { ?><h1>Kein Treffer in der DSB Datenbank !</h1><?php }  ?>
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="dewis" />
	<input type="hidden" name="controller" value="dewis" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
