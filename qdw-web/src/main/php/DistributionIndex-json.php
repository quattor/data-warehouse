<?php
    header('Content-type: application/json');
    if (array_key_exists("attribute", $_REQUEST)) {
        $attribute = preg_replace("/[^A-Za-z0-9\/-_]/","",$_REQUEST["attribute"]);
        chdir('/var/www/data-warehouse/qdw/src/main/python/');
        system('./qdw.py -n -d '.$attribute);
    }
    else {
        echo "[['Profiles have yet to be analysed','1']]";
    }
?>
