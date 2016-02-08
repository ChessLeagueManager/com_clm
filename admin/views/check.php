<?php

/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2016 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.fishpoke.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

class CLMViewCheck
{
public static function setCheckToolbar()
	{
		JToolBarHelper::title( JText::_( 'CHECK_TITLE' ), 'generic.png' );
		JToolBarHelper::cancel();
		JToolBarHelper::help( 'screen.clm.pruefen' );
	}

public static function check( &$row, $dat, $rnd, $liga, $dg ,$runde)
	{
	CLMViewCheck::setCheckToolbar();
	JRequest::setVar( 'hidemainmenu', 1 );
	JFilterOutput::objectHTMLSafe( $row, ENT_QUOTES, 'extrainfo' );
	// Konfigurationsparameter auslesen
	$config = clm_core::$db->config();
	$countryversion= $config->countryversion;
		?>
	
		<form action="index.php" method="post" name="adminForm" id="adminForm">

<h1> <?php echo $rnd[0]->name.','.JText::_('CHECK_ROUND').$rnd[0]->nr.','.JText::_('CHECK_DATE').' '.JHtml::_('date',  $rnd[0]->datum, JText::_('DATE_FORMAT_CLM_F')); ?> </h1>
<?php echo JText::_('CHECK_COMMENT');?>

 <div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'CHECK_LEGEND_DOUBLE')." - ".JText::_('CHECK_DATE').' '.JHtml::_('date',  $rnd[0]->datum, JText::_('DATE_FORMAT_CLM_F')); ?></legend>

   <?php // Heimmannschaften , Gastmannschaften auswerten
	$spieler = 0;
	$z=0;
	
	// Paarungsweise laden
	for ($x=0; $x < count($dat); $x++) {
	// Bretter laden
			$check_spl = CLMControllerCheck::check_d_spl($dat[$z]->hzps,$dat[$z]->spieler,$dat[$z]->PKZ,$runde,$dg,$rnd);  
		if ($check_spl > 1)
			{
			echo JText::_('CHECK_PAAR_1').$dat[$z]->hname.JText::_('CHECK_PAAR_2').$dat[$z]->paar.JText::_('CHECK_PAAR_3').$dat[$z]->brett;
			echo JText::_('CHECK_PAAR_4').$check_spl.JText::_('CHECK_PAAR_5');

			$liga = CLMControllerCheck::show_d_spl($dat[$z]->hzps,$dat[$z]->spieler,$dat[$z]->PKZ,$runde,$dg,$rnd); 
			foreach ($liga as $liga){ 
			echo JText::_('CHECK_PAAR_6'); ?>
			<font color="#FF0000"><?php echo $liga->name; ?></font><?php echo JText::_('CHECK_PAAR_7').$liga->paar.JText::_('CHECK_PAAR_8').$liga->brett; ?> 
			<br>
			<?php } 
			$spieler++;
		     }
	$z++;
	}
	if ( $spieler < 1 ) { echo JText::_('CHECK_PAAR_9'); } ?>

  </fieldset>
  </div>

  
  <div class="width-50 fltrt">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'CHECK_ROUND_1').$runde.JText::_( 'CHECK_ROUND_2').$dg ; ?></legend>
	<?php // Heimmannschaften , Gastmannschaften auswerten

	$spieler = 0;
	$z=0;
	
	// Paarungsweise laden
	for ($x=0; $x < count($dat); $x++) {
	// Bretter laden
	$check_spl = CLMControllerCheck::check_r_spl($dat[$z]->hzps,$dat[$z]->spieler,$dat[$z]->PKZ,$runde,$dg,$rnd);
		if ($check_spl > 1)
			{
			echo JText::_('CHECK_PAAR_1').$dat[$z]->hname.JText::_('CHECK_PAAR_2').$dat[$z]->paar.JText::_('CHECK_PAAR_3').$dat[$z]->brett;
			echo JText::_('CHECK_PAAR_4').$check_spl.JText::_('CHECK_PAAR_5');

			$liga = CLMControllerCheck::show_r_spl($dat[$z]->hzps,$dat[$z]->spieler,$dat[$z]->PKZ,$runde,$dg,$rnd);
			foreach ($liga as $liga){ 
			echo JText::_('CHECK_PAAR_6'); ?>
			<font color="#FF0000"><?php echo $liga->name; ?></font><?php echo JText::_('CHECK_PAAR_7').$liga->paar.JText::_('CHECK_PAAR_8').$liga->brett; ?> 
			<br>
			<?php } 
			$spieler++;
		     }
	$z++;
	}
	if ( $spieler < 1 ) { echo JText::_('CHECK_PAAR_9'); } ?>

  </fieldset>
  </div>
  
  
 <div class="width-50 fltlft">
  <fieldset class="adminform">
   <legend><?php echo JText::_( 'CHECK_AUF_1' ); ?></legend>
	<?php // Heimmannschaften , Gastmannschaften auswerten
	$spieler = 0;
	$z=0;
	// Paarungsweise laden
	for ($x=0; $x < $rnd[0]->teil; $x++) {
	// Bretter laden
	for ($y=0; $y < (($rnd[0]->stamm)-1); $y++) {
		if (!isset($dat[$z+1])) break;
 		if ($dat[$z]->hnr+(1000*($dat[$z]->rmnr)) >= $dat[$z+1]->hnr+(1000*($dat[$z+1]->rmnr)) AND $dat[$z+1]->brett > 1 )
			{
			echo "<br><b>".$dat[$z+1]->hname."</b>, ".$dat[$z+1]->man_heim.JText::_('CHECK_PAAR_7').$dat[$z+1]->paar.JText::_('CHECK_PAAR_8').$dat[$z+1]->brett.JText::_('CHECK_AUF_2');
			$spieler++;
			}
	$z++;
	}}
	if ( $spieler < 1 ) { echo JText::_('CHECK_PAAR_9'); } ?>

  </fieldset>
  </div>
		<div class="clr"></div>

		<input type="hidden" name="section" value="runden" />
		<input type="hidden" name="option" value="com_clm" />
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_( 'form.token' ); ?>
		</form>
		<?php
	}
}