<?php

function VerifyName($summoner_name) {
    $invalid_char_list = '"`¬$`£%*()_-+={[}]@\'~#?/>.<,;:|\\';
    $invalid_char_array = str_split($invalid_char_list);
    $summoner_name_array = str_split($summoner_name);

    foreach ($invalid_char_array as &$char){
        foreach ($summoner_name_array as &$summonerChar){
            if($char == $summonerChar){
                return false;
            }
        }
    }

    return true;
}

function ReturnShoppyGroup($region){
    switch($region){
        case "EUW":
            return "SHOPPY PRODUCT GROUP ID";
        case "NA":
            return "SHOPPY PRODUCT GROUP ID";
        case "LAN":
            return "SHOPPY PRODUCT GROUP ID";
        case "EUNE": 
            return "SHOPPY PRODUCT GROUP ID";
        case "OCE":
            return "SHOPPY PRODUCT GROUP ID";
        case "LAS":
            return "SHOPPY PRODUCT GROUP ID";
    }
}

?>