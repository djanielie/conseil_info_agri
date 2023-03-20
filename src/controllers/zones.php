<?php

use Slim\Http\Request;

class Zones extends ZoneModel
{
    public function load(Request $request)
    {
        $this->setResponse("success", 200,$this->getAllZones());

        return $this->data_response;
    }

    public function create(Request $request)
    {
        return $this->data_response;
    }
    
}
