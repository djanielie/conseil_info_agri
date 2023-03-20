<?php

use Config\Sms_keccel;
use Firebase\JWT\JWT;
use Ramsey\Uuid\Uuid;
use Slim\Http\Request;

class Account extends AccountModel
{
    public function create_account(Request $request)
    {

        if (!$this->isEmpty(["fullname", "phone", "email", "genre", "adresse", "id_categ", "id_zone", "password1", "password2"], $request)) {

            $this->fullname = htmlspecialchars($request->getParsedBody()["fullname"]);
            $this->phone = htmlspecialchars($request->getParsedBody()["phone"]);
            $this->email = htmlspecialchars($request->getParsedBody()["email"]);
            $this->genre = htmlspecialchars($request->getParsedBody()["genre"]);
            $this->adresse = htmlspecialchars($request->getParsedBody()["adresse"]);
            $this->id_categ = htmlspecialchars($request->getParsedBody()["id_categ"]);
            $this->id_zone = htmlspecialchars($request->getParsedBody()["id_zone"]);
            $this->password = htmlspecialchars($request->getParsedBody()["password1"]);

            $password_confirm = htmlspecialchars($request->getParsedBody()["password2"]);
            if (strlen($this->password) >= 6) {
                if ($password_confirm == $this->password) {

                    if (strlen($this->phone) == 13) {
                        if ($this->email != "-") {

                            if ($this->isMail($this->email)) {
                                if ($this->getValue("select id i from account where email=?", [$this->email]) == null) {
                                    $this->saveAccount();
                                } else {
                                    $this->setResponse("Cette adresse mail est deja utilisée", 201);
                                }
                            }
                        } else {
                            $this->saveAccount();
                        }
                    } else {
                        $this->setResponse("Le numéro de telephone doit avoir 13 caracteres ex : +243000000000", 201);
                    }
                } else {
                    $this->setResponse("Le mot deux mot des passe ne sont pas identique !.", 201);
                }
            } else {
                $this->setResponse("Le mot de passe doit avoir au minimum 6 caractères !.", 201);
            }
        }

        return $this->data_response;
    }

    public function verify(Request $request)
    {

        if (!$this->isEmpty(["phone", "code"], $request)) {

            $this->phone = htmlspecialchars($request->getParsedBody()["phone"]);
            $this->codeVal = htmlspecialchars($request->getParsedBody()["code"]);

            if (strlen($this->phone) == 13) {

                if ($this->getValue("select id i from account where phone=? ", [$this->phone]) != null) {

                    $code = $this->getValue("select codeVal i from account where phone=? and codeVal=? and expireAt>=CURRENT_TIMESTAMP and etat=0;", [$this->phone, $this->codeVal]);
                    if ($code != null) {
                        $this->execute("UPDATE account SET etat=1,updatedAt=CURRENT_TIMESTAMP where phone=?", [$this->phone]);
                    } else {
                        $this->setResponse("Le code que vous avez saisi n'est pas valable ou a déjà expiré ", 201);
                    }
                } else {
                    $this->setResponse("Ce numéro de téléphone n'existe pas dans le système", 201);
                }
            } else {
                $this->setResponse("Le numéro de telephone doit avoir 13 caracteres ex : +243000000000", 201);
            }
        }

        return $this->data_response;
    }

    public function login(Request $request)
    {
        $status = 201;
        if (!$this->isEmpty(["username", "password"], $request)) {

            $this->username = htmlspecialchars($request->getParsedBody()["username"]);
            $this->password = htmlspecialchars($request->getParsedBody()["password"]);
            $id_user = null;
            $token = null;
            $etat = 0;
            $observation = null;
            $infos = null;

            $user = $this->getAll("select * from v_account where (phone=? or email=?) and password=md5(?)", [$this->username, $this->username, $this->password], true);
            if ($user) {
                $infos["etat"] = $user->etat;
                $infos["fullname"] = $user->fullname;
                $infos["role"] = $user->categorie;
                $infos["phone"] = $user->phone;

                if ($user->etat == 1) {
                    $infos["email"] = $user->email;
                    $infos["zone"] = $user->zone;
                    $id_user = $user->id;

                    $now = new DateTime();
                    $future = new DateTime('now +12 hours');
                    $payload = [
                        'iat' => $now->getTimeStamp(),
                        'exp' => $future->getTimeStamp(),
                        'id' => $user->id,
                        'role' => $infos["role"]
                    ];

                    $secret = JWT_SECRET;
                    $token = JWT::encode($payload, $secret, JWT_ALGORITHM);

                    $etat = 1;
                    $observation = "Successful connection with success";
                    $status = 200;
                } else {
                    $observation = "La connexion a été effectuée avec succès mais votre compte n'est pas actif. Veuillez contacter l'administrateur ou suivre la procédure de restauration de compte.";
                    $this->setResponse($observation, $status);
                }
            } else {
                $observation = "Nom d'utilisateur ou mot de passe incorrect";
                $this->setResponse($observation, $status);
            }
            $this->execute(
                "INSERT`login_history` SET `id`=?,`id_account`=?,`etat`=?,`observation`=?,`token`= ?,username=?",
                [Uuid::uuid4(), $id_user, $etat, $observation, $token, $this->username]
            );
            $this->setResponse($observation, $status, $infos, $token);
        }

        return $this->data_response;
    }

    public function load_categories(Request $request)
    {
        $this->setResponse("success", 200, $this->getAllCategorie());
        return $this->data_response;
    }

    public function recover_account(Request $request)
    {
        if (!$this->isEmpty(["phone"], $request)) {
            $this->phone = htmlspecialchars($request->getParsedBody()["phone"]);

            if ($this->getValue("select id i from account where phone=?", [$this->phone]) != null) {

                $this->etat = 0;
                $this->expireAt = $this->NextDateMunite(30);
                $this->codeVal = random_int(100000, 900000);

                if ($this->execute("update account set etat=0,codeVal=?,expireAt=? where phone=?", [$this->codeVal, $this->expireAt, $this->phone])) {
                    $cls = new Sms_keccel();
                    $cls->sendSMS($this->phone, "Le code de vérification est : " . $this->codeVal . ' valide pour 30 Min', "RECOVER PASSWORD");
                    $this->setResponse("Success", 200, ["phone" => $this->phone]);
                }

            } else {
                $this->setResponse("Ce numéro de téléphone n'est pas reconnu par le système", 201);
            }
        }

        return $this->data_response;
    }
}
