<?php

function index(){
    echo 'Funcion Por defecto de este recurso.';
}
function test1(){
    echo 'Entraste a Test1';
}

function test2(){
    global $parametros;
    echo 'Entraste a Test2';
    if($parametros['id']){
        echo '<br>';
        echo "El id que enviaste es: ". $parametros['id'];
        echo '<br>';
        echo "El nombre que enviaste es: ". $parametros['nombre'];
    }
}