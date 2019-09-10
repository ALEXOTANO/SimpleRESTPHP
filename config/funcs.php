<?php
require_once('conn.php');
require_once('mat_enc.php');
//================================================================================================== GENERALES DE API
function oPost($dato){
    if (!isset($_POST[$dato])){
        return FALSE;
    }else{
        return $_POST[$dato];
    }
}
function oParams($dato){
    global $parametros;
    if (!isset($parametros[$dato])){
        return FALSE;
    }else{
        return $parametros[$dato];
    }
}
function oGet($dato){
    if (!isset($_GET[$dato])){
        return FALSE;
    }else{
        return $_GET[$dato];
    }
}
function oServer($dato){
    if (!isset($_SERVER[$dato])){
        return FALSE;
    }else{
        return $_SERVER[$dato];
    }
}
function genera_insert($DATOS_ARREGLO, $TABLA, $ID){
    global $con_pdo;
    //CONVERTIR A ARREGLO DE STRINGS
    $ARREGLO_VALORES = array_values($DATOS_ARREGLO);
    $ARREGLO_COLUMNAS = array_keys($DATOS_ARREGLO);    
    //RECORREMOS
    $VALORES = array();
    $COLUMNAS = array();
    $i = 0;
    foreach($ARREGLO_COLUMNAS as $COL){
        if(strtoupper($COL) !== strtoupper($ID) && $ARREGLO_VALORES[$i] !== '@NO'){
            $COLUMNAS[] = strtoupper($COL);
            $VALORES[] = $con_pdo->quote($ARREGLO_VALORES[$i]);
        }
        $i++;
    }
    $COLUMNAS = implode(", ", $COLUMNAS);
    $VALORES = implode(',', $VALORES);
    $QUERY = "INSERT INTO $TABLA ($COLUMNAS) VALUES ($VALORES)";
    return $QUERY;

}
function genera_update($DATOS_ARREGLO, $TABLA, $ID){
    global $con_pdo;
    //CONVERTIR A ARREGLO DE STRINGS
    $ARREGLO_VALORES = array_values($DATOS_ARREGLO);
    $ARREGLO_COLUMNAS = array_keys($DATOS_ARREGLO);    
    //RECORREMOS
    $SET = array();
    $WHERE = "";
    $i = 0;
    foreach($ARREGLO_COLUMNAS as $COL){
        if(strtoupper($COL) !== strtoupper($ID) && $ARREGLO_VALORES[$i] !== '@NO'){
            $SET[] = strtoupper($COL) . "=" . $con_pdo->quote($ARREGLO_VALORES[$i]);            
        }else{
            if($ARREGLO_VALORES[$i] !== '@NO'){
                $WHERE = strtoupper($COL) . " = " . $con_pdo->quote($ARREGLO_VALORES[$i]);
            }
        }
        $i++;
    }
    $SET = implode(", ", $SET);
    $QUERY = "UPDATE  $TABLA SET $SET WHERE $WHERE";
    return $QUERY;

}
function resultado_minusculas($RESULTADO_ARRAY){
    $RES = array_change_key_case($RESULTADO_ARRAY);
    $i = 0;
    foreach($RES AS $VAL){
        $RES[$i] = array_change_key_case($VAL);
        $i++;
    }
    return $RES;

}
function devolver_mensaje($mensaje, $data){
    echo json_encode(
        Array(
            "status" => $mensaje, 
            "data" => $data
            )
    );
}
function devolver_error($mensaje, $data, $error_numero_http=500){
    global $con_pdo;
    try{
        $qqq = "INSERT INTO LOGS_ERRORES (FECHA, MENSAJE, ERROR) VALUES(NOW(), '$mensaje', '$data')";
        $query = $con_pdo->prepare($qqq);
        $query->execute();
    }catch (PDOException $e) {
        http_response_code($error_numero_http);
        echo json_encode(
            Array(
                "status" => $mensaje, 
                "data" => "Error al generar el log del error: " . $e->getMessage(),
                "error" => $data
                )
        );   
        exit;
    }

    http_response_code($error_numero_http);
    echo json_encode(
        Array(
            "status" => $mensaje, 
            "data" => "Error-log cargado...",
            "error" => $data
            )
    );
    exit;
}
function qry($qqq){
    global $con_pdo;
    try{
        $query = $con_pdo->prepare($qqq);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }catch (PDOException $e) {
        devolver_error('ERROR SERVER QRY', $e->getMessage(). "QRY: ".$qqq, 500);
    }
}
function exe($qqq){
    global $con_pdo;
    try{
        $query = $con_pdo->prepare($qqq);
        return $query->execute();
    }catch (PDOException $e) {
        devolver_error('ERROR SERVER EXE', $e->getMessage() . "QRY: ".$qqq, 500);
    }
}
//================================================================================================== CORREOS ELECTRONICOS
function SIB_Mail($deNombre,$deMail,$paraNombre,$paraMail,$asunto,$msj,$detalle){
    require_once '../../../zite/3-plugins/sendinblue/Mailin.php';
    // ver datos en https://github.com/mailin-api/mailin-api-php/blob/master/V2.0/examples/tutorial1.php
    $SIB = new Mailin('https://api.sendinblue.com/v2.0','w08IW2OnYycAXzRH',5000);

    $datos = array( "to" => array($paraMail=>$paraNombre), //aca se pueden poner varios mails, habria que recorrer el arreglo
        //"cc" => array("cc@example.net"=>"cc whom!"),      // hacer un slit de ; y ~ ... mandar un texto nombre~mail;nombre2~mail2;nombre3~mail3;
        //"bcc" =>array("bcc@example.net"=>"bcc whom!"),
        "from" => array($deMail,$deNombre),
        "replyto" => array($deMail,$deNombre),
        "subject" => $asunto,
        "html" => $msj //."<img src='{".$filePOST."}' alt='image' border='0'><br/>", //asi se pone el archivo en el cuerpo del mensaje
        //"text" => $textPOST, //MENSAJE SOLO DE TEXTO
        //"inline_image" => array($filePOST => $b64POST) //arrerglo de archivos de iamgen para ponerlos dentro del correo
        //"attachment" => array(), // ver el tema de los attachments
    );
    $respuesta = $SIB->send_email($datos);
    if($respuesta["code"] === "success"){
        return true;
    }
    return $respuesta;




}
function mail_otc_sendgrid($deNombre,$deMail,$paraNombre,$paraMail,$asunto,$msj,$detalle){
    require_once '..//sendGrid/lib/helpers/mail/Mail.php';
    require_once 'Mis_Plugins/sendGrid/lib/Client.php';
    require_once 'Mis_Plugins/sendGrid/lib/Response.php';
    require_once 'Mis_Plugins/sendGrid/lib/SendGrid.php';


    $from = new SendGrid\Email($deNombre, $deMail);
    $to = new SendGrid\Email($paraNombre, $paraMail);
    $content = new SendGrid\Content("text/html", $msj);
    $mail = new SendGrid\Mail($from, $asunto, $to, $content);

    $apiKey = ""; //YOUR SENDDRID API KEY

    $sg = new \SendGrid($apiKey);

    $response = $sg->client->mail()->send()->post($mail);
    if($detalle == '1') {
        $detalle = "CODE: " . $response->statusCode() . "<br>";
        for($i = 0;$i<= sizeof($response->headers())-1;$i++){
            $detalle .=$response->headers()[$i]."<br>";
        }
        $detalle .= "\r\n ".$response->body();
        return $detalle;
    }else{
        return $response->statusCode();
    }
}
//================================================================================================== NETWORKING REDES Y DATA DE MAQUINA USUARIO
function getUserIP(){
    $client  = '';//@$_SERVER['HTTP_CLIENT_IP'];
    $forward = '';//@$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP))
    {
        $ip = $client;
    }
    elseif(filter_var($forward, FILTER_VALIDATE_IP))
    {
        $ip = $forward;
    }
    else
    {
        $ip = $remote;
    }

    return $ip;
}
function Hacer_Ping($ip){
    $ping = exec("PING -c2 -w2 " . $ip, $outcome, $status);
    if ($status == 0) {
        $status = true;
    } else {
        $status = false;
        if(strpos ( $outcome[0], 'administrative')>=0){$status = true;}

    }

    return $status;
}
//================================================================================================== ENCRIPTACION Y CODIFICACION
function otcKey(){
    global $con_pdo;

    $ID_USU_KEY = "0";
    $otcKey= "";

    while ($ID_USU_KEY != ""){
        $otcKey = md5(uniqid(rand(), true));
        $ID_USU_KEY = id_det($otcKey, "USU_KEY", "ID_USU_KEY", "USUS_KEYS");
    }
    return $otcKey;
}
function Enc_Dat($Dato){
    $Tex = "";
    $col = 1;
    for($i=1;$i<=strlen($Dato);$i++){
        if(intval(($i / 3)) == ($i / 3)) {
            $col++;
            if($col > 7){$col = 1;}
        }
        $ka = Enc_Asg("E", strtoupper(substr($Dato, $i-1, 1)), $col);
        $Tex = $Tex.$ka;
    }

    return $Tex;


}
function Dat_Enc($Dato){
    $Tex = "";
    $col = 1;
    for($i=1;$i<=strlen($Dato);$i++){
        if(intval(($i / 3)) == ($i / 3)) {
            $col++;
            if($col > 7){$col = 1;}
        }
        $ka = Enc_Asg("D", substr($Dato, $i-1, 1), $col);
        $Tex = $Tex.$ka;
    }

    return $Tex;

}
function DesEncripta($xDato){
    $key = pack('H*', "bcb04230688a0cd8b5476305abcabcbc55abe029fdebae5e1d417e2ffb2a00a3");
    $iv_size = 16; //ojo esta es una constante que se asume a partir de la encriptacion, REVISAR FUNCION Encripta();
    $ciphertext_dec = base64_decode($xDato);
    $iv_dec = substr($ciphertext_dec, 0, $iv_size); //es el valor de $iv_size que se determina en la encriptacion .. hay que ver que siempre sea 16..
    $ciphertext_dec = substr($ciphertext_dec, $iv_size);
    $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key,$ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
    $plaintext_dec = rtrim($plaintext_dec, "\0");

    return $plaintext_dec;

}
function Encripta($xDato){
    $key = pack('H*', "bcb04230688a0cd8b5476305abcabcbc55abe029fdebae5e1d417e2ffb2a00a3");
    //$key_size =  strlen($key);
    $plaintext = $xDato;
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC); //esto hay que ver que siempre sea 16... se usa en la desencriptacion
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key,$plaintext, MCRYPT_MODE_CBC, $iv);
    $ciphertext = $iv . $ciphertext;
    $ciphertext_base64 = base64_encode($ciphertext);

    return $ciphertext_base64;
}
function Enc_Asg($Tip, $Letra, $Col){
    global $Mat_Enc;
    $SqlX = "";
    if($Tip == "E"){
        for($Y = 0;$Y<=49;$Y++){
            if($Mat_Enc[$Y][0] == $Letra) {
                $SqlX = $Mat_Enc[$Y][$Col]; 
                break;
            }
        }
    }else{
        for($Y = 0;$Y<=49;$Y++){
            if($Mat_Enc[$Y][$Col] == $Letra){
                $SqlX = $Mat_Enc[$Y][0];
                break;
            }
        }
    }
    if($SqlX <> "" ) {
        return $SqlX;
    }else {
        return "@";
    }

}
$cp1252_map = array(
    "\xc2\x80" => "\xe2\x82\xac", /* EURO SIGN */
    "\xc2\x82" => "\xe2\x80\x9a", /* SINGLE LOW-9 QUOTATION MARK */
    "\xc2\x83" => "\xc6\x92",     /* LATIN SMALL LETTER F WITH HOOK */
    "\xc2\x84" => "\xe2\x80\x9e", /* DOUBLE LOW-9 QUOTATION MARK */
    "\xc2\x85" => "\xe2\x80\xa6", /* HORIZONTAL ELLIPSIS */
    "\xc2\x86" => "\xe2\x80\xa0", /* DAGGER */
    "\xc2\x87" => "\xe2\x80\xa1", /* DOUBLE DAGGER */
    "\xc2\x88" => "\xcb\x86",     /* MODIFIER LETTER CIRCUMFLEX ACCENT */
    "\xc2\x89" => "\xe2\x80\xb0", /* PER MILLE SIGN */
    "\xc2\x8a" => "\xc5\xa0",     /* LATIN CAPITAL LETTER S WITH CARON */
    "\xc2\x8b" => "\xe2\x80\xb9", /* SINGLE LEFT-POINTING ANGLE QUOTATION */
    "\xc2\x8c" => "\xc5\x92",     /* LATIN CAPITAL LIGATURE OE */
    "\xc2\x8e" => "\xc5\xbd",     /* LATIN CAPITAL LETTER Z WITH CARON */
    "\xc2\x91" => "\xe2\x80\x98", /* LEFT SINGLE QUOTATION MARK */
    "\xc2\x92" => "\xe2\x80\x99", /* RIGHT SINGLE QUOTATION MARK */
    "\xc2\x93" => "\xe2\x80\x9c", /* LEFT DOUBLE QUOTATION MARK */
    "\xc2\x94" => "\xe2\x80\x9d", /* RIGHT DOUBLE QUOTATION MARK */
    "\xc2\x95" => "\xe2\x80\xa2", /* BULLET */
    "\xc2\x96" => "\xe2\x80\x93", /* EN DASH */
    "\xc2\x97" => "\xe2\x80\x94", /* EM DASH */

    "\xc2\x98" => "\xcb\x9c",     /* SMALL TILDE */
    "\xc2\x99" => "\xe2\x84\xa2", /* TRADE MARK SIGN */
    "\xc2\x9a" => "\xc5\xa1",     /* LATIN SMALL LETTER S WITH CARON */
    "\xc2\x9b" => "\xe2\x80\xba", /* SINGLE RIGHT-POINTING ANGLE QUOTATION*/
    "\xc2\x9c" => "\xc5\x93",     /* LATIN SMALL LIGATURE OE */
    "\xc2\x9e" => "\xc5\xbe",     /* LATIN SMALL LETTER Z WITH CARON */
    "\xc2\x9f" => "\xc5\xb8"      /* LATIN CAPITAL LETTER Y WITH DIAERESIS*/
);
//================================================================================================== ID_NVO PARAMETROS UPDS
function lastID(){
    global $con_pdo;
    $sql =  "SELECT LAST_INSERT_ID()";
    try{
        $query = $con_pdo->prepare($sql);
        $query->execute();
        $list = $query->fetchAll();
        foreach ($list as $rs) {
            return $rs[0];
        }
    } catch (PDOException $e) {
        return NULL;
    }
}
function Id_Det( $id, $id_campo, $campo, $tabla, $Condiciones="" ){
    global $con_pdo;
    $sql10 = "SELECT ".$campo." FROM ".$tabla." WHERE ".$id_campo." = '".$id."' ".$Condiciones. " LIMIT 1";
    try{
        $query = $con_pdo->prepare($sql10);
        $query->execute();
        $list = $query->fetchAll();
        $DATO=null;
        foreach ($list as $rs) {$DATO = $rs[$campo];}

    }catch (PDOException $e) {
        echo 'Error ID_DET: ' . $e->getMessage() . '<br><br>SQL:' . $sql10;
        exit;
    }
	return $DATO;
}
function Id_Nvo($SYS_PRM){
	global $con_pdo;
    $Id_Actual =null;
	$Id_Actual = Id_Det($SYS_PRM,"SYS_PRM","SYS_PRM_VAL","SYSS_PRMS");
	$Id_Actual++;
	$sql10 = "UPDATE SYSS_PRMS SET SYS_PRM_VAL = " . $Id_Actual . " WHERE SYS_PRM = '" . $SYS_PRM . "'";
    try {
        $query = $con_pdo->prepare($sql10);;
        $query->execute();
    } catch (PDOException $e) {
        $Id_Actual = null;
    }
	return $Id_Actual;
}

