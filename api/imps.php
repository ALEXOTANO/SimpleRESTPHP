<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function imps(){
    $sql = "SELECT * FROM IMPS 
            WHERE BAJA = 0 AND ID_IMP >= 10";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}