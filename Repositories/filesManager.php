<?php
function ReadJSON($fileName = ''){
    $arrayJSON = [];

    if(!empty($fileName)){            
        if(file_exists($fileName)){
            
            $file = fopen($fileName,'r');
            $fSize = filesize($fileName);

            if ($fSize > 0) {
                $fread = fread($file,$fSize);
            } else {
                $fread = '{}';
            }
            fclose($file);
            $arrayJSON = json_decode($fread);
        }
        else{
            throw new Exception('File does not exist');
        }
    return $arrayJSON;
    }
}    

function SaveJSON($fileName = '',$arrayObj = null){
    if(!empty($fileName)){
        if($arrayObj !== null){
            $file = fopen($fileName,'w');
            fwrite($file,json_encode($arrayObj));
            fclose($file);
            return true;
        }
    }else{
        throw new Exception('Filename cant be empty');
    }
}
?>