function SYS_PRM($SYS_PRM){
    global $PRMS,$con_pdo,$Mostrar_Errores;
    $sql1 = "SELECT SYS_PRM_VAL FROM SYSS_PRMS WHERE SYS_PRM = '" . $SYS_PRM . "'";
    try {
        $query = $con_pdo->prepare($sql1);;
        $query->execute();
        $list = $query->fetchAll();
        $SYS_PRM_VAL = null;
        foreach ($list as $rs) {
            $SYS_PRM_VAL = $rs["SYS_PRM_VAL"];
        }
    } catch (PDOException $e) {
        $SYS_PRM_VAL = null;
    }
    return $SYS_PRM_VAL;
}
//================================================================================================== LEFT RIGTH
function right($value, $count){
    return substr($value, ($count*-1));
}
function left($string, $count){
    return substr($string, 0, $count);
}
//================================================================================================== NUMEROS
function nro_imp($importe,$DC=0){
    switch($DC) {
        case 1:
            return str_replace(".", "",str_replace(",", "",number_format(floatval($importe),4)));
            break;
        case 0:
            return intval(str_replace(".", "", str_replace(",", "", $importe)));
            break;
        default:
            return intval(str_replace(".", "", str_replace(",", "", $importe)));
            break;
    }
}
function imp_nro($importe){
	if (left($importe, 1) == "-") {
		$sig = "-";
		$importe = right($importe, strlen($importe) - 1);
	}else{
		$sig = "";
	}

	switch (strlen($importe)) {
	case 0:
		$importe = "00000" . $importe;
		break;
	case 1:
		$importe = "00" . $importe;
		break;
	case 2:
	    $importe = "0" . $importe;
	    break;
	}
	//SE PARAMETRIZO QUE SI O SI UASRA PUNTO DECIMAL Y NO COMA
	if (Configuracion_Regional() == 0) {
		return $sig . sprintf(left($importe, strlen($importe) - 2) . "." . right($importe, 2), "#,##0.00");	//ESTE ES PARA QUE USE COMA SE REEMPLAZO POR PUNTO
	}else{
		return $sig . sprintf(left($importe, strlen($importe) - 2) . "." . right($importe, 2), "#,##0.00");
	}
}
function imp_nro_3($importe){
	switch (strlen($importe)) {
	Case 0:
		$importe = "000000" . $importe;
		break;
	Case 1:
		$importe = "000" . $importe;
		break;
	Case 2:
		$importe = "0" . $importe;
		break;
	}

	return sprintf(left($importe, strlen($importe) - 3) . "." . right($importe, 3), "#,##0.000");
}
function imp_nro_4($importe){
    switch (strlen($importe)) {
        Case 0:
            $importe = "000000" . $importe;
            break;
        Case 1:
            $importe = "0000" . $importe;
            break;
        Case 2:
            $importe = "0". $importe;
            break;
    }

    return sprintf(left($importe, strlen($importe) - 4) . "." . right($importe, 4), "#,##0.0000");
}
function Configuracion_Regional(){
	$q = 8;
	$q = strlen(sprintf("1.000,00", "#,##0.00"));
	if ($q == 8) {
		return 0;
	}else{
		return 1;
	}
}
//================================================================================================== FECHAS Y HORAS
function nro_fec($x){
    if(strlen($x)<3){return 0;}
    $x = left($x,10);
	return right($x, 4) . substr($x, 3, 2) . left($x, 2);
}
function fec_fecMySQL($x){
    if(strlen($x)<3){return 0;}
    $x = left($x,10);
    return  right($x, 2)."/". substr($x, 5, 2) ."/". left($x, 4);
}
function fec_nro($fec){
    if(strlen($fec)<7){return "fe/no/vali";}
    $fec = left($fec,10);
    return right($fec,2) . "/" . right(left($fec,6),2) . "/" . left($fec,4);
}
function fecUNIX_nro($fec){
    if(strlen($fec)<7){return "fe/no/vali";}
    return left($fec,4) .  "-" . right(left($fec,6),2) . "-" . right($fec,2);
}
function sumaDia($fecha,$dia){
	list($day,$mon,$year) = explode("/",$fecha);
	return date('d/m/Y',mktime(0,0,0,$mon,$day+$dia,$year));
}
function sumaMes($fecha,$Meses){
    list($day,$mon,$year) = explode('/',$fecha);
    return date('d/m/Y',mktime(0,0,0,$mon+$Meses,$day,$year));
}
function hora_actual_seg(){
    $horas = date('H', time());
    $minutos = date('i', time());
    $segundos = date('s', time());

    return ($horas*60*60)+($minutos*60);//+$segundos;
}
function hora_numero($x){
	$x = $x / 3600;
	$horas = intval($x);
	$minutos = ($x - $horas) * 60;
	$segundos = ($minutos - intval($minutos)) * 60;
	if ($segundos - intval($segundos) > 0.5) {$segundos = $segundos + 1;}
	if ($segundos >= 60) {
		$minutos = $minutos + 1;
		$segundos = $segundos - 1;
	}
	if ($minutos >= 60) {
		$horas = $horas + 1;
		$minutos = $minutos - 1;
	}

	$horas = intval($horas);
	$minutos = intval($minutos);
	$segundos = intval($segundos);

	return sprintf('%02d', $horas) . ":" . sprintf('%02d', $minutos);
}
function numero_hora($HH_mm){
    //REVISA QUE TENGA POR LO MENOS 5 CARACTERES
    if(strlen($HH_mm)<5){
        return "00:00";
    }
    //SACA LOS : SEPARADORES
    $HH_mm = str_replace(':','',$HH_mm);

    //REVISA SI CONTIENE O NO SEGUNDOS
    if(strlen($HH_mm)>4){
        $horas = intval(substr($HH_mm,0,2));
        $minutos = intval(substr($HH_mm,2,2));
        $segundos = intval(substr($HH_mm,4,2));
    }else{
        $horas = intval(substr($HH_mm,0,2));
        $minutos = intval(substr($HH_mm,2,2));
        $segundos = 0;
    }
    return (($horas*3600)+($minutos*60)+$segundos);
}
function Fecha_Diferencia($mmddYYYY_1, $mmddYYYY_2) {
    $current = $mmddYYYY_1;
    $datetime2 = date_create($mmddYYYY_2);
    $count = 0;
    while(date_create($current) < $datetime2){
        $current = gmdate("Y-m-d", strtotime("+1 day", strtotime($current)));
        $count++;
    }
    return $count;
}
