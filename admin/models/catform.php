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

class CLMModelCatForm extends JModelLegacy
{
    // benötigt für Pagination
    public function __construct()
    {
        parent::__construct();

        // user
        $this->user = JFactory::getUser();

        $this->_getData();

        $this->_getForms();

    }


    public function _getData()
    {

        // Instanz der Tabelle
        $this->category = JTable::getInstance('categories', 'TableCLM');
        if ($id = clm_core::$load->request_int('id')) {
            $this->category->load($id);
        }

    }


    // alle vorhandenen Filter
    public function _getForms()
    {

        //CLM parameter auslesen
        $clm_config = clm_core::$db->config();
        if ($clm_config->field_search == 1) {
            $field_search = "js-example-basic-single";
        } else {
            $field_search = "inputbox";
        }

        // get Tree
        list($this->parentArray, $this->parentKeys) = CLMCategoryTree::getTree();

        // parent
        $parentlist[]	= JHTML::_('select.option', '0', CLMText::selectOpener(JText::_('NO_PARENT')), 'id', 'name');
        foreach ($this->parentArray as $key => $value) {
            // Einträge ausscheiden, die die Kategorie selbst sind, ODER der Kategorie untergeordent sind!
            if ($key != $this->category->id) {
                if ((!isset($this->parentKeys[$key]) or (isset($this->parentKeys[$key]) and !in_array($this->category->id, $this->parentKeys[$key])))) {
                    $parentlist[]	= JHTML::_('select.option', $key, $value, 'id', 'name');
                }
            }
        }
        if (!isset($this->form) or is_null($this->form)) {
            $this->form = array();
        }
        //		$this->form['parent'] = JHTML::_('select.genericlist', $parentlist, 'parentid', 'class="js-example-basic-single" size="1"', 'id', 'name', intval($this->category->parentid));
        $this->form['parent'] = JHTML::_('select.genericlist', $parentlist, 'parentid', 'class="'.$field_search.'" size="1"', 'id', 'name', intval($this->category->parentid));



        // director/tl
        // $this->form['tl']	= CLMForm::selectDirector('tl', $this->category->tl);

        // published
        $this->form['published']	= CLMForm::radioPublished('published', $this->category->published);

    }

}
