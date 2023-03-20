<?php

namespace Config;

use DateTime;
use Exception;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use Ramsey\Uuid\Uuid;

class Dbo extends Vars
{
    /**
     * @return PDO|null
     */
    public function con()
    {
        $dbo = null;
        try {
            $timezone = "Africa/Cairo";

            date_default_timezone_set($timezone);
            $now = new DateTime();
            $mins = $now->getOffset() / 60;
            $sgn = ($mins < 0 ? -1 : 1);
            $mins = abs($mins);
            $hrs = floor($mins / 60);
            $mins -= $hrs * 60;
            $offset = sprintf('%+d:%02d', $hrs * $sgn, $mins);
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $dbo = new PDO("mysql:host=" . DBO_HOST . ";dbname=" . DBO_DATABASE . "", DBO_USERNAME, DBO_PASSWORD, $pdo_options);
            $dbo->exec("SET time_zone='$offset';");
            $dbo->query('SET NAMES UTF8');
        } catch (Exception $exception) {
            $this->setError("CONNECTION||",$exception);
        }
        return $dbo;
    }

    /**
     * @param $rqt
     * @param $data
     * @param $isOneObject
     * @return array|mixed
     */
    public function getAll($rqt, $data = [], $isOneObject = false)
    {
        try {
            $var = [];
            $req = $this->con()->prepare($rqt);
            $req->execute($data);
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                if ($isOneObject) {
                    $var = (Object) $data;
                } else {
                    $var[] = $data;
                }
            }
            $req->closeCursor();
        } catch (Exception $exception) {
          $this->setError("GET_ALL||".$rqt,$exception);
        }
        return $var;
    }

    /**
     * @param $rqt
     * @param $data
     * @return mixed|null
     */
    public function getValue($rqt, $data = [])
    {
        $var = null;
        try {

            $req = $this->con()->prepare($rqt);
            $req->execute($data);
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $var = $data['i'];
            }
        } catch (Exception $exception) {
            $this->setError("GET_VALUE||".$rqt,$exception);
        }
        return $var;
    }

    /**
     * @param $rqt
     * @param $data
     * @param $message_success
     * @param $message_erreur
     * @return bool
     */
    public  function execute($rqt, $data = [], $message_success = "Traitement réussie avec success", $message_erreur = "Une erreur s'est produite")
    {
        $bool = false;
        try {
            $req = $this->con()->prepare($rqt);
            $bool = $req->execute($data);
            $this->setResponse($message_success);
            return $bool;
        } catch (Exception $exception) {
            $this->setError("EXECUTE_SCRIPT||".$rqt,$exception);

        }
        return $bool;
    }

    /**
     * @param $nombre_de_jours
     * @return DateTime
     */
    public  function NextDateMunite($nombre_de_jours){
        $date = $this->getValue("SELECT DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $nombre_de_jours DAY_MINUTE) i;");
        return $date;
    }

    function sendEmail($id_user, $mail_to, $name, $object, $message, $fulltext = '')
    {

        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);
        $etat = 0;
        try {

            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'mlinzitech.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'system@mlinzitech.com';           //SMTP username
            $mail->Password   = 'RL0YAEiN_oPQ';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->CharSet = "UTF-8";
            //Recipients
            $mail->setFrom('system@mlinzitech.com', 'Mlinzi Tech');
            $mail->addAddress($mail_to, $name);     //Add a recipient
            $mail->addCC('mlinzirdc@gmail.com');

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $object;
            $mail->Body    = $message;
            $mail->AltBody = $fulltext;

            $mail->SMTPDebug = 0;

            $mail->send();
            $observation = "Envoie réussie";
            $etat = 1;
        } catch (Exception $e) {
            $observation =  $e->getMessage();
            $this->setError("ENVOIE_EMAIL||".$mail_to,false);
        }

        $this->execute(
            "INSERT INTO `historique_email` SET `id`=?,`id_user`=?,`email`=?,`message`=?,`etat`=?,`observation`=?,`objectif`=?",
            [Uuid::uuid4(), $id_user,$mail_to,$message,$etat ,$observation,$object]
        );

        return $etat;
    }

}