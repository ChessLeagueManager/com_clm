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
// no direct access
defined('_JEXEC') or die('Restricted access');

class TableCLMRangspieler extends JTable
{
    public $Gruppe		= '';
    public $ZPS		= '';
    public $ZPSmgl		= '00000';
    public $Mgl_Nr		= '';
    public $PKZ		= '';
    public $Rang		= '';
    public $man_nr		= '';
    public $sid		= '';

    public function __construct(&$_db)
    {
        parent::__construct('#__clm_rangliste_spieler', 'id', $_db);
    }

    /**
     * Overloaded check function
     *
     * @access public
     * @return boolean
     * @see JTable::check
     * @since 1.5
     */
    public function check()
    {
        // check for valid client name
        /*	if (trim($this->Gruppe == '')) {
                $this->setError(JText::_( 'BNR_CLIENT_NAME' ));
                return false;
            }
*/
        // check for valid client contact
        /**		if (trim($this->sid == '')) {
                    $this->setError(JText::_( 'Saison muss angegeben werden !' ));
                    return false;
                }

        **/

        return true;
    }
}
