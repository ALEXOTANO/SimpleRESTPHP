<?php
include("../../zite/0-dbd/conn.php");
include("../../zite/0-dbd/funcs.php");
date_default_timezone_set("America/Argentina/Buenos_Aires");

if(!isset($_GET["MDL"])){echo "Error101: No se encontró el módulo";}


switch($_GET["MDL"]) {
     case "CTES":
         $DATOS = json_decode($_POST['DATOS'],true);
         $USU_SES = json_decode($_POST['USU_SES'],true);
         $FILTROS = json_decode($_POST['FILTROS'],true);
         $BUSQ = $DATOS['BUSQ'];

         $DBDS = DBD_EMPS($FILTROS['SUCS'],$USU_SES['EMP_DBD'],'0');
         $sql ="";
         foreach($DBDS as $DBD){
             $sql .= "SELECT C.* FROM $DBD.FCPS AS C WHERE C.BAJA = 0 AND CONCAT(FCP,' ',RSO) like '%$BUSQ%' LIMIT 10 UNION ALL";
         }
         $sql=trim(str_replace("\r\n",'',$sql));
         $sql = left($sql,strlen($sql)-strlen("UNION ALL"));
         $xxx[$_GET["MDL"]] = qry($sql);

         echo json_encode($xxx);
    break;
    case "ARTS":
        $DATOS = json_decode($_POST['DATOS'],true);
        $USU_SES = json_decode($_POST['USU_SES'],true);
        $FILTROS = json_decode($_POST['FILTROS'],true);
        $BUSQ = $DATOS['BUSQ'];


        $DBDS = DBD_EMPS($FILTROS['SUCS'],$USU_SES['EMP_DBD'],'0');
        $sql ="";
        foreach($DBDS as $DBD){
            $sql .= "SELECT A.* FROM $DBD.ARTS AS A WHERE A.BAJA = 0 AND A.ID_MDL = 7 AND CONCAT(CODIGO,' ',ART) like '%$BUSQ%' LIMIT 10 UNION ALL";
        }
        $sql=trim(str_replace("\r\n",'',$sql));
        $sql = left($sql,strlen($sql)-strlen("UNION ALL"));
        $xxx[$_GET["MDL"]] = qry($sql);

        echo json_encode($xxx);
    break;
    case "ARTS_RUBS":
        $DATOS = json_decode($_POST['DATOS'],true);
        $USU_SES = json_decode($_POST['USU_SES'],true);
        $FILTROS = json_decode($_POST['FILTROS'],true);
        $BUSQ = $DATOS['BUSQ'];

        $DBDS = DBD_EMPS($FILTROS['SUCS'],$USU_SES['EMP_DBD'],'0');
        $sql ="";
        foreach($DBDS as $DBD){
            $sql .= "SELECT A.* FROM $DBD.ARTS_RUBS AS A WHERE A.ID_ART_RUB > 1 AND A.BAJA = 0 AND CONCAT(ART_RUB) like '%$BUSQ%' LIMIT 10 UNION ALL";
        }
        $sql=trim(str_replace("\r\n",'',$sql));
        $sql = left($sql,strlen($sql)-strlen("UNION ALL"));
        $xxx[$_GET["MDL"]] = qry($sql);

        echo json_encode($xxx);
    break;
    case "CMPS":
        $DATOS = json_decode($_POST['DATOS'],true);
        $USU_SES = json_decode($_POST['USU_SES'],true);
        $FILTROS = json_decode($_POST['FILTROS'],true);
        $BUSQ = $DATOS['BUSQ'];

        $DBDS = DBD_EMPS($FILTROS['SUCS'],$USU_SES['EMP_DBD'],'0');
        $sql ="";
        foreach($DBDS as $DBD){
            $sql .= "SELECT * FROM $DBD.CMPS WHERE MDLS LIKE '%70%' AND CONCAT(CMP) like '%$BUSQ%' LIMIT 10 UNION ALL";
        }
        $sql=trim(str_replace("\r\n",'',$sql));
        $sql = left($sql,strlen($sql)-strlen("UNION ALL"));
        $xxx[$_GET["MDL"]] = qry($sql);

        echo json_encode($xxx);
        break;
    default:
    break;
}