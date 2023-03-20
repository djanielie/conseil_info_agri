<?php

namespace Config;

use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\UploadedFile;
use Exception;

class Vars
{

    public $data_response = ["message" => "success", "status" => 200, "data" => null];

    public function setResponse($message, $status = 200, $data = [], $token = null)
    {
        if ($token == null) {

            $this->data_response = ["message" => $message, "status" => $status, "data" => $data];
        } else {
            $this->data_response = ["message" => $message, "status" => $status, "data" => $data, 'token' => $token];
        }
    }

    function setError($origin, $message, $alert = true)
    {
        $date = date("Y/m/d h:i:sa");
        if ($alert) {
            $this->setResponse("Une erreur s'est produite veillez ressayer plus tard", 404);
        }

        try {
            $last = file_get_contents(URL_LOG_FILE);
            file_put_contents(URL_LOG_FILE, "");

            file_put_contents(URL_LOG_FILE, array($last, ($last . '\n_._DATABASE:' . DBO_DATABASE . '<>' . $origin . ' => ' . $date . "\n" . $message), PHP_EOL), FILE_APPEND);
        } catch (Exception $th) {
            //            echo $th->getMessage();
        }
    }

    public function getIdFromToken($request)
    {
        try {
            $token = str_replace("Bearer ", "", (string) $request->getHeaderLine('Authorization'));

            if (!$token) return ["id" => null,"role" => null];

            return (array)(JWT::decode($token, JWT_SECRET, array(JWT_ALGORITHM)));
        } catch (Exception $th) {
            $this->setError("GET_TOKEN_FROM_REQUEST", $th);
        }
        return ["id" => null];
    }

    public function getToken($request)
    {
        try {
            $token = str_replace("Bearer ", "", (string) $request->getHeaderLine('Authorization'));
            if (!$token) return null;
            return $token;
        } catch (Exception $th) {
            $this->setError("GET_TOKEN_FROM_REQUEST", $th);
        }
        return null;
    }

    public function filter_params_required()
    {
    }

    public function isEmpty($required_params, $request)
    {
        $error          = false;
        $error_params   = '';
        $request_params = $request->getParsedBody();

        foreach ($required_params as $param) {

            if (!isset($request_params[$param]) || strlen($request_params[$param]) <= 0) {
                $error = true;

                $error_params .= $param . ', ';
            }
        }

        if ($error) {
            $this->setResponse('Paramètres requis ' . substr($error_params, 0, -2) . ' sont vides ou manquantes :(', 201);
        }

        return $error;
    }

    public function isMail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setResponse("Votre adresse email est incorrecte !. veuillez vérifier le format et réessayer ex : votrenom@domaine.com", 201);
            return false;
        }
        return true;
    }

    public  function saveFileOne($file, Request $request)
    {
        $filename = null;
        try {

            $uploadedFiles = $request->getUploadedFiles();
            var_dump($request);
            if (count($uploadedFiles) > 0) {
                // handle single input with single file upload
                if (isset($uploadedFiles[$file])) {
                    $uploadedFile = $uploadedFiles[$file];
                    if ($uploadedFile != null) {
                        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                            $filename = $this->moveUploadedFile($uploadedFile);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            $this->setError("SAVE_IMAGE_ON_SERVER", $th->getMessage());
        }

        return $filename;
    }

    public function getFolder()
    {

        $origin_folder =  '../uploads';

        if (!file_exists($origin_folder)) {
            // Create a new file or direcotry
            mkdir($origin_folder, 0777, true);
        }

        return $origin_folder;
    }

    public function saveFile($file, Request $request)
    {
        $filename = [];
        try {
            $uploadedFiles = $request->getUploadedFiles();

            if (count($uploadedFiles) > 0) {
                $i = 0;
                foreach ($uploadedFiles as $key => $value) {
                    $uploadedFile = $value;

                    if ($uploadedFile != null) {
                        if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                            $filename[$i] = $this->moveUploadedFile($uploadedFile);

                            $i++;
                        }
                    }
                }
            }
        } catch (Exception $th) {
            $this->setError("SAVE_IMAGE_ON_SERVER", $th->getMessage());
        }

        return json_encode($filename);
    }

    public function getShortName($name)
    {
        return explode(' ', $name);
    }

    public function moveUploadedFile(UploadedFile $uploadedFile)
    {
        $directory = $this->getFolder();
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(32)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    public function CheckInt($value): bool
    {
        if (is_bool($value)) {
            return false;
        }

        if (0 === filter_var($value, FILTER_VALIDATE_INT) || filter_var($value, FILTER_VALIDATE_INT)) {
            return true;
        }

        return false;
    }
}
