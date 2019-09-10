<?php
function iniciar(){
    $DATOS = json_decode(oPost('DATOS'), true);
    if($DATOS){
        try{
            $DATOS['mdl'] = 'ini'; 
            
            $sql = "SELECT U.*
                FROM usus AS U
                WHERE U.USU='" . $DATOS['email'] . "' AND U.PASS='" . $DATOS['clave'] . "' AND U.BAJA = 0";
            $regs = qry($sql);

            if (count($regs) == 0) {
                devolver_error('Usuario o Clave invalidos', '', 404);
            }
            
            $OTC_KEY = md5(uniqid(rand(), true));
            $FECHA_HORA_ACTURAL = date('Y-m-d H:i:s');
            $ID_USU = $regs[0]["ID_USU"];
            $xxx[] = Array(
                'ID_USU' => $regs[0]["ID_USU"],
                'USU' => $regs[0]["USU"],
                'APYNOM' => $regs[0]["APYNOM"],
                'MDL' => "ini",
                'FECHA' => $FECHA_HORA_ACTURAL,
                'ID_USU_TIP' => "1",
                'OTC_KEY' => $OTC_KEY
            );
            //CREA LA SESION Y PARAMETROS DE SEGURIDAD ****************************************************.
            $DATA_SESION = Array(
                'ID_USU' => $regs[0]["ID_USU"],
                'USU' => $regs[0]["USU"],
                'FECHA' => $FECHA_HORA_ACTURAL
            );
            $DATA_SESION = json_encode($DATA_SESION);
            $IP = getUserIP();
            $EQUIPO = $_SERVER['HTTP_USER_AGENT'];
            exe("DELETE FROM SESS WHERE ID_USU = '$ID_USU' AND EQUIPO = '$EQUIPO'");
            exe("INSERT INTO SESS (ID_USU, EQUIPO, DATA, IP, OTC_KEY, FECHA, ESTADO) VALUES ('$ID_USU','$EQUIPO','$DATA_SESION','$IP','$OTC_KEY','$FECHA_HORA_ACTURAL','0')");

            //**********************************************************************************************.
            //**********************************************************************************************.
            //
            //---------------------------------------------------------------------------------------------.
            //TODO: QUERY PARA MENUS ----------------------------------------------------------------------.
           /* $sql = "SELECT M.ID_MNU,M.ID_MNU_REG, M.NIVEL, M.MNU, M.MNU_RUT, M.MNU_ARC, if(MDL<>'' AND MDL<>'-',CONCAT(MNU_ARC,'~',MDL),MNU_ARC) AS MNU_ARC_MDL, M.VIS_RUT, M.VIS_ICO, M.MDL, M.ACC
                    FROM MENUES AS M 
                    INNER JOIN USUS_ACSS AS UA ON UA.ID_MNU_REG = M.ID_MNU_REG 
                    WHERE ID_USU = '$ID_USU'
                    ORDER BY M.ID_MNU";
            $xxx[] = qry($sql);
            //---------------------------------------------------------------------------------------------.
            //TODO: QUERY PARA USUARIO ACCESO -------------------------------------------------------------.
            $sql = "SELECT $ID_USU AS ID_USU,  UA.* 
                    FROM USUS_ACSS AS UA
                    WHERE ID_USU= $ID_USU";
            $xxx[] = qry($sql);
            //VARIABLE DE SEGURIDAD
            $_SESSION["USU_ACSS"] = $xxx[2];
            */
            //---------------------------------------------------------------------------------------------.

            devolver_mensaje('ok', $xxx);

        }catch (PDOException $e) {
            devolver_error('Error de servidor.','Error: ' . $e->getMessage() . ' QRY: '.$sql);
        }
    }else{
        devolver_error('No se recibieron datos.', '', 401);
    }
}

function verificar(){
    devolver_mensaje('ok','');
}
