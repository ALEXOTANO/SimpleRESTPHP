<?php
require_once '../config/conn.php';
require_once '../config/funcs.php';

$datos = file("./catalogos/claveProdServ.txt");

exe("TRUNCATE TABLE CS_PROS_SRVS");

$arreglo = array();
$i = 0;
foreach ($datos as $linea) {
    if ($i>0){
        $linea_arreglo = explode("\t", $linea);
        $clave = $linea_arreglo[0];
        $valor = trim(strtr(utf8_encode($linea_arreglo[1]), $cp1252_map)); // PASA TODOS LOS VALORES A UTF-8
                                                                           // (LA VAR cp1252_map ESRTRA EN FUNCS.. ES UN MAPA DE CARACTERES A REEMPLAZAR)
        $valor = $con_pdo->quote($valor);
        exe("INSERT INTO CS_PROS_SRVS (idCsProSrv, csProSrv) VALUES ('$clave', $valor)");
    }
    $i++;
}
echo $i-1 . " registros ingresados con exito.";
