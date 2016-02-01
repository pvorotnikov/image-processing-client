<?php
require_once('../config.php');
require_once('../includes/functions.php');
require_once('../lib/nusoap.php');

// create soap client
$client = new nusoap_client(Config::WSDL_URL, true);

// check whether client connected successfully
$error = $client->getError();
if ($error) {
    http_response_code(503);
    $response = array('status' => 'error', 'errorMessage' => $error);
    echo json_encode($response);
}

header('Content-type: application/json');

switch ($_GET['do']) {

    /**
     * ===========================
     * Classify an image
     * ===========================
     */
    case 'classify':

        // Save image in tmp
        $uploaded_file = process_file();
        $target_file = 'tmp/' . basename($uploaded_file);

        // base64-encode-image
        $im = file_get_contents($uploaded_file);
        $imdata = base64_encode($im);

        // send image over SOAP
        $result = $client->call("classifyImage", array("image" => $imdata));

        // Return result
        if ($client->fault) {
            $response = array('status' => 'error', 'errorMessage' => $result);
        } else {
            $error = $client->getError();
            if ($error) {
                $response = array('status' => 'error', 'errorMessage' => $error);
            } else {
                $res =  json_decode($result);
                $response = array('status' => 'ok', 'data' => array(
                    'image' => $target_file,
                    'classification' => $res->classification
                ));
            }
        }

        // also add debug information
        if (isset($_GET['debug'])) {
            $response['debug'] = array(
                'req' => htmlspecialchars($client->request, ENT_QUOTES),
                'res' => htmlspecialchars($client->response, ENT_QUOTES)
            );
        }

        echo json_encode($response);
        break;

    /**
     * ===========================
     * Reclassify image
     * ===========================
     */
    case 'reclassify':

        // base64-encode-image
        $im = file_get_contents('../' . base64_decode($_GET['image']));
        $imdata = base64_encode($im);

        // send image over SOAP
        $result = $client->call("classifyImage", array("image" => $imdata));

        // Return result
        if ($client->fault) {
            $response = array('status' => 'error', 'errorMessage' => $result);
        } else {
            $error = $client->getError();
            if ($error) {
                $response = array('status' => 'error', 'errorMessage' => $error);
            } else {
                $res =  json_decode($result);
                $response = array('status' => 'ok', 'data' => array(
                    'classification' => $res->classification
                ));
            }
        }

        // also add debug information
        if (isset($_GET['debug'])) {
            $response['debug'] = array(
                'req' => htmlspecialchars($client->request, ENT_QUOTES),
                'res' => htmlspecialchars($client->response, ENT_QUOTES)
            );
        }

        echo json_encode($response);
        break;

    /**
     * ===========================
     * Get the Sobel edge detection of an image
     * ===========================
     */
    case 'edge':

        // base64-encode-image
        $filename = base64_decode($_GET['image']);
        $im = file_get_contents('../' . $filename);
        $imdata = base64_encode($im);

        // send image over SOAP
        $result = $client->call("getSobel", array("image" => $imdata));

        // Return result
        if ($client->fault) {
            $response = array('status' => 'error', 'errorMessage' => $result);
        } else {
            $error = $client->getError();
            if ($error) {
                $response = array('status' => 'error', 'errorMessage' => $error);
            } else {

                $target_file = save_file($result, 'sobel-' . basename($filename));
                $target_file = 'tmp/' . basename($target_file);

                $response = array('status' => 'ok', 'data' => array(
                    'image' => $target_file
                ));
            }
        }

        // also add debug information
        if (isset($_GET['debug'])) {
            $response['debug'] = array(
                'req' => htmlspecialchars($client->request, ENT_QUOTES),
                'res' => htmlspecialchars($client->response, ENT_QUOTES)
            );
        }

        echo json_encode($response);
        break;

    /**
     * Default request handler.
     * Respond with 400
     */
    default:
        http_response_code(400);
        $response = array('status' => 'error', 'errorMessage' => 'Bad request');
        echo json_encode($response);
        break;
}

function save_file($contents, $filename) {
    $target_file = '../tmp/' . $filename;
    $handle = fopen($target_file, "wb");
    fwrite($handle, base64_decode($contents));
    fclose($handle);
    return $target_file;
}

function process_file() {

    $target_dir = "../tmp/";
    $target_file = $target_dir . md5(date('Y-m-d H:i:s:u')) . '-' . basename($_FILES["imageFile"]["name"]);
    $upload_ok = true;
    $image_file_type = pathinfo($target_file, PATHINFO_EXTENSION);
    $result = 'error';

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["imageFile"]["tmp_name"]);
    if ($check !== false) {
        $upload_ok = true;
    } else {
        $upload_ok = false;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        $upload_ok = false;
    }

    // Check file size
    if ($_FILES["imageFile"]["size"] > 500000) {
        $upload_ok = false;
    }

    // Allow certain file formats
    if ($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg" && $image_file_type != "gif" ) {
        $upload_ok = false;
    }

    // Check if $upload_ok is set to false by an error
    if ($upload_ok == false) {
        $result = 'error';

    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["imageFile"]["tmp_name"], $target_file)) {
            $result = $target_file;
        } else {
            $result = 'error';
        }
    }

    return $result;
}
