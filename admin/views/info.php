<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008 - 2014 Thomas Schwietert & Andreas Dorn. All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );

class CLMViewInfo extends JView {

	function display () {
		
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
		$clmAccess = new CLMAccess();
		$db 		=& JFactory::getDBO();
		$query = " SELECT COUNT(id) as count FROM #__clm_user ";
		$db->setQuery($query);
		$count_id	= $db->loadObjectList();
		$count		= $count_id[0]->count;

		// Joomla-Version ermitteln
		$version = new JVersion();
		$joomlaVersion = $version->getShortVersion();
		if (substr_count($joomlaVersion, '1.5')) {
			$user	= & JFactory::getUser();
			$jid	= $user->get('id');
			$type	= $user->get('usertype');

			if($type =='Super Administrator' OR $type =='Administrator') { $admin_access = 'YES';}
			else { $admin_access ='NO'; }
		} else {
			//////////////////////////////////////////////////////////////////////////////////
			// Hier kommt der Code zum ermitteln des Joomla Nutzers unter J1.6 und größer hin.
			//////////////////////////////////////////////////////////////////////////////////
			$user	= & JFactory::getUser();
			$jid	= $user->get('id');
			$groups	= $user->get('groups');
			$admin_access ='NO';
			foreach ($groups as $group) {
				if ($group > 5) $admin_access ='YES';
			}
		}
		// Menubilder laden
		require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.'admin_menue_images.php');
	
		if (CLM_usertype == "NO" AND $count != 0 AND $admin_access == 'NO') {
			JError::raiseNotice( 6000,  JText::_( 'INFO_NO_CLM' ));
		}
		if (CLM_usertype == "NO" AND ( $count == 0 OR $admin_access == 'YES')) {
			if($admin_access == 'YES') {
			      JError::raiseNotice( 6000,  JText::_( 'Drücken Sie den \'User erstellen\' Knopf um ihren CLM Admin Zugang zu aktivieren !' ));
			      JToolBarHelper::title(   JText::_( 'Admin Account anlegen' ), 'generic.png' );
			      JToolBarHelper::custom('admin_user','send.png','send_f2.png','INFO_FIRST_USER',false);
			} else {
			      JToolBarHelper::title(   JText::_( 'INFO_FIRST_CLM' ), 'generic.png' );
			      JToolBarHelper::custom('first_user','send.png','send_f2.png','INFO_FIRST_USER',false);
			}
		} else {
			JToolBarHelper::title(   JText::_( 'TITLE_INFO' ), 'clm_logo_bg.png' );
		}
		$clmAccess->accesspoint = 'BE_config_general';
		if ($count != 0 AND $clmAccess->access() == true) {
			JToolBarHelper::preferences('com_clm');
		}
		JToolBarHelper::help( 'screen.clm.info' );
	
