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
<pre><?php  //print_r($this->data);
?></pre>

<form action="index.php" method="post" name="adminForm"  enctype="multipart/form-data" >

<?php if(!empty($this->data)){ ?>
<h1>Turnier Details : <?php echo $this->data->tournament->tname.' ('.$this->data->tournament->tcode;?>)</h1>
<table width="100%" class="adminlist">
<tr>
	<td width="40%" style="vertical-align: top;">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Turnier Details' ); ?></legend>
		<table class="adminlist">
		<tr>
		<td>Name</td>
		<td ><?php echo $this->data->tournament->tname; ?></td>
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
		<td ><a href="index.php?option=com_clm&view=dewis&task=turnier_auswertung&turnier=<?php echo $this->data->tournament->tcode; ?>"><?php echo $this->data->tournament->computedOn; ?></a></td>
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
		<legend><?php echo JText::_( 'Rundenübersicht' ); ?></legend>
		
		<?php foreach($this->data->rounds as $r) { ?>
		<table class="adminlist">
			<thead>
				<tr><th colspan="6" style="text-align:left;">Runde <?php echo $r->no;
				if (!empty($r->appointment)) {
					echo " ( ".$r->appointment." )";
				}?>
				</th>
				</tr>

				<tr>
				<th>ID Weiss</th><th>Weiss</th><th>-</th><th>ID Schwarz</th>
				<th>Schwarz</th><th>Ergebnis</th>
				</tr>
			</thead>
		
		<?php foreach ($r->games as $g) { ?>
		<tr>

		<tr>
		<td style="text-align:center; width:55px;"><?php echo $g->idWhite; ?></td>
		<td style="text-align:left;width:180px;"><a href="index.php?option=com_clm&view=dewis&task=spieler_detail&name=<?php echo $g->white; ?>&pkz=<?php echo $g->idWhite; ?>"><?php echo $g->white; ?></a></td>
		<td style="text-align:center;width:10px;">-</td>
		<td style="text-align:center;width:55px;"><?php echo $g->idBlack; ?></td>
		<td style="text-align:left;"><a href="index.php?option=com_clm&view=dewis&task=spieler_detail&name=<?php echo $g->black; ?>&pkz=<?php echo $g->idBlack; ?>"><?php echo $g->black; ?></a></td>
		<td style="text-align:center;width:50px;"><?php echo $g->result; ?></td>
		</tr>

		<?php } ?>
		</tr>
		</table>
		<?php } ?>

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
