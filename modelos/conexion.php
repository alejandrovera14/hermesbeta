<?php

class Conexion{

    static public function conectar(){
<<<<<<< HEAD
        $link = new PDO("mysql:host=localhost;dbname=hermes002", "root", "");
=======
        $link = new PDO("mysql:host=localhost;dbname=hermes002", "root", "root");
>>>>>>> 090b15c (fix: update database connection settings to use 'hermes002' and set password)
        $link -> exec("set names utf8");
        return $link;
    }
}