<?php
function mostrarVuelos()
{

    require 'vendor/autoload.php'; // incluir lo bueno de Composer
    require 'conexion.php';

    $parameters = array();
    $contador = 0;

    if (isset($_GET["fecha"]) && isset($_GET["origen"]) && isset($_GET["destino"])) {

        $fecha = $_GET["fecha"];
        $origen = $_GET["origen"];
        $origen = strtoupper($origen);
        $destino = $_GET["destino"];
        $destino = strtoupper($destino);


        $result = $coleccion->find(['fecha' => $fecha, 'origen' => $origen, 'destino' => $destino]);
        $parameters["fecha"] = $fecha;
        $parameters["origen"] = $origen;
        $parameters["destino"] = $destino;

    } elseif (isset($_GET["fecha"])) {

        $fecha = $_GET["fecha"];
        $fecha = strtoupper($fecha);
        $result = $coleccion->find(['fecha' => $fecha]);
        $parameters["fecha"] = $fecha;

    } elseif (isset($_GET["origen"])) {

        $origen = $_GET["origen"];
        $origen = strtoupper($origen);
        $result = $coleccion->find(['origen' => $origen]);
        $parameters["origen"] = $origen;

    } elseif (isset($_GET["destino"])) {

        $destino = $_GET["destino"];
        $destino = strtoupper($destino);
        $result = $coleccion->find(['destino' => $destino]);
        $parameters["destino"] = $destino;

    } else {

        $result = $coleccion->find();

    }

    $encontrados = $coleccion->count();
    $vuelos = array();


    if (isset($result) && $result) {

        if ($encontrados > 0) {

            foreach ($result as $entry) {
                
                $arrayVuelo = array(

                    "codigo" => $entry['codigo'],
                    "origen" => $entry['origen'],
                    "destino" => $entry['destino'],
                    "fecha" => $entry['fecha'],
                    "hora" => $entry['hora'],
                    "plazas_totales" => $entry['plazas_totales'],
                    "plazas_disponibles" => $entry['plazas_disponibles'],
                    "precio" => $entry['precio']
                );

                $vuelos[] = $arrayVuelo;
                $contador++;
            }

            $arrayMensaje = array(
                "estado" => true,
                "encontrados" => $contador,
                "busqueda" => $parameters,
                "vuelos" => $vuelos
            );

            $mensajeJSON = json_encode($arrayMensaje, JSON_PRETTY_PRINT);
        } else {
            $arrayMensaje["estado"] = true;
            $arrayMensaje["encontrados"] = 0;
        }
    } else {
        $arrayMensaje["estado"] = "error";
        $arrayMensaje["mensaje"] = "Se ha producido un error al conectar con la BD";
    }
    if (isset($_GET["debug"]) && $_GET["debug"] == 1) {
        echo "<pre>";
        echo $mensajeJSON;
        echo "</pre>";
    } else {
        echo $mensajeJSON;
    }
}
