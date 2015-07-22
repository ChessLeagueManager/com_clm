<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2015 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
 
*/
defined('_JEXEC') or die('Restricted access');

?>
<script language="javascript" type="text/javascript"><!--

function showTournaments(){
	document.adminForm.turnier.value = 0;
	
	var saison = document.adminForm.saison.value;
	
	var optionen = document.adminForm.turnier.getElementsByTagName('option');
	var i = 1;
	while(i < optionen.length) {
		if(optionen[i].getAttribute('sid') != saison) {
			optionen[i].style.display = 'none';
		} else {
			optionen[i].style.display = 'block';
		}
		i++;
	}
}
-->
	
	Joomla.submitbutton = function (pressbutton) { 
		var form = document.adminForm;
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		
		// do field validation
		if (form.name.value == "") {
			alert( jserror['enter_name'] );
		}
		if (form.use_birthYear_filter.value == 1 ) {
			if (form.birthYear_younger_than.value < '1901' || form.birthYear_younger_than.value > '2155') {
				alert( jserror['check_year_ay'] );
			} else if (form.birthYear_older_than.value < '1901' || form.birthYear_older_than.value > '2155') {
				alert( jserror['check_year_ao'] );
			}
		} 
		if (form.use_sex_year_filter.value == 1 ) {
			if (form.maleYear_younger_than.value < '1901' || form.maleYear_younger_than.value > '2155') {
			alert( jserror['check_year_cmy'] );
		} else if (form.maleYear_older_than.value < '1901' || form.maleYear_older_than.value > '2155') {
			alert( jserror['check_year_cmo'] );
		} else if (form.femaleYear_younger_than.value < '1901' || form.femaleYear_younger_than.value > '2155') {
			alert( jserror['check_year_cfy'] );
		} else if (form.femaleYear_older_than.value < '1901' || form.femaleYear_older_than.value > '2155') {
			alert( jserror['check_year_cfo'] );
			}
		} 
		submitform( pressbutton );
	}  
</script>


