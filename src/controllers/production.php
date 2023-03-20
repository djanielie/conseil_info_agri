<?php

use Slim\Http\Request;

class Production extends ProductionModel
{
    public function create_account(Request $request)
    {
        if (!$this->isEmpty(["id_produit", "id_zone", "prix", "devise", "adresse", "date_production", "observation", "qte_dispo"], $request)) {
            $this->id_user_create = $this->getIdFromToken($request)["id"];
            $this->id_produit = htmlspecialchars($request->getParsedBody()["id_produit"]);
            $this->id_zone = htmlspecialchars($request->getParsedBody()["id_zone"]);
            $this->prix = htmlspecialchars($request->getParsedBody()["prix"]);
            $this->devise = htmlspecialchars($request->getParsedBody()["devise"]);
            $this->adresse = htmlspecialchars($request->getParsedBody()["adresse"]);
            $this->observation = htmlspecialchars($request->getParsedBody()["observation"]);
            $this->dateProduction = htmlspecialchars($request->getParsedBody()["date_production"]);
            $this->images = $this->saveFile('images', $request);
            $this->qte_dispo = htmlspecialchars($request->getParsedBody()["qte_dispo"]);


            $this->save_production();
        }

        return $this->data_response;
    }

    public function create_update(Request $request)
    {
        if (!$this->isEmpty(["id_produit", "id_zone", "prix", "devise", "adresse", "date_production", "observation", "qte_dispo"], $request)) {
            $this->id = $request->getAttribute("id");
            $this->id_account_update = $this->getIdFromToken($request)["id"];
            $this->id_produit = htmlspecialchars($request->getParsedBody()["id_produit"]);
            $this->id_zone = htmlspecialchars($request->getParsedBody()["id_zone"]);
            $this->prix = htmlspecialchars($request->getParsedBody()["prix"]);
            $this->devise = htmlspecialchars($request->getParsedBody()["devise"]);
            $this->adresse = htmlspecialchars($request->getParsedBody()["adresse"]);
            $this->observation = htmlspecialchars($request->getParsedBody()["observation"]);
            $this->dateProduction = htmlspecialchars($request->getParsedBody()["date_production"]);
            $this->images = $this->saveFile('images', $request);
            $this->qte_dispo = htmlspecialchars($request->getParsedBody()["qte_dispo"]);


            $this->update_production();
        }

        return $this->data_response;
    }

    public function load_production(Request $request)
    {

        $this->role = $this->getIdFromToken($request)["role"];
        $this->id_user_create = $this->getIdFromToken($request)["id"];
        $this->id = $request->getAttribute("id");


        $this->setResponse("success", 200, $this->loadProduction());

        return $this->data_response;
    }

    public function load_by_id(Request $request)
    {
        $this->id = $request->getAttribute("id");
        $this->setResponse("success", 200, $this->getById());
        return $this->data_response;
    }

    public function delete_production(Request $request)
    {
        $this->id = $request->getAttribute("id");

        if ($this->getValue("select id i from commande where id_production=?", [$this->id]) == null) {
            $this->execute("DELETE FROM production where id=?", [$this->id]);
        } else {
            $this->setResponse("success", 200, "Cette production ayant déjà fait l'objet de plusieurs commandes, elle ne peut être supprimée.");
        }

        return $this->data_response;
    }

    public function ActiveProduction(Request $request)
    {

        if (!$this->isEmpty(["id_production", "observation", "status"], $request)) {

            $this->id = $request->getParsedBody()["id_production"];
            $this->id_user = $this->getIdFromToken($request)["id"];
            $this->role = $this->getIdFromToken($request)["role"];
            $this->observation = htmlspecialchars($request->getParsedBody()["observation"]);
            $this->etat = htmlspecialchars($request->getParsedBody()["status"]);

            $this->etat = ($this->etat == 'active') ? 1 : 0;

            if ($this->role == 'Admin') {
                if ($this->getValue("select id i from production where id=?", [$this->id]) != null) {
                    $this->execute("update production set etat=?,id_account_validate=?,validateAt=CURRENT_TIMESTAMP,validate_observation=? where id=?", array($this->etat, $this->id_user, $this->observation, $this->id));
                } else {
                    $this->setResponse("Invalid ID", 201);
                }
            } else {
                $this->setResponse("Action non autorisée", 201);
            }
        }


        return $this->data_response;
    }
}
