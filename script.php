<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
//the name of the class must be the name of your component + InstallerScript
//for example: com_contentInstallerScript for com_content.
class com_clmInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {
		
		// Erlaube die Installation nur auf Joomla 2.5
      if (version_compare( JVERSION, '3.0', '>=' ) == 1 || version_compare( JVERSION, '1.7', '<=' ) == 1) {             
         Jerror::raiseWarning(null, 'Dieses Paket ist nur für Joomla 2.5 geeignet.');
      	return false;
      }
		
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// Show the essential information at the install/update back-end
	    echo "<h3>Start CLM-Installation</h3>";
		echo 'Art der Installation: ' . $type;
		DEFINE ('NEW_CLM_VERSION', $this->release);
		DEFINE ('OLD_CLM_VERSION', $this->getParam('version'));
		echo '<br>Zu installierende CLM-Version = ' . NEW_CLM_VERSION;
		echo '<br>Vorhandene CLM-Version = ' . OLD_CLM_VERSION;
		echo '<br>Mindestens erwartetet Joomla Version = ' . $this->minimum_joomla_release;
		echo '<br>Installierte Joomla Version = ' . $jversion->getShortVersion();
		if ($type == 'update' AND NEW_CLM_VERSION > '1.3.1') {
			//echo "<br>neue Version > 1.3.1";
			if (OLD_CLM_VERSION < '1.2.0' AND OLD_CLM_VERSION > '') {
                Jerror::raiseWarning(null, 'Update von '.OLD_CLM_VERSION.' nach '.NEW_CLM_VERSION.' nicht möglich. Bitte zuerst auf 1.2.0 updaten.');
                return false;
            }
		}
		//echo 'Art der Installation: ' . $type; //die('  script');
	
		//echo '<p>' . JText::_('COM_CLM_PREFLIGHT_' . $type . ' ' . $rel) . '</p>';
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {
		//echo '<p>' . JText::_('COM_CLM_INSTALL to ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly installed component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		//echo '<p>' . JText::_('COM_CLM_UPDATE_ to ' . $this->release) . '</p>';
		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_democompupdate');
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
 
		////////////////////////////
		// Parameter zurückschreiben 
		$db	=& JFactory::getDBO();
		// Backup Paramter holen
		$sql = " SELECT params FROM #__clm_params ";
		$db->setQuery( $sql);
		$param_clm = $db->loadObjectList();
		if (isset($param_clm) AND count($param_clm) == 1) {																							
		// Parameter schreiben
			$sql = " UPDATE #__extensions SET `params` = '".$param_clm[0]->params."'"
			." WHERE `element` = 'com_clm'";
			$db->setQuery( $sql);
			$db->query();
			echo '<br>'.'CLM Parameter von der letzten Deinstallation &uuml;bernommen';
		} else {
			echo '<br>'.'CLM Parameter unver&auml;ndert &uuml;bernommen';
		} 
		// Parameter löschen
		$sql = " TRUNCATE TABLE #__clm_params ";
		$db->setQuery( $sql);
		$db->query();
		// Ende Parameter
		/////////////////
 
		//echo '<p>' . JText::_('COM_CLM_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
	
		echo "<br /><h3>Installation erfolgreich beendet!</h3>";
	 
		echo "<br /><b><font color='red'>Achtung</font></b>: Es wurde die Version 1.5.4 oder h&ouml;her der CLM-Hauptkomponente installiert.";
		echo "<br />Wenn Sie auch den CLM-Turnier-Modul nutzen, ist mindestens Modulversion 1.0.3 erforderlich.";

		echo "<br /><b><font color='red'>Hinweis</font></b>: Es wurde die Hauptkomponente des ChessLeagueManagers installiert.";
		echo "<br />Auf unserer Projekt-Seite www.chessleaguemanager.de unter Schnellstart finden Sie erste Hinweise zum Setup.";
		echo "<br />Auch m&ouml;chten wir auf die n&ouml;tigen Module zur Darstellung im Frontend aufmerksam machen:";
		echo "<br />	- Darstellungsmodul mod_clm zur Darstellung von Ligen oder Mannschaftsturnieren";
		echo "<br />	  (falls beides: den Eintrag in der Modultabelle kopieren)";
		echo "<br />	- Login-Modul mod_clm_log, wenn die Ergebnisse durch die Mannschaftsleiter &uuml;ber das Frontend eingegeben werden.";
		echo "<br />	  (ein sehr h&auml;ufiger Ansatz)";
		echo "<br />	- Einzelturnier-Modul mod_clm_turmultiple zur Darstellung von Einzelturnieren";
		echo "<br />	- Termin-Modul mod_clm_termine zur Darstellung der Spiel- und Veranstaltungstermine im Kalender";
		echo "<br />	- Archiv-Modul mod_clm_archiv zur Darstellung der Ligen und Mannschaftsturniere der Vorjahre";
		echo "<br />	  (also erst ab zweiter Saison sinnvoll)";

	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
		//echo '<p>' . JText::_('COM_CLM_UNINSTALL ' . $this->release) . '</p>';
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE element = "com_clm"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_clm"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_clm"' );
				$db->query();
		}
	}
}
