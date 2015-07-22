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
<pre><?php // print_r($this->data);
?></pre>

<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data" >
<h1>Turnierauswertung : <?php echo $this->data->tournament->tname.' ('.$this->data->tournament->tcode;?>)</h1>

<?php if(!empty($this->data)){ ?>
<table width="100%" class="adminlist">
<tr>
	<td width="40%" style="vertical-align: top;">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Turnier Details' ); ?></legend>
		<table class="adminlist">
		<tr>
		<td>Name</td>
		<td ><a href="index.php?option=com_clm&view=dewis&task=turnier_detail&turnier=<?php echo $this->data->tournament->tcode; ?>"><?php echo $this->data->tournament->tname; ?></a></td>
		</tr>
		<tr>
		<td>Turniercode</td>
		<td ><?php echo $this->data->tournament->tcode; ?></td>
		</tr>
		<td>Typ</td>
		<td ><?php echo $this->data->tournament->type; ?></td>
		</tr>
		<tr>
		<td>beendet am</td>
		<td ><?php echo $this->data->tournament->finishedOn; ?></td>
		</tr>
		<tr>
		<td>ausgewertet am</td>
		<td ><?php echo $this->data->tournament->computedOn; ?></td>
		</tr>
		<tr>
		<td>zuletzt ausgewertet am</td>
		<td ><?php echo $this->data->tournament->recomputedOn; ?></td>
		</tr>
		<tr>
		<td>ID Auswerter 1</td>
		<td ><?php echo $this->data->tournament->assessor1; ?></td>
		</tr>
		<tr>
		<td>ID Auswerter 2</td>
		<td ><?php echo $this->data->tournament->assessor2; ?></td>
		</tr>
		<tr>
		<td>Anzahl Runden</td>
		<td ><?php echo $this->data->tournament->rounds; ?></td>
		</tr>

		<td>Anzahl Spieler</td>
		<td ><?php echo $this->data->tournament->cntPlayer; ?></td>
		</tr>
		<tr>
		<td>Anzahl Partien</td>
		<td ><?php echo $this->data->tournament->cntGames; ?></td>
		</tr>
		</table>

		</fieldset>
	
	</td>
	
	<td width="60%" style="vertical-align: top;">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Turnierauswertung' ); ?></legend>
		
		<table class="adminlist">
			<thead>
				<tr>
				<th>ID</th><th>Name</th><th>DWZ alt</th><th>Pkt</th><th>Par.</th><th>ung.</th>
				<th>We</th><th>&#216; Geg.</th><th>Leist.</th><th>Ef</th><th>DWZ Neu</th>
				</tr>
			</thead>
		
		<?php foreach ($this->data->evaluation as $m) { ?>
		<tr>

		<tr>
		<td><?php echo $m->pid; ?></td>
		<td><a href="index.php?option=com_clm&view=dewis&task=spieler_detail&name=<?php echo $m->surname.','.$m->firstname; ?>&pkz=<?php echo $m->pid; ?>"><?php echo $m->surname.','.$m->firstname; ?></a></td>
		<td><?php echo $m->ratingOld.'-'.$m->ratingOldIndex; ?></td>
		<td><?php echo $m->points; ?></td>
		<td><?php echo $m->games; ?></td>
		<td><?php echo $m->unratedGames; ?></td>
		<td><?php echo $m->we; ?></td>
		<td><?php echo $m->level; ?></td>
		<td><?php echo $m->achievement; ?></td>
		<td><?php echo $m->eCoefficient; ?></td>
		<td><?php echo $m->ratingNew.'-'.$m->ratingNewIndex; ?></td>
		
		
<!--		<td style="text-align:center; width:55px;"></td>
		<td style="text-align:left;width:180px;"></td>
	-->	</tr>

		<?php } ?>
		</tr>
		</table>

		</fieldset>
	</td>
</tr>
</table>	

<?php } else { ?><h1>Kein Treffer in der DSB Datenbank !</h1><?php }  ?>

	<input type="hidden" name="name" value="<?php echo $this->name;?>" />
		
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="dewis" />
	<input type="hidden" name="controller" value="dewis" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
