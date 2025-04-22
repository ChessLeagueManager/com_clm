<?php

/**
 * @ Chess League Manager (CLM) Component
 * @Copyright (C) 2008-2021 CLM Team.  All rights reserved
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link http://www.chessleaguemanager.de
 * @author Thomas Schwietert
 * @email fishpoke@fishpoke.de
 * @author Andreas Dorn
 * @email webmaster@sbbl.org
*/

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.html.parameter');

class CLMModelTurnier_Player extends JModelLegacy
{
    public function __construct()
    {

        parent::__construct();

        $this->turnierid = clm_core::$load->request_int('turnier');
        $this->snr = clm_core::$load->request_int('snr', 1);

        $this->_getTurnierData();

        $this->_getPlayerData();

        if ($this->turnier->rnd == 1) { // bereits ausgelost?
            $this->_getPlayerMatches();
            $this->_getTurnierPoints();
        }

        $this->_getPlayerPhoto();

    }



    public function _getTurnierData()
    {

        $query = "SELECT id, sid, name, typ, runden, rnd, published, params, catidAlltime, catidEdition"
            ." FROM #__clm_turniere"
            ." WHERE id = ".$this->turnierid
        ;
        $this->_db->setQuery($query);
        $this->turnier = $this->_db->loadObject();

        // TO-DO: auslagern
        // zudem PGN-Parameter auswerten
        $turParams = new clm_class_params($this->turnier->params);
        $pgnInput = $turParams->get('pgnInput', 1);
        $pgnPublic = $turParams->get('pgnPublic', 1);

        // User ermitteln
        $user = JFactory::getUser();

        // Flag für View und Template setzen: pgnShow
        // FALSE - PGN nicht verlinken/anzeigen
        // TRUE - PGN-Links setzen und anzeigen
        // 'pgnInput möglich' UND ('pgn öffentlich' ORDER 'User eingeloggt')
        if ($pgnInput == 1 and ($pgnPublic == 1 or $user->id > 0)) {
            $this->pgnShow = true;
        } else {
            $this->pgnShow = false;
        }

        // turniernamen anpassen?
        $addCatToName = $turParams->get('addCatToName', 0);
        if ($addCatToName != 0 and ($this->turnier->catidAlltime > 0 or $this->turnier->catidEdition > 0)) {
            $this->turnier->name = CLMText::addCatToName($addCatToName, $this->turnier->name, $this->turnier->catidAlltime, $this->turnier->catidEdition);
        }

    }

    public function _getPlayerData()
    {

        $query = "SELECT *"
            ." FROM #__clm_turniere_tlnr"
            ." WHERE turnier = ".$this->turnierid." AND snr = ".$this->snr
        ;
        $this->_db->setQuery($query);
        $this->player = $this->_db->loadObject();
        if (isset($this->player)) {
            if ($this->player->FIDEcco == null or $this->player->FIDEid == null) {
                $query = "SELECT *"
                    ." FROM #__clm_dwz_spieler"
                    ." WHERE sid = ".$this->player->sid
                    ." AND ZPS = '".$this->player->zps."' AND Mgl_nr = ".$this->player->mgl_nr
                ;
                $this->_db->setQuery($query);
                $this->spieler = $this->_db->loadObject();
                if (isset($this->spieler)) {
                    $this->player->FIDEcco = $this->spieler->FIDE_Land;
                    $this->player->FIDEid = $this->spieler->FIDE_ID;
                }
            }
        }

    }

