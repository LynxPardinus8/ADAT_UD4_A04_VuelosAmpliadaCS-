<?php

function modificarVuelo()
{

    require 'vendor/autoload.php'; // incluir lo bueno de Composer
    require 'conexion.php';

    $arrEsperado = array(
        "codigo" => "IB706",
        "dni" => "44556677H",
        "codigoVenta" => "GHJ7766GG",
        "dniNuevo" => "44556677H",
        "apellido" => "Rodriguez",
        "nombre" => "María"
    );

    function JSONCorrectoAnnadir($recibido)
    {

        $aux = false;

        if (isset($recibido["codigo"]) && isset($recibido["dni"]) && isset($recibido["codigoVenta"]) && isset($recibido["dniNuevo"]) && isset($recibido["apellido"]) && isset($recibido["nombre"])) {
            $aux = true;
        }

        return $aux;
    }
    

    $arrMensaje = array(); 

    $parameters = file_get_contents("php://input");

    if (isset($parameters)) {

        $mensajeRecibido = json_decode($parameters, true);

        if (JSONCorrectoAnnadir($mensajeRecibido)) {

            $codigo = $mensajeRecibido["codigo"];
            $dni = $mensajeRecibido["dni"];
            $codigoVenta = $mensajeRecibido["codigoVenta"];
            $dniNuevo = $mensajeRecibido["dniNuevo"];
            $apellido = $mensajeRecibido["apellido"];
            $nombre = $mensajeRecibido["nombre"];

            $updateResult = $coleccion->updateOne(
                ['dni' => $dni, 'codigoVenta' => $codigoVenta, 'codigo' => $codigo], 
                ['$set' => ["apellido" => $apellido, "nombre" => $nombre, "dni" => $dniNuevo]]
            );

            //-----------------------------------------

        if (isset ( $updateResult ) && $updateResult) { // Si pasa por este if, la query está está bien y se ha insertado correctamente
            $arrMensaje["estado"] = true;
            $arrMensaje["mensaje"] = "Se ha completado la actualizacion";
        }
        else { // Se ha producido algún error al ejecutar la query
            $arrMensaje["estado"] = false;
            $arrMensaje["mensaje"] = "No se ha podido realizar la actualizacion por error en la query";

        }

    }
    else { // Nos ha llegado un json no tiene los campos necesarios
        $arrMensaje["estado"] = false;
        $arrMensaje["mensaje"] = "No se ha podido realizar la actualizacion por que los campos no tiene los datos correspondientes";
        $arrMensaje["recibido"] = $mensajeRecibido;
        $arrMensaje["esperado"] = $arrEsperado;
    }

}
else { // No nos han enviado el json correctamente
    $arrMensaje["estado"] = false;
    $arrMensaje["mensaje"] = "No se ha podido realizar la actualizacion por error en los datos recibidos";

}

$mensajeJSON = json_encode($arrMensaje, JSON_PRETTY_PRINT);

//echo "<pre>";  // Descomentar si se quiere ver updateResult$updateResultado "bonito" en navegador. Solo para pruebas
echo $mensajeJSON;
//echo "</pre>"; // Descomentar si se quiere
}

