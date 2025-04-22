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

/**
 * Category
*/

class CLMCategory extends stdClass
{
    public function __construct($catid, $getData = false)
    {
        // $catid übergibt id der category
        // $getData, ob die Turneirdaten aus clm_categories sofort ausgelesen werden sollen

        // DB
        $this->_db				= JFactory::getDBO();

        // catid
        $this->catid = $catid;

        // get data?
        if ($getData) {
            $this->_getData();
        }

    }


    public function _getData()
    {

        $this->data = JTable::getInstance('categories', 'TableCLM');
        $this->data->load($this->catid);

    }


    /**
    * check, ob User Zugriff hat
    * drei Zugangsmöichgkeiten - aller per Default auf TRUE
    */
    public function checkAccess($usertype_admin = true, $usertype_tl = true, $id_tl = true)
    {

        // admin?
        if ($usertype_admin and clm_core::$access->getType() == 'admin') {
            return true;
        }
        // tl?
        if ($usertype_tl and clm_core::$access->getType() == 'tl') {
            return true;
        }
        // category->tl
        if ($id_tl and clm_core::$access->getJid() == $this->data->tl) {
            return true;
        }
        // nichts hat zugetroffen
        return false;

    }

    public function checkDelete()
    {

        $query = "SELECT COUNT(*) FROM #__clm_categories WHERE parentid = '".$this->catid."'";
        $this->_db->setQuery($query);
        if ($this->_db->loadResult() > 0) {
            return false;
        }

        return true;

    }



}
