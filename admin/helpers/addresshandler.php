<?php

class AddressHandler
{
    private $config;
    private $geo_enabled;
    public function __construct()
    {
        $this->config = clm_core::$db->config();
        $this->geo_enabled = $this->config->googlemaps;
    }

    public function queryLocation($result,$club)
    {
        if($this->geo_enabled && !empty($result[0]->lokal)){
            $coord = $this->extractCoordinatesFromText($result[0]->lokal_coord_text);
            if(is_null($coord[0])||is_null($coord[1]))
            {
                $lokal_coord = $this->convertAddress($result[0]->lokal);
                if(is_null($lokal_coord)||$lokal_coord==-1){
                    $this->updateCoordinates("POINT(0 0)",$result[0]->id,$club);
                }
    
                    else{
                    $this->updateCoordinates($lokal_coord,$result[0]->id,$club);
                }
                $coord = $this->extractCoordinatesFromText($lokal_coord);
            }
            if($coord[0]==0 && $coord[1]==0){
                $result[0]->lokal_coord_lat = null;
                $result[0]->lokal_coord_long = null;
            }
            else{
                $result[0]->lokal_coord_lat = $coord[0];
                $result[0]->lokal_coord_long = $coord[1];
            }
        }
        else{
            $result[0]->lokal_coord_lat = null;
            $result[0]->lokal_coord_long = null;
        }
    }

    private function extractCoordinatesFromText($coord_text){
        if (!is_null($coord_text)) {
            preg_match('/POINT\(([-\d\.]+) ([-\d\.]+)\)/', $coord_text, $matches);
            if ($matches) {
                $lat = $matches[1];
                $long = $matches[2];
            } else {
			$lat = null;
				$long = null;
            }
        } else {
			$lat = null;
			$long = null;
        }
		return array($lat, $long);
    }

    public function convertAddress($address)
    {
        // First check if user enabled adress conversion
        if(!$this->geo_enabled){
            return -1; //Map Services (external) not enabled
        }

        // Prepare adress for geo lookup
        $address = $this->prepareAddress($address);

        // Lookup based on activated service
        $coordinates = $this->getCoordinates($address);

        if(is_null($coordinates)){
            return NULL;
        }
        else{
            // Return as Text 
            $point = "POINT($coordinates[0] $coordinates[1])";

            return $point;
        }
    }

    private function prepareAddress($address)
    {
        $googlemaps_rtype = $this->config->googlemaps_rtype;
        $addressArray = explode(',', $address);

        if (isset($addressArray[2]) && $googlemaps_rtype == 1) {
            $address = implode(",", array($addressArray[0], $addressArray[1], $addressArray[2]));
        } elseif (isset($addressArray[2]) && $googlemaps_rtype == 2) {
            $address = implode(",", array($addressArray[1], $addressArray[2]));
        } elseif (isset($addressArray[1]) && $googlemaps_rtype == 3) {
            $address = implode(",", array($addressArray[0], $addressArray[1]));
        } else {
            $address = implode(",", $addressArray);
        }

        // Encode the address
        $address = urlencode($address);
        return ($address);
    }

    private function getCoordinates($address)
    {
        $service = $this->config->maps_resolver;

        if ($service == 1) { // Google Maps
            $gAPIKey = $this->config->googlemaps_api;
            if (!is_null($gAPIKey)) {
                return $this->getCoordinatesFromGoogle($address, $gAPIKey);
            }
        }

        return $this->getCoordinatesFromOSM($address);
    }

    private function getCoordinatesFromOSM($address)
    {
        $options = [
            'http' => [
                'method' => 'GET',
                'header' => 'User-Agent: ' . $_SERVER['HTTP_HOST']
            ]
        ];

        $userAgent = stream_context_create($options);
        $url = "https://nominatim.openstreetmap.org/?format=json&addressdetails=1&q={$address}&format=json&limit=1";
        $resp_json = file_get_contents($url, false, $userAgent);
        $resp = json_decode($resp_json, true);

        if (is_null($resp)) {
            return NULL;
        } elseif (isset($resp[0]['lat']) && isset($resp[0]['lon'])) {
            return array($resp[0]['lat'], $resp[0]['lon']);
        } else {
            return NULL;
        }
    }

    private function getCoordinatesFromGoogle($address, $gAPIKey)
    {
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key={$gAPIKey}";
        $resp_json = file_get_contents($url, false);
        $resp = json_decode($resp_json, true);

        if ($resp['status'] == 'OK' && isset($resp['results'][0]['geometry']['location']['lat']) && isset($resp['results'][0]['geometry']['location']['lng'])) {
            return array($resp['results'][0]['geometry']['location']['lat'], $resp['results'][0]['geometry']['location']['lng']);
        } else {
            return NULL;
        }
    }

    public function updateClubCoordinates($coord, $rowId)
    {
        $club=1;
        $this->updateCoordinates($coord, $rowId, $club);
    }

    public function updateTeamCoordinates($coord, $rowId)
    {
        $club=0;
        $this->updateCoordinates($coord, $rowId, $club);
    }

    private function updateCoordinates($coord, $rowId, $club)
    {
        $db 	=JFactory::getDBO();

        if($club==1){
            $table = '#__clm_vereine';
        }
        else
        {
            $table = '#__clm_mannschaften';
        }
        if(is_null($coord) or $coord==-1){
			$query = "UPDATE $table "
			. " SET lokal_coord = NULL"
			. " WHERE id = $rowId";
			clm_core::$db->query($query);
		}
		else{
			//Store in db
			$query = "UPDATE $table"
				. " SET lokal_coord = ST_GeomFromText('$coord')"
				. " WHERE id = $rowId";
			clm_core::$db->query($query);
		}  
    }


}
?>
