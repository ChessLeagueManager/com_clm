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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class CLMControllerInfo extends JController
{
  function display()
  {
        require_once(JPATH_COMPONENT.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'info.php');
        CLMViewInfo::display( );
  }

	function cancel ()
  {
        $this->setRedirect( 'index.php?option='.$option );
  }

function first_user()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');

	$query = " SELECT COUNT(id) as count FROM #__clm_user ";
	$db->setQuery($query);
	$count_id	= $db->loadObjectList();
	$count		= $count_id[0]->count;

	if ($count > 0) {
		JError::raiseWarning( 500, JText::_( 'INFO_USER_EXIST') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link );
			}

	// User ID holen
	$user = & JFactory::getUser();
	$jid = $user->get('id');

	// Daten sammeln
	$query = " SELECT name,username,email "
		." FROM #__users "
		." WHERE id =".$jid;
	$db->setQuery($query);
	$data_jid	= $db->loadObjectList();

	$name		= $data_jid[0]->name;
	$username	= $data_jid[0]->username;
	$email		= $data_jid[0]->email;

	$query = " REPLACE #__clm_user ( `sid`, `jid`, `name`, `username`, `aktive`, `email` "
		.",`usertype`,`user_clm`, `published`,`ordering`, `block`) VALUES "
		." ( 1,'$jid','$name','$username',1,'$email','admin', 100,1,1,0)"
		;
	$db->setQuery($query);
	$db->query();

	// Verband Datensätze in DB schreiben
	$filesDir 	= JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.$option.DIRECTORY_SEPARATOR.'verband.sql';
	$open 		= fopen("$filesDir","r"); 
	$content 	= fread($open,filesize("$filesDir"));
	$teil 		= explode(";", $content);

	for ($x=0; $x<count($teil); $x++) {
			$db->setQuery(utf8_encode($teil[$x]));
			$db->query();
				}

	JError::raiseNotice( 6000,  JText::_( 'INFO_ACCOUNT' ));
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link );
	}


function admin_user()
	{
	$mainframe	= JFactory::getApplication();

	// Check for request forgeries
	JRequest::checkToken() or die( 'Invalid Token' );

	$db 		=& JFactory::getDBO();
	$option		= JRequest::getCmd('option');
	$section	= JRequest::getVar('section');
	$user 		= & JFactory::getUser();
	$jid		= $user->get('id');

	// CLM Userstatus auslesen
	$query = "SELECT a.usertype, a.user_clm FROM #__clm_user as a"
		." LEFT JOIN #__clm_saison as s ON s.id = a.sid"
		." WHERE a.jid = ".$jid
		." AND a.published = 1 "
		." AND s.published = 1 AND s.archiv = 0 ";
	$db	= & JFactory::getDBO();
	$db->setQuery($query);
	$userdata = $db->loadObjectList();

	if ($userdata[0]->usertype != '') {
		JError::raiseWarning( 500, JText::_( 'INFO_USER_EXIST') );
		$link = 'index.php?option='.$option.'&section='.$section;
		$mainframe->redirect( $link );
	}

	$query = "SELECT id FROM #__clm_saison "
		." WHERE published = 1 "
		." AND archiv = 0 ";
	$db	= & JFactory::getDBO();
	$db->setQuery($query);
	$sid_data = $db->loadObjectList();
	$sid_admin= $sid_data[0]->id;

	// Daten sammeln
	$query = " SELECT name,username,email "
		." FROM #__users "
		." WHERE id =".$jid;
	$db->setQuery($query);
	$data_jid	= $db->loadObjectList();

	$name		= $data_jid[0]->name;
	$username	= $data_jid[0]->username;
	$email		= $data_jid[0]->email;

	$query = " REPLACE #__clm_user ( `sid`, `jid`, `name`, `username`, `aktive`, `email` "
		.",`usertype`,`user_clm`, `published`,`ordering`, `block`) VALUES "
		." ( '$sid_admin','$jid','$name','$username',1,'$email','admin', 100,1,1,0)"
		;
	$db->setQuery($query);
	$db->query();

	JError::raiseNotice( 6000,  JText::_( 'INFO_ACCOUNT' ));
	$link = 'index.php?option='.$option.'&section='.$section;
	$mainframe->redirect( $link );
	}
}