<form action="index.php" method="post" name="adminForm" id="adminForm"> 
	<table class="admintable"> 
		<tr>
			<td width="50%" style="vertical-align: top;">
				<fieldset class="adminform"> 
					<legend><?php echo JText::_( 'JDETAILS' ); ?></legend> 
					<table class="admintable">
						<?php if(!$this->isNew): ?>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="id">ID</label> 
							</td> 
							<td> 
								<strong><?php echo $this->sonderrangliste->id;?></strong> 
							</td> 
						</tr>
						<?php endif; ?>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="name"><?php echo JText::_( 'SPECIALRANKING_NAME' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="name" 
								id="name" size="48" maxlength="255" 
								value="<?php echo $this->sonderrangliste->name;?>" /> 
							</td> 
						</tr> 
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="saison"><?php echo JText::_( 'SPECIALRANKING_SAISON' ); ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['saison'];?> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="turnier"><?php echo JText::_( 'SPECIALRANKING_TOURNEMENT' ); ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['turnier'];?> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="published"><?php echo JText::_( 'JPUBLISHED' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['published'];?> 
							</fieldset></td> 
						</tr>
						<!--<tr> 
							<td width="100" align="right" class="key"> 
								<label for="description"><?php //echo JText::_( 'DESCRIPTION' ); ?></label> 
							</td> 
							<td> 
								<textarea class="inputbox" type="text" name="description" 
								id="description" cols="25" rows="3" /><?php //echo $this->video->description;?></textarea>
							</td> 
						</tr>-->
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="ordering"><?php echo JText::_( 'JFIELD_ORDERING_LABEL' ); ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['ordering'];?> 
							</td> 
						</tr>
					</table> 
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SPECIALRANKING_RATING_FILTER_OPTIONS' ); ?></legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="use_rating_filter"><?php echo JText::_( 'SPECIALRANKING_OPTION_USE_RATING_FILTER' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['use_rating_filter'];?> 
							</fieldset></td> 
						</tr>
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="rating_type"><?php echo JText::_( 'SPECIALRANKING_OPTION_RATING_TYPE' ); ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['rating_type'];?> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="rating_higher_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_RATING_HIGHER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="rating_higher_than" 
								id="rating_higher_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->rating_higher_than;?>" /> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="rating_lower_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_RATING_LOWER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="rating_lower_than" 
								id="rating_lower_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->rating_lower_than;?>" /> 
							</td> 
						</tr>  						
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SPECIALRANKING_BIRTHYEAR_FILTER_OPTIONS' ); ?></legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="use_birtYear_filter"><?php echo JText::_( 'SPECIALRANKING_OPTION_USE_BIRTHYEAR_FILTER' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['use_birthYear_filter'];?> 
							</fieldset></td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="birthYear_younger_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_BIRTHYEAR_YOUNGER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="birthYear_younger_than" 
								id="birthYear_younger_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->birthYear_younger_than;?>" /> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="birthYear_older_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_BIRTHYEAR_OLDER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="birthYear_older_than" 
								id="birthYear_older_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->birthYear_older_than;?>" /> 
							</td> 
						</tr>  						
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SPECIALRANKING_SEX_FILTER_OPTIONS' ); ?></legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="use_rating_filter"><?php echo JText::_( 'SPECIALRANKING_OPTION_USE_SEX_FILTER' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['use_sex_filter'];?> 
							</fieldset></td> 
						</tr>
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="rating_type"><?php echo JText::_( 'SPECIALRANKING_OPTION_SEX' ); ?></label> 
							</td> 
							<td> 
								<?php  echo $this->lists['sex'];?> 
							</td> 
						</tr>
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SPECIALRANKING_SEX_YEAR_FILTER_OPTIONS' ); ?></legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="use_sex_year_filter"><?php echo JText::_( 'SPECIALRANKING_OPTION_USE_SEX_YEAR_FILTER' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['use_sex_year_filter'];?> 
							</fieldset></td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="maleYear_younger_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_MALEYEAR_YOUNGER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="maleYear_younger_than" 
								id="maleYear_younger_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->maleYear_younger_than;?>" /> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="maleYear_older_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_MALEYEAR_OLDER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="maleYear_older_than" 
								id="maleYear_older_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->maleYear_older_than;?>" /> 
							</td> 
						</tr>  						
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="femaleYear_younger_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_FEMALEYEAR_YOUNGER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="femaleYear_younger_than" 
								id="femaleYear_younger_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->femaleYear_younger_than;?>" /> 
							</td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="femaleYear_older_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_FEMALEYEAR_OLDER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="femaleYear_older_than" 
								id="femaleYear_older_than" size="4" maxlength="4" 
								value="<?php echo $this->sonderrangliste->femaleYear_older_than;?>" /> 
							</td> 
						</tr>  						
					</table>
				</fieldset>
				<fieldset class="adminform">
					<legend><?php echo JText::_( 'SPECIALRANKING_ZPS_FILTER_OPTIONS' ); ?></legend>
					<table class="admintable">
						<tr>
							<td width="100" align="right" class="key"> 
								<label for="use_zps_filter"><?php echo JText::_( 'SPECIALRANKING_OPTION_USE_ZPS_FILTER' ); ?></label> 
							</td> 
							<td><fieldset class="radio"> 
								<?php  echo $this->lists['use_zps_filter'];?> 
							</fieldset></td> 
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="zps_higher_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_ZPS_HIGHER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="zps_higher_than" 
								id="zps_higher_than" size="4" maxlength="5" 
								value="<?php echo $this->sonderrangliste->zps_higher_than;?>" /> 
			</td>
						</tr>
						<tr> 
							<td width="100" align="right" class="key"> 
								<label for="zps_lower_than"><?php echo JText::_( 'SPECIALRANKING_OPTION_ZPS_LOWER_THAN' ); ?></label> 
							</td> 
							<td> 
								<input class="inputbox" type="text" name="zps_lower_than" 
								id="zps_lower_than" size="4" maxlength="5" 
								value="<?php echo $this->sonderrangliste->zps_lower_than;?>" /> 
							</td> 
						</tr>  						
					</table>
				</fieldset>

			</td>
			<td width="50%" style="vertical-align: top;">
			</td>
		</tr>
    </table> 
	<div class="clr"></div> 
	<input type="hidden" name="option" value="com_clm" />
	<input type="hidden" name="view" value="sonderranglistenform" />
	<input type="hidden" name="id" value="<?php echo $this->sonderrangliste->id; ?>" />
	<input type="hidden" name="controller" value="sonderranglistenform" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>

</form>
<script language="javascript" type="text/javascript"><!-- 
	var saison = document.adminForm.saison.value;
	
	var optionen = document.adminForm.turnier.getElementsByTagName('option');
	var i = 1;
	while(i < optionen.length) {
		if(optionen[i].getAttribute('sid') != saison) {
			optionen[i].style.display = 'none';
		} else {
			optionen[i].style.display = 'block';
		}
		i++;
	}
--></script>
 