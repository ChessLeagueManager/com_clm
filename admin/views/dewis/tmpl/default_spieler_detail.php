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

<?php if(!empty($this->data)){ ?>
	<h1>Spieler Details : <?php echo $this->data->member->surname.','.$this->data->member->firstname;?></h1>
<table width="100%" class="adminlist">
<tr>
	<td width="25%" style="vertical-align: top;">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Persönliche Daten' ); ?></legend>
		<table class="adminlist">
		<tr>
		<td>Name</td>
		<td ><?php echo $this->data->member->surname.','.$this->data->member->firstname; ?></td>
		</tr>
		<tr>
		<td>Geburtsjahr</td>
		<td ><?php echo $this->data->member->yearOfBirth; ?></td>
		</tr>
		<tr>
		<td>Geschlecht</td>
		<td ><?php echo $this->data->member->gender ?></td>
		</tr>
		<tr>
		<td>PKZ</td>
		<td ><a href="http://www.schachbund.de/spieler.html?pkz=<?php echo $this->data->member->pid; ?>" target="_blank"><?php echo $this->data->member->pid; ?></a></td>
		</tr>
		<tr>
		<td>DWZ</td>
		<td ><?php echo $this->data->member->rating.'-'.$this->data->member->ratingIndex; ?></td>
		</tr>
		<tr>
		<td>FIDE-ID</td>
		<td ><a href="http://ratings.fide.com/card.phtml?event=<?php echo $this->data->member->idfide; ?>" target="_blank"><?php echo $this->data->member->idfide; ?></a></td>
		</tr>
		<tr>
		<td>Elo</td>
		<td ><?php echo $this->data->member->elo; ?></td>
		</tr>
		<tr>
		<td>FIDE-Titel</td>
		<td ><?php echo $this->data->member->fideTitle; ?></td>
		</tr>
		<tr>
		<td>FIDE-Nation</td>
		<td ><?php echo $this->data->member->fideNation; ?></td>
		</tr>
		</table>

		</fieldset>
		
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Mitgliedschaften' ); ?></legend>
		<table class="adminlist">
		<thead>
		<tr>
		<th>ZPS</th><th>Verein</th><th>Mgl.</th><th>Status</th>
		</tr>
		<thead>

		<?php foreach ($this->data->memberships as $mitglied) { ?>
		<tr>
		<td style="text-align:center;"><?php echo $mitglied->vkz; ?></td>
		<td ><a href="index.php?option=com_clm&view=dewis&task=verein_detail&zps=<?php echo $mitglied->vkz; ?>"><?php echo $mitglied->club; ?></a></td>
		<td style="text-align:center;"><?php echo $mitglied->membership; ?></td>
		<td style="text-align:center;"><?php echo $mitglied->state; ?></td>
		</tr>
		<?php } ?>
		</table>
		</fieldset>

		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Rankings' ); ?></legend>
		<table class="adminlist">
		<thead>
		<tr>
		<th>Organisation</th><th>Rang</th>
		</tr>
		<thead>

		<?php foreach ($this->data->ranking as $rankings) { ?>
		<?php foreach ($rankings as $ranking) { ?>
		<tr>
		<td style="text-align:center;"><?php echo $ranking->organization; ?></td>
		<td style="text-align:center;"><?php echo $ranking->rank; ?></td>
		</tr>
		<?php }} ?>
		</table>
		</fieldset>

	</td>
	
	<td width="75%" style="vertical-align: top;">
		<fieldset class="adminform">
		<legend><?php echo JText::_( 'Turnierkarte' ); ?></legend>
				<table class="adminlist">
		<thead>
		<tr>
		<th>Turniercode</th><th>Name</th><th>DWZ alt</th><th>Pkt.</th>
		<th>Par.</th><th>We</th><th>&#216; Gegner</th>
		<th>Lstg.</th><th>Ef</th><th>DWZ neu</th>
		</tr>
		<thead>

		<?php foreach ($this->data->tournaments as $turnier) { ?>
		<tr>
		<td style="text-align:left;"><?php echo $turnier->tcode; ?></td>
		<td style="text-align:left;"><a href="index.php?option=com_clm&view=dewis&task=turnier_detail&turnier=<?php echo $turnier->tcode; ?>"><?php echo $turnier->tname; ?></a></td>
		<td style="text-align:center;"><?php echo $turnier->ratingOld.'-'.$turnier->ratingOldIndex; ?></td>
		<td style="text-align:center;"><?php echo $turnier->points; ?></td>
		<td style="text-align:center;"><?php echo $turnier->games; ?></td>
		<td style="text-align:center;"><?php echo $turnier->we; ?></td>
		<td style="text-align:center;"><?php echo $turnier->level; ?></td>
		<td style="text-align:center;"><?php echo $turnier->achievement; ?></td>
		<td style="text-align:center;"><?php echo $turnier->eCoefficient; ?></td>
		<td style="text-align:center;"><?php echo $turnier->ratingNew.'-'.$turnier->ratingNewIndex; ?></td>
		</tr>
		<?php } ?>
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
