<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

class CLMController extends JControllerLegacy
{
	function display($cachable = false, $urlparams = false)
	{
		// Setzt einen Standard view 
		if ( clm_core::$load->request_string('view') == '' ) {
			$_GET["view"] = 'categories';
		}

		parent::display();
	}

	function activate()
	{
		$mainframe	= JFactory::getApplication();

		// Initialize some variables
		$db			=JFactory::getDBO();
		$user 		=JFactory::getUser();
		$document   =JFactory::getDocument();
		$pathway 	=& $mainframe->getPathWay();

		$usersConfig = JComponentHelper::getParams( 'com_users' );
		$userActivation			= $usersConfig->get('useractivation');
		$allowUserRegistration	= $usersConfig->get('allowUserRegistration');

		// Check to see if they're logged in, because they don't need activating!
		if ($user->get('id')) {
			// They're already logged in, so redirect them to the home page
			$mainframe->redirect( 'index.php' );
		}

		if ($allowUserRegistration == '0' || $userActivation == '0') {
			$this->setRedirect('index.php?option=com_user&view=reset', JText::_( 'Access Forbidden' ));
			return;
		}

		// create the view
		require_once (JPATH_COMPONENT.DS.'views'.DS.'register'.DS.'view.html.php');
		$view = new UserViewRegister();

		$message = new stdClass();

		// Do we even have an activation string?
		$activation = 	clm_core::$load->request_string('activation', '');
		$activation = $db->getEscaped( $activation );

		if (empty( $activation ))
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_NOT_FOUND' );
			$view->assign('message', $message);
			$view->display('message');
			return;
		}

		// Lets activate this user
		jimport('joomla.user.helper');
		if (JUserHelper::activateUser($activation))
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_COMPLETE_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_COMPLETE' );
		}
		else
		{
			// Page Title
			$document->setTitle( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ) );
			// Breadcrumb
			$pathway->addItem( JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' ));

			$message->title = JText::_( 'REG_ACTIVATE_NOT_FOUND_TITLE' );
			$message->text = JText::_( 'REG_ACTIVATE_NOT_FOUND' );
		}

		$view->assign('message', $message);
		$view->display('message');
	}

	/**
	 * Password Reset Request Method
	 *
	 * @access	public
	 */
	function requestreset()
	{
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// Get the input
		$email		= clm_core::$load->request_string('email');

		// Get the model
		$model = &$this->getModel('Reset');

		// Request a reset
		if ($model->requestReset($email) === false)
		{
			$message = JText::sprintf('PASSWORD_RESET_REQUEST_FAILED', $model->getError());
			$this->setRedirect('index.php?option=com_user&view=reset', $message);
			return false;
		}

		$this->setRedirect('index.php?option=com_user&view=reset&layout=confirm');
	}

	/**
	 * Password Reset Confirmation Method
	 *
	 * @access	public
	 */
	function confirmreset()
	{
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// Get the input
		//$token = JRequest::getVar('token', null, 'post', 'alnum');
		$token = clm_core::$load->request_string('token', '');

		// Get the model
		$model = &$this->getModel('Reset');

		// Verify the token
		if ($model->confirmReset($token) === false)
		{
		//	$message = JText::sprintf('Der Link scheint fehlerhaft zu sein. Wenden Sie sich umgehend an einen Administrator', $model->getError());
			$this->setRedirect('index.php?option=com_clm&view=reset&layout=error', $message);
			return false;
		}

		$this->setRedirect('index.php?option=com_clm&view=reset&layout=complete');
	}

	/**
	 * Password Reset Completion Method
	 *
	 * @access	public
	 */
	function completereset()
	{
		// Check for request forgeries
		defined('_JEXEC') or die( 'Invalid Token' );

		// Get the input
		$password1 = clm_core::$load->request_string('password1', '');
		$password2 = clm_core::$load->request_string('password2', '');

		// Get the model
		$model = $this->getModel('Reset');

		// Reset the password
		if ($model->completeReset($password1, $password2) === false)
		{
			$message = JText::_('PASSWORD_RESET_FAILED')." ". $model->getError();
			$this->setRedirect('index.php?option=com_clm&view=reset&layout=complete', $message);
			return false;
		}

		$message = JText::_('Ihr Passwort wurde gespeichert !');
		$this->setRedirect(JRoute::_('index.php?option=com_users&view=login', $message));
	}
}
