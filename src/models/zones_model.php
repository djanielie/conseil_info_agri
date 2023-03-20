<?php

use Config\Dbo;

class ZoneModel extends Dbo
{
    public function getAllZones(){
        return $this->getAll("select zones.id,zones.name as zone,z.name as zone_parent,zones.createdAt from zones LEFT JOIN zones z on z.id = zones.id_zone order by zones.name asc;");
    }
    
}
