<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}

function cte(){
    /* PARA HACER INSERT O UPDATE*/
    $CTE = json_decode(oPost('DATOS'), true);
    $ID_FCP = '';
    if(isset($CTE['id_fcp'])){$ID_FCP = $CTE['id_fcp'];}
    //COMO EL DOM VA EN OTRA TABLA, LO PONE EN OTRA VARIABLE, A LA DEL VECTOR LE PONE @NO PARA QUE NO LO INSERTE, 
    $DOM = $CTE['dom'];
    $DOM['id_fcp'] = $ID_FCP;
    $CTE['dom'] = '@NO';
    if(strlen($ID_FCP)>0){
        $sql = genera_update($CTE, 'FCPS', 'ID_FCP');
        exe($sql);
        $sql = genera_update($DOM, 'FCPS_DATS', 'ID_FCP');
        exe($sql);
    }else {
        $sql = genera_insert($CTE, 'FCPS', 'ID_FCP');
        exe($sql);
        $DOM['id_fcp'] = lastID();
        exe("DELETE FROM FCPS_DATS WHERE ID_FCP = '$ID_FCP'");
        $sql = genera_insert($DOM, 'FCPS_DATS', '');
        exe($sql);
    }
    devolver_mensaje('ok',$CTE);
}

function ctes(){
    $sql = "SELECT * FROM FCPS AS F
            INNER JOIN FCPS_DATS AS FD ON FD.ID_FCP = F.ID_FCP
            WHERE F.BAJA = 0 AND F.ID_FCP > 1";
    $xxx = qry($sql);
    $xxx = resultado_minusculas($xxx);
    devolver_mensaje('ok', $xxx);
}
