<?php
error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR);
//date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once('config.php');

$host=strtoupper($_SERVER['SERVER_NAME']);

switch ($host) {
    case "LOCALHOST":
        $con_data = $config['PRINCIPAL'];
        break;
    default:
        $con_data = $config['PRINCIPAL'];
        break;
}

try{
    $con_pdo = new PDO('mysql:host='.$con_data["H"].';port='.$con_data["P"].';dbname='.$con_data["B"],$con_data["U"],$con_data["C"], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}catch (PDOException $e){
    http_response_code(500);
    echo json_encode(Array("ERROR BD.", $e->getMessage()));
    exit;
}

/*function qry($qqq){
    global $con_pdo;

    try{
        $query = $con_pdo->prepare($qqq);
        $query->execute();
        return $query->fetchAll();
    }catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(Array("ERROR SERVER QRY.", $e->getMessage(). "QRY: ".$qqq));
        exit;
    }
}
function exe($qqq){
    global $con_pdo;
    try{
        $query = $con_pdo->prepare($qqq);
        $query->execute();
    }catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(Array("ERROR SERVER EXE.", $e->getMessage() . "QRY: ".$qqq));
        exit;
    }

}*/
