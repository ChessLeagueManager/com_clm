<?php

class AddressHandler
{
    private $config;
    private $geo_enabled;

    /**
     * AddressHandler constructor.
     * 
     * Initializes the AddressHandler object.
     */
    public function __construct()
    {
        $this->config = clm_core::$db->config(); // Get CLM config
        $this->geo_enabled = $this->config->googlemaps; // Check if map services are enabled
    }

    /**
     * Queries the location and updates the coordinates based on the result.
     *
     * @param array $result The result of the location query.
     * @param string $club The club name.
     * @return void
     */
    public function queryLocation($result, $club)
    {
        if ($this->geo_enabled && !empty($result[0]->lokal)) { // Check if map services are enabled and the location is not empty
            $coord = $this->extractCoordinatesFromText($result[0]->lokal_coord); //Extract coordinates from text

            if (is_null($coord[0]) || is_null($coord[1])) { // Check if coordinates are null -> Coordinates not stored in db
                $lokal_coord = $this->convertAddress($result[0]->lokal); // Convert address to coordinates
                if (is_null($lokal_coord) || $lokal_coord == -1) { // Check if coordinates are null or -1 -> Lookup failed
                    $this->updateCoordinates("POINT(0 0)", $result[0]->id, $club); // Update coordinates to 0,0 to suppress further lookups with the same address
                } else {
                    $this->updateCoordinates($lokal_coord, $result[0]->id, $club); // Update coordinates in db
                }
                $coord = $this->extractCoordinatesFromText($lokal_coord); // Extract coordinates from updated text
            }

            if ($coord[0] == 0 && $coord[1] == 0) { // Previous lookup failed, set coordinates to null for map display
                $result[0]->lokal_coord_lat = null;
                $result[0]->lokal_coord_long = null;
            } else { // Previous lookup successful, set coordinates for map display
                $result[0]->lokal_coord_lat = $coord[0];
                $result[0]->lokal_coord_long = $coord[1];
            }
        } else { // Map services not enabled or location is empty, set coordinates to null for map display
            $result[0]->lokal_coord_lat = null;
            $result[0]->lokal_coord_long = null;
        }
    }

    /**
     * Extracts coordinates from a given text.
     *
     * @param string|null $coord_text The text containing the coordinates.
     * @return array An array containing the latitude and longitude extracted from the text.
     */
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

    /**
     * Converts an address to geographic coordinates.
     *
     * @param string $address The address to be converted.
     * @return string|null The geographic coordinates in the format "POINT(x y)" or null if conversion fails.
     */
    public function convertAddress($address)
    {
        // First check if user enabled address conversion
        if(!$this->geo_enabled){
            return -1; // Map Services (external) not enabled
        }

        // Prepare address for geo lookup
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

    /**
     * Prepares the address for encoding.
     *
     * This method takes an address as input and prepares it for encoding by manipulating it based on the configuration settings.
     *
     * @param string $address address .
     * @return string The prepared and encoded address.
     */
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

    /**
     * Retrieves the coordinates (latitude and longitude) for a given address.
     *
     * @param string $address The address for which to retrieve the coordinates.
     * @return array The coordinates as an array with keys 'latitude' and 'longitude'.
     */
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

    /**
     * Retrieves the coordinates (latitude and longitude) from OpenStreetMap (OSM) for a given address.
     *
     * @param string $address The address for which to retrieve the coordinates.
     * @return array|null An array containing the latitude and longitude as elements, or NULL if the coordinates could not be retrieved.
     */
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

    /**
     * Retrieves the coordinates (latitude and longitude) of a given address using the Google Geocoding API.
     *
     * @param string $address The address to retrieve the coordinates for.
     * @param string $gAPIKey The Google API key for accessing the Geocoding API.
     * @return array|null An array containing the latitude and longitude of the address, or NULL if the coordinates could not be retrieved.
     */
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

    /**
     * Updates the coordinates of a club.
     *
     * @param string $coord The new coordinates of the club.
     * @param int $rowId The ID of the club row to update.
     * @return void
     */
    public function updateClubCoordinates($coord, $rowId)
    {
        $club=1;
        $this->updateCoordinates($coord, $rowId, $club);
    }

    /**
     * Updates the team coordinates.
     *
     * @param string $coord The coordinates to update.
     * @param int $rowId The row ID of the team.
     * @return void
     */
    public function updateTeamCoordinates($coord, $rowId)
    {
        $club=0;
        $this->updateCoordinates($coord, $rowId, $club);
    }

    /**
     * Updates the coordinates of a row in the database table.
     *
     * @param string|null $coord The coordinates to update. If null or -1, the coordinates will be set to NULL.
     * @param int $rowId The ID of the row to update.
     * @param int $club The club identifier. If 1, the table will be set to '#__clm_vereine', otherwise it will be set to '#__clm_mannschaften'.
     * @return void
     */
    private function updateCoordinates($coord, $rowId, $club)
    {
        $db 	= JFactory::getDBO();

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
                . " SET lokal_coord = '$coord'"
                . " WHERE id = $rowId";
            clm_core::$db->query($query);
        }  
    }


}
?>
