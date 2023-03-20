<?php

use Config\Dbo;
use Config\Sms_keccel;
use Ramsey\Uuid\Uuid;

class AccountModel extends Dbo
{
    public $id, $id_categ, $id_zone, $fullname, $genre, $phone, $email, $adresse, $password, $datenaiss, $id_card, $matricule, $etat, $codeVal, $expireAt, $createdAt, $updatedAt,$username;

    public function saveAccount()
    {
        $this->codeVal = random_int(100000, 900000);
        $this->matricule = random_int(100000, 900000);
        $this->etat = 0;
        $this->expireAt = $this->NextDateMunite(30);

        if ($this->getValue("select id i from account where phone=?", [$this->phone]) == null) {
            $this->id = Uuid::uuid4();
            $is = $this->execute(
                "INSERT INTO `account` SET `id`=?,`id_categ`=?,`id_zone`=?,`fullname`=?,`genre`=?,`phone`=?,`email`=?,`adresse`=?,`password`=md5(?),`datenaiss`=?,`id_card`=?,`matricule`=?,`etat`=?,`codeVal`=?,`expireAt`=?",
                [$this->id, $this->id_categ, $this->id_zone, $this->fullname, $this->genre, $this->phone, $this->email, $this->adresse, $this->password, $this->datenaiss, $this->id_card, $this->matricule, $this->etat, $this->codeVal, $this->expireAt]
            );
            if($is){
                $message = "Votre compte a été créé avec succès et votre code de validation est le suivant  : ".$this->codeVal." pour 30 munites";
                $cls = new Sms_keccel();
                $cls->sendSMS($this->phone, $message,"CREATE_ACCOUNT");
            } 
        } else {
            $this->setResponse("Le numéro de téléphone est déjà utilisé par une autre personne", 201);
        }
    }

    public function getAllCategorie()
    {
        return $this->getAll("select * from account_categorie order by description desc");
    }
}
