<?php

use Config\Dbo;
use Ramsey\Uuid\Uuid;

class ProduitModel extends Dbo
{
    public $id, $designation, $description, $createdAt, $updatedAt;
    public $id_categ, $name, $day_validite, $images, $id_account_create;

    public function save_produit()
    {
        if ($this->getValue("select id i from produit where name=?", [$this->name]) == null) {

            $this->execute(
                "INSERT INTO `produit` SET `id`=?,`id_categ`=?,`name`=?,`description`=?,`day_validite`=?,`images`=?,`id_account_create`=?",
                [Uuid::uuid4(), $this->id_categ, $this->name, $this->description, $this->day_validite, $this->images, $this->id_account_create]
            );
        } else {
            $this->setResponse("Cet produit existe dans le système", 201);
        }
    }

    public function update_produit()
    {
        if($this->images==null){
            $this->images = $this->getValue("select images i from produit where id=?", array($this->id));
        }

        $this->execute(
            "UPDATE `produit` SET `id_categ`=?,`name`=?,`description`=?,`day_validite`=?,`images`=?,`id_account_update`=? where `id`=?",
            [$this->id_categ, $this->name, $this->description, $this->day_validite, $this->images, $this->id_account_create,$this->id]
        );
    }

    public function save_categorie()
    {
        if ($this->getValue("select id i from produit_categorie where designation=?", [$this->designation]) == null) {

            $this->execute(
                "INSERT INTO `produit_categorie` SET `id`=?,`designation`=?,`description`=?,id_account_create=?",
                [Uuid::uuid4(), $this->designation, $this->description, $this->id_account_create]
            );
        } else {
            $this->setResponse("Cette categorie existe dans le système", 201);
        }
    }

    public function update_categorie()
    {
        $this->execute(
            "UPDATE `produit_categorie` SET `designation`=?,`description`=?,id_account_create=? where `id`=?",
            [$this->designation, $this->description, $this->id_account_create, $this->id]
        );
    }
}
