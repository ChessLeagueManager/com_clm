<?php

class AddressHandler
{
    private $config;
    
    public function __construct()
    {
        $this->config = clm_core::$db->config();
    }

    public function convertAddress($address)
    {
        // First check if user enabled adress conversion
        if(!$this->config->googlemaps){
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

        if ($service == 1) {
            $gAPIKey = $this->config->googlemaps_api;
            if (!is_null($gAPIKey)) {
                return $this->getCoordinatesFromGoogle($address, $gAPIKey);
            }
        }

        return $this->getCoordinatesFromOSM($address);
    }

    private function getCoordinatesFromOSM($address)
    {
        $options = array(
            'http' => array(
                'method' => "GET",
                'header' => "Accept-language: en\r\n" .
                            "User-Agent: Test-Developer\r\n"
            )
        );

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


}
?>
