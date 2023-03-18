<?php
/**
 * @ Chess League Manager (CLM) Component 
 * @Copyright (C) 2008-2023 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
*/
/**
 * @version		$Id: reset.php 7399 2007-05-14 04:10:09Z eddieajau $
 * @package		Joomla
 * @subpackage	User
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * User Component Reset Model
 *
 * @author		Rob Schley <rob.schley@joomla.org>
 * @package		Joomla
 * @subpackage	User
 * @since		1.5
 */
class CLMModelReset extends JModelLegacy
{
	/**
	 * Registry namespace prefix
	 *
	 * @var	string
	 */
	var $_namespace	= 'com_user.reset.';


	/**
	 * Checks a user supplied token for validity
	 * If the token is valid, it pushes the token
	 * and user id into the session for security checks.
	 *
	 * @since	1.5
	 * @param	token	An md5 hashed randomly generated string
	 * @return	bool	True on success/false on failure
	 */
	function confirmReset($token)
	{
	$mainframe	= JFactory::getApplication();
//	$option 	= JRequest::getCmd( 'option' );
	$option 	= clm_core::$load->request_string('option', '');

		$db	= JFactory::getDBO();
		$db->setQuery('SELECT id FROM #__users WHERE block = 0 AND activation = '.$db->Quote($token));

		// Verify the token
		if (!($id = $db->loadResult()))
		{
			$this->setError(JText::_('INVALID_TOKEN'));
			return false;
		}

		// Push the token and user id into the session
		$mainframe->setUserState($this->_namespace.'token',	$token);
		$mainframe->setUserState($this->_namespace.'id',	$id);

		return true;
	}

	/**
	 * Takes the new password and saves it to the database.
	 * It will only save the password if the user has the
	 * correct user id and token stored in her session.
	 *
	 * @since	1.5
	 * @param	string	New Password
	 * @param	string	New Password
	 * @return	bool	True on success/false on failure
	 */
	function completeReset($password1, $password2)
	{
		jimport('joomla.user.helper');

		$mainframe	= JFactory::getApplication();
		//$option 	= JRequest::getCmd( 'option' );
		$option 	= clm_core::$load->request_string('option', '');

		// Make sure that we have a pasword
		if ( ! $password1 )
		{
			$this->setError(JText::_('PASSWORD_MUST_GIVEN'));
			return false;
		}

		// Verify that the passwords match
		if ($password1 != $password2)
		{
			$this->setError(JText::_('PASSWORDS_DO_NOT_MATCH'));
			return false;
		}

		// Verify the password against the rules
		$pattern = '/(?=^.{8,}$)(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[+,.;:\-_!%&\/]).*$/';
		if (!preg_match($pattern, $password1) == 1) 
		{
			$this->setError(JText::_('PASSWORD_DO_NOT_MATCH_RULES'));
			return false;
		}

		// Get the necessary variables
		$db			= JFactory::getDBO();
		$id			= $mainframe->getUserState($this->_namespace.'id');
		$token		= $mainframe->getUserState($this->_namespace.'token');
//		$salt		= JUserHelper::genRandomPassword(32);
//		$crypt		= JUserHelper::getCryptedPassword($password1, $salt);
//		$password	= $crypt.':'.$salt;

		$password = JUserHelper::hashPassword($password1);
 
		// Get the user object
		$user = new JUser($id);

		// Fire the onBeforeStoreUser trigger
		JPluginHelper::importPlugin('user');
//		$dispatcher =& JDispatcher::getInstance();
//		$dispatcher->trigger('onBeforeStoreUser', array($user->getProperties(), false));

		// Build the query
		$query 	= 'UPDATE #__users'
				. ' SET password = '.$db->Quote($password)
				. ' , activation = ""'
				. ' WHERE id = '.(int) $id
				. ' AND activation = '.$db->Quote($token)
				. ' AND block = 0';

		$db->setQuery($query);

		// Save the password
//		if (!$result = $db->query())
		if (!clm_core::$db->query($query))
		{
			$this->setError(JText::_('DATABASE_ERROR'));
			return false;
		}

		// Update the user object with the new values.
		$user->password			= $password;
		$user->activation		= '';
		$user->password_clear	= $password1;

		// Fire the onAfterStoreUser trigger
//		$dispatcher->trigger('onAfterStoreUser', array($user->getProperties(), false, $result, $this->getError()));

		// Flush the variables from the session
		$mainframe->setUserState($this->_namespace.'id',	null);
		$mainframe->setUserState($this->_namespace.'token',	null);

		return true;
	}

}