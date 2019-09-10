<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function przs(){
    $sql = "SELECT * FROM PRZS WHERE ID_PRZ > 1";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}