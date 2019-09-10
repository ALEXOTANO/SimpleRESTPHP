<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function unis_meds(){
    $sql = "SELECT * FROM UNIS_MEDS WHERE BAJA = 0 AND ID_UNI_MED >= 10";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}