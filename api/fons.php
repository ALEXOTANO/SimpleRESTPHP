<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function fons(){
    $sql = "SELECT * FROM FONS WHERE BAJA = 0 AND ID_FON >= 50";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}