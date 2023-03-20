<?php

use Slim\Http\Request;

class Produit extends ProduitModel
{
    public function create_produit(Request $request)
    {
        if (!$this->isEmpty(["name", "description", "day_validite"], $request)) {

            $this->name = htmlspecialchars($request->getParsedBody()["name"]);
            $this->description = htmlspecialchars($request->getParsedBody()["description"]);
            $this->day_validite = htmlspecialchars($request->getParsedBody()["day_validite"]);
            $this->id_categ = htmlspecialchars($request->getParsedBody()["id_categ"]);
            $this->images = $this->saveFile('image', $request);
            $this->save_produit();
        }
        return $this->data_response;
    }

    public function create_update_produit(Request $request)
    {
        if (!$this->isEmpty(["name", "description",], $request)) {
            $this->id = $request->getAttribute("id");
            $this->name = htmlspecialchars($request->getParsedBody()["name"]);
            $this->description = htmlspecialchars($request->getParsedBody()["description"]);
            $this->day_validite = htmlspecialchars($request->getParsedBody()["day_validite"]);
            $this->id_categ = htmlspecialchars($request->getParsedBody()["id_categ"]);
            $this->images = $this->saveFile('image', $request);
            $this->update_produit();
        }
        return $this->data_response;
    }

    public function update_image(Request $request)
    {
        $this->id = $request->getAttribute("id");
        $this->images = $this->saveFile('image', $request);
        if ($this->images != '[]') {
            $this->execute("UPDATE produit set images = ? where id = ?", array($this->images, $this->id));
        } else {
            $this->setResponse("Vous n'avez selectionnée aucune image", 201);
        }

        return $this->data_response;
    }

    public function create_categorie(Request $request)
    {
        if (!$this->isEmpty(["designation", "description"], $request)) {

            $this->designation = htmlspecialchars($request->getParsedBody()["designation"]);
            $this->description = htmlspecialchars($request->getParsedBody()["description"]);
            $this->id_account_create = $this->getIdFromToken($request)["id"];
            $this->save_categorie();
        }
        return $this->data_response;
    }
    public function create_update_categorie(Request $request)
    {
        if (!$this->isEmpty(["designation", "description"], $request)) {

            $this->id = $request->getAttribute("id");
            $this->designation = htmlspecialchars($request->getParsedBody()["designation"]);
            $this->description = htmlspecialchars($request->getParsedBody()["description"]);
            $this->id_account_create = $this->getIdFromToken($request)["id"];
            $this->update_categorie();
        }

        return $this->data_response;
    }

    public function create_delete(Request $request)
    {

        $this->id = $request->getAttribute("id");
        if ($this->getValue("select id i from produit where id_categ=?") == null) {
            $this->execute("delete from produit_categorie where id=?", array($this->id));
        } else {
            $this->setResponse("Vous ne pouvez pas supprimer cette categorie car il est deja utilisé ", 201);
        }

        return $this->data_response;
    }
    public function delete_produit(Request $request)
    {

        $this->id = $request->getAttribute("id");
        if ($this->getValue("select id i from production where id_produit=?") == null) {
            $this->execute("delete from produit where id=?", array($this->id));
        } else {
            $this->setResponse("Vous ne pouvez pas supprimer cette categorie car il est deja utilisé ", 201);
        }

        return $this->data_response;
    }

    public function load_categorie(Request $request)
    {

        $this->setResponse("success", 200, $this->getAll("select id,designation,description from produit_categorie order by designation desc"));


        return $this->data_response;
    }

    public function load_produit(Request $request)
    {
        $this->id = $request->getAttribute("id");
        if ($this->id == null) {
            $all = $this->getAll("SELECT * FROM `v_produit` order by name desc");
        } else {
            $all = $this->getAll("SELECT * FROM `v_produit` where id=?", [$this->id], true);
        }

        $this->setResponse(
            "success",
            200,
            $all
        );

        return $this->data_response;
    }
}
