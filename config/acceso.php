<?php
session_start();
if(isset($_GET['MDL'])){$MDL = $_GET['MDL'];}
if($MDL !== "LOGIN"){
    $USU_SES = json_decode($_POST['USU_SES'],true);
    $USU_ACSS = json_decode($_POST['USU_ACSS'],true);

    $_SESSION["USU_SES2"] = Array(
        'ID_USU' => $USU_SES["ID_USU"],
        'USU' => $USU_SES["USU"],
        'FECHA' => $USU_SES['FECHA'],
        'OTC_KEY' => $USU_SES['OTC_KEY']
    );
    $_SESSION["USU_ACSS2"] = $USU_ACSS;

    if(!isset($_SESSION['USU_SES']) || !isset($_SESSION['USU_ACSS'])){
        http_response_code(401);
        session_destroy();
        session_unset();
        exit;
    }
    if($_SESSION["USU_ACSS"]!=$_SESSION["USU_ACSS2"] || $_SESSION["USU_SES"]!=$_SESSION["USU_SES2"]){
        http_response_code(401);
        session_destroy();
        session_unset();
        exit;
    }
}



