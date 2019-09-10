<?php	
    session_start(); 
    session_unset(); //Borra las variables de la sesion
 	session_destroy(); //Destruye la sesion
	header('Location: ../../../index.php');