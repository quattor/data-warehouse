<?php
    header('Content-type: application/json');
    
    function arrayify($i) {
        return(array($i));
    }
    
    if (array_key_exists("attribute", $_REQUEST)) {
        if (array_key_exists("value", $_REQUEST)) {
            $attribute = preg_replace("/[^A-Za-z0-9\/-_]/","",$_REQUEST["attribute"]);
            $value = preg_replace("/[^A-Za-z0-9\s_\-\(\)\.:\/=,@]/","",$_REQUEST["value"]);
            $value = "'$value'";
            chdir('qdwprogram');
            $results = exec('./qdw.py -n -f '.$attribute.' '.$value);
            $results = json_decode($results);
            $results = array_map("arrayify", $results);
            echo json_encode($results);    
        }
    }
    else {
        echo "[['Profiles have yet to be analysed','1']]";
    }
?>