    public function _getPlayerMatches()
    {

        $query = "SELECT s.*, r.name as roundName, pg.text"
            ." FROM #__clm_turniere_rnd_spl AS s"
            ." LEFT JOIN #__clm_turniere_rnd_termine AS r ON s.dg = r.dg AND s.runde = r.nr"
            ." LEFT JOIN #__clm_pgn as pg ON s.pgn = pg.id "
            ." WHERE s.turnier = ".$this->turnierid." AND s.turnier = r.turnier AND spieler = '".$this->snr."'"
            ." ORDER BY r.dg ASC, r.nr ASC"
        ;
        $this->_db->setQuery($query);
        $this->matches = $this->_db->loadObjectList();

        // Daten zu den Gegnern holen
        // zudem Stats ermitteln
        // INIT
        $this->player->countMatchesPlayed = 0;
        $this->player->sumTWZ = 0;
        $this->player->countTWZplayers = 0;
        $this->player->countTWZplayersNone = 0;
        // alle Matches durchgehen
        foreach ($this->matches as $key => $value) {
            if ($value->gegner > 0) {
                $query = "SELECT name, twz, verein, zps"
                    ." FROM #__clm_turniere_tlnr"
                    ." WHERE turnier = ".$this->turnierid." AND snr = ".$value->gegner
                ;
                $this->_db->setQuery($query);
                list($this->matches[$key]->oppName, $this->matches[$key]->oppTWZ, $this->matches[$key]->oppVerein, $this->matches[$key]->oppZPS) = $this->_db->loadRow();
                if ($value->ergebnis != '') {
                    $this->player->countMatchesPlayed++;
                }
                // für TWZ-Stats
                if ($this->matches[$key]->oppTWZ > 0) {
                    $this->player->sumTWZ += $this->matches[$key]->oppTWZ;
                    $this->player->countTWZplayers++;
                } else {
                    $this->player->countTWZplayersNone++;
                }

            }
        }

    }

    public function _getTurnierPoints()
    {

        $this->points = array();
        // Übernehmen der Sonderpunkte als Startpunkt
        $query = "SELECT snr, s_punkte "
            ." FROM #__clm_turniere_tlnr"
            ." WHERE turnier = ".$this->turnierid
        ;
        $this->_db->setQuery($query);
        $this->s_points = $this->_db->loadObjectList();
        if (isset($this->s_points)) {
            foreach ($this->s_points as $pvalue) {
                $this->points[$pvalue->snr] = $pvalue->s_punkte;
            }
        }

        // Matchpunkte hinzufügen, alle ermittelten Runden durchgehen
        $query = "SELECT spieler, ergebnis "
                ." FROM #__clm_turniere_rnd_spl"
                ." WHERE turnier = ".$this->turnierid
//				." AND ( dg < ".$this->dg." OR ( dg = ".$this->dg." AND runde < ".$this->runde." ) )"
                ." ORDER BY dg ASC, runde ASC, brett ASC"
        ;
        $this->_db->setQuery($query);
        $this->round_points = $this->_db->loadObjectList();
        foreach ($this->round_points as $pvalue) {
            if ($pvalue->ergebnis == 1 or $pvalue->ergebnis == 5 or $pvalue->ergebnis == 11) {
                $point = 1;
            } elseif ($pvalue->ergebnis == 2 or $pvalue->ergebnis == 10 or $pvalue->ergebnis == 12) {
                $point = .5;
            } else {
                $point = 0;
            }
            if (isset($this->points[$pvalue->spieler])) {
                $this->points[$pvalue->spieler] += $point;
            } else {
                $this->points[$pvalue->spieler] = $point;
            }
        }

    }

    public function _getPlayerPhoto()
    {

        // Test, ob joomgallery installiert ist
        $query = "SHOW TABLES LIKE '%joomgallery'";
        $result = clm_core::$db->query($query);
        // JoomGallery-Parameter auswerten
        $turParams = new clm_class_params($this->turnier->params);
        $this->joomGalleryPhotosWidth = $turParams->get('joomGalleryPhotosWidth', '');
        if (!is_numeric($this->joomGalleryPhotosWidth)) {
            $this->joomGalleryPhotosWidth = 0;
        }
        $joomGalleryDisplayPlayerPhotos = $turParams->get('joomGalleryDisplayPlayerPhotos', 0);
        $joomGalleryCatId = $turParams->get('joomGalleryCatId', '');
        if ($joomGalleryDisplayPlayerPhotos == 1 and $joomGalleryCatId != '' and $result->num_rows == 1) {
            $query = "SELECT id"
                ." FROM #__joomgallery"
                ." WHERE catid = ".$joomGalleryCatId." AND ordering = ".$this->snr
            ;
            $this->_db->setQuery($query);
            $playerPhotoId = $this->_db->loadObject();
            if (isset($playerPhotoId->id)) {
                $this->playerPhoto = $playerPhotoId->id;
            } else {
                $this->playerPhoto = '';
            }
        } else {
            $this->playerPhoto = '';
        }

    }

}
