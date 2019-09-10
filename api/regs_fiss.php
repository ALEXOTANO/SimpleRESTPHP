<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function regs_fiss(){
    $sql = "SELECT * FROM REGS_FISS WHERE BAJA = 0 AND ID_REG_FIS >= 10";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}