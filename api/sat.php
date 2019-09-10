<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function pros_srvs(){
    $sql = "SELECT * FROM CSAT_PROS_SRVS";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}

function unis_meds(){
    $sql = "SELECT * FROM CSAT_UNIS_MEDS";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}

function regs_fiss(){
    $sql = "SELECT * FROM CSAT_REGS_FISS";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}