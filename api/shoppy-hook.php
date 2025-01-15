<?php
// Receive Payload
$payload = json_decode(file_get_contents('php://input'));

// Verify Signature
$signature = hash_hmac('sha512', file_get_contents('php://input'), 'hash_key');
$is_valid = hash_equals($signature, $_SERVER['HTTP_X_SHOPPY_SIGNATURE']);

// Do Rest
if($is_valid){
    switch($payload->event){
        case "simulator":
            break;
        case "order:paid":
            include '../assets/php/db.php';
            $dbHandler = new databasehandler();
            $dbHandler->RemoveSold($payload->data->product->id);
            break;
    }
} else {
    echo "Invalid Request";
}
?>