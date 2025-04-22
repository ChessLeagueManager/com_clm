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
defined('_JEXEC') or die('Restricted access');

class CLMModelSWT extends JModelLegacy
{
    public $_swtFiles;

    public function __construct()
    {
        parent::__construct();
    }

    public function getSwtFiles()
    {
        jimport('joomla.filesystem.folder');

        $filesDir = 'components'.DS."com_clm".DS.'swt';
        $this->swtFiles = JFolder::files($filesDir, '.SWT$|.swt$', false, true);

        return $this->swtFiles;
    }

    public function upload()
    {
        jimport('joomla.filesystem.file');

        //Datei wird hochgeladen
        $file = clm_core::$load->request_file('swt_datei', null);

        //Dateiname wird bereinigt
        $filename = JFile::makeSafe($file['name']);
        $_POST['filename'] = $filename;
        //Temporärer Name und Ziel werden festgesetzt
        $src = $file['tmp_name'];
        $dest = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR . $filename;

        //Datei wird auf dem Server gespeichert (abfrage auf .swt Endung)
        if (strtolower(JFile::getExt($filename)) == 'swt') {
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

    public function delete()
    {
        jimport('joomla.filesystem.file');

        //Name der zu löschenden Datei wird geladen
        $filename = clm_core::$load->request_string('swt_file', '');
        if ($filename == '') {
            $msg = JText::_('SWT_FILE_ERROR');
            return $msg;
        }
        //SWT-Verzeichnis
        $path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . "swt" . DIRECTORY_SEPARATOR;

        //Datei l�schen
        if (JFile::delete($path.$filename)) {
            $msg = JText::_('SWT_DELETE_SUCCESS');
        } else {
            $msg = JText::_('SWT_DELETE_ERROR');
        }
        return $msg;
    }

    public function import()
    {
        //Name der zu löschenden Datei wird geladen
        $filename = clm_core::$load->request_string('swt_file', '');

        //SWT-Verzeichnis
        $path = JPATH_COMPONENT . DS . "swt" . DS;

        if ($filename != "" && file_exists($path.$filename)) {
            return CLMSWT::readInt($path.$filename, 606, 1);
        } else {
            return -1;
        }
    }


}
