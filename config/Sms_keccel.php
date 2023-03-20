<?php

namespace Config;

use Exception;
use Models\SmsModel;
use Ramsey\Uuid\Uuid;

class Sms_keccel extends Dbo
{
    public $isSend = "NOT SEND";
    public $message_id = null;
    public $description = "Une erreur c'est produite";
    public $id_provider = null;

    public function sendSMS($phone, $message,$motif)
    {
        $token = 'GHPK3A29WFG6Q4K';
        $curl = curl_init();

        try {
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.keccel.com/sms/v2/message.asp',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => '{
                                    "token":"' . $token . '",
                                    "to":"' . $phone . '",
                                    "from":"KivuGreen",
                                    "message":"' . $message . '"
                                }',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Cookie: ASPSESSIONIDQWBDCBCD=AFEDHGMCELHLJBJLNPEPLHID'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            $result = json_decode($response, true);
            if ($result) {
                $this->isSend = $result['status'];
                $this->message_id = $result['messageID'];
                $this->description = $result['description'];
            }
        } catch (Exception $e) {
            $this->setError("SEND_SMS_KECCEL",$e);
            $this->description = $e->getMessage();
        }

        $id_account = $this->getValue("select id i from account where phone=?",[$phone]);
        $this->execute("INSERT INTO `message` SET `id`=?,`id_account`=?,`motif`=?,`texte`=?,`etat`=?,`observation`=?,phone=?",
        [Uuid::uuid4(),$id_account,$motif,$message,$this->isSend,$this->description,$phone]);
    }
}
