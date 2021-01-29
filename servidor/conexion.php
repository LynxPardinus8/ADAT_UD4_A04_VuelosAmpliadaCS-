<?php
require 'vendor/autoload.php'; // incluir lo bueno de Composer

$cliente = new MongoDB\Client("mongodb://localhost:27017");

$coleccion = $cliente->vuelos2_0->vuelos;

?>