		// Erster User noch nicht angelegt
		if (CLM_usertype == "NO" AND ( $count == 0 OR $admin_access == 'YES')) { ?>
	
		<form action="index.php" method="post" name="adminForm" id="adminForm">
	
		<?php if($count ==0) { echo JText::_( 'INFO_NO_USER'); } ?>
			<input type="hidden" name="section" value="info" />
			<input type="hidden" name="option" value="com_clm" />
			<input type="hidden" name="task" value="" />
			<?php echo JHtml::_( 'form.token' ); ?>
			</form>
	
	
	<?php 
	} else { // User angelegt -> Informationen einblenden
	
	$option	= JRequest::getCmd('option');
	
	require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_clm'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'CLMAccess.class.php');
	$clmAccess = new CLMAccess();
	?>
	
	<fieldset class="adminform">
		<legend>Info</legend>
	
	<div class="width-70 fltlft">
	<div id="clm">
	
	<?php //Nur Admin
		//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) { 
	$clmAccess->accesspoint = 'BE_season_general';
	if($clmAccess->access() == true) {
	?>
	<div class="icon-container">
	<div class="icon">
		<a href="index.php?option=com_clm&section=saisons" title="">
		<img src="components/com_clm/images/clm_saison.png" align="middle" border="0" alt="" />
		<span><?php echo JText::_( 'INFO_BUTTON_SEASON' );?></span></a> 
	</div>
		</div>
	
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=<?php echo $option; ?>&amp;section=saisons&amp;task=add" title=""> <img src="components/com_clm/images/clm_saison_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_SEASON_ADD' );?></span></a> </div>
	</div>
	<?php } 
	
	$clmAccess->accesspoint = 'BE_event_general';
	if($clmAccess->access() == true) {
	?>	
	<div class="icon-container">
	<div class="icon"> 
			<?php
			$adminLink = new AdminLink();
			$adminLink->view = "terminemain";
			$adminLink->more = array();
			$adminLink->makeURL();
			?>
		<a href="<?php echo $adminLink->url; ?>" title=""> <img src="components/com_clm/images/clm_termine.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TERMINE' );?></span></a> 
		</div>
	</div>
	<div class="icon-container">
	<div class="icon"> 
			<?php
			$adminLink = new AdminLink();
			$adminLink->view = "termineform";
			$adminLink->more = array('task' => 'add');
			$adminLink->makeURL();
			?>
		<a href="<?php echo $adminLink->url; ?>" title=""> <img src="components/com_clm/images/clm_termine_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TERMINE_ADD' );?></span></a> 
		</div>
	</div>
	<?php } 
	
	$clmAccess->accesspoint = 'BE_tournament_general';
	if($clmAccess->access() == true) {
	?>	
	<div class="icon-container">
	<div class="icon"> 
			<?php
			$adminLink = new AdminLink();
			$adminLink->view = "turmain";
			$adminLink->more = array();
			$adminLink->makeURL();
			?>
		<a href="<?php echo $adminLink->url; ?>" title=""> <img src="components/com_clm/images/clm_turniere.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TOURNAMENTS' );?></span></a> 
		</div>
	</div>
	<?php } 
		
		//Nur Admin
		//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) { 
	$clmAccess->accesspoint = 'BE_tournament_create';
	if($clmAccess->access() == true) {
	?>
	<div class="icon-container">
	<div class="icon"> 
			<?php
			$adminLink = new AdminLink();
			$adminLink->view = "turform";
			$adminLink->more = array('task' => 'add');
			$adminLink->makeURL();
			?>
		<a href="<?php echo $adminLink->url; ?>" title=""> <img src="components/com_clm/images/clm_turniere_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TOURNAMENT_ADD' );?></span></a> 
		</div>
	</div>
	<?php }
	$clmAccess->accesspoint = 'BE_league_general';
	if($clmAccess->access() == true) {
	?>
	
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=ligen" title=""> <img src="components/com_clm/images/clm_liga.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_LEAGUE' );?></span></a> </div>
	</div>
	<?php  } 
		
		//Nur Admin
		//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) { 
	$clmAccess->accesspoint = 'BE_league_create';
	if($clmAccess->access() == true) {
	?>
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=<?php echo $option; ?>&amp;section=ligen&amp;task=add" title=""> <img src="components/com_clm/images/clm_liga_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_LEAGUE_ADD' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_teamtournament_general';
	if($clmAccess->access() == true) {
	?>
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=mturniere" title=""> <img src="components/com_clm/images/clm_mturnier.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_T_TOURNAMENTS' );?></span></a> </div>
	</div>
	<?php  } 
	
		//Nur Admin
		//if (JFactory::getUser()->authorise('core.manage.clm', 'com_clm')) { 
	$clmAccess->accesspoint = 'BE_teamtournament_create';
	if($clmAccess->access() == true) {
	?>
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=<?php echo $option; ?>&amp;section=mturniere&amp;task=add" title=""> <img src="components/com_clm/images/clm_mturnier_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_T_TOURNAMENT_ADD' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_club_general';
	if($clmAccess->access() == true) {
	?>	
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=vereine" title=""> <img src="components/com_clm/images/clm_verein.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_CLUB' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_club_create';
	if($clmAccess->access() == true) {
	?>		
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=<?php echo $option; ?>&amp;section=vereine&amp;task=add" title=""> <img src="components/com_clm/images/clm_verein_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_CLUB_ADD' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_team_general';
	if($clmAccess->access() == true) {
	?>		
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=mannschaften" title=""> <img src="components/com_clm/images/clm_mannschaften.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TEAM' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_team_create';
	if($clmAccess->access() == true) {
	?>		
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=<?php echo $option; ?>&amp;section=mannschaften&amp;task=add" title=""> <img src="components/com_clm/images/clm_mannschaften_neu.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_TEAM_ADD' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_club_edit_member';
	if($clmAccess->access() == true) {
	?>		
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=dwz" title=""> <img src="components/com_clm/images/clm_mitgliederverwaltung.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_MEMBER' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_user_general';
	if($clmAccess->access() == true) {
	?>		
	<div class="icon-container">
	<div class="icon"> <a href="index.php?option=com_clm&section=users" title=""> <img src="components/com_clm/images/clm_benutzer.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_USER' );?></span></a> </div>
	</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_elobase_general';
	if($clmAccess->access() == true) {
	?>		
		<div class="icon-container">
	<!--<div class="icon"> <a href="index.php?option=com_clm&section=elobase" title=""> <img src="components/com_clm/images/clm_elo-base.png" align="middle" border="0" alt="" /> <span><?php //echo JText::_( 'INFO_BUTTON_ELO' );?></span></a> </div>-->
		<div class="icon"> <a href="index.php?option=com_clm&view=auswertung" title=""> <img src="components/com_clm/images/clm_elo-base.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'DeWIS' );?></span></a> </div>
		</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_database_general';
	if($clmAccess->access() == true) {
	?>		
		<div class="icon-container">
	<!--<div class="icon"> <a href="index.php?option=com_clm&section=db" title=""> <img src="components/com_clm/images/clm_datenbank.png" align="middle" border="0" alt="" /> <span><?php //echo JText::_( 'INFO_BUTTON_DATABASE' );?></span></a> </div>-->
		<div class="icon"> <a href="index.php?option=com_clm&view=db" title=""> <img src="components/com_clm/images/clm_datenbank.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_DATABASE' );?></span></a> </div>
		</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_swt_general';
	if($clmAccess->access() == true) {
	?>		
		<div class="icon-container">
		<div class="icon"> <a href="index.php?option=com_clm&view=swt" title=""> <img src="components/com_clm/images/clm_generic.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'INFO_BUTTON_SWT' );?></span></a> </div>
		</div>
	<?php }
	
	$clmAccess->accesspoint = 'BE_config_general';
	if($clmAccess->access() == true) {
		//if (CLM_user == 100) {
	?>
		<div class="icon-container">
		<div class="icon"> <a href="index.php?option=com_clm&view=config" title=""> <img src="components/com_clm/images/clm_einstellungen.png" align="middle" border="0" alt="" /> <span><?php echo JText::_( 'CONFIG_TITLE' );?></span></a> </div>
		</div>
	<?php
	}
	?>
	</div>
	</div>
	
	<?php $Dir = JPATH_ADMINISTRATOR .DIRECTORY_SEPARATOR. 'components'.DIRECTORY_SEPARATOR.'com_clm'; $data = JApplicationHelper::parseXMLInstallFile($Dir.DIRECTORY_SEPARATOR.'clm.xml');
	
	// aktuelle Versionsnummern auslesen !
	if (ini_get('allow_url_fopen') == 0) {
		$clm_version[1]="<font color='red'>".JText::_( 'INFO_HINT_NO_FOPEN' )."</font>";
		$clm_version[3]="<font color='red'>".JText::_( 'INFO_HINT_NO_FOPEN' )."</font>";
	} elseif (!$fp = fopen("http://www.chessleaguemanager.de/download/clm_version_J25.csv","r")) {
		$clm_version[1]="<font color='red'>".JText::_( 'INFO_HINT_NO_SERVER' )."</font>";
		$clm_version[3]="<font color='red'>".JText::_( 'INFO_HINT_NO_SERVER' )."</font>";
	} else {
		$clm_version = fgetcsv($fp,500,"|");
		$upgrade = "00FF00";
		$aktuell = explode("v", $clm_version[1]);
		$install = explode("v", $data['version']);
		if ( $install[0] < $aktuell[0] ) {$upgrade = "FF0000";}
		if ( $install[0] == $aktuell[0] AND $aktuell[1] > $install[1] ) { $upgrade = "FE9A2E";}
	}
	?>
	
	<div id="clm_infobox" class="width-30 fltrt">
		<fieldset class="adminform">
		<legend>CLM</legend>
	<img src="components/com_clm/images/clm_logo.png" alt="" />
	<br>
	<h3><?php echo JText::_( 'INFO_HINT_1' );?></h3>
	<table border="0">
	<tr>
		<td><?php echo JText::_( 'INFO_HINT_2' );?></td>
		<td>&nbsp;</td>
		<td><font color="#<?php echo $upgrade; ?>"><?php echo $data['version'];?></font></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'INFO_HINT_3' );?></td>
		<td>&nbsp;</td>
		<td><?php echo $clm_version[1];?></td>
	</tr>
	<tr>
		<td><?php echo JText::_( 'INFO_HINT_4' );?></td>
		<td>&nbsp;</td>
		<td><?php echo $clm_version[3];?></td>
	</tr>
	</table>
	
	<br>
	<a href="http://www.chessleaguemanager.de" target="blank"><?php echo JText::_( 'INFO_HINT_5' );?></a>  |  <a href="http://www.chessleaguemanager.de/index.php?option=com_kunena" target="blank"><?php echo JText::_( 'INFO_HINT_6' );?></a>
	<br>
	<br>
	<u><?php echo JText::_( 'INFO_HINT_7' );?></u>
	<ul type="square">
		<?php
		for ($h=8; $h<=14; $h++) {
			echo '<li>'.JText::_( 'INFO_HINT_'.$h ).'</li>';
		}
		?>
	<br>
	</div>
	</fieldset><?php
	}
	}

}
?>
