<?php
class filesManager
{
    function ReadJSON($fileName = '')
    {
        $arrayJSON = [];

        if (!empty($fileName)) {
            if (file_exists($fileName)) {
                $fread = file_get_contents($fileName); // Read the entire file content
                if ($fread !== false) {
                    $arrayJSON = json_decode($fread); // Decoding JSON into an associative array
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception('JSON decoding error: ' . json_last_error_msg());
                    }
                } else {
                    throw new Exception('Failed to read file content');
                }
            } else {
                throw new Exception('File does not exist');
            }
        }

        return $arrayJSON;
    }

    function SaveJSON($fileName = '', $arrayObj = null)
    {
        if (!empty($fileName)) {
            if ($arrayObj !== null) {
                $file = fopen($fileName, 'w');
                fwrite($file, json_encode($arrayObj));
                fclose($file);
                return true;
            }
        } else {
            throw new Exception('Filename cant be empty');
        }
    }
}
