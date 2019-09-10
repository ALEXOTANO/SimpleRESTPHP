<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}

function art(){
    /* PARA HACER INSERT O UPDATE*/
    $ART = json_decode(oPost('DATOS'), true);
    $ID_ART = '';
    if(isset($ART['id_art'])){$ID_ART = $ART['id_art'];}

    if(strlen($ID_ART)>0){
        $sql = genera_update($ART, 'ARTS', 'ID_ART');
        exe($sql);
    }else {
        $sql = genera_insert($ART, 'ARTS', 'ID_ART');
        exe($sql);
    }
    devolver_mensaje('ok',$ART);
}
function arts(){
    $sql = "SELECT * FROM ARTS WHERE BAJA = 0";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}

function test(){
    $xxx = file_get_contents('https://jsonplaceholder.typicode.com/users');
    $xxx = json_decode($xxx, true);
    devolver_mensaje('ok', $xxx);
}