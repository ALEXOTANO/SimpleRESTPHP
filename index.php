<?php
header('Access-Control-Allow-Origin: http://localhost:4200');
header("Content-Type: application/x-www-form-urlencoded",false);
header('Access-Control-Allow-Credentials: true', FALSE);

require_once('config/funcs.php');
//DETERMINA ARCHIVO , FUNCION Y PARAMETROS
$ruta = $_SERVER['REQUEST_URI'];
$pos = strpos($ruta,'.php');
$api = substr($ruta,$pos+4,strlen($ruta));
$datos = explode('/',$api);
$api = 'api/';
$funcion = '';
$parametros = Array();
$encontre_archivo = 0;
$encuentra_funcion = 0;
$encuentra_valor = 0;
foreach($datos as $d){
    if($encontre_archivo==1 && $encuentra_funcion==1){
        if($encuentra_valor==0){ 
            $encuentra_valor = 1;
            $key = $d;
        }else{
            $encuentra_valor = 0;
            $valor = $d;
            $parametros[$key] = $valor;        
        }
    }

    if($encontre_archivo==1 && $encuentra_funcion==0){
        $funcion = $d;
        $encuentra_funcion=1;
    }

    $archivo = $api . $d . '.php';
    if(file_exists($archivo) && strlen($d) > 0 && $encontre_archivo == 0){
        $api = $archivo;
        $encontre_archivo = 1;
    }    
}
//SI SE VA A EJECUTAR EL CIERRE DE SESION LO CIERRA Y DEVUELVE.
if($api=='api/login.php' && $funcion == 'logout'){

    $USU_SES = json_decode(oPost('SES'), true);    
    $ID_USU = $USU_SES[0]["ID_USU"];
    exe("DELETE FROM SESS WHERE ID_USU = '$ID_USU'");
    devolver_error('Logged out.','Logged out.', 401);       
}

//SI ES LOGIN LO DEJA PASAR
if(!($api=='api/login.php' && $funcion == 'iniciar')){
    //TOMA LOS DATOS DEL USUARIO ACTUAL
    $USU_SES = json_decode(oPost('SES'), true);    
    $USU_SES = $USU_SES[0];
    $IP_ACTUAL = getUserIP(); //NO LA VOY A SUAR POR AHORA PERO QUEDA
    $EQUIPO_ACTUAL = $_SERVER['HTTP_USER_AGENT'];
    $ID_USU = $USU_SES["ID_USU"];
    $SESION_RECIBIDA = Array(
        'ID_USU' => $USU_SES["ID_USU"],
        'USU' => $USU_SES["USU"]
    );
    if(!$USU_SES){
        devolver_error('No logged user.','No session data.', 401);
    }

    //TOMA LOS DATOS DE LA BASE PARA COMPARA SI EXISTE Y ES CORRECTA LA SESION
    $DATA_SESION = qry('SELECT * FROM SESS WHERE OTC_KEY = \'' . $USU_SES['OTC_KEY'] . '\' AND ID_USU = \'' . $USU_SES["ID_USU"] . '\'');
    if (count($DATA_SESION)==0){
        exe("DELETE FROM SESS WHERE ID_USU = '$ID_USU'");
        devolver_error('No logged user.','No session on DB.', 401);
    }

    //VERIFICA QUE LA SESION SEA DEL MISMO EQUIPO
    $DATA_SESION = $DATA_SESION[0];
    if($DATA_SESION['EQUIPO'] !==$EQUIPO_ACTUAL){
        exe("DELETE FROM SESS WHERE ID_USU = '$ID_USU'");
        devolver_error('No logged user.','Some change data from the original session, EQ:.'.$EQUIPO_ACTUAL, 401);
    }

    //REVISA DATOS GENERALES DE SESION DE USUARIO, DESPUES AGREGAR ACA LOS ACCESOS, POR AHORA QUEDA ASI BASICO
    $SESION_SERVER = json_decode($DATA_SESION['DATA'], TRUE);
    $SESION_SERVER = Array(
        'ID_USU' => $SESION_SERVER["ID_USU"],
        'USU' => $SESION_SERVER["USU"]
    );

    if($SESION_SERVER != $SESION_RECIBIDA){
        exe("DELETE FROM SESS WHERE ID_USU = '$ID_USU'");
        devolver_error('No logged user.','Invalid session, it\'s been modified.', 401);
    }
}
include_once('config/conn.php');

try{
    eval('require (\'' . $api . '\');');
}catch(Exception $e) {
    echo "Error loadign API.";
}

try{

    if(strlen($funcion)>0){
        if(function_exists($funcion)){
           eval($funcion.'();');
        }else{
            devolver_error('No method.', $funcion, 401);
        }
    }else{
        index();
    }
}catch(Exception $e) {
    devolver_error('No method.', $funcion, 401);
}
 