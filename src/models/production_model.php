<?php

use Config\Dbo;
use Ramsey\Uuid\Rfc4122\UuidV4;
use Ramsey\Uuid\Uuid;

class ProductionModel extends Dbo
{
    public $id, $id_user_create, $id_produit, $id_zone, $prix, $devise, $etat, $adresse, $observation, $dateProduction, $createdAt, $updatedAt, $id_account_update, $images, $expireAt, $qte_dispo, $role,$id_user;

    public function save_production()
    {
        $day_validite = $this->getValue("select day_validite i from produit where id=?", [$this->id_produit]);
        $this->expireAt = $this->getValue("SELECT DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $day_validite DAY) i;");;

        $this->execute(
            "INSERT INTO `production` SET `id`=?,`id_user_create`=?,`id_produit`=?,`id_zone`=?,`prix`=?,`devise`=?,`adresse`=?,`observation`=?,`dateProduction`=?,images=?,expireAt=?,`qte_dispo`=?",
            [Uuid::uuid4(), $this->id_user_create, $this->id_produit, $this->id_zone, $this->prix, $this->devise, $this->adresse, $this->observation, $this->dateProduction, $this->images, $this->expireAt, $this->qte_dispo]
        );
    }

    public function update_production()
    {
        $day_validite = $this->getValue("select day_validite i from produit where id=?", [$this->id_produit]);
        $this->expireAt = $this->getValue("SELECT DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $day_validite DAY) i;");;

        $this->execute(
            "UPDATE `production` SET `id_account_update`=?,`id_produit`=?,`id_zone`=?,`prix`=?,`devise`=?,`adresse`=?,`observation`=?,`dateProduction`=?,images=?,expireAt=?,`qte_dispo`=? where `id`=?",
            [$this->id_account_update, $this->id_produit, $this->id_zone, $this->prix, $this->devise, $this->adresse, $this->observation, $this->dateProduction, $this->images, $this->expireAt, $this->qte_dispo, $this->id]
        );
    }

    public function loadProduction()
    {
        if ($this->role == 'Vendeur') {
            $all = $this->getAll("SELECT * FROM `v_production` where id_user_create=? order by createdAt desc", [$this->id_user_create]);
        } elseif ($this->role == 'Admin') {
            $all = $this->getAll("SELECT * FROM `v_production` order by createdAt desc");
        } else {
            $all = $this->getAll("SELECT * FROM `v_production` where expireAt>=CURRENT_TIMESTAMP and qte_dispo>0 and etat=1", [$this->id_user_create]);
        }

        return $all;
    }

    public function getById()
    {
        if ($this->role == 'Vendeur') {
            return $this->getAll("SELECT * FROM `v_production` where id=? and id_user_create=? order by createdAt desc", [$this->id, $this->id_user_create], true);
        } else {
            return $this->getAll("SELECT * FROM `v_production` where id=? order by createdAt desc", [$this->id], true);
        }
    }


}
