<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2024 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/
defined('_JEXEC') or die('Restricted access');

class CLMModelPGNImport extends JModelLegacy
{
    public $_pgnFiles;

    public function __construct()
    {
        parent::__construct();
        $filter_saison	= clm_core::$load->request_int('filter_saison', $this->_getAktuelleSaison());

        $this->setState('filter_saison', $filter_saison);
    }

    public function getSaisons()
    {
        if (empty($this->_saisons)) {
            $query =  ' SELECT id, name FROM #__clm_saison'
                    . ' WHERE published = 1 ';
            $this->_saisons = $this->_getList($query);
        }
        return $this->_saisons;
    }

    public function getLigen()
    {
        if (empty($this->_ligen)) {
            $query = ' SELECT  id, name FROM #__clm_liga '
                    .'WHERE sid = '.$this->getState('filter_saison').'';
            $this->_ligen = $this->_getList($query);
        }
        return $this->_ligen;
    }

    public function getTurniere()
    {
        if (empty($this->_turniere)) {
            $query = ' SELECT  id, name FROM #__clm_turniere '
                    .'	WHERE sid = '.$this->getState('filter_saison').'';
            $this->_turniere = $this->_getList($query);
        }
        return $this->_turniere;
    }

    public function _getAktuelleSaison()
    {
        if (empty($this->_aktuelleSaison)) {

            $query =  ' SELECT `id`'
                    . ' FROM #__clm_saison'
                    . ' WHERE published = 1 AND archiv = 0';
            $var = $this->_getList($query);

        }

        if (isset($var[0]->id)) {
            return $var[0]->id;
        }

        return 0;
    }

    public function getPgnFiles()
    {
        jimport('joomla.filesystem.folder');

        $filesDir = 'components'.DS."com_clm".DS.'swt';
        $this->pgnFiles = JFolder::files($filesDir, '.PGN$|.pgn$', false, true);

        return $this->pgnFiles;
    }

    public function pgn_upload()
    {
        jimport('joomla.filesystem.file');

        //Datei wird hochgeladen
        $file = clm_core::$load->request_file('pgn_datei', null);

        //Dateiname wird bereinigt
        $pgn_file = JFile::makeSafe($file['name']);
        $_POST['pgn_file'] = $pgn_file;

        //Temporärer Name und Ziel werden festgesetzt
        $src = $file['tmp_name'];
        $dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $pgn_file;

        //Datei wird auf dem Server gespeichert (abfrage auf .pgn Endung)
        if (strtolower(JFile::getExt($pgn_file)) == 'pgn') {
            if (JFile::upload($src, $dest)) {
                $msg = JText::_('SWT_UPLOAD_SUCCESS');
            } else {
                $msg = JText::_('SWT_UPLOAD_ERROR');
            }
        } else {
            $msg = JText::_('SWT_UPLOAD_ERROR_WRONG_EXT');
        }
        return $msg;
    }

    public function pgn_delete()
    {
        jimport('joomla.filesystem.file');

        //Name der zu löschenden Datei wird geladen
        $pgn_file = clm_core::$load->request_string('pgn_file', '');
        if ($pgn_file == '') {
            $msg = JText::_('SWT_FILE_ERROR');
            return $msg;
        }
        //SWT-Verzeichnis
        $path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;

        //Datei löschen
        if (JFile::delete($path.$pgn_file)) {
            $msg = JText::_('SWT_DELETE_SUCCESS');
        } else {
            $msg = JText::_('SWT_DELETE_ERROR');
        }
        return $msg;
    }

    public function pgn_import()
    {
        //Name der zu Datei wird geladen
        $pgn_file = clm_core::$load->request_string('pgn_file', '');

        //SWT-Verzeichnis
        $path = JPATH_COMPONENT . DS . "swt" . DS;

        if ($pgn_file != "" && file_exists($path.$pgn_file)) {
            return CLMSWT::readInt($path.$pgn_file, 606, 1);
        } else {
            return -1;
        }
    }

}
