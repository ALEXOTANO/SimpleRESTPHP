<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function paises(){
    $sql = "SELECT * FROM PAISES WHERE ID_PAIS > 1";